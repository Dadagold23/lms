<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';
requireAdminLogin();

$admin = $_SESSION['admin'] ?? $_SESSION['user'] ?? [];
$flash = $_SESSION['enroll_assign_flash'] ?? null;
unset($_SESSION['enroll_assign_flash']);

// ─── Handle POST: assign instructor ───
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $enrollId   = (int)($_POST['enrollment_id'] ?? 0);
    $instructorId = (int)($_POST['instructor_id'] ?? 0);

    if ($enrollId <= 0 || $instructorId <= 0) {
        $_SESSION['enroll_assign_flash'] = ['type' => 'danger', 'msg' => 'Invalid selection.'];
        redirect('admin_enrollment_assignments.php');
    }

    try {
        // Verify the instructor is assigned to this enrollment's course
        $enRow = $pdo->prepare("SELECT course_id, student_id FROM lms_enrollments WHERE id = ? LIMIT 1");
        $enRow->execute([$enrollId]);
        $enrollment = $enRow->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            $_SESSION['enroll_assign_flash'] = ['type' => 'danger', 'msg' => 'Enrollment not found.'];
            redirect('admin_enrollment_assignments.php');
        }

        $courseId = (int)$enrollment['course_id'];

        // Ensure instructor-course mapping exists (create if not)
        $pdo->prepare("INSERT IGNORE INTO lms_instructor_courses (instructor_id, course_id) VALUES (?, ?)")
            ->execute([$instructorId, $courseId]);

        // Update enrollment
        $pdo->prepare("
            UPDATE lms_enrollments
            SET assigned_instructor_id = ?, needs_instructor_assignment = 0
            WHERE id = ?
        ")->execute([$instructorId, $enrollId]);

        $_SESSION['enroll_assign_flash'] = ['type' => 'success', 'msg' => 'Instructor assigned successfully.'];
    } catch (Throwable $e) {
        $_SESSION['enroll_assign_flash'] = ['type' => 'danger', 'msg' => 'Assignment failed: ' . $e->getMessage()];
    }
    redirect('admin_enrollment_assignments.php');
}

// ─── Filters ───
$filterCourse = (int)($_GET['course_id'] ?? 0);
$search       = trim((string)($_GET['q'] ?? ''));
$showAll      = (int)($_GET['show_all'] ?? 0);
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 25;
$offset       = ($page - 1) * $perPage;

$where  = 'WHERE 1=1';
$params = [];

