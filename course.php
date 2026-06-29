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
if ($studentId <= 0) redirect('login.php');

/* ── Resolve course by slug (clean URL) or legacy ?id= ── */
$slugParam = trim((string)($_GET['slug'] ?? ''));
$idParam   = (int)($_GET['id'] ?? 0);

if ($slugParam !== '') {
    // Clean URL: /course/{slug}
    $courseId = 0; // resolved below after fetch
} elseif ($idParam > 0) {
    $courseId = $idParam;
} else {
    http_response_code(400); exit('Invalid course.');
}

/* ── Course ── */
$courseSql = "SELECT id,title,slug,description,short_description,price,level,intro_video"
    . workspaceCourseSelectSql($pdo)
    . ($slugParam !== ''
        ? " FROM lms_courses WHERE slug=? LIMIT 1"
        : " FROM lms_courses WHERE id=? LIMIT 1");
try {
    $stmt = $pdo->prepare($courseSql);
    $stmt->execute([$slugParam !== '' ? $slugParam : $courseId]);
    $course = (array)($stmt->fetch(PDO::FETCH_ASSOC) ?: []);
} catch (PDOException $e) {
    $fallbackSql = $slugParam !== ''
        ? "SELECT id,title,slug,description,short_description,price,level,intro_video FROM lms_courses WHERE slug=? LIMIT 1"
        : "SELECT id,title,slug,description,short_description,price,level,intro_video FROM lms_courses WHERE id=? LIMIT 1";
    $stmt = $pdo->prepare($fallbackSql);
    $stmt->execute([$slugParam !== '' ? $slugParam : $courseId]);
    $course = (array)($stmt->fetch(PDO::FETCH_ASSOC) ?: []);
}
$course = workspaceCourseRow($course);
if (!$course) { http_response_code(404); exit('Course not found.'); }

// Normalise courseId — needed for enrollment queries below
$courseId = (int)$course['id'];

$price = (float)($course['price'] ?? 0);

// Fetch student's affiliate info to apply price cap
$st = $pdo->prepare("SELECT is_affiliate, affiliate_class_range FROM lms_students WHERE id = ? LIMIT 1");
$st->execute([$studentId]);
$student = $st->fetch() ?: [];
$isAffiliate = !empty($student['is_affiliate']);
$classRange  = $student['affiliate_class_range'] ?? '';

if ($isAffiliate && ($classRange === 'JSS' || $classRange === 'SSS')) {
    $price = min($price, 5000.0);
}

