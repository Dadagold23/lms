<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$courseId  = (int)($_GET['course_id'] ?? 0);
if ($courseId <= 0) { http_response_code(400); exit('Invalid course.'); }

/* ── Enrollment + access check ── */
$en = $pdo->prepare("
    SELECT e.paid_amount, e.status, e.access_expires_at, e.payment_type, e.next_due_date, e.created_at, c.price, c.title" . workspaceCourseSelectSql($pdo, 'c') . "
    FROM lms_enrollments e
    JOIN lms_courses c ON c.id = e.course_id
    WHERE e.student_id=? AND e.course_id=?
    LIMIT 1
");
$en->execute([$studentId, $courseId]);
$row = workspaceCourseRow((array)($en->fetch(PDO::FETCH_ASSOC) ?: []));

if (!$row) { http_response_code(403); exit('You are not enrolled in this course.'); }

$access = enrollmentAccessState($row);
$isUnlocked = (bool)$access['is_unlocked'];

if (!$isUnlocked) redirect('course.php?id=' . $courseId);

/* ── Lessons for THIS course only ── */
$lessons = $pdo->prepare("
    SELECT id, title, sort_order
    FROM lms_lessons
    WHERE course_id=? AND is_published=1
    ORDER BY sort_order ASC, id ASC
");
$lessons->execute([$courseId]);
$lessons = $lessons->fetchAll(PDO::FETCH_ASSOC);

/* ── Lesson completion status ── */
$completedIds = [];
try {
    $cStmt = $pdo->prepare("
        SELECT lesson_id FROM lms_lesson_completions
        WHERE student_id=? AND course_id=?
    ");
    $cStmt->execute([$studentId, $courseId]);
    $completedIds = array_column($cStmt->fetchAll(PDO::FETCH_ASSOC), 'lesson_id', 'lesson_id');
} catch (Throwable $e) {}

$totalLessons     = count($lessons);
$completedCount   = count(array_intersect(array_column($lessons, 'id'), array_keys($completedIds)));
$progressPct      = $totalLessons > 0 ? round($completedCount / $totalLessons * 100) : 0;
$allDone          = $totalLessons > 0 && $completedCount >= $totalLessons;

/* ── Enrollment date for weekly schedule (6-month / 26-week span) ── */
$enrolledAt = null;
try {
    $enDate = $pdo->prepare("SELECT created_at FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
    $enDate->execute([$studentId, $courseId]);
    $enrolledAt = $enDate->fetchColumn();
} catch (Throwable $e) {}
$enrollTs = $enrolledAt ? strtotime((string)$enrolledAt) : time();
$now      = time();

function lessonUnlockTs(int $index, int $enrollTs): int {
    return $enrollTs + ($index * 7 * 24 * 3600);
}

/* ── Flash message ── */
$msg = $_GET['msg'] ?? '';
$lockedMsg = urldecode((string)($_GET['msg'] ?? ''));
$courseTitle = (string)$row['title'];
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Course Lessons';
$seoDesc    = 'Access your course lessons at Grafix@Mirror LMS. Complete all lessons to unlock your exam and certificate.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<title>Lessons — <?= e($courseTitle) ?> | Grafix@Mirror LMS</title>
<meta name="description" content="Course lessons for <?= e($courseTitle) ?> at Grafix@Mirror LMS.">
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

<div class="container py-4" style="max-width:760px">

  <?php if ($msg === 'complete_lessons'): ?>
    <div class="lms-alert lms-alert-warning mb-4">
      <i class="fa fa-exclamation-triangle me-1"></i>
      Complete all lessons before taking the exam.
    </div>
  <?php elseif (!empty($_GET['locked'])): ?>
    <div class="lms-alert lms-alert-warning mb-4">
      <i class="fa fa-lock me-1"></i><?= e($lockedMsg) ?>
    </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
    <div>
      <h4 class="page-title mb-0"><?= e($courseTitle) ?></h4>
      <div class="text-muted" style="font-size:.85rem">
        <i class="fa fa-list-ul me-1"></i><?= $totalLessons ?> lesson<?= $totalLessons !== 1 ? 's' : '' ?>
        &nbsp;·&nbsp;
        <i class="fa fa-check-circle me-1" style="color:var(--success)"></i><?= $completedCount ?> completed
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= e(workspaceLaunchUrl(['id' => $courseId, 'workspace_type' => $row['workspace_type'] ?? 'default', 'title' => $courseTitle])) ?>" class="btn-ghost"><i class="fa fa-laptop-code me-1"></i>Workspace</a>
      <a href="videos.php?course_id=<?= $courseId ?>" class="btn-ghost"><i class="fa fa-play-circle me-1"></i>Videos</a>
      <a href="assignments.php?course_id=<?= $courseId ?>" class="btn-ghost"><i class="fa fa-tasks me-1"></i>Assignments</a>
    </div>
  </div>

  <!-- Progress bar -->
  <?php if ($totalLessons > 0): ?>
  <div class="lms-card mb-4 py-3 px-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span style="font-weight:600;font-size:.9rem">Course Progress</span>
      <span style="font-weight:700;color:<?= $allDone ? 'var(--success)' : 'var(--brand)' ?>;font-size:.9rem">
        <?= $progressPct ?>%
        <?php if ($allDone): ?><i class="fa fa-check-circle ms-1"></i><?php endif; ?>
      </span>
    </div>
    <div class="progress" style="height:10px;border-radius:99px;background:var(--border)">
      <div class="progress-bar" role="progressbar"
           style="width:<?= $progressPct ?>%;background:<?= $allDone ? 'var(--success)' : 'var(--brand)' ?>;border-radius:99px;transition:width .4s ease">
      </div>
    </div>
    <div class="d-flex justify-content-between mt-1" style="font-size:.75rem;color:var(--muted)">
      <span><?= $completedCount ?> of <?= $totalLessons ?> lessons completed</span>
      <?php if ($allDone): ?>
        <a href="exams.php?course_id=<?= $courseId ?>" style="color:var(--success);font-weight:600">
          <i class="fa fa-pen-alt me-1"></i>Take Exam →
        </a>
      <?php else: ?>
        <span><?= $totalLessons - $completedCount ?> remaining</span>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (empty($lessons)): ?>
    <div class="lms-alert lms-alert-info">
      <i class="fa fa-info-circle me-1"></i>
      No lessons published for this course yet. Check back soon.
    </div>
  <?php else: ?>
    <div class="lms-card p-0" style="overflow:hidden">
      <?php foreach ($lessons as $i => $l):
        $isDone      = isset($completedIds[$l['id']]);
        $isLast      = ($i === count($lessons) - 1);
        $unlockTs    = lessonUnlockTs($i, $enrollTs);
        $isTimeOpen  = $now >= $unlockTs;
        $prevDone    = $i === 0 || isset($completedIds[$lessons[$i - 1]['id']]);
        $isAccessible = $isDone || ($prevDone && $isTimeOpen);
        $unlockDate  = date('d M Y', $unlockTs);
      ?>
        <?php if ($isAccessible): ?>
          <a href="lesson.php?id=<?= (int)$l['id'] ?>&course_id=<?= $courseId ?>"
             class="d-flex align-items-center gap-3 text-decoration-none px-4 py-3"
             style="<?= !$isLast ? 'border-bottom:1px solid var(--border);' : '' ?>transition:background .15s;color:var(--dark)"
             onmouseover="this.style.background='var(--brand-light)'" onmouseout="this.style.background=''">

            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;
                 background:<?= $isDone ? 'var(--success)' : 'var(--brand-light)' ?>;
                 color:<?= $isDone ? '#fff' : 'var(--brand)' ?>">
              <?php if ($isDone): ?>
                <i class="fa fa-check" style="font-size:.8rem"></i>
              <?php else: ?>
                <?= $i + 1 ?>
              <?php endif; ?>
            </div>

            <div class="flex-grow-1">
              <div style="font-weight:600;font-size:.92rem"><?= e($l['title']) ?></div>
              <?php if ($isDone): ?>
                <div style="font-size:.75rem;color:var(--success)"><i class="fa fa-check-circle me-1"></i>Completed</div>
              <?php else: ?>
                <div style="font-size:.75rem;color:var(--muted)">Week <?= $i + 1 ?> · Available now</div>
              <?php endif; ?>
            </div>

            <i class="fa fa-chevron-right" style="color:var(--muted);font-size:.8rem"></i>
          </a>
        <?php else: ?>
          <!-- Locked lesson -->
          <div class="d-flex align-items-center gap-3 px-4 py-3"
               style="<?= !$isLast ? 'border-bottom:1px solid var(--border);' : '' ?>opacity:.6;cursor:not-allowed">

            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;background:#e5e7eb;color:#9ca3af">
              <i class="fa fa-lock" style="font-size:.8rem"></i>
            </div>

            <div class="flex-grow-1">
              <div style="font-weight:600;font-size:.92rem;color:#6b7280"><?= e($l['title']) ?></div>
              <div style="font-size:.75rem;color:#9ca3af">
                <?php if (!$prevDone): ?>
                  <i class="fa fa-lock me-1"></i>Complete the previous lesson first
                <?php else: ?>
                  <i class="fa fa-clock me-1"></i>Week <?= $i + 1 ?> · Unlocks <?= $unlockDate ?>
                <?php endif; ?>
              </div>
            </div>

            <i class="fa fa-lock" style="color:#d1d5db;font-size:.8rem"></i>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!-- CTA after all lessons done -->
    <?php if ($allDone): ?>
      <div class="lms-alert lms-alert-success mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div><i class="fa fa-trophy me-2"></i><strong>All lessons completed!</strong> You can now take the exam.</div>
        <a href="exams.php?course_id=<?= $courseId ?>" class="btn-brand">
          <i class="fa fa-pen-alt me-1"></i> Take Exam
        </a>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>
</body>
</html>
