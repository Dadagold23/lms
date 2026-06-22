<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/live_session_tools.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ── Enrolled course IDs ── */
$enrolled = $pdo->prepare("SELECT course_id FROM lms_enrollments WHERE student_id=?");
$enrolled->execute([$studentId]);
$enrolledIds = array_column($enrolled->fetchAll(PDO::FETCH_ASSOC), 'course_id');

/* ── Handle join (redirect into LMS session room) ── */
if (isset($_GET['join'])) {
    $sessionId = (int)$_GET['join'];
    redirect('live_session_room.php?id=' . $sessionId);
}

/* ── Fetch sessions for enrolled courses ── */
$sessions = [];
if (!empty($enrolledIds)) {
    $ids = implode(',', $enrolledIds);
    $sessions = $pdo->query("
        SELECT s.*, c.title AS course_title,
               i.full_name AS instructor_name,
               (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees,
               (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id AND student_id={$studentId}) AS joined
        FROM lms_live_sessions s
        JOIN lms_courses c ON c.id = s.course_id
        LEFT JOIN lms_instructors i ON i.id = s.instructor_id
        WHERE s.course_id IN ({$ids})
          AND s.status IN ('scheduled','live','completed')
        ORDER BY s.scheduled_at DESC
        LIMIT 50
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/* ── Week attendance count ── */
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd   = date('Y-m-d', strtotime('sunday this week'));
$weekJoined = (int)$pdo->query("
    SELECT COUNT(*) FROM lms_session_attendance a
    JOIN lms_live_sessions s ON s.id=a.session_id
    WHERE a.student_id={$studentId}
      AND DATE(s.scheduled_at) BETWEEN '{$weekStart}' AND '{$weekEnd}'
")->fetchColumn();

$error = $_SESSION['live_error'] ?? null;
unset($_SESSION['live_error']);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Live Sessions';
$seoDesc    = 'Join live tutoring sessions with your instructor at Grafix@Mirror LMS — Mirror Age Concepts.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

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
      <a href="ai_tutor.php" class="btn-brand" style="font-size:.82rem;padding:.4rem .9rem">
        <i class="fa fa-robot me-1"></i> AI Tutor
      </a>
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:960px">

  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h4 class="page-title mb-1"><i class="fa fa-video me-2"></i>Live Sessions</h4>
      <p class="text-muted mb-0" style="font-size:.88rem">
        Native LMS live classes with browser video, screen sharing, attendance, recording, and chat.
      </p>
    </div>
    <!-- Weekly usage indicator -->
    <div class="lms-card py-2 px-3 text-center" style="min-width:160px">
      <div style="font-size:.75rem;color:var(--muted)">This Week</div>
      <div style="font-size:1.5rem;font-weight:800;color:<?= $weekJoined >= 2 ? 'var(--danger)' : 'var(--brand)' ?>">
        <?= $weekJoined ?>/2
      </div>
      <div style="font-size:.75rem;color:var(--muted)">sessions joined</div>
      <div class="progress mt-1" style="height:5px">
        <div class="progress-bar" style="width:<?= min(100, $weekJoined * 50) ?>%;background:<?= $weekJoined >= 2 ? 'var(--danger)' : 'var(--brand)' ?>"></div>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="lms-alert lms-alert-danger mb-4"><i class="fa fa-exclamation-circle me-1"></i><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (empty($sessions)): ?>
    <div class="lms-card text-center py-5">
      <i class="fa fa-calendar-times fa-3x mb-3" style="color:var(--muted)"></i>
      <h5>No Sessions Scheduled Yet</h5>
      <p class="text-muted">Your instructor will schedule live sessions here. Check back soon.</p>
      <a href="ai_tutor.php" class="btn-brand mt-2">
        <i class="fa fa-robot me-1"></i> Chat with AI Tutor Instead
      </a>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($sessions as $s):
        $isLive      = $s['status'] === 'live';
        $isScheduled = $s['status'] === 'scheduled';
        $isDone      = $s['status'] === 'completed';
        $hasJoined   = (int)$s['joined'] > 0;
        $isPast      = strtotime($s['scheduled_at']) < time();
        $canJoin     = ($isLive || ($isScheduled && !$isPast)) && !$hasJoined && $weekJoined < 2;
        $dt          = date('D d M Y, g:ia', strtotime($s['scheduled_at']));
      ?>
        <div class="col-md-6">
          <div class="lms-card h-100 d-flex flex-column" style="<?= $isLive ? 'border-color:var(--danger);border-width:2px' : '' ?>">

            <!-- Status badge -->
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div style="font-size:.75rem;color:var(--brand);font-weight:600;text-transform:uppercase">
                <?= e($s['course_title']) ?>
              </div>
              <?php if ($isLive): ?>
                <span class="badge-danger" style="animation:pulse 1.5s infinite">
                  <i class="fa fa-circle me-1" style="font-size:.6rem"></i>LIVE
                </span>
              <?php elseif ($isScheduled && !$isPast): ?>
                <span class="badge-info">Upcoming</span>
              <?php elseif ($isDone): ?>
                <span class="badge-success">Completed</span>
              <?php else: ?>
                <span class="badge-muted">Ended</span>
              <?php endif; ?>
            </div>

            <h6 style="font-weight:700;margin-bottom:.5rem"><?= e($s['title']) ?></h6>

            <?php if (!empty($s['description'])): ?>
              <p class="text-muted mb-2" style="font-size:.85rem"><?= e(mb_substr($s['description'], 0, 120)) ?></p>
            <?php endif; ?>

            <div class="d-flex flex-column gap-1 mb-3" style="font-size:.82rem;color:var(--muted)">
              <span><i class="fa fa-calendar me-2"></i><?= e($dt) ?></span>
              <span><i class="fa fa-clock me-2"></i><?= (int)$s['duration_minutes'] ?> minutes</span>
              <?php if (!empty($s['instructor_name'])): ?>
                <span><i class="fa fa-chalkboard-teacher me-2"></i><?= e($s['instructor_name']) ?></span>
              <?php endif; ?>
              <span><i class="fa fa-tower-broadcast me-2"></i><?= e(liveSessionIsTeams($s['meeting_link'] ?? '') ? 'Microsoft Teams channel' : 'Native LMS classroom') ?></span>
            </div>

            <div class="mt-auto d-flex gap-2 flex-wrap">
              <?php if ($canJoin): ?>
                <a href="live_session.php?join=<?= (int)$s['id'] ?>"
                   class="btn-brand"
                   onclick="return confirm('Join this session? This counts as 1 of your 2 weekly sessions.')">
                  <i class="fa fa-sign-in-alt me-1"></i>
                  <?= $isLive ? 'Enter Classroom' : 'Open Classroom' ?>
                </a>
              <?php elseif ($hasJoined): ?>
                <span class="badge-success"><i class="fa fa-check me-1"></i>Attendance Marked</span>
                <a href="live_session_room.php?id=<?= (int)$s['id'] ?>" class="btn-ghost" style="font-size:.82rem">
                  <i class="fa fa-door-open me-1"></i>Open Classroom
                </a>
              <?php elseif ($weekJoined >= 2 && !$hasJoined && !$isDone): ?>
                <span class="badge-warning">Weekly limit reached</span>
              <?php endif; ?>

              <?php if ($isDone && !empty($s['recording_url'])): ?>
                <a href="<?= e($s['recording_url']) ?>" target="_blank" class="btn-outline-brand" style="font-size:.82rem">
                  <i class="fa fa-play-circle me-1"></i>Watch Recording
                </a>
              <?php endif; ?>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- AI Tutor CTA -->
  <div class="lms-card mt-4" style="background:linear-gradient(135deg,var(--brand-light),#e0f2fe);border-color:var(--brand)">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div style="font-weight:700;font-size:1rem"><i class="fa fa-robot me-2" style="color:var(--brand)"></i>AI Tutor Available 24/7</div>
        <div class="text-muted" style="font-size:.88rem">Get instant answers, explanations, and guidance on any topic — anytime, no waiting.</div>
      </div>
      <a href="ai_tutor.php" class="btn-brand">
        <i class="fa fa-comments me-1"></i> Start AI Session
      </a>
    </div>
  </div>

</div>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>
</body>
</html>