/* ── Enrollment ── */
$en = $pdo->prepare("SELECT id,paid_amount,payment_type,status,next_due_date,access_expires_at,created_at FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
$en->execute([$studentId, $courseId]);
$enrollment = $en->fetch(PDO::FETCH_ASSOC);

$isEnrolled   = (bool)$enrollment;
$enrollmentId = $isEnrolled ? (int)$enrollment['id'] : 0;
$paid         = $isEnrolled ? (float)$enrollment['paid_amount'] : 0.0;
$enStatus     = $isEnrolled ? (string)$enrollment['status'] : '';
$ptype        = $isEnrolled ? (string)($enrollment['payment_type'] ?? 'full') : 'full';

$access = $isEnrolled ? enrollmentAccessState([
    'price' => $price,
    'paid_amount' => $paid,
    'payment_type' => $ptype,
    'status' => $enStatus,
    'next_due_date' => $enrollment['next_due_date'] ?? null,
    'access_expires_at' => $enrollment['access_expires_at'] ?? null,
    'created_at' => $enrollment['created_at'] ?? null,
]) : null;
$isExpired = $isEnrolled && (bool)($access['is_expired'] ?? false);
$isUnlocked = $isEnrolled && (bool)($access['is_unlocked'] ?? false);
$installmentDue = $isEnrolled && (bool)($access['installment_due'] ?? false);
$effectiveDueDate = (string)($access['next_due_date'] ?? ($enrollment['next_due_date'] ?? ''));

/* ── Lesson count ── */
$lessonCount = (int)$pdo->prepare("SELECT COUNT(*) FROM lms_lessons WHERE course_id=? AND is_published=1")->execute([$courseId])
    ? $pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE course_id={$courseId} AND is_published=1")->fetchColumn() : 0;

$balance = max(0, $price - $paid);
$workspaceLabel = workspaceTypeLabel($course);
$workspaceIcon = workspaceTypeIcon($course);
$workspaceDescription = workspaceTypeDescription($course);
$workspaceHref = workspaceLaunchUrl($course);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Course Details';
$seoDesc    = 'View course details, lessons, videos and enrol at Grafix@Mirror LMS — Mirror Age Concepts professional technology training.';
$seoNoIndex = false;
require_once __DIR__ . '/includes/seo.php';
?>
<title><?= e($course['title']) ?> | Grafix@Mirror LMS</title>
<meta name="description" content="<?= e($course['short_description'] ?? $course['description'] ?? '') ?>">
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
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-arrow-left me-1"></i>Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:1000px">

  <div class="row g-4">

    <!-- LEFT: Course info + intro video -->
    <div class="col-lg-8">
      <div class="lms-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
          <div>
            <h3 class="mb-1" style="font-weight:800"><?= e($course['title']) ?></h3>
            <div class="d-flex gap-2 flex-wrap" style="font-size:.82rem;color:var(--muted)">
              <span><i class="fa fa-layer-group me-1"></i><?= e(ucfirst($course['level'] ?? '')) ?></span>
              <span><i class="fa fa-book me-1"></i><?= $lessonCount ?> Lessons</span>
              <span><i class="fa fa-tag me-1"></i><?= formatMoney($price) ?></span>
            </div>
          </div>
          <?php
          if (!$isEnrolled)       echo '<span class="badge-muted">Not Enrolled</span>';
          elseif ($isExpired)     echo '<span class="badge-danger">Expired</span>';
          elseif ($isUnlocked)    echo '<span class="badge-success">Unlocked ✓</span>';
          else                    echo '<span class="badge-warning">Payment Required</span>';
          ?>
        </div>

        <?php if (!empty($course['short_description'])): ?>
          <p class="text-muted mb-3"><?= nl2br(e($course['short_description'])) ?></p>
        <?php elseif (!empty($course['description'])): ?>
          <p class="text-muted mb-3"><?= nl2br(e($course['description'])) ?></p>
        <?php endif; ?>

        <div class="lms-alert lms-alert-info mb-3">
          <i class="fa <?= e($workspaceIcon) ?> me-1"></i>
          Workspace: <strong><?= e($workspaceLabel) ?></strong><br>
          <span style="font-size:.85rem"><?= e($workspaceDescription) ?></span>
        </div>

        <!-- Intro Video (YouTube or local) -->
        <?php if (!empty($course['intro_video'])): ?>
          <div class="mt-2">
            <div class="form-section-title"><i class="fa fa-play-circle me-1"></i>Introduction Video</div>
            <?= renderIntroVideo((string)$course['intro_video']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: Actions panel -->
    <div class="col-lg-4">
      <div class="lms-card">
        <div class="form-section-title"><i class="fa fa-bolt me-1"></i>Course Actions</div>

        <?php if (!$isEnrolled): ?>
          <div class="lms-alert lms-alert-info mb-3">
            <i class="fa fa-info-circle"></i> Enroll to access this course.
          </div>
          <form method="post" action="course_register.php">
            <?= csrfField() ?>
            <input type="hidden" name="course_id" value="<?= (int)$courseId ?>">
            <div class="mb-2">
              <label class="form-label">Payment Option</label>
              <select name="payment_option" class="form-select">
                <option value="full">Full Payment</option>
                <option value="installment">Installment Plan</option>
              </select>
              <div class="form-text">Choose the payment plan you prefer for this course enrollment.</div>
            </div>
            <button class="btn-brand w-100 justify-content-center d-flex">
              <i class="fa fa-plus me-1"></i> Enroll Now
            </button>
          </form>

        <?php elseif ($isExpired): ?>
          <div class="lms-alert lms-alert-danger mb-3">
            <i class="fa fa-exclamation-circle"></i> Access expired. Renew to continue.
          </div>
          <a class="btn-brand w-100 justify-content-center d-flex mb-2" style="background:var(--danger)"
             href="pay.php?enrollment_id=<?= $enrollmentId ?>">
            <i class="fa fa-redo me-1"></i> Renew Access
          </a>

        <?php elseif (!$isUnlocked): ?>
          <div class="lms-alert lms-alert-warning mb-3">
            <i class="fa fa-lock"></i>
            <?php if ($ptype === 'installment' && $paid <= 0): ?>
              Pay 1st installment (50% = <?= formatMoney($price * 0.5) ?>) to unlock.
            <?php elseif ($ptype === 'installment' && $installmentDue): ?>
              Your 2nd installment is overdue. Pay the balance to continue access.
            <?php else: ?>
              Balance: <?= formatMoney($balance) ?> remaining.
            <?php endif; ?>
          </div>
          <a class="btn-brand w-100 justify-content-center d-flex mb-2" style="background:var(--warning);color:#000"
             href="pay.php?enrollment_id=<?= $enrollmentId ?>">
            <i class="fa fa-credit-card me-1"></i> Make Payment
          </a>

        <?php else: ?>
          <div class="lms-alert lms-alert-success mb-3">
            <i class="fa fa-check-circle"></i> Course unlocked — start learning!
          </div>
          <a class="btn-brand w-100 justify-content-center d-flex mb-2"
             href="<?= e($workspaceHref) ?>">
            <i class="fa <?= e($workspaceIcon) ?> me-1"></i> Launch <?= e($workspaceLabel) ?>
          </a>
          <?php if ($balance > 0): ?>
            <div class="lms-alert lms-alert-warning mb-3" style="font-size:.82rem">
              <i class="fa fa-info-circle"></i>
              Installment balance: <?= formatMoney($balance) ?>
              <?php if ($ptype === 'installment' && $effectiveDueDate !== ''): ?>
                — due <?= e(date('d M Y', strtotime($effectiveDueDate))) ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <!-- Learning links -->
        <div class="d-grid gap-2 mt-3">
          <a class="btn-outline-brand d-flex align-items-center gap-2 <?= $isUnlocked ? '' : 'disabled' ?>"
             href="<?= e($workspaceHref) ?>" <?= $isUnlocked ? '' : 'tabindex="-1" aria-disabled="true"' ?>>
            <i class="fa <?= e($workspaceIcon) ?>"></i> Workspace
            <span class="badge-muted ms-auto"><?= e($workspaceLabel) ?></span>
          </a>
          <a class="btn-outline-brand d-flex align-items-center gap-2 <?= $isUnlocked ? '' : 'disabled' ?>"
             href="course_lessons.php?course_id=<?= $courseId ?>" <?= $isUnlocked ? '' : 'tabindex="-1" aria-disabled="true"' ?>>
            <i class="fa fa-list-ul"></i> Lessons
            <?php if ($lessonCount > 0): ?><span class="badge-muted ms-auto"><?= $lessonCount ?></span><?php endif; ?>
          </a>
          <a class="btn-outline-brand d-flex align-items-center gap-2 <?= $isUnlocked ? '' : 'disabled' ?>"
             href="videos.php?course_id=<?= $courseId ?>" <?= $isUnlocked ? '' : 'tabindex="-1" aria-disabled="true"' ?>>
            <i class="fa fa-play-circle"></i> Videos
          </a>
          <a class="btn-outline-brand d-flex align-items-center gap-2 <?= $isUnlocked ? '' : 'disabled' ?>"
             href="assignments.php?course_id=<?= $courseId ?>" <?= $isUnlocked ? '' : 'tabindex="-1" aria-disabled="true"' ?>>
            <i class="fa fa-tasks"></i> Assignments
          </a>
          <a class="btn-outline-brand d-flex align-items-center gap-2 <?= $isUnlocked ? '' : 'disabled' ?>"
             href="exams.php?course_id=<?= $courseId ?>" <?= $isUnlocked ? '' : 'tabindex="-1" aria-disabled="true"' ?>>
            <i class="fa fa-pen-alt"></i> Take Exam
          </a>
        </div>

        <hr style="border-color:var(--border)">
        <a class="btn-ghost w-100 text-center d-block" href="dashboard.php">← Back to Dashboard</a>
      </div>
    </div>

  </div>
</div>
</body>
</html>
