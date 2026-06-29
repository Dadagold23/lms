<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$partnerId   = (int)($_SESSION['partner_id'] ?? 0);
$partnerType = (string)($_SESSION['partner_type'] ?? '');

if ($partnerId <= 0 || empty($partnerType)) {
    redirect('unitary_academy/');
}

$referralId = (int)($_GET['referral_id'] ?? 0);
if ($referralId <= 0) {
    exit('Invalid referral ID.');
}

// Fetch referral details
$stmtRef = $pdo->prepare("SELECT * FROM lms_affiliate_referrals WHERE id = ? AND partner_id = ? LIMIT 1");
$stmtRef->execute([$referralId, $partnerId]);
$ref = $stmtRef->fetch(PDO::FETCH_ASSOC);
if (!$ref) {
    exit('Referral not found or unauthorized.');
}

// Fetch pupil student account
$stmtStud = $pdo->prepare("SELECT * FROM lms_students WHERE email = ? LIMIT 1");
$stmtStud->execute([$ref['pupil_email']]);
$stud = $stmtStud->fetch(PDO::FETCH_ASSOC);
if (!$stud) {
    exit('Associated student account not found.');
}
$studentId = (int)$stud['id'];

// Fetch enrollment details
$cId = (int)($ref['affiliate_course_id'] ?: $ref['course_id']);
$stmtEn = $pdo->prepare("
    SELECT e.*, c.title AS course_title, c.price AS original_price 
    FROM lms_enrollments e 
    JOIN lms_courses c ON c.id = e.course_id 
    WHERE e.student_id = ? AND e.course_id = ? 
    LIMIT 1
");
$stmtEn->execute([$studentId, $cId]);
$en = $stmtEn->fetch(PDO::FETCH_ASSOC);
if (!$en) {
    exit('Enrollment record not found.');
}
$enrollmentId = (int)$en['id'];

if ($en['status'] === 'paid') {
    redirect("unitary_academy/" . $partnerType . "/index.php");
}

// Pricing and commission calculation
$cPrice = (float)$en['original_price'];
$isAff = !empty($stud['is_affiliate']);
$range = $stud['affiliate_class_range'] ?? '';
if ($isAff && ($range === 'JSS' || $range === 'SSS')) {
    $cPrice = min($cPrice, 5000.0);
}

$due = max(0.0, $cPrice - (float)$en['paid_amount']);
if ($due <= 0.0) {
    exit('No balance due.');
}

require_once __DIR__ . '/includes/affiliate_helpers.php';
$rate = getPartnerCommissionRate($pdo, $partnerId, $ref['campaign_id']);
$commission = round($due * ($rate / 100), 2);
$netDue = round($due - $commission, 2);

// Generate reference
$reference = 'CAMP_' . bin2hex(random_bytes(10));

// Insert pending payment record
$stmtPay = $pdo->prepare("
    INSERT INTO lms_payments (student_id, enrollment_id, amount, channel, reference, status, created_at)
    VALUES (?, ?, ?, 'campaign', ?, 'pending', NOW())
");
$stmtPay->execute([$studentId, $enrollmentId, $netDue, $reference]);

// Paystack configuration
$paystack = require __DIR__ . '/config/paystack.php';
$paystackPublicKey = $paystack['public_key'];
$paystackCallback  = !empty($paystack['callback_url'])
    ? rtrim($paystack['callback_url'], '/') . '/partner_pay_verify.php'
    : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
       . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
       . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\') . '/partner_pay_verify.php');

$partnerEmail = $_SESSION['partner_email'] ?? 'partner@example.com';
$amountKobo = (int)($netDue * 100);
?>
<!doctype html>
<html lang="en">
<head>
  <?php
  $seoTitle   = 'Partner Checkout';
  $seoDesc    = 'Secure campaign payment checkout for Grafix@Mirror LMS affiliate partners.';
  $seoNoIndex = true;
  require_once __DIR__ . '/includes/seo.php';
  ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://js.paystack.co/v1/inline.js"></script>
  <style>
    body { background: #f3f4f6; font-family: 'Inter', sans-serif; }
    .checkout-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
    .btn-brand { background: #4f46e5; color: #ffffff; border: none; padding: .75rem 1.5rem; font-weight: 600; border-radius: 10px; transition: all 0.2s; }
    .btn-brand:hover { background: #4338ca; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="checkout-card p-4">
  <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
    <h5 class="fw-bold mb-0">Partner Payment Checkout</h5>
    <span class="badge bg-secondary text-uppercase"><?= e($partnerType) ?> track</span>
  </div>

  <div class="mb-4">
    <h6 class="text-muted small mb-1">Referral Pupil</h6>
    <div class="fw-bold fs-5 text-dark"><?= e($ref['pupil_name']) ?></div>
    <div class="text-muted small"><?= e($ref['pupil_email']) ?></div>
  </div>

  <div class="mb-4">
    <h6 class="text-muted small mb-1">Course</h6>
    <div class="fw-semibold text-dark"><?= e($en['course_title']) ?></div>
  </div>

  <div class="card bg-light border-0 p-3 mb-4" style="border-radius: 12px;">
    <h6 class="fw-bold text-dark mb-2">Cost Breakdown</h6>
    <table class="table table-borderless table-sm mb-0">
      <tr>
        <td class="text-muted ps-0">Course Fee</td>
        <td class="text-end fw-semibold pe-0">₦<?= number_format($cPrice, 2) ?></td>
      </tr>
      <tr>
        <td class="text-success ps-0">Partner Commission (<?= (float)$rate ?>%)</td>
        <td class="text-end text-success fw-semibold pe-0">- ₦<?= number_format($commission, 2) ?></td>
      </tr>
      <tr class="border-top">
        <td class="fw-bold ps-0 pt-2 text-dark">Net Amount Due</td>
        <td class="text-end fw-bold pe-0 pt-2 fs-5 text-indigo text-primary">₦<?= number_format($netDue, 2) ?></td>
      </tr>
    </table>
  </div>

  <div class="d-grid gap-2">
    <button id="payBtn" class="btn btn-brand btn-lg">
      Pay ₦<?= number_format($netDue, 2) ?> via Paystack
    </button>
    <a href="unitary_academy/<?= e($partnerType) ?>/index.php" class="btn btn-outline-secondary btn-lg" style="border-radius: 10px;">
      Cancel & Return
    </a>
  </div>
</div>

<script>
document.getElementById('payBtn').addEventListener('click', function () {
  const handler = PaystackPop.setup({
    key: <?= json_encode($paystackPublicKey) ?>,
    email: <?= json_encode($partnerEmail) ?>,
    amount: <?= (int)$amountKobo ?>,
    ref: <?= json_encode($reference) ?>,
    currency: 'NGN',
    callback: function (response) {
      window.location.href = <?= json_encode($paystackCallback) ?> 
        + "?reference=" + encodeURIComponent(response.reference)
        + "&partner_type=" + encodeURIComponent(<?= json_encode($partnerType) ?>);
    },
    onClose: function () {
      if (!confirm('Payment cancelled. Return to dashboard?')) return;
      window.location.href = "unitary_academy/<?= e($partnerType) ?>/index.php";
    }
  });
  handler.openIframe();
});
</script>
</body>
</html>