if (!$showAll) {
    $where .= ' AND e.needs_instructor_assignment = 1';
}
if ($filterCourse > 0) {
    $where .= ' AND e.course_id = ?';
    $params[] = $filterCourse;
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
    LEFT JOIN lms_instructors ai ON ai.id = e.assigned_instructor_id
    {$where}
";

$total = (int)$pdo->prepare("SELECT COUNT(*) {$baseSql}")->execute($params) ? 0 : 0;
$countStmt = $pdo->prepare("SELECT COUNT(*) {$baseSql}");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$dataStmt = $pdo->prepare("
    SELECT
        e.id AS enrollment_id,
        e.course_id,
        e.student_id,
        e.status,
        e.created_at,
        e.needs_instructor_assignment,
        e.assigned_instructor_id,
        CONCAT(s.first_name,' ',s.last_name) AS student_name,
        s.email AS student_email,
        c.title AS course_title,
        ai.full_name AS assigned_instructor_name
    {$baseSql}
    ORDER BY e.needs_instructor_assignment DESC, e.created_at DESC
    LIMIT {$perPage} OFFSET {$offset}
");
$dataStmt->execute($params);
$enrollments = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

// Instructors for dropdown (active only)
$instructors = $pdo->query("SELECT id, full_name, specialization FROM lms_instructors WHERE status='active' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
// Per-course instructors map
$icMap = [];
$icRows = $pdo->query("SELECT instructor_id, course_id FROM lms_instructor_courses")->fetchAll(PDO::FETCH_ASSOC);
foreach ($icRows as $r) {
    $icMap[(int)$r['course_id']][] = (int)$r['instructor_id'];
}

// Courses for filter
$allCourses = $pdo->query("SELECT id, title FROM lms_courses ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

// Count needing assignment
$needsCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments WHERE needs_instructor_assignment = 1")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Enrollment Assignments — Admin';
$seoDesc    = 'Assign instructors to student enrollments.';
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
  background: linear-gradient(135deg,#92400e,#d97706,#f59e0b);
  color: #fff;
  border-radius: 16px;
  padding: 28px 32px;
  margin-bottom: 24px;
}
.enroll-table th {
  background: #f8faff;
  color: #4f46e5;
  font-size: .72rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  font-weight: 700;
  white-space: nowrap;
}
.enroll-table td { font-size: .85rem; vertical-align: middle; }
.flag-row td { background: #fff7ed !important; }
.flag-icon { color: #f59e0b; }
.assigned-icon { color: #16a34a; }
.filter-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  padding: 16px 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(79,70,229,.05);
}
.needs-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: #fef3c7; color: #92400e;
  border: 1px solid #fcd34d;
  border-radius: 8px;
  padding: .35rem .75rem;
  font-size: .78rem; font-weight: 700;
}
.assigned-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: #f0fdf4; color: #15803d;
  border: 1px solid #86efac;
  border-radius: 8px;
  padding: .35rem .75rem;
  font-size: .78rem; font-weight: 700;
}
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
      <a href="admin_student_performance.php" class="nav-link text-white small"><i class="fa fa-chart-bar me-1"></i>Performance</a>
      <a href="admin_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <!-- Hero -->
  <div class="page-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h3 class="fw-bold mb-1 text-white"><i class="fa fa-link me-2"></i>Enrollment Assignments</h3>
        <p class="mb-0" style="color:rgba(255,255,255,.8);font-size:.9rem">Assign instructors to student enrollments. Flagged enrollments need immediate attention.</p>
      </div>
      <?php if ($needsCount > 0): ?>
      <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:16px 24px;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#fff"><?= $needsCount ?></div>
        <div style="font-size:.75rem;color:rgba(255,255,255,.75);text-transform:uppercase;letter-spacing:.05em">Need Assignment</div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible mb-3">
      <?= e($flash['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filter -->
  <div class="filter-card">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Search Student</label>
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Name or email..." value="<?= e($search) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Course</label>
        <select name="course_id" class="form-select form-select-sm">
          <option value="0">All Courses</option>
          <?php foreach ($allCourses as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $filterCourse === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold">View</label>
        <select name="show_all" class="form-select form-select-sm">
          <option value="0" <?= !$showAll ? 'selected' : '' ?>>Needs Assignment Only</option>
          <option value="1" <?= $showAll ? 'selected' : '' ?>>All Enrollments</option>
        </select>
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-search"></i></button>
      </div>
      <div class="col-md-1">
        <a href="admin_enrollment_assignments.php" class="btn btn-outline-secondary btn-sm w-100"><i class="fa fa-times"></i></a>
      </div>
    </form>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 enroll-table">
        <thead>
          <tr>
            <th style="padding:14px 16px">#</th>
            <th>Student</th>
            <th>Course</th>
            <th>Enrolled</th>
            <th>Status</th>
            <th>Instructor</th>
            <th style="min-width:220px">Assign Instructor</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($enrollments)): ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="fa fa-check-circle fa-2x mb-2 d-block" style="color:#16a34a"></i>
                <?= $showAll ? 'No enrollments found.' : 'All enrollments have instructors assigned! 🎉' ?>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($enrollments as $idx => $row):
              $needsAssign = (int)$row['needs_instructor_assignment'];
              $courseId    = (int)$row['course_id'];
              // Instructors already assigned to this course
              $courseInstructorIds = $icMap[$courseId] ?? [];
              // Filter instructors to show course-assigned ones first
              $courseInstructors  = array_filter($instructors, fn($i) => in_array((int)$i['id'], $courseInstructorIds, true));
              $otherInstructors   = array_filter($instructors, fn($i) => !in_array((int)$i['id'], $courseInstructorIds, true));
            ?>
            <tr class="<?= $needsAssign ? 'flag-row' : '' ?>">
              <td class="text-muted ps-4 small"><?= $offset + $idx + 1 ?></td>
              <td>
                <div class="fw-semibold"><?= e($row['student_name']) ?></div>
                <div class="text-muted" style="font-size:.76rem"><?= e($row['student_email']) ?></div>
              </td>
              <td style="font-size:.85rem;max-width:180px" class="fw-semibold"><?= e($row['course_title']) ?></td>
              <td class="text-muted" style="font-size:.78rem">
                <?= $row['created_at'] ? date('M j, Y', strtotime($row['created_at'])) : '—' ?>
              </td>
              <td>
                <span style="font-size:.76rem;padding:.2rem .6rem;border-radius:6px;font-weight:600;
                  <?= $row['status'] === 'paid' ? 'background:#dcfce7;color:#15803d' : 'background:#e0f2fe;color:#0369a1' ?>">
                  <?= ucfirst((string)$row['status']) ?>
                </span>
              </td>
              <td>
                <?php if ($row['assigned_instructor_name']): ?>
                  <span class="assigned-badge">
                    <i class="fa fa-check-circle"></i> <?= e($row['assigned_instructor_name']) ?>
                  </span>
                <?php elseif ($needsAssign): ?>
                  <span class="needs-badge">
                    <i class="fa fa-exclamation-triangle"></i> Not Assigned
                  </span>
                <?php else: ?>
                  <span class="text-muted small">—</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="post" class="d-flex gap-2 align-items-center">
                  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                  <input type="hidden" name="enrollment_id" value="<?= (int)$row['enrollment_id'] ?>">
                  <select name="instructor_id" class="form-select form-select-sm" required style="min-width:170px">
                    <option value="">— Select Instructor —</option>
                    <?php if (!empty($courseInstructors)): ?>
                      <optgroup label="✓ Course Instructors">
                        <?php foreach ($courseInstructors as $ins): ?>
                          <option value="<?= $ins['id'] ?>" <?= (int)$row['assigned_instructor_id'] === (int)$ins['id'] ? 'selected' : '' ?>>
                            <?= e($ins['full_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endif; ?>
                    <?php if (!empty($otherInstructors)): ?>
                      <optgroup label="Other Instructors">
                        <?php foreach ($otherInstructors as $ins): ?>
                          <option value="<?= $ins['id'] ?>">
                            <?= e($ins['full_name']) ?>
                            <?= $ins['specialization'] ? ' — ' . e($ins['specialization']) : '' ?>
                          </option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endif; ?>
                  </select>
                  <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap">
                    <i class="fa fa-save me-1"></i>Assign
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

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
