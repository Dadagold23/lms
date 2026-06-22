<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ======================
   ENROLLED COURSES
   Prefer the student's registered course first
====================== */
$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.title,
        c.intro_video,
        c.price,
        e.paid_amount,
        e.status        AS enroll_status,
        e.payment_type,
        e.next_due_date,
        e.access_expires_at,
        e.created_at
    FROM lms_courses c
    INNER JOIN lms_enrollments e ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$stmt->execute([$studentId]);
$enrolledCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Default to the student's primary registered course */
$courseId = (int)($_GET['course_id'] ?? 0);
if ($courseId <= 0 && !empty($enrolledCourses)) {
    $courseId = (int)$enrolledCourses[0]['id'];
}

/* ======================
   SELECTED COURSE
====================== */
$selectedCourse = null;
foreach ($enrolledCourses as $c) {
    if ((int)$c['id'] === $courseId) {
        $selectedCourse = $c;
        break;
    }
}

if ($courseId > 0 && !$selectedCourse) {
    http_response_code(403);
    exit('Access denied: you are not enrolled in this course.');
}

/* ======================
   ACCESS CHECK
   Unlocked if: status=paid OR paid_amount >= price
   Installment students get access once first payment is made
====================== */
$isUnlocked = false;
if ($selectedCourse) {
    $access = enrollmentAccessState($selectedCourse);
    $isUnlocked = (bool)$access['is_unlocked'];
}

