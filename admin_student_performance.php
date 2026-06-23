<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';
requireAdminLogin();

$admin = $_SESSION['admin'] ?? $_SESSION['user'] ?? [];

// ─── Filters ───
$filterCourse     = (int)($_GET['course_id'] ?? 0);
$filterInstructor = (int)($_GET['instructor_id'] ?? 0);
$filterStatus     = trim((string)($_GET['status'] ?? ''));
$filterDateFrom   = trim((string)($_GET['date_from'] ?? ''));
$filterDateTo     = trim((string)($_GET['date_to'] ?? ''));
$search           = trim((string)($_GET['q'] ?? ''));
$page             = max(1, (int)($_GET['page'] ?? 1));
$perPage          = 30;
$offset           = ($page - 1) * $perPage;

// ─── Build WHERE ───
$where  = 'WHERE 1=1';
$params = [];

if ($filterCourse > 0) {
    $where .= ' AND e.course_id = ?';
    $params[] = $filterCourse;
}
if ($filterInstructor > 0) {
    $where .= ' AND ic.instructor_id = ?';
    $params[] = $filterInstructor;
}
if ($filterStatus !== '') {
    $where .= ' AND e.status = ?';
    $params[] = $filterStatus;
}
if ($filterDateFrom !== '') {
    $where .= ' AND DATE(e.created_at) >= ?';
    $params[] = $filterDateFrom;
}
if ($filterDateTo !== '') {
    $where .= ' AND DATE(e.created_at) <= ?';
    $params[] = $filterDateTo;
}
if ($search !== '') {
    $where .= ' AND (CONCAT(s.first_name," ",s.last_name) LIKE ? OR s.email LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
}

$baseSql = "
    FROM lms_enrollments e
    JOIN lms_students s ON s.id = e.student_id
    JOIN lms_courses c ON c.id = e.course_id
    LEFT JOIN lms_instructor_courses ic ON ic.course_id = e.course_id
    LEFT JOIN lms_instructors ins ON ins.id = ic.instructor_id
    {$where}
";

// Count
$countStmt = $pdo->prepare("SELECT COUNT(DISTINCT e.id) {$baseSql}");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

