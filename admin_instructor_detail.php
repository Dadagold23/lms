<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';
requireAdminLogin();

$insId = (int)($_GET['id'] ?? 0);
if ($insId <= 0) redirect('admin_instructors.php');

$admin = $_SESSION['admin'] ?? $_SESSION['user'] ?? [];

// ─── Fetch instructor ───
$ins = $pdo->prepare("SELECT * FROM lms_instructors WHERE id = ? LIMIT 1");
$ins->execute([$insId]);
$instructor = $ins->fetch(PDO::FETCH_ASSOC);
if (!$instructor) redirect('admin_instructors.php');

// ─── Performance Summary ───
$perf = $pdo->prepare("
    SELECT
        (SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id = :id1) AS total_courses,
        (SELECT COUNT(l.id) FROM lms_lessons l JOIN lms_instructor_courses ic ON ic.course_id = l.course_id WHERE ic.instructor_id = :id2) AS total_lessons,
        (SELECT COUNT(DISTINCT e.student_id) FROM lms_enrollments e JOIN lms_instructor_courses ic ON ic.course_id = e.course_id WHERE ic.instructor_id = :id3) AS total_students,
        (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id = :id4) AS total_submissions,
        (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id = :id5 AND s.score IS NOT NULL) AS graded_submissions,
        (SELECT ROUND(AVG(s.score),1) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id = :id6 AND s.score IS NOT NULL) AS avg_score
");
$perf->execute([
    ':id1' => $insId,
    ':id2' => $insId,
    ':id3' => $insId,
    ':id4' => $insId,
    ':id5' => $insId,
    ':id6' => $insId
]);
$summary = $perf->fetch(PDO::FETCH_ASSOC);

// ─── Per-course Breakdown ───
$coursesStmt = $pdo->prepare("
    SELECT
        c.id AS course_id,
        c.title AS course_title,
        c.is_active,
        (SELECT COUNT(*) FROM lms_enrollments WHERE course_id = c.id) AS students,
        (SELECT COUNT(*) FROM lms_lessons WHERE course_id = c.id) AS lessons,
        (SELECT COUNT(*) FROM lms_assignments WHERE course_id = c.id) AS assignments,
        (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id WHERE a.course_id = c.id AND s.score IS NOT NULL) AS graded,
        (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id WHERE a.course_id = c.id) AS total_subs,
        (SELECT ROUND(AVG(s.score),1) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id WHERE a.course_id = c.id AND s.score IS NOT NULL) AS avg_score
    FROM lms_instructor_courses ic
    JOIN lms_courses c ON c.id = ic.course_id
    WHERE ic.instructor_id = ?
    ORDER BY c.title ASC
");
$coursesStmt->execute([$insId]);
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// ─── Per-course students ───
$studentsByCourse = [];
foreach ($courses as $course) {
    $cid = (int)$course['course_id'];
    $stStmt = $pdo->prepare("
        SELECT
            s.id,
            CONCAT(s.first_name,' ',s.last_name) AS full_name,
            s.email,
            e.status AS enroll_status,
            e.paid_amount,
            e.created_at AS enrolled_at,
            (SELECT COUNT(*) FROM lms_assignment_submissions sub JOIN lms_assignments a ON a.id = sub.assignment_id WHERE a.course_id = ? AND sub.student_id = s.id) AS submissions,
            (SELECT COUNT(*) FROM lms_assignment_submissions sub JOIN lms_assignments a ON a.id = sub.assignment_id WHERE a.course_id = ? AND sub.student_id = s.id AND sub.score IS NOT NULL) AS graded,
            (SELECT ROUND(AVG(sub.score),1) FROM lms_assignment_submissions sub JOIN lms_assignments a ON a.id = sub.assignment_id WHERE a.course_id = ? AND sub.student_id = s.id AND sub.score IS NOT NULL) AS avg_score
        FROM lms_enrollments e
        JOIN lms_students s ON s.id = e.student_id
        WHERE e.course_id = ?
        ORDER BY s.first_name ASC
        LIMIT 50
    ");
    $stStmt->execute([$cid, $cid, $cid, $cid]);
    $studentsByCourse[$cid] = $stStmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalSubs   = (int)($summary['total_submissions'] ?? 0);
$gradedSubs  = (int)($summary['graded_submissions'] ?? 0);
$pendingSubs = $totalSubs - $gradedSubs;
$gradePercent = $totalSubs > 0 ? round(($gradedSubs / $totalSubs) * 100) : 0;

$avail     = (string)($instructor['availability_status'] ?? 'available');
$availColor = match($avail) { 'busy' => '#f59e0b', 'leave' => '#ef4444', default => '#10b981' };
$availText  = match($avail) { 'busy' => 'Busy', 'leave' => 'On Leave', default => 'Available' };
$st        = (string)($instructor['status'] ?? 'active');
$stColor   = match($st) { 'active' => '#10b981', 'suspended' => '#ef4444', default => '#f59e0b' };

$initials = '';
if (!empty($instructor['full_name'])) {
    $parts = explode(' ', $instructor['full_name']);
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Profile — ' . ($instructor['full_name'] ?? '');
$seoDesc    = 'Detailed instructor performance and student analytics.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
body { background: #f0f4ff; font-family: Inter, system-ui; }
.detail-header {
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4f46e5 100%);
  padding: 40px;
  border-radius: 20px;
  color: #fff;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.detail-header::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 220px; height: 220px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.stat-card {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 16px rgba(79,70,229,.07);
  border: 1px solid #e8ecf8;
  transition: transform .2s, box-shadow .2s;
  height: 100%;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(79,70,229,.12); }
.stat-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  margin-bottom: 12px;
}
.stat-val { font-size: 2rem; font-weight: 800; color: #1e1b4b; line-height: 1; }
.stat-label { font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; margin-top: 4px; }
.course-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #e8ecf8;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(79,70,229,.05);
  margin-bottom: 20px;
}
.course-card-header {
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  color: #fff;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.avatar-hero {
  width: 88px; height: 88px;
  border-radius: 50%;
  border: 4px solid rgba(255,255,255,.3);
  object-fit: cover;
}
.avatar-hero-placeholder {
  width: 88px; height: 88px;
  border-radius: 50%;
  background: rgba(255,255,255,.15);
  border: 4px solid rgba(255,255,255,.3);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem; font-weight: 800; color: #fff;
}
.badge-pill-sm {
  padding: .3rem .75rem;
  border-radius: 20px;
  font-size: .75rem;
  font-weight: 600;
  display: inline-flex; align-items: center; gap: 4px;
}
.table-students th {
  background: #f8faff;
  color: #4f46e5;
  font-size: .75rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  font-weight: 700;
}
.score-badge {
  padding: .2rem .6rem;
  border-radius: 6px;
  font-size: .78rem;
  font-weight: 700;
}
.score-high { background: #dcfce7; color: #15803d; }
.score-mid  { background: #fef9c3; color: #854d0e; }
.score-low  { background: #fee2e2; color: #991b1b; }
.score-none { background: #f1f5f9; color: #64748b; }
.back-btn {
  display: inline-flex; align-items: center; gap: 8px;
  color: #a5b4fc; font-size: .88rem; text-decoration: none;
  margin-bottom: 20px;
  transition: color .15s;
}
.back-btn:hover { color: #fff; }
</style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm py-2" style="background:#0f172a;">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="admin_dashboard.php">
      <div style="width:30px;height:30px;background:rgba(255,255,255,.12);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.8rem">A</div>
      <span>Admin <span style="color:#a5b4fc">Panel</span></span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
      <a href="admin_instructors.php" class="nav-link text-white small"><i class="fa fa-users me-1"></i>Instructors</a>
      <a href="admin_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <a href="admin_instructors.php" class="back-btn" style="color:#4f46e5">
    <i class="fa fa-arrow-left"></i> Back to Instructors
  </a>

  <!-- ─── HERO HEADER ─── -->
  <div class="detail-header">
    <div class="d-flex align-items-start gap-4 flex-wrap">
      <!-- Avatar -->
      <div class="flex-shrink-0">
        <?php if (!empty($instructor['photo'])): ?>
          <img src="uploads/<?= e($instructor['photo']) ?>" class="avatar-hero" alt="">
        <?php else: ?>
          <div class="avatar-hero-placeholder"><?= $initials ?></div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
          <h2 class="fw-bold mb-0 text-white" style="font-size:1.7rem"><?= e($instructor['full_name']) ?></h2>
          <span class="badge-pill-sm" style="background:rgba(255,255,255,.15);color:#c7d2fe"><?= e($instructor['specialization'] ?? '') ?></span>
        </div>
        <div class="d-flex gap-2 flex-wrap mb-3">
          <span class="badge-pill-sm" style="background:<?= $stColor ?>22;color:<?= $stColor ?>;border:1px solid <?= $stColor ?>44">
            <i class="fa fa-circle fa-xs"></i> <?= ucfirst($st) ?>
          </span>
          <span class="badge-pill-sm" style="background:<?= $availColor ?>22;color:<?= $availColor ?>;border:1px solid <?= $availColor ?>44">
            <i class="fa fa-clock"></i> <?= $availText ?>
          </span>
          <?php if (!empty($instructor['experience_years'])): ?>
          <span class="badge-pill-sm" style="background:rgba(255,255,255,.1);color:#c7d2fe">
            <i class="fa fa-star"></i> <?= (int)$instructor['experience_years'] ?> yrs experience
          </span>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-3 flex-wrap" style="font-size:.85rem;color:#c7d2fe">
          <span><i class="fa fa-envelope me-1"></i><?= e($instructor['email']) ?></span>
          <?php if (!empty($instructor['phone'])): ?>
          <span><i class="fa fa-phone me-1"></i><?= e($instructor['phone']) ?></span>
          <?php endif; ?>
          <?php if (!empty($instructor['qualification'])): ?>
          <span><i class="fa fa-graduation-cap me-1"></i><?= e($instructor['qualification']) ?></span>
          <?php endif; ?>
          <?php if (!empty($instructor['linkedin_url'])): ?>
          <a href="<?= e($instructor['linkedin_url']) ?>" target="_blank" style="color:#93c5fd"><i class="fab fa-linkedin me-1"></i>LinkedIn</a>
          <?php endif; ?>
        </div>
        <?php if (!empty($instructor['bio'])): ?>
        <p class="mt-3 mb-0" style="font-size:.9rem;color:#a5b4fc;max-width:580px;line-height:1.6"><?= e($instructor['bio']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Edit -->
      <div class="ms-auto">
        <a href="admin_instructors.php" class="btn btn-sm" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2)">
          <i class="fa fa-edit me-1"></i> Edit
        </a>
      </div>
    </div>
  </div>

  <!-- ─── SUMMARY STATS ─── -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto" style="background:#ede9fe;color:#4f46e5"><i class="fa fa-book-open"></i></div>
        <div class="stat-val"><?= (int)($summary['total_courses'] ?? 0) ?></div>
        <div class="stat-label">Courses Assigned</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto" style="background:#dcfce7;color:#16a34a"><i class="fa fa-play-circle"></i></div>
        <div class="stat-val"><?= (int)($summary['total_lessons'] ?? 0) ?></div>
        <div class="stat-label">Lessons Published</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto" style="background:#fef3c7;color:#d97706"><i class="fa fa-user-graduate"></i></div>
        <div class="stat-val"><?= (int)($summary['total_students'] ?? 0) ?></div>
        <div class="stat-label">Students</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card text-center">
        <div class="stat-icon mx-auto" style="background:#fee2e2;color:#dc2626"><i class="fa fa-star"></i></div>
        <div class="stat-val"><?= $summary['avg_score'] !== null ? $summary['avg_score'] . '%' : '—' ?></div>
        <div class="stat-label">Avg Grade</div>
      </div>
    </div>
  </div>

  <!-- ─── GRADING PERFORMANCE + CHART ─── -->
  <div class="row g-3 mb-4">
    <div class="col-md-7">
      <div class="stat-card">
        <h6 class="fw-bold mb-3" style="color:#1e1b4b"><i class="fa fa-chart-bar me-2 text-indigo-500" style="color:#4f46e5"></i>Grading Performance</h6>

        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Graded Submissions</span>
            <span class="fw-bold small"><?= $gradedSubs ?> / <?= $totalSubs ?> (<?= $gradePercent ?>%)</span>
          </div>
          <div class="progress" style="height:10px;border-radius:5px">
            <div class="progress-bar" role="progressbar" style="width:<?= $gradePercent ?>%;background:linear-gradient(90deg,#4f46e5,#7c3aed);border-radius:5px"></div>
          </div>
        </div>

        <div class="row g-2 mt-1">
          <div class="col-6">
            <div style="background:#f0fdf4;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:1.6rem;font-weight:800;color:#16a34a"><?= $gradedSubs ?></div>
              <div style="font-size:.75rem;color:#64748b;text-transform:uppercase;letter-spacing:.05em">Graded</div>
            </div>
          </div>
          <div class="col-6">
            <div style="background:#fff7ed;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:1.6rem;font-weight:800;color:#d97706"><?= $pendingSubs ?></div>
              <div style="font-size:.75rem;color:#64748b;text-transform:uppercase;letter-spacing:.05em">Pending</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="stat-card d-flex flex-column align-items-center justify-content-center">
        <h6 class="fw-bold mb-3 text-center" style="color:#1e1b4b">Submission Overview</h6>
        <canvas id="gradingDonut" width="180" height="180"></canvas>
        <div class="d-flex gap-3 mt-3" style="font-size:.8rem">
          <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#4f46e5;margin-right:5px"></span>Graded</span>
          <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e2e8f0;margin-right:5px"></span>Pending</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── PER-COURSE BREAKDOWN ─── -->
  <h5 class="fw-bold mb-3" style="color:#1e1b4b"><i class="fa fa-layer-group me-2" style="color:#4f46e5"></i>Course Breakdown</h5>

  <?php if (empty($courses)): ?>
    <div class="stat-card text-center py-5">
      <i class="fa fa-book fa-3x mb-3" style="color:#c7d2fe"></i>
      <p class="text-muted">No courses assigned to this instructor yet.</p>
      <a href="admin_instructors.php" class="btn btn-sm btn-outline-primary">Assign a Course</a>
    </div>
  <?php else: ?>
    <?php foreach ($courses as $course):
      $cid       = (int)$course['course_id'];
      $cStudents = $studentsByCourse[$cid] ?? [];
      $cGraded   = (int)$course['graded'];
      $cTotal    = (int)$course['total_subs'];
      $cPct      = $cTotal > 0 ? round(($cGraded / $cTotal) * 100) : 0;
      $scoreColor = $course['avg_score'] !== null
        ? ($course['avg_score'] >= 70 ? 'score-high' : ($course['avg_score'] >= 50 ? 'score-mid' : 'score-low'))
        : 'score-none';
    ?>
    <div class="course-card">
      <div class="course-card-header">
        <div>
          <h6 class="fw-bold mb-1 text-white"><?= e($course['course_title']) ?></h6>
          <div class="d-flex gap-3" style="font-size:.8rem;color:#c7d2fe">
            <span><i class="fa fa-users me-1"></i><?= (int)$course['students'] ?> students</span>
            <span><i class="fa fa-play me-1"></i><?= (int)$course['lessons'] ?> lessons</span>
            <span><i class="fa fa-tasks me-1"></i><?= (int)$course['assignments'] ?> assignments</span>
          </div>
        </div>
        <div class="text-end">
          <span class="badge-pill-sm" style="background:rgba(255,255,255,.15);color:#fff">
            <?= $course['avg_score'] !== null ? $course['avg_score'] . '% avg' : 'No grades yet' ?>
          </span>
        </div>
      </div>

      <div class="p-3">
        <!-- Grading bar -->
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Grading Progress</span>
            <span class="fw-semibold small"><?= $cGraded ?>/<?= $cTotal ?> (<?= $cPct ?>%)</span>
          </div>
          <div class="progress" style="height:6px;border-radius:3px">
            <div class="progress-bar" style="width:<?= $cPct ?>%;background:linear-gradient(90deg,#4f46e5,#7c3aed);border-radius:3px"></div>
          </div>
        </div>

        <!-- Students table -->
        <?php if (!empty($cStudents)): ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle table-students mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Email</th>
                <th>Submissions</th>
                <th>Avg Score</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cStudents as $idx => $st): 
                $scoreClass = $st['avg_score'] !== null
                  ? ($st['avg_score'] >= 70 ? 'score-high' : ($st['avg_score'] >= 50 ? 'score-mid' : 'score-low'))
                  : 'score-none';
              ?>
              <tr>
                <td class="text-muted small"><?= $idx + 1 ?></td>
                <td class="fw-semibold small"><?= e($st['full_name']) ?></td>
                <td class="text-muted small"><?= e($st['email']) ?></td>
                <td class="small"><?= (int)$st['graded'] ?>/<?= (int)$st['submissions'] ?></td>
                <td>
                  <span class="score-badge <?= $scoreClass ?>">
                    <?= $st['avg_score'] !== null ? $st['avg_score'] . '%' : '—' ?>
                  </span>
                </td>
                <td>
                  <span class="badge-pill-sm"
                    style="font-size:.7rem;<?= $st['enroll_status'] === 'paid' ? 'background:#dcfce7;color:#15803d' : 'background:#f1f5f9;color:#64748b' ?>">
                    <?= ucfirst((string)$st['enroll_status']) ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <p class="text-muted small text-center py-2 mb-0">No students enrolled yet.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Grading donut chart
const ctx = document.getElementById('gradingDonut');
if (ctx) {
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Graded', 'Pending'],
      datasets: [{
        data: [<?= $gradedSubs ?>, <?= max(0, $pendingSubs) ?>],
        backgroundColor: ['#4f46e5', '#e2e8f0'],
        borderWidth: 0,
        borderRadius: 4,
      }]
    },
    options: {
      cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed}`
          }
        }
      }
    }
  });
}
</script>
</body>
</html>
