<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

/* KPIs */
$kpiStudents = (int)$pdo->query("SELECT COUNT(*) FROM lms_students WHERE role='student'")->fetchColumn();
$kpiCourses  = (int)$pdo->query("SELECT COUNT(*) FROM lms_courses")->fetchColumn();
$kpiEnroll   = (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments")->fetchColumn();
$kpiRevenue  = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE status='success'")->fetchColumn();

/* Revenue by month (last 6) */
$revRows = $pdo->query("
  SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COALESCE(SUM(amount),0) AS total
  FROM lms_payments
  WHERE status='success'
  GROUP BY ym
  ORDER BY ym DESC
  LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
$revRows = array_reverse($revRows);

/* Enrollment per course (top 10) */
$topCourses = $pdo->query("
  SELECT c.title, COUNT(e.id) AS enrollments
  FROM lms_courses c
  LEFT JOIN lms_enrollments e ON e.course_id = c.id
  GROUP BY c.id
  ORDER BY enrollments DESC, c.title ASC
  LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

/* Completion (based on progress rows) */
$completion = $pdo->query("
  SELECT c.title,
         COUNT(DISTINCT e.id) AS enrolled,
         COUNT(DISTINCT cert.id) AS certified
  FROM lms_courses c
  LEFT JOIN lms_enrollments e ON e.course_id = c.id
  LEFT JOIN lms_certificates cert ON cert.course_id = c.id
  GROUP BY c.id
  ORDER BY certified DESC, enrolled DESC
  LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Analytics';
$seoDesc    = 'Student and revenue analytics at Grafix@Mirror LMS — Mirror Age Concepts admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f7fbff;font-family:Inter,system-ui}
.card{border-radius:14px}
.kpi{font-size:1.6rem;font-weight:800}
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark sticky-top">
  <div class="container">
    <span class="navbar-brand fw-bold">Analytics</span>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Admin Dashboard</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card p-3 text-center"><div class="kpi"><?= $kpiStudents ?></div><small>Students</small></div></div>
  <div class="col-md-3"><div class="card p-3 text-center"><div class="kpi"><?= $kpiCourses ?></div><small>Courses</small></div></div>
  <div class="col-md-3"><div class="card p-3 text-center"><div class="kpi"><?= $kpiEnroll ?></div><small>Enrollments</small></div></div>
  <div class="col-md-3"><div class="card p-3 text-center"><div class="kpi"><?= formatMoney($kpiRevenue) ?></div><small>Revenue</small></div></div>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card p-4 h-100">
      <h5 class="mb-3">Revenue (Last 6 Months)</h5>
      <?php if (empty($revRows)): ?>
        <p class="text-muted">No revenue yet.</p>
      <?php else: ?>
        <table class="table table-sm">
          <thead><tr><th>Month</th><th class="text-end">Total</th></tr></thead>
          <tbody>
            <?php foreach ($revRows as $r): ?>
              <tr><td><?= e($r['ym']) ?></td><td class="text-end"><?= formatMoney($r['total']) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card p-4 h-100">
      <h5 class="mb-3">Top Courses (Enrollments)</h5>
      <?php if (empty($topCourses)): ?>
        <p class="text-muted">No enrollments yet.</p>
      <?php else: ?>
        <table class="table table-sm">
          <thead><tr><th>Course</th><th class="text-end">Enrollments</th></tr></thead>
          <tbody>
          <?php foreach ($topCourses as $c): ?>
            <tr><td><?= e($c['title']) ?></td><td class="text-end"><?= (int)$c['enrollments'] ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-12">
    <div class="card p-4">
      <h5 class="mb-3">Certification Overview</h5>
      <?php if (empty($completion)): ?>
        <p class="text-muted">No data.</p>
      <?php else: ?>
        <table class="table table-sm">
          <thead><tr><th>Course</th><th class="text-end">Enrolled</th><th class="text-end">Certified</th></tr></thead>
          <tbody>
          <?php foreach ($completion as $c): ?>
            <tr>
              <td><?= e($c['title']) ?></td>
              <td class="text-end"><?= (int)$c['enrolled'] ?></td>
              <td class="text-end"><?= (int)$c['certified'] ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

</div>
</body>
</html>
