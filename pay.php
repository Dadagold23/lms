<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin(); // ✅ student must login - only this

$studentId    = (int)($_SESSION['user']['id'] ?? 0);
$email        = (string)($_SESSION['user']['email'] ?? '');
$enrollmentId = (int)($_GET['enrollment_id'] ?? $_POST['enrollment_id'] ?? 0);

if ($studentId <= 0 || $enrollmentId <= 0 || $email === '') {
    http_response_code(400);
    exit('Invalid request.');
}

/* ======================
   FETCH ENROLLMENT + COURSE + PAYMENT TYPE
====================== */
$stmt = $pdo->prepare("
    SELECT
        e.id AS enrollment_id,
        e.student_id,
        e.course_id,
        COALESCE(e.paid_amount, 0) AS paid_amount,
        COALESCE(e.status, 'active') AS enroll_status,
        COALESCE(e.payment_type, 'full') AS payment_type,
        e.next_due_date,
        c.title,
        c.price
    FROM lms_enrollments e
    INNER JOIN lms_courses c ON c.id = e.course_id
    WHERE e.id = ? AND e.student_id = ?
    LIMIT 1
");
$stmt->execute([$enrollmentId, $studentId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    exit('Invalid course enrollment.');
}

$courseTitle  = (string)$row['title'];
$coursePrice  = (float)$row['price'];
$paidAmount   = (float)$row['paid_amount'];
$paymentType  = (string)$row['payment_type'];
$enrollStatus = (string)$row['enroll_status'];

if ($coursePrice <= 0) {
    http_response_code(400);
    exit('Course price not set.');
}

// Fetch student's affiliate info to apply price cap
$st = $pdo->prepare("SELECT is_affiliate, affiliate_class_range FROM lms_students WHERE id = ? LIMIT 1");
$st->execute([$studentId]);
$student = $st->fetch() ?: [];
$isAffiliate = !empty($student['is_affiliate']);
$classRange  = $student['affiliate_class_range'] ?? '';

if ($isAffiliate && ($classRange === 'JSS' || $classRange === 'SSS')) {
    $coursePrice = min($coursePrice, 5000.0);
}

$balance = max(0, $coursePrice - $paidAmount);

if ($balance <= 0) {
    redirect('dashboard.php');
}

// For installment: first payment = 50%, subsequent = remaining balance
$isFirstPayment = ($paidAmount <= 0);
if ($paymentType === 'installment' && $isFirstPayment) {
    $due = round($coursePrice * 0.5, 2); // 50% first installment
} else {
    $due = $balance; // full balance (or remaining installment)
}

$amountKobo = (int) round($due * 100);
$reference  = '';
$manualReference = '';
$manualFlash = $_SESSION['manual_payment_flash'] ?? null;
unset($_SESSION['manual_payment_flash']);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $action = (string)($_POST['action'] ?? '');
    if ($action === 'manual_request') {
        $manualStmt = $pdo->prepare("
            SELECT id, reference
            FROM lms_payments
            WHERE student_id = ?
              AND enrollment_id = ?
              AND channel = 'manual'
              AND status = 'pending'
              AND amount = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $manualStmt->execute([$studentId, $enrollmentId, $due]);
        $existingManual = $manualStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingManual && !empty($existingManual['reference'])) {
            $manualReference = (string)$existingManual['reference'];
        } else {
            $manualReference = 'MANUAL_' . bin2hex(random_bytes(6));
            $pdo->prepare("
                INSERT INTO lms_payments (
                    student_id,
                    enrollment_id,
                    amount,
                    channel,
                    reference,
                    status,
                    created_at
                ) VALUES (?,?,?,?,?,'pending',NOW())
            ")->execute([
                $studentId,
                $enrollmentId,
                $due,
                'manual',
                $manualReference,
            ]);
        }

        $_SESSION['manual_payment_flash'] = "Manual payment request submitted. Give admin this reference: {$manualReference}";
        redirect('pay.php?enrollment_id=' . $enrollmentId);
    } elseif ($action === 'campaign_portal_payment') {
        if ($isAffiliate) {
            try {
                require_once __DIR__ . '/includes/affiliate_helpers.php';
                
                // Retrieve the partner/campaign details for this referred student
                $stmtRef = $pdo->prepare("SELECT partner_id, campaign_id FROM lms_affiliate_referrals WHERE pupil_email = ? LIMIT 1");
                $stmtRef->execute([$email]);
                $referral = $stmtRef->fetch(PDO::FETCH_ASSOC);
                
                $partnerId = $referral ? (int)$referral['partner_id'] : 0;
                $campaignId = ($referral && $referral['campaign_id'] !== null) ? (int)$referral['campaign_id'] : null;
                
                if ($partnerId > 0) {
                    processCampaignPayment($pdo, $studentId, $enrollmentId, $due, $partnerId, $campaignId);
                    $_SESSION['pay_success'] = "Course unlocked successfully via Campaign/Partner Portal Payment Access.";
                    redirect('payments.php');
                } else {
                    $_SESSION['manual_payment_flash'] = "No valid affiliate partner referral found for this student.";
                    redirect('pay.php?enrollment_id=' . $enrollmentId);
                }
            } catch (Throwable $e) {
                error_log("Failed campaign portal payment: " . $e->getMessage());
                $_SESSION['manual_payment_flash'] = "Failed to process campaign/partner payment: " . $e->getMessage();
                redirect('pay.php?enrollment_id=' . $enrollmentId);
            }
        }
    }
}

