<?php
declare(strict_types=1);

/* ======================
   BOOTSTRAP
====================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

/* ======================
   AUTH
====================== */
requireLogin();

$user = $_SESSION['user'] ?? [];
$studentId = (int)($user['id'] ?? 0);

if ($studentId <= 0) {
    redirect('login.php');
}

/* ======================
   FETCH PAYMENTS (robust)
====================== */
$payments = [];
$errorMsg = null;

try {
    // ✅ BEST PRACTICE: payments -> enrollments -> courses
    // Works when lms_payments has enrollment_id (recommended)
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.reference,
            p.amount,
            p.status,
            p.paid_at,
            p.created_at,
            c.title AS course_title
        FROM lms_payments p
        INNER JOIN lms_enrollments e ON e.id = p.enrollment_id
        INNER JOIN lms_courses c ON c.id = e.course_id
        WHERE e.student_id = ?
        ORDER BY COALESCE(p.paid_at, p.created_at) DESC, p.id DESC
    ");
    $stmt->execute([$studentId]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e1) {
    // Fallback 1: maybe payments table stores user_id
    try {
        $stmt = $pdo->prepare("
            SELECT
                p.id,
                p.reference,
                p.amount,
                p.status,
                p.created_at
            FROM lms_payments p
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$studentId]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e2) {
        // Fallback 2: maybe payments table stores student_id
        try {
            $stmt = $pdo->prepare("
                SELECT
                    p.id,
                    p.reference,
                    p.amount,
                    p.status,
                    p.created_at
                FROM lms_payments p
                WHERE p.student_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$studentId]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e3) {
            $errorMsg = "Payments query failed. Your lms_payments schema does not match the expected columns.
Try ensuring lms_payments has `enrollment_id` (recommended), or tell me your columns.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Payment History';
$seoDesc    = 'View your payment history and outstanding balances at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body{background:#f7fbff;font-family:Inter,system-ui}
  .card{border-radius:14px}
</style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <span class="navbar-brand fw-bold text-primary">Payments</span>
    <div class="ms-auto d-flex gap-2">
      <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

<?php if ($errorMsg): ?>
  <div class="alert alert-danger">
    <?= e($errorMsg) ?>
  </div>
<?php endif; ?>

<div class="card p-4">
  <h5 class="mb-3">Payment History</h5>

  <?php if (empty($payments)): ?>
    <p class="text-muted mb-0">No payments found yet.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Course</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e(date('Y-m-d', strtotime((string)($p['paid_at'] ?? $p['created_at'] ?? 'now')))) ?></td>
            <td><?= e((string)($p['reference'] ?? '—')) ?></td>
            <td><?= e((string)($p['course_title'] ?? '—')) ?></td>
            <td><?= formatMoney($p['amount'] ?? 0) ?></td>
            <td>
              <?php
                $st = (string)($p['status'] ?? '');
                $badge = 'secondary';
                if (in_array($st, ['success','paid','completed'], true)) $badge = 'success';
                elseif (in_array($st, ['pending','processing'], true)) $badge = 'warning';
                elseif (in_array($st, ['failed','cancelled'], true)) $badge = 'danger';
              ?>
              <span class="badge bg-<?= $badge ?>"><?= e($st ?: 'unknown') ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

</div>
</body>
</html>
