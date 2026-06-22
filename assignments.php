<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ── Enrolled courses for selector ── */
$enStmt = $pdo->prepare("
    SELECT c.id, c.title, e.status AS enroll_status, e.paid_amount, e.payment_type, e.next_due_date, e.access_expires_at, e.created_at, c.price
    FROM lms_courses c
    JOIN lms_enrollments e ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$enStmt->execute([$studentId]);
$enrolledCourses = $enStmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Default to first enrolled course ── */
$courseId = (int)($_GET['course_id'] ?? 0);
if ($courseId <= 0 && !empty($enrolledCourses)) {
    $courseId = (int)$enrolledCourses[0]['id'];
}

/* ── Verify student is enrolled in selected course ── */
$selectedCourse = null;
foreach ($enrolledCourses as $ec) {
    if ((int)$ec['id'] === $courseId) { $selectedCourse = $ec; break; }
}

if ($courseId > 0 && !$selectedCourse) {
    http_response_code(403); exit('Access denied: not enrolled in this course.');
}

/* ── Access check ── */
$isUnlocked = false;
if ($selectedCourse) {
    $access = enrollmentAccessState($selectedCourse);
    $isUnlocked = (bool)$access['is_unlocked'];
}

/* ── Assignments for selected course ── */
$assignments = [];
if ($courseId > 0 && $isUnlocked) {
    $aStmt = $pdo->prepare("
        SELECT a.id, a.title, a.instructions, a.due_date, a.attachment_path,
               s.id AS submission_id, s.status AS sub_status, s.submitted_at,
               s.score, s.feedback
        FROM lms_assignments a
        LEFT JOIN lms_assignment_submissions s
               ON s.assignment_id = a.id AND s.student_id = ?
        WHERE a.course_id = ? AND a.is_published = 1
        ORDER BY a.due_date ASC, a.id ASC
    ");
    $aStmt->execute([$studentId, $courseId]);
    $assignments = $aStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Assignments';
$seoDesc    = 'View and submit your course assignments at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<title>Assignments<?= $selectedCourse ? ' — ' . e($selectedCourse['title']) : '' ?> | Grafix@Mirror LMS</title>
<meta name="description" content="Course assignments for <?= e($selectedCourse['title'] ?? 'your enrolled course') ?> at Grafix@Mirror LMS.">
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="brand text-decoration-none">
      <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:900px">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <h4 class="page-title mb-0"><i class="fa fa-tasks me-2"></i>Assignments</h4>

    <?php if (count($enrolledCourses) > 1): ?>
    <form method="get">
      <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:200px">
        <?php foreach ($enrolledCourses as $ec): ?>
          <option value="<?= (int)$ec['id'] ?>" <?= (int)$ec['id'] === $courseId ? 'selected' : '' ?>>
            <?= e($ec['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php endif; ?>
  </div>

  <?php if (!$selectedCourse): ?>
    <div class="lms-alert lms-alert-info">
      <i class="fa fa-info-circle"></i> You are not enrolled in any course yet. <a href="courses.php">Browse courses</a>.
    </div>

  <?php elseif (!$isUnlocked): ?>
    <div class="lms-alert lms-alert-warning">
      <i class="fa fa-lock"></i> Complete your payment or overdue installment balance to access assignments for <strong><?= e($selectedCourse['title']) ?></strong>.
    </div>

  <?php elseif (empty($assignments)): ?>
    <div class="lms-alert lms-alert-info">
      <i class="fa fa-info-circle"></i> No assignments published for <strong><?= e($selectedCourse['title']) ?></strong> yet.
    </div>

  <?php else: ?>
    <div class="d-flex flex-column gap-4">
      <?php foreach ($assignments as $a):
        $isOverdue   = !empty($a['due_date']) && strtotime($a['due_date']) < time();
        $isSubmitted = !empty($a['submission_id']);
        $subStatus   = (string)($a['sub_status'] ?? '');
      ?>
        <div class="lms-card">
          <!-- Header row -->
          <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
            <h5 class="mb-0" style="font-weight:700"><?= e($a['title']) ?></h5>
            <?php if ($isSubmitted && $subStatus === 'graded'): ?>
              <span class="badge-success">Graded</span>
            <?php elseif ($isSubmitted): ?>
              <span class="badge-success">Submitted</span>
            <?php elseif ($isOverdue): ?>
              <span class="badge-danger">Overdue</span>
            <?php else: ?>
              <span class="badge-info">Pending</span>
            <?php endif; ?>
          </div>

          <!-- Due date -->
          <?php if (!empty($a['due_date'])): ?>
            <div class="mb-3" style="font-size:.85rem;color:<?= $isOverdue ? 'var(--danger)' : 'var(--muted)' ?>">
              <i class="fa fa-calendar me-1"></i>Due: <strong><?= e(date('d M Y', strtotime($a['due_date']))) ?></strong>
            </div>
          <?php endif; ?>

          <!-- Full instructions -->
          <?php if (!empty($a['instructions'])): ?>
            <div class="mb-3 p-3" style="background:var(--surface);border-radius:10px;border:1px solid var(--border);font-size:.92rem;line-height:1.7;white-space:pre-wrap"><?= e($a['instructions']) ?></div>
          <?php endif; ?>

          <!-- Attachment -->
          <?php if (!empty($a['attachment_path'])): ?>
            <div class="mb-3">
              <a class="btn-ghost" style="font-size:.85rem" target="_blank" href="<?= e($a['attachment_path']) ?>">
                <i class="fa fa-paperclip me-1"></i>Download Attachment
              </a>
            </div>
          <?php endif; ?>

          <!-- Grade / feedback if graded -->
          <?php if ($isSubmitted && $subStatus === 'graded'): ?>
            <div class="mb-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;font-size:.88rem">
              <?php if (!empty($a['score'])): ?>
                <div><i class="fa fa-star me-1" style="color:var(--success)"></i><strong>Score:</strong> <?= e($a['score']) ?></div>
              <?php endif; ?>
              <?php if (!empty($a['feedback'])): ?>
                <div class="mt-1"><i class="fa fa-comment me-1" style="color:var(--success)"></i><strong>Feedback:</strong> <?= nl2br(e($a['feedback'])) ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- Submit button -->
          <div>
            <a class="btn-brand" href="assignment_submit.php?assignment_id=<?= (int)$a['id'] ?>">
              <i class="fa fa-<?= $isSubmitted ? 'redo' : 'upload' ?> me-1"></i>
              <?= $isSubmitted ? 'Resubmit Assignment' : 'Submit Assignment' ?>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
