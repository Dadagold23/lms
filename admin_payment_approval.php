<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/payment_processing.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$adminId = (int)($_SESSION['admin']['id'] ?? 0);

$flash = $_SESSION['admin_pay_flash'] ?? null;
unset($_SESSION['admin_pay_flash']);

/* ======================
   HANDLE APPROVE/REJECT
====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $paymentId = (int)($_POST['payment_id'] ?? 0);
    $action    = (string)($_POST['action'] ?? '');
    $note      = trim((string)($_POST['manual_note'] ?? ''));

    if ($paymentId <= 0 || !in_array($action, ['approve','reject'], true)) {
        $_SESSION['admin_pay_flash'] = 'Invalid request.';
        redirect('admin_payment_approval.php');
    }

    $pdo->beginTransaction();
    try {
        // lock payment row
        $pay = lockPaymentById($pdo, $paymentId, 'pending', 'manual');

        if (!$pay) {
            $pdo->rollBack();
            $_SESSION['admin_pay_flash'] = 'Payment not found or already processed.';
            redirect('admin_payment_approval.php');
        }

        if ($action === 'reject') {
            $pdo->prepare("
                UPDATE lms_payments
                SET status='failed',
                    manual_note=?,
                    approved_by=?,
                    approved_at=NOW()
                WHERE id=?
            ")->execute([$note, $adminId, $paymentId]);

            $pdo->commit();
            $_SESSION['admin_pay_flash'] = 'Payment rejected.';
            redirect('admin_payment_approval.php');
        }

        // approve
        $enrollmentId = (int)($pay['enrollment_id'] ?? 0);
        $amount       = (float)($pay['amount'] ?? 0);

        if ($enrollmentId <= 0 || $amount <= 0) {
            $pdo->rollBack();
            $_SESSION['admin_pay_flash'] = 'Invalid enrollment or amount.';
            redirect('admin_payment_approval.php');
        }

        $en = lockEnrollmentForPayment($pdo, $enrollmentId, (int)($pay['student_id'] ?? 0));

        if (!$en) {
            $pdo->rollBack();
            $_SESSION['admin_pay_flash'] = 'Enrollment not found.';
            redirect('admin_payment_approval.php');
        }

        applyPaymentSuccess($pdo, $pay, $en, $amount, 'manual', [
            'manual_note' => $note,
            'approved_by' => $adminId,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $pdo->commit();
        $_SESSION['admin_pay_flash'] = 'Payment approved and enrollment updated.';
        redirect('admin_payment_approval.php');

    } catch (Throwable $e) {
        $pdo->rollBack();
        $_SESSION['admin_pay_flash'] = 'Approval failed. Check DB and try again.';
        redirect('admin_payment_approval.php');
    }
}

/* ======================
   LIST PENDING MANUAL
====================== */
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;
$q = trim((string)($_GET['q'] ?? ''));
$where = "p.status='pending' AND p.channel='manual'";
$args = [];

if ($q !== '') {
    $where .= " AND (p.reference LIKE ? OR s.email LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
    $search = '%' . $q . '%';
    $args[] = $search;
    $args[] = $search;
    $args[] = $search;
}

$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM lms_payments p
    JOIN lms_students s ON s.id = p.student_id
    WHERE {$where}
");
$countStmt->execute($args);
$totalPending = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalPending / $perPage));

$pendingStmt = $pdo->prepare("
    SELECT p.id, p.amount, p.reference, p.created_at, p.enrollment_id,
           s.first_name, s.last_name, s.email
    FROM lms_payments p
    JOIN lms_students s ON s.id = p.student_id
    WHERE {$where}
    ORDER BY p.created_at DESC
    LIMIT {$perPage} OFFSET {$offset}
");
$pendingStmt->execute($args);
$pending = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Payment Approvals';
$seoDesc    = 'Review and approve manual payments at Grafix@Mirror LMS admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{background:#f7fbff;font-family:Inter,system-ui}
  .card{border-radius:14px}
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white text-decoration-none" href="admin_dashboard.php">Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_courses.php" class="btn btn-outline-light btn-sm">Courses</a>
      <a href="admin_logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <?php if ($flash): ?>
    <div class="alert alert-info"><?= e($flash) ?></div>
  <?php endif; ?>

  <div class="card p-4">
    <h5 class="mb-3">Pending Manual Payments</h5>
    <form method="get" class="row g-2 mb-3">
      <div class="col-md-6">
        <input class="form-control form-control-sm" name="q" value="<?= e($q) ?>" placeholder="Search by student, email, or reference">
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-primary btn-sm">Search</button>
      </div>
    </form>

    <?php if (empty($pending)): ?>
      <p class="text-muted">No pending manual payments.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Student</th>
              <th>Email</th>
              <th>Enrollment</th>
              <th>Amount</th>
              <th>Reference</th>
              <th>Date</th>
              <th style="width:340px"></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($pending as $p): ?>
            <tr>
              <td><?= e(($p['first_name'] ?? '').' '.($p['last_name'] ?? '')) ?></td>
              <td><?= e($p['email'] ?? '') ?></td>
              <td>#<?= (int)$p['enrollment_id'] ?></td>
              <td><?= formatMoney($p['amount'] ?? 0) ?></td>
              <td><?= e($p['reference'] ?? '') ?></td>
              <td><?= e(date('Y-m-d H:i', strtotime((string)$p['created_at']))) ?></td>
              <td>
                <form method="post" class="d-flex gap-2">
                  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                  <input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>">

                  <input class="form-control form-control-sm" name="manual_note" placeholder="Note (optional)">

                  <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                  <button name="action" value="reject"  class="btn btn-outline-danger btn-sm">Reject</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">Page <?= $page ?> of <?= $totalPages ?>, <?= $totalPending ?> pending request<?= $totalPending !== 1 ? 's' : '' ?></small>
          <div class="d-flex gap-2">
            <?php if ($page > 1): ?>
              <a class="btn btn-outline-secondary btn-sm" href="?page=<?= $page - 1 ?>&q=<?= urlencode($q) ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
              <a class="btn btn-outline-secondary btn-sm" href="?page=<?= $page + 1 ?>&q=<?= urlencode($q) ?>">Next</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
