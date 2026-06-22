<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

/* ======================
   FILTERS
====================== */
$status   = trim((string)($_GET['status'] ?? ''));   // success|pending|failed|manual|etc
$channel  = trim((string)($_GET['channel'] ?? ''));  // paystack|manual
$q        = trim((string)($_GET['q'] ?? ''));        // name/email/ref
$dateFrom = trim((string)($_GET['from'] ?? ''));     // YYYY-MM-DD
$dateTo   = trim((string)($_GET['to'] ?? ''));       // YYYY-MM-DD

$where = [];
$args  = [];

if ($status !== '') { $where[] = "p.status = ?";  $args[] = $status; }
if ($channel !== '') { $where[] = "p.channel = ?"; $args[] = $channel; }

if ($q !== '') {
    $where[] = "(p.reference LIKE ? OR s.email LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
    $args[] = "%$q%";
    $args[] = "%$q%";
    $args[] = "%$q%";
}

if ($dateFrom !== '') { $where[] = "DATE(p.created_at) >= ?"; $args[] = $dateFrom; }
if ($dateTo !== '')   { $where[] = "DATE(p.created_at) <= ?"; $args[] = $dateTo; }

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

/* ======================
   FETCH REPORT
====================== */
$sql = "
SELECT
  p.id,
  p.amount,
  p.channel,
  p.reference,
  p.status,
  p.paid_at,
  p.created_at,
  s.first_name,
  s.last_name,
  s.email,
  c.title AS course_title
FROM lms_payments p
LEFT JOIN lms_students s ON s.id = p.student_id
LEFT JOIN lms_enrollments e ON e.id = p.enrollment_id
LEFT JOIN lms_courses c ON c.id = e.course_id
{$whereSql}
ORDER BY p.created_at DESC
LIMIT 500
";

$stmt = $pdo->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   TOTALS
====================== */
$total = 0.0;
foreach ($rows as $r) {
    $total += (float)($r['amount'] ?? 0);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Finance Report';
$seoDesc    = 'Financial reports and revenue data at Grafix@Mirror LMS admin panel.';
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
    <span class="navbar-brand fw-bold">Finance Report</span>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <div class="card p-3 mb-3">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-md-2">
        <label class="form-label small">Status</label>
        <input name="status" value="<?= e($status) ?>" class="form-control form-control-sm" placeholder="success">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Channel</label>
        <input name="channel" value="<?= e($channel) ?>" class="form-control form-control-sm" placeholder="paystack">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Search (name/email/ref)</label>
        <input name="q" value="<?= e($q) ?>" class="form-control form-control-sm" placeholder="Search...">
      </div>
      <div class="col-md-2">
        <label class="form-label small">From</label>
        <input type="date" name="from" value="<?= e($dateFrom) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-md-2">
        <label class="form-label small">To</label>
        <input type="date" name="to" value="<?= e($dateTo) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-primary btn-sm">Filter</button>
      </div>
    </form>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="fw-bold">Total (shown): <?= formatMoney($total) ?></div>
    <div class="text-muted small">Showing up to 500 records</div>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Course</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Channel</th>
            <th>Reference</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="8" class="text-muted">No records found.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td>
              <?= e(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?><br>
              <small class="text-muted"><?= e((string)($r['email'] ?? '')) ?></small>
            </td>
            <td><?= e((string)($r['course_title'] ?? '-')) ?></td>
            <td class="fw-bold"><?= formatMoney($r['amount']) ?></td>
            <td><?= e((string)$r['status']) ?></td>
            <td><?= e((string)$r['channel']) ?></td>
            <td><code><?= e((string)$r['reference']) ?></code></td>
            <td><?= e(date('Y-m-d H:i', strtotime((string)$r['created_at']))) ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