// Data
$dataStmt = $pdo->prepare("
    SELECT
        s.id AS student_id,
        CONCAT(s.first_name,' ',s.last_name) AS full_name,
        s.email,
        c.id AS course_id,
        c.title AS course_title,
        ins.id AS instructor_id,
        ins.full_name AS instructor_name,
        e.id AS enrollment_id,
        e.status AS enroll_status,
        e.paid_amount,
        e.created_at AS enrolled_at,
        e.needs_instructor_assignment,
        (SELECT COUNT(*) FROM lms_assignment_submissions sub
          JOIN lms_assignments a ON a.id = sub.assignment_id
          WHERE a.course_id = e.course_id AND sub.student_id = s.id) AS total_submissions,
        (SELECT COUNT(*) FROM lms_assignment_submissions sub
          JOIN lms_assignments a ON a.id = sub.assignment_id
          WHERE a.course_id = e.course_id AND sub.student_id = s.id AND sub.score IS NOT NULL) AS graded,
        (SELECT ROUND(AVG(sub.score),1) FROM lms_assignment_submissions sub
          JOIN lms_assignments a ON a.id = sub.assignment_id
          WHERE a.course_id = e.course_id AND sub.student_id = s.id AND sub.score IS NOT NULL) AS avg_score
    {$baseSql}
    GROUP BY e.id
    ORDER BY e.created_at DESC
    LIMIT {$perPage} OFFSET {$offset}
");
$dataStmt->execute($params);
$rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

// Filter options
$allCourses     = $pdo->query("SELECT id, title FROM lms_courses ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$allInstructors = $pdo->query("SELECT id, full_name FROM lms_instructors ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

// Summary stats
$statsStmt = $pdo->prepare("
    SELECT
        COUNT(DISTINCT e.id)                    AS total_enrollments,
        COUNT(DISTINCT e.student_id)            AS unique_students,
        SUM(e.needs_instructor_assignment)      AS needs_assign,
        ROUND(AVG(sub_avg.avg),1)               AS overall_avg
    FROM lms_enrollments e
    LEFT JOIN (
        SELECT sub.student_id, AVG(sub.score) AS avg
        FROM lms_assignment_submissions sub
        WHERE sub.score IS NOT NULL
        GROUP BY sub.student_id
    ) sub_avg ON sub_avg.student_id = e.student_id
    {$where}
");
$statsStmt->execute($params);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Student Performance — Admin';
$seoDesc    = 'View all student performance metrics across courses.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
body { background: #f0f4ff; font-family: Inter, system-ui; }
.page-hero {
  background: linear-gradient(135deg,#1e1b4b,#4f46e5);
  color: #fff;
  border-radius: 16px;
  padding: 28px 32px;
  margin-bottom: 24px;
}
.stat-pill {
  background: rgba(255,255,255,.12);
  border-radius: 10px;
  padding: 14px 20px;
  text-align: center;
  min-width: 110px;
}
.stat-pill .val { font-size: 1.6rem; font-weight: 800; }
.stat-pill .lbl { font-size: .7rem; color: #c7d2fe; text-transform: uppercase; letter-spacing: .05em; }
.filter-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  padding: 18px 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 10px rgba(79,70,229,.05);
}
.perf-table th {
  background: #f8faff;
  color: #4f46e5;
  font-size: .72rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  font-weight: 700;
  white-space: nowrap;
}
.perf-table td { font-size: .85rem; vertical-align: middle; }
.score-badge { padding: .2rem .6rem; border-radius: 6px; font-size: .78rem; font-weight: 700; }
.score-high { background: #dcfce7; color: #15803d; }
.score-mid  { background: #fef9c3; color: #854d0e; }
.score-low  { background: #fee2e2; color: #991b1b; }
.score-none { background: #f1f5f9; color: #64748b; }
.flag-badge { background: #fff7ed; color: #d97706; border: 1px solid #fcd34d; padding: .15rem .5rem; border-radius: 5px; font-size: .72rem; font-weight: 600; }
</style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top py-2" style="background:#0f172a">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="admin_dashboard.php">
      <div style="width:30px;height:30px;background:rgba(255,255,255,.12);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800">A</div>
      <span>Admin <span style="color:#a5b4fc">Panel</span></span>
    </a>
    <div class="ms-auto d-flex gap-3 align-items-center">
      <a href="admin_instructors.php" class="nav-link text-white small"><i class="fa fa-users me-1"></i>Instructors</a>
      <a href="admin_enrollment_assignments.php" class="nav-link text-white small"><i class="fa fa-link me-1"></i>Assignments</a>
      <a href="admin_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <!-- Hero -->
  <div class="page-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h3 class="fw-bold mb-1 text-white"><i class="fa fa-chart-line me-2"></i>Student Performance</h3>
        <p class="mb-0" style="color:#c7d2fe;font-size:.9rem">Track every student's grades, submission activity and enrollment status across all courses.</p>
      </div>
      <div class="d-flex gap-3 flex-wrap">
        <div class="stat-pill">
          <div class="val"><?= (int)($stats['total_enrollments'] ?? 0) ?></div>
          <div class="lbl">Enrollments</div>
        </div>
        <div class="stat-pill">
          <div class="val"><?= (int)($stats['unique_students'] ?? 0) ?></div>
          <div class="lbl">Students</div>
        </div>
        <div class="stat-pill">
          <div class="val"><?= $stats['overall_avg'] !== null ? $stats['overall_avg'] . '%' : '—' ?></div>
          <div class="lbl">Avg Grade</div>
        </div>
        <?php if ((int)($stats['needs_assign'] ?? 0) > 0): ?>
        <div class="stat-pill" style="background:rgba(245,158,11,.2)">
          <div class="val" style="color:#fbbf24"><?= (int)$stats['needs_assign'] ?></div>
          <div class="lbl" style="color:#fcd34d">Need Instructor</div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-card">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Search Student</label>
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Name or email..." value="<?= e($search) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold">Course</label>
        <select name="course_id" class="form-select form-select-sm">
          <option value="0">All Courses</option>
          <?php foreach ($allCourses as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $filterCourse === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold">Instructor</label>
        <select name="instructor_id" class="form-select form-select-sm">
          <option value="0">All Instructors</option>
          <?php foreach ($allInstructors as $ins): ?>
            <option value="<?= $ins['id'] ?>" <?= $filterInstructor === (int)$ins['id'] ? 'selected' : '' ?>><?= e($ins['full_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All Statuses</option>
          <option value="active"      <?= $filterStatus === 'active'      ? 'selected' : '' ?>>Active</option>
          <option value="paid"        <?= $filterStatus === 'paid'        ? 'selected' : '' ?>>Paid</option>
          <option value="installment" <?= $filterStatus === 'installment' ? 'selected' : '' ?>>Installment</option>
          <option value="suspended"   <?= $filterStatus === 'suspended'   ? 'selected' : '' ?>>Suspended</option>
        </select>
      </div>
      <div class="col-md-1">
        <label class="form-label small fw-semibold">From</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filterDateFrom) ?>">
      </div>
      <div class="col-md-1">
        <label class="form-label small fw-semibold">To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filterDateTo) ?>">
      </div>
      <div class="col-md-1 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-search"></i></button>
        <a href="admin_student_performance.php" class="btn btn-outline-secondary btn-sm"><i class="fa fa-times"></i></a>
      </div>
    </form>
  </div>

  <!-- TABLE -->
  <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 perf-table">
        <thead>
          <tr>
            <th style="padding:14px 16px">#</th>
            <th>Student</th>
            <th>Course</th>
            <th>Instructor</th>
            <th>Submissions</th>
            <th>Avg Score</th>
            <th>Status</th>
            <th>Enrolled</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="fa fa-user-graduate fa-2x mb-2 d-block"></i>
                No students found matching filters.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $idx => $row):
              $scoreClass = $row['avg_score'] !== null
                ? ($row['avg_score'] >= 70 ? 'score-high' : ($row['avg_score'] >= 50 ? 'score-mid' : 'score-low'))
                : 'score-none';
              $enrollBadge = match($row['enroll_status'] ?? '') {
                'paid'        => 'background:#dcfce7;color:#15803d',
                'active'      => 'background:#e0f2fe;color:#0369a1',
                'installment' => 'background:#fef3c7;color:#d97706',
                default       => 'background:#f1f5f9;color:#64748b',
              };
            ?>
            <tr>
              <td class="text-muted ps-4" style="font-size:.8rem"><?= ($offset + $idx + 1) ?></td>
              <td>
                <div class="fw-semibold" style="font-size:.88rem"><?= e($row['full_name']) ?></div>
                <div class="text-muted" style="font-size:.76rem"><?= e($row['email']) ?></div>
              </td>
              <td>
                <a href="admin_instructor_detail.php?id=<?= $row['instructor_id'] ?>" style="font-size:.85rem;color:#4f46e5;text-decoration:none;font-weight:600">
                  <?= e($row['course_title']) ?>
                </a>
              </td>
              <td>
                <?php if ($row['instructor_name']): ?>
                  <a href="admin_instructor_detail.php?id=<?= $row['instructor_id'] ?>" style="font-size:.83rem;color:#1e293b;text-decoration:none">
                    <?= e($row['instructor_name']) ?>
                  </a>
                <?php else: ?>
                  <?php if ((int)$row['needs_instructor_assignment']): ?>
                    <span class="flag-badge"><i class="fa fa-exclamation-triangle me-1"></i>Unassigned</span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td class="text-center" style="font-size:.85rem">
                <?= (int)$row['graded'] ?> / <?= (int)$row['total_submissions'] ?>
              </td>
              <td>
                <span class="score-badge <?= $scoreClass ?>">
                  <?= $row['avg_score'] !== null ? $row['avg_score'] . '%' : '—' ?>
                </span>
              </td>
              <td>
                <span style="padding:.25rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600;<?= $enrollBadge ?>">
                  <?= ucfirst((string)$row['enroll_status']) ?>
                </span>
              </td>
              <td class="text-muted" style="font-size:.78rem">
                <?= $row['enrolled_at'] ? date('M j, Y', strtotime($row['enrolled_at'])) : '—' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center p-3 border-top">
      <span class="text-muted small">Page <?= $page ?> of <?= $totalPages ?> — <?= $total ?> results</span>
      <div class="d-flex gap-2">
        <?php if ($page > 1): ?>
          <a class="btn btn-sm btn-outline-secondary" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Prev</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
          <a class="btn btn-sm btn-outline-secondary" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next →</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
