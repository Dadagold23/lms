<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ── Enrolled + unlocked courses ── */
$enStmt = $pdo->prepare("
    SELECT c.id AS course_id, c.title AS course_title,
           e.paid_amount, e.status AS enroll_status, e.payment_type, e.next_due_date, e.access_expires_at, e.created_at, c.price
    FROM lms_enrollments e
    JOIN lms_courses c ON c.id = e.course_id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$enStmt->execute([$studentId]);
$enrolledCourses = $enStmt->fetchAll(PDO::FETCH_ASSOC);

/* Build set of unlocked course IDs */
$unlockedCourseIds = [];
foreach ($enrolledCourses as $ec) {
    $access = enrollmentAccessState($ec);
    if ($access['is_unlocked']) {
        $unlockedCourseIds[] = (int)$ec['course_id'];
    }
}

/* ── Optional course filter ── */
$filterCourseId = (int)($_GET['course_id'] ?? 0);

/* ── Fetch ONE exam per course (latest published) for enrolled+unlocked courses ── */
$exams = [];
if (!empty($unlockedCourseIds)) {
    $ids = implode(',', $unlockedCourseIds);
    $filter = $filterCourseId > 0 && in_array($filterCourseId, $unlockedCourseIds, true)
              ? "AND ex.course_id = {$filterCourseId}" : '';

    $exams = $pdo->query("
        SELECT ex.id, ex.course_id, ex.title, ex.duration_minutes,
               ex.total_marks, ex.pass_mark, ex.total_questions,
               c.title AS course_title,
               r.id AS result_id, r.score, r.total, r.percent, r.status AS result_status, r.taken_at
        FROM lms_exams ex
        JOIN lms_courses c ON c.id = ex.course_id
        LEFT JOIN lms_exam_results r ON r.exam_id = ex.id AND r.student_id = {$studentId}
        WHERE ex.is_published = 1
          AND ex.course_id IN ({$ids})
          {$filter}
          AND ex.id = (
              SELECT id FROM lms_exams
              WHERE course_id = ex.course_id AND is_published = 1
              ORDER BY created_at DESC LIMIT 1
          )
        ORDER BY c.title ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/* ── Lesson completion check per course ── */
$lessonProgress = [];
foreach ($unlockedCourseIds as $cid) {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE course_id={$cid} AND is_published=1")->fetchColumn();
    $done  = 0;
    try {
        $done = (int)$pdo->query("SELECT COUNT(*) FROM lms_lesson_completions WHERE student_id={$studentId} AND course_id={$cid}")->fetchColumn();
    } catch (Throwable $e) {}
    $lessonProgress[$cid] = ['total' => $total, 'done' => $done, 'complete' => ($total === 0 || $done >= $total)];
}

/* ── Course selector list ── */
$selectorCourses = array_filter($enrolledCourses, fn($ec) => in_array((int)$ec['course_id'], $unlockedCourseIds, true));
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Exams';
$seoDesc    = 'Take your course exams at Grafix@Mirror LMS. Complete all lessons first to unlock your exam.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<meta name="description" content="Take your course exams at Grafix@Mirror LMS.">
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
    <h4 class="page-title mb-0"><i class="fa fa-pen-alt me-2"></i>Exams</h4>

    <?php if (count($selectorCourses) > 1): ?>
    <form method="get">
      <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:200px">
        <option value="0">All My Courses</option>
        <?php foreach ($selectorCourses as $ec): ?>
          <option value="<?= (int)$ec['course_id'] ?>" <?= (int)$ec['course_id'] === $filterCourseId ? 'selected' : '' ?>>
            <?= e($ec['course_title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php endif; ?>
  </div>

  <?php if (empty($unlockedCourseIds)): ?>
    <div class="lms-alert lms-alert-warning">
      <i class="fa fa-lock"></i> Complete your course payment to unlock exams.
      <a href="dashboard.php" class="ms-2">Go to Dashboard</a>
    </div>

  <?php elseif (empty($exams)): ?>
    <div class="lms-alert lms-alert-info">
      <i class="fa fa-info-circle"></i> No exams available for your enrolled courses yet.
    </div>

  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($exams as $ex):
        $hasTaken  = !empty($ex['result_id']);
        $passed    = $hasTaken && $ex['result_status'] === 'pass';
        $failed    = $hasTaken && $ex['result_status'] === 'fail';
      ?>
        <div class="col-md-6">
          <div class="lms-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
              <div>
                <div style="font-size:.75rem;color:var(--brand);font-weight:600;text-transform:uppercase;letter-spacing:.05em">
                  <?= e($ex['course_title']) ?>
                </div>
                <h6 class="mb-0 mt-1" style="font-weight:700"><?= e($ex['title']) ?></h6>
              </div>
              <?php if ($passed): ?>
                <span class="badge-success">Passed</span>
              <?php elseif ($failed): ?>
                <span class="badge-danger">Failed</span>
              <?php else: ?>
                <span class="badge-info">Not Taken</span>
              <?php endif; ?>
            </div>

            <div class="d-flex gap-3 mt-2 mb-3 flex-wrap" style="font-size:.8rem;color:var(--muted)">
              <?php if ($ex['duration_minutes']): ?>
                <span><i class="fa fa-clock me-1"></i><?= (int)$ex['duration_minutes'] ?> mins</span>
              <?php endif; ?>
              <?php if ($ex['total_marks']): ?>
                <span><i class="fa fa-star me-1"></i><?= (int)$ex['total_marks'] ?> marks</span>
              <?php endif; ?>
              <?php if ($ex['pass_mark']): ?>
                <span><i class="fa fa-check me-1"></i>Pass: <?= (int)$ex['pass_mark'] ?>%</span>
              <?php endif; ?>
            </div>

            <?php if ($hasTaken): ?>
              <div class="lms-alert lms-alert-<?= $passed ? 'success' : 'danger' ?> mb-3" style="font-size:.82rem">
                <i class="fa fa-<?= $passed ? 'check' : 'times' ?>-circle"></i>
                Score: <strong><?= (int)$ex['score'] ?>/<?= (int)$ex['total'] ?></strong>
                (<?= number_format((float)$ex['percent'], 1) ?>%)
                — <?= e(date('d M Y', strtotime($ex['taken_at']))) ?>
              </div>
            <?php endif; ?>

            <div class="mt-auto">
              <?php
                $lp = $lessonProgress[$ex['course_id']] ?? ['complete' => true, 'done' => 0, 'total' => 0];
                if (!$lp['complete']):
              ?>
                <div class="lms-alert lms-alert-warning mb-2" style="font-size:.8rem">
                  <i class="fa fa-book me-1"></i>
                  Complete all lessons first (<?= $lp['done'] ?>/<?= $lp['total'] ?> done).
                </div>
                <a class="btn-ghost w-100 text-center d-block"
                   href="course_lessons.php?course_id=<?= (int)$ex['course_id'] ?>">
                  <i class="fa fa-list-ul me-1"></i> Go to Lessons
                </a>
              <?php else: ?>
                <a class="btn-brand w-100 justify-content-center d-flex"
                   href="take_exam.php?id=<?= (int)$ex['id'] ?>">
                  <i class="fa fa-<?= $hasTaken ? 'redo' : 'play' ?> me-1"></i>
                  <?= $hasTaken ? 'Retake Exam' : 'Start Exam' ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