/* ======================
   FETCH VIDEOS (only if unlocked)
====================== */
$videos = [];
if ($courseId > 0 && $isUnlocked) {
    $stmt = $pdo->prepare("
        SELECT id, course_id, lesson_id, title, video_path, duration_seconds, created_at
        FROM lms_videos
        WHERE course_id = ? AND is_published = 1
        ORDER BY id ASC
    ");
    $stmt->execute([$courseId]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ======================
   HELPERS
====================== */
function videoSrc(string $path): string
{
    $path = trim($path);
    if ($path === '') return '';
    if (preg_match('~^https?://~i', $path)) return $path;
    return 'uploads/' . ltrim($path, '/');
}

function isYouTube(string $src): bool
{
    return (bool)preg_match('~(youtube\.com|youtu\.be)~i', $src);
}

function isExternalVideo(string $src): bool
{
    return (bool)preg_match('~^https?://~i', $src) && !isYouTube($src);
}

function fmtDur(?int $s): string
{
    $s = (int)($s ?? 0);
    if ($s <= 0) return '';
    $h = intdiv($s, 3600);
    $m = intdiv($s % 3600, 60);
    $sec = $s % 60;
    return $h > 0
        ? sprintf('%d:%02d:%02d', $h, $m, $sec)
        : sprintf('%d:%02d', $m, $sec);
}
?>
<!doctype html>
<html lang="en">
<head>
  <?php
$seoTitle   = 'Course Videos';
$seoDesc    = 'Watch course video lectures at Grafix@Mirror LMS — Mirror Age Concepts professional technology training.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
  <title>Course Videos | Mirror LMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      <a href="assignments.php" class="btn-ghost">Assignments</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:1100px">

  <!-- Course selector -->
  <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
    <h4 class="mb-0" style="font-weight:700">Course Videos</h4>
    <?php if (count($enrolledCourses) > 1): ?>
    <form method="get">
      <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:200px">
        <?php foreach ($enrolledCourses as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= ((int)$c['id'] === $courseId) ? 'selected' : '' ?>>
            <?= e($c['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php endif; ?>
  </div>

  <?php if (!$selectedCourse): ?>
    <div class="lms-alert lms-alert-warning">
      You are not enrolled in any course yet. <a href="courses.php">Browse courses</a>.
    </div>

  <?php else: ?>

    <!-- ===== INTRO VIDEO (always visible to enrolled students) ===== -->
    <?php if (!empty($selectedCourse['intro_video'])): ?>
    <?php $introSrc = videoSrc((string)$selectedCourse['intro_video']); ?>
    <div class="lms-card mb-4">
      <div class="form-section-title mb-3">
        <i class="fa fa-play-circle me-2" style="color:var(--brand)"></i>
        <?= e($selectedCourse['title']) ?> — Introduction
      </div>
      <?php if (isYouTube($introSrc)): ?>
        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;background:#000">
          <iframe src="<?= e(youtubeEmbedUrl($introSrc) ?? $introSrc) ?>"
                  style="position:absolute;top:0;left:0;width:100%;height:100%;border:0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen loading="lazy" title="Introduction"></iframe>
        </div>
      <?php else: ?>
        <video class="w-100 rounded" controls preload="metadata" style="max-height:360px;background:#000;object-fit:contain">
          <source src="<?= e($introSrc) ?>" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ===== PAYMENT GATE ===== -->
    <?php if (!$isUnlocked): ?>
      <?php
        $access = enrollmentAccessState($selectedCourse);
        $price  = (float)$selectedCourse['price'];
        $paid   = (float)$selectedCourse['paid_amount'];
        $ptype  = (string)($selectedCourse['payment_type'] ?? 'full');
        // get enrollment id for pay link
        $enStmt = $pdo->prepare("SELECT id FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
        $enStmt->execute([$studentId, $courseId]);
        $enId = (int)$enStmt->fetchColumn();
      ?>
      <div class="lms-card text-center py-5">
        <i class="fa fa-lock fa-3x mb-3" style="color:var(--muted)"></i>
        <h5>Course Content Locked</h5>
        <p class="text-muted mb-1">
          <?php if ($ptype === 'installment' && $paid <= 0): ?>
            Make your first installment payment (50% = ₦<?= number_format($price * 0.5, 2) ?>) to unlock this course.
          <?php elseif ($ptype === 'installment' && !empty($access['installment_due'])): ?>
            Your second installment is overdue. Balance: â‚¦<?= number_format(max(0, $price - $paid), 2) ?>
          <?php elseif ($ptype === 'installment'): ?>
            Your installment payment is pending. Balance: ₦<?= number_format(max(0, $price - $paid), 2) ?>
          <?php else: ?>
            Complete your payment of ₦<?= number_format(max(0, $price - $paid), 2) ?> to access course videos.
          <?php endif; ?>
        </p>
        <?php if ($enId > 0): ?>
          <a href="pay.php?enrollment_id=<?= $enId ?>" class="btn-brand mt-3 d-inline-block">
            <i class="fa fa-credit-card me-1"></i> Make Payment
          </a>
        <?php endif; ?>
      </div>

    <?php else: ?>

      <!-- ===== INSTALLMENT REMINDER ===== -->
      <?php
        $access  = enrollmentAccessState($selectedCourse);
        $ptype   = (string)($selectedCourse['payment_type'] ?? 'full');
        $price   = (float)$selectedCourse['price'];
        $paid    = (float)$selectedCourse['paid_amount'];
        $balance = max(0, $price - $paid);
        $dueDate = $access['next_due_date'] ?? ($selectedCourse['next_due_date'] ?? null);
        $enStmt  = $pdo->prepare("SELECT id FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
        $enStmt->execute([$studentId, $courseId]);
        $enId = (int)$enStmt->fetchColumn();
      ?>
      <?php if ($ptype === 'installment' && $balance > 0): ?>
      <div class="lms-alert lms-alert-warning mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
          <i class="fa fa-info-circle me-1"></i>
          Installment balance: <strong>₦<?= number_format($balance, 2) ?></strong>
          <?php if ($dueDate): ?>
            — next due <strong><?= e(date('d M Y', strtotime($dueDate))) ?></strong>
          <?php endif; ?>
          <?php if (!empty($access['installment_due'])): ?>
            <div class="mt-1" style="font-size:.8rem;color:var(--danger)">This second payment is overdue and access is restricted until the balance is paid.</div>
          <?php endif; ?>
        </div>
        <?php if ($enId > 0): ?>
          <a href="pay.php?enrollment_id=<?= $enId ?>" class="btn btn-warning btn-sm">Pay Balance</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- ===== VIDEO LIST ===== -->
      <?php if (empty($videos)): ?>
        <div class="lms-alert lms-alert-info">No published videos for this course yet. Check back soon.</div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($videos as $v): ?>
            <?php
              $src = videoSrc((string)($v['video_path'] ?? ''));
              $dur = fmtDur(isset($v['duration_seconds']) ? (int)$v['duration_seconds'] : null);
              $isYT  = $src !== '' && isYouTube($src);
              $isExt = $src !== '' && isExternalVideo($src);
            ?>
            <div class="col-md-6 col-lg-4">
              <div class="lms-card h-100 p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                  <h6 class="mb-0" style="font-size:.9rem;font-weight:600"><?= e((string)($v['title'] ?? 'Video')) ?></h6>
                  <?php if ($dur !== ''): ?>
                    <span class="badge bg-secondary flex-shrink-0"><?= e($dur) ?></span>
                  <?php endif; ?>
                </div>

                <?php if ($src !== ''): ?>
                  <?php if ($isYT): ?>
                    <!-- YouTube embed -->
                    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;background:#000;flex-shrink:0">
                      <iframe src="<?= e(youtubeEmbedUrl($src) ?? $src) ?>"
                              style="position:absolute;top:0;left:0;width:100%;height:100%;border:0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen loading="lazy"
                              title="<?= e((string)($v['title'] ?? '')) ?>"></iframe>
                    </div>
                  <?php elseif ($isExt): ?>
                    <!-- External URL — open in new tab -->
                    <a href="<?= e($src) ?>" target="_blank" rel="noopener"
                       class="d-flex align-items-center justify-content-center rounded text-decoration-none"
                       style="height:140px;background:#1a1a2e;border-radius:8px;color:#e3c162;font-size:2rem;flex-shrink:0">
                      <i class="fa fa-play-circle"></i>
                    </a>
                  <?php else: ?>
                    <!-- Uploaded file -->
                    <video class="w-100 rounded" controls preload="metadata"
                           style="max-height:180px;object-fit:cover;flex-shrink:0">
                      <source src="<?= e($src) ?>" type="video/mp4">
                    </video>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="text-muted small py-3 text-center flex-shrink-0">
                    <i class="fa fa-video-slash"></i> No video file
                  </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                  <small class="text-muted">
                    <?= !empty($v['created_at']) ? e(date('d M Y', strtotime((string)$v['created_at']))) : '' ?>
                  </small>
                  <div class="d-flex gap-2">
                    <?php if ($isYT || $isExt): ?>
                      <a class="btn btn-outline-secondary btn-sm" href="<?= e($src) ?>" target="_blank" rel="noopener">
                        <i class="fa fa-external-link-alt me-1"></i>Open
                      </a>
                    <?php endif; ?>
                    <?php if (!empty($v['lesson_id'])): ?>
                      <a class="btn btn-outline-primary btn-sm" href="lesson.php?id=<?= (int)$v['lesson_id'] ?>&course_id=<?= $courseId ?>">
                        <i class="fa fa-book-open me-1"></i>Lesson
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    <?php endif; // isUnlocked ?>
  <?php endif; // selectedCourse ?>

</div>
</body>
</html>