/* ======================
   REUSE OR CREATE PENDING PAYMENT
====================== */
$stmt = $pdo->prepare("
    SELECT id, reference
    FROM lms_payments
    WHERE student_id = ?
      AND enrollment_id = ?
      AND status = 'pending'
      AND amount = ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$studentId, $enrollmentId, $due]);
$pending = $stmt->fetch(PDO::FETCH_ASSOC);

if ($pending && !empty($pending['reference'])) {
    $reference = (string)$pending['reference'];
} else {
    $reference = 'LMS_' . bin2hex(random_bytes(6));
    $stmt = $pdo->prepare("
        INSERT INTO lms_payments (
            student_id,
            enrollment_id,
            amount,
            channel,
            reference,
            status,
            created_at
        ) VALUES (?,?,?,?,?,'pending',NOW())
    ");
    $stmt->execute([
        $studentId,
        $enrollmentId,
        $due,
        'paystack',
        $reference
    ]);
}

/* ======================
   PAYSTACK CONFIG
====================== */
$paystack          = require __DIR__ . '/config/paystack.php';
$paystackPublicKey = $paystack['public_key'];
// Use live callback URL from .env, fallback to relative path
$paystackCallback  = !empty($paystack['callback_url'])
    ? rtrim($paystack['callback_url'], '/') . '/pay_verify.php'
    : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
       . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
       . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\') . '/pay_verify.php');
?>
<!doctype html>
<html lang="en">
<head>
  <?php
$seoTitle   = 'Make Payment';
$seoDesc    = 'Complete your course payment at Grafix@Mirror LMS — Mirror Age Concepts.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
  <title>Pay for <?= e($courseTitle) ?> | Paystack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">

<div class="card shadow p-4" style="max-width:480px;width:100%;border-radius:14px">
  <h5 class="mb-1"><?= e($courseTitle) ?></h5>
  <p class="text-muted small mb-3">Complete your payment to access course content.</p>

  <?php if ($manualFlash): ?>
    <div class="alert alert-info small"><?= e($manualFlash) ?></div>
  <?php endif; ?>

  <table class="table table-sm mb-3">
    <tr><td class="text-muted">Course Fee</td><td class="fw-semibold text-end">₦<?= number_format($coursePrice, 2) ?></td></tr>
    <tr><td class="text-muted">Already Paid</td><td class="fw-semibold text-end text-success">₦<?= number_format($paidAmount, 2) ?></td></tr>
    <?php if ($paymentType === 'installment' && $isFirstPayment): ?>
    <tr class="table-warning"><td>1st Installment (50%)</td><td class="fw-bold text-end">₦<?= number_format($due, 2) ?></td></tr>
    <tr><td class="text-muted small">Balance after this</td><td class="text-muted small text-end">₦<?= number_format($balance - $due, 2) ?></td></tr>
    <?php elseif ($paymentType === 'installment'): ?>
    <tr class="table-info"><td>Remaining Installment</td><td class="fw-bold text-end">₦<?= number_format($due, 2) ?></td></tr>
    <?php else: ?>
    <tr class="table-primary"><td>Amount Due</td><td class="fw-bold text-end">₦<?= number_format($due, 2) ?></td></tr>
    <?php endif; ?>
  </table>

  <div class="d-grid gap-2">
    <button id="payBtn" class="btn btn-success btn-lg">
      Pay ₦<?= number_format($due, 2) ?> via Paystack
    </button>
    <?php if ($isAffiliate): ?>
    <form method="post">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="enrollment_id" value="<?= (int)$enrollmentId ?>">
      <input type="hidden" name="action" value="campaign_portal_payment">
      <button class="btn btn-info w-100 fw-bold text-dark" type="submit">
        <i class="fa fa-key me-1"></i> Access via Campaign / Partner Portal
      </button>
    </form>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="enrollment_id" value="<?= (int)$enrollmentId ?>">
      <input type="hidden" name="action" value="manual_request">
      <button class="btn btn-outline-primary w-100" type="submit">
        Request Manual Approval Instead
      </button>
    </form>
    <div class="small text-muted">
      Use this if you paid by bank transfer or want admin to confirm offline. The LMS will create a pending manual-payment record for admin approval.
    </div>
    <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
  </div>
</div>

<script>
document.getElementById('payBtn').addEventListener('click', function () {
  const handler = PaystackPop.setup({
    key: <?= json_encode($paystackPublicKey) ?>,
    email: <?= json_encode($email) ?>,
    amount: <?= (int)$amountKobo ?>,
    ref: <?= json_encode($reference) ?>,
    currency: 'NGN',
    callback: function (response) {
      window.location.href = <?= json_encode($paystackCallback) ?> + "?reference=" + encodeURIComponent(response.reference);
    },
    onClose: function () {
      if (!confirm('Payment cancelled. Go back to dashboard?')) return;
      window.location.href = "dashboard.php";
    }
  });
  handler.openIframe();
});
</script>

</body>
</html>
