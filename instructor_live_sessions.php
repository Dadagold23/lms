<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/includes/live_session_tools.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();
$ins   = $_SESSION['instructor'];
$insId = (int)($ins['id'] ?? 0);

$ok  = $_SESSION['ins_session_ok']  ?? null;
$err = $_SESSION['ins_session_err'] ?? null;
unset($_SESSION['ins_session_ok'], $_SESSION['ins_session_err']);

/* ── Handle actions ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title    = trim($_POST['title'] ?? '');
        $courseId = (int)($_POST['course_id'] ?? 0);
        $desc     = trim($_POST['description'] ?? '');
        $anydesk  = trim($_POST['anydesk_id'] ?? '');
        $meetLink = trim($_POST['meeting_link'] ?? '');
        $schedAt  = trim($_POST['scheduled_at'] ?? '');
        $duration = (int)($_POST['duration_minutes'] ?? 60);

        if ($title === '' || $courseId <= 0 || $schedAt === '') {
            $_SESSION['ins_session_err'] = 'Title, course, and scheduled time are required.';
            redirect('instructor_live_sessions.php');
        }

        $pdo->prepare("INSERT INTO lms_live_sessions (course_id,instructor_id,title,description,anydesk_id,meeting_link,scheduled_at,duration_minutes,status,created_by) VALUES (?,?,?,?,?,?,?,?,'scheduled',?)")
            ->execute([$courseId, $insId, $title, $desc, $anydesk, $meetLink, $schedAt, $duration, $insId]);
        $sessionId = (int)$pdo->lastInsertId();
        $courseTitleStmt = $pdo->prepare("SELECT title FROM lms_courses WHERE id = ? LIMIT 1");
        $courseTitleStmt->execute([$courseId]);
        $courseTitle = (string)($courseTitleStmt->fetchColumn() ?: '');
        $notifiedCount = createLiveSessionStudentNotifications(
            $pdo,
            $courseId,
            $sessionId,
            $courseTitle,
            $title,
            $schedAt,
            $meetLink
        );
        $_SESSION['ins_live_notify_course_id'] = $courseId;
        $_SESSION['ins_live_notify_session_id'] = $sessionId;
        $_SESSION['ins_live_notify_count'] = $notifiedCount;
        $_SESSION['ins_session_ok'] = "Session scheduled successfully. {$notifiedCount} student notification" . ($notifiedCount === 1 ? '' : 's') . ' sent.';
        redirect('instructor_live_sessions.php');
    }

    if ($action === 'go_live') {
        $sid = (int)($_POST['session_id'] ?? 0);
        $pdo->exec("UPDATE lms_live_sessions SET status='live' WHERE id={$sid} AND instructor_id={$insId}");
        $_SESSION['ins_session_ok'] = 'Session is now LIVE — students can join.';
        redirect('instructor_live_sessions.php');
    }

    if ($action === 'complete') {
        $sid    = (int)($_POST['session_id'] ?? 0);
        $recUrl = trim($_POST['recording_url'] ?? '');
        $pdo->prepare("UPDATE lms_live_sessions SET status='completed', recording_url=? WHERE id=? AND instructor_id=?")
            ->execute([$recUrl, $sid, $insId]);
        $_SESSION['ins_session_ok'] = 'Session marked complete. Recording saved.';
        redirect('instructor_live_sessions.php');
    }

    if ($action === 'delete') {
        $sid = (int)($_POST['session_id'] ?? 0);
        $pdo->exec("DELETE FROM lms_live_sessions WHERE id={$sid} AND instructor_id={$insId}");
        $_SESSION['ins_session_ok'] = 'Session deleted.';
        redirect('instructor_live_sessions.php');
    }
}

/* ── Fetch instructor's assigned courses ── */
$courses = $pdo->query("
    SELECT c.id, c.title FROM lms_courses c
    LEFT JOIN lms_instructor_courses ic ON ic.course_id=c.id AND ic.instructor_id={$insId}
    WHERE c.is_active=1
    ORDER BY c.title
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Fetch sessions ── */
$sessions = $pdo->query("
    SELECT s.*, c.title AS course_title,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    WHERE s.instructor_id={$insId}
    ORDER BY s.scheduled_at DESC LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Live Sessions';
$seoDesc    = 'Schedule and manage live tutoring sessions at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav lms-nav-instructor">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">I</div>
      <span style="color:#fff">Instructor <span style="color:#c7d2fe">Portal</span></span>
    </div>
    <a href="instructor_dashboard.php" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.3)">
      <i class="fa fa-arrow-left me-1"></i>Dashboard
    </a>
  </div>
</nav>

<div class="container py-4" style="max-width:1000px">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h4 class="page-title mb-1"><i class="fa fa-video me-2" style="color:var(--danger)"></i>My Live Sessions</h4>
      <p class="text-muted mb-0" style="font-size:.88rem">Run LMS-native live classes with browser video, screen share, chat, attendance, and recording.</p>
    </div>
    <button class="btn-brand" style="background:var(--danger)" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fa fa-plus me-1"></i> Schedule Session
    </button>
  </div>

  <?php if ($ok): ?>
    <div class="lms-alert lms-alert-success mb-4"><i class="fa fa-check-circle me-1"></i><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="lms-alert lms-alert-danger mb-4"><i class="fa fa-exclamation-circle me-1"></i><?= e($err) ?></div>
  <?php endif; ?>
  <?php
  $notifyCourseId = (int)($_SESSION['ins_live_notify_course_id'] ?? 0);
  $notifySessionId = (int)($_SESSION['ins_live_notify_session_id'] ?? 0);
  $notifyCount = (int)($_SESSION['ins_live_notify_count'] ?? 0);
  unset($_SESSION['ins_live_notify_course_id'], $_SESSION['ins_live_notify_session_id'], $_SESSION['ins_live_notify_count']);
  ?>
  <?php if ($notifyCourseId > 0 && $notifySessionId > 0): ?>
    <div class="lms-alert lms-alert-success mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <span><?= $notifyCount ?> enrolled student<?= $notifyCount === 1 ? '' : 's' ?> received an LMS notification for this live session.</span>
      <a class="btn-brand" href="whatsapp_course_notify.php?course_id=<?= $notifyCourseId ?>&kind=live_session&session_id=<?= $notifySessionId ?>">
        <i class="fab fa-whatsapp me-1"></i>Open WhatsApp Links
      </a>
    </div>
  <?php endif; ?>

  <!-- Native guide -->
  <div class="lms-card mb-4" style="background:linear-gradient(135deg,#fff7ed,#fef3c7);border-color:var(--warning)">
    <div class="d-flex gap-3 align-items-start">
      <i class="fa fa-desktop fa-2x mt-1" style="color:var(--warning)"></i>
      <div>
        <div style="font-weight:700;margin-bottom:.25rem">How Native LMS Live Classes Work</div>
        <ol style="font-size:.88rem;color:var(--dark);margin:0;padding-left:1.25rem">
          <li>Schedule the session inside the LMS and set the time, duration, and topic.</li>
          <li>When ready, click <strong>Go Live</strong> and open the classroom.</li>
          <li>Use browser camera/mic for lecture and <strong>Share Screen</strong> for lab demos.</li>
          <li>Students join the LMS classroom directly, where attendance and chat are tracked.</li>
          <li>Use the built-in recording button in the classroom and end the session when done.</li>
        </ol>
      </div>
    </div>
  </div>

  <?php if (empty($sessions)): ?>
    <div class="lms-card text-center py-5">
      <i class="fa fa-calendar-plus fa-3x mb-3" style="color:var(--muted)"></i>
      <h5>No Sessions Yet</h5>
      <p class="text-muted">Schedule your first live session to start teaching.</p>
      <button class="btn-brand mt-2" style="background:var(--danger)" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa fa-plus me-1"></i> Schedule First Session
      </button>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($sessions as $s):
        $isLive  = $s['status'] === 'live';
        $isDone  = $s['status'] === 'completed';
        $isSched = $s['status'] === 'scheduled';
      ?>
        <div class="col-md-6">
          <div class="lms-card h-100 d-flex flex-column" style="<?= $isLive ? 'border-color:var(--danger);border-width:2px' : '' ?>">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div style="font-size:.75rem;color:var(--brand);font-weight:600;text-transform:uppercase"><?= e($s['course_title']) ?></div>
              <?php if ($isLive): ?>
                <span style="color:var(--danger);font-weight:700;font-size:.8rem">● LIVE</span>
              <?php elseif ($isSched): ?>
                <span class="badge-info">Scheduled</span>
              <?php elseif ($isDone): ?>
                <span class="badge-success">Completed</span>
              <?php else: ?>
                <span class="badge-muted">Cancelled</span>
              <?php endif; ?>
            </div>

            <h6 style="font-weight:700;margin-bottom:.5rem"><?= e($s['title']) ?></h6>

            <div class="d-flex flex-column gap-1 mb-3" style="font-size:.82rem;color:var(--muted)">
              <span><i class="fa fa-calendar me-2"></i><?= e(date('D d M Y, g:ia', strtotime($s['scheduled_at']))) ?></span>
              <span><i class="fa fa-clock me-2"></i><?= (int)$s['duration_minutes'] ?> minutes</span>
              <span><i class="fa fa-users me-2"></i><?= (int)$s['attendees'] ?> student<?= (int)$s['attendees'] !== 1 ? 's' : '' ?> joined</span>
              <span><i class="fa fa-tower-broadcast me-2"></i><?= e(liveSessionIsTeams($s['meeting_link'] ?? '') ? 'Microsoft Teams channel' : 'Native LMS classroom') ?></span>
            </div>

            <div class="mt-auto d-flex gap-2 flex-wrap">
              <?php if ($isSched): ?>
                <form method="post" class="d-inline">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="go_live">
                  <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Start this session LIVE now?')">
                    <i class="fa fa-broadcast-tower me-1"></i>Go Live
                  </button>
                </form>
              <?php elseif ($isLive): ?>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal"
                        data-id="<?= (int)$s['id'] ?>" data-rec="<?= e($s['recording_url'] ?? '') ?>">
                  <i class="fa fa-check me-1"></i>End & Save Recording
                </button>
                <a href="live_session_room.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-door-open me-1"></i>Open Classroom
                </a>
              <?php else: ?>
                <a href="live_session_room.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-door-open me-1"></i>Open Classroom
                </a>
              <?php endif; ?>

              <?php if ($isDone && !empty($s['recording_url'])): ?>
                <a href="<?= e($s['recording_url']) ?>" target="_blank" class="btn-outline-brand" style="font-size:.82rem">
                  <i class="fa fa-play-circle me-1"></i>View Recording
                </a>
              <?php endif; ?>

              <?php if (!$isLive): ?>
                <form method="post" class="d-inline">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this session?')">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-video me-2"></i>Schedule Live Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Session Title <span style="color:var(--danger)">*</span></label>
              <input name="title" class="form-control" required placeholder="e.g. Week 3 Live Tutoring — Web Design Fundamentals">
            </div>
            <div class="col-md-6">
              <label class="form-label">Course <span style="color:var(--danger)">*</span></label>
              <select name="course_id" class="form-select" required>
                <option value="">— Select Course —</option>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Scheduled Date & Time <span style="color:var(--danger)">*</span></label>
              <input type="datetime-local" name="scheduled_at" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Duration (minutes)</label>
              <input type="number" name="duration_minutes" class="form-control" value="60" min="15" max="480">
            </div>
            <div class="col-md-4">
              <label class="form-label">Remote Support ID (optional)</label>
              <input name="anydesk_id" class="form-control" placeholder="Optional AnyDesk ID">
            </div>
            <div class="col-md-4">
              <label class="form-label">Microsoft Teams Channel Link</label>
              <input type="url" name="meeting_link" class="form-control" placeholder="https://teams.microsoft.com/l/meetup-join/...">
              <div class="form-text">Paste a Teams meeting link to run this class through Teams from the LMS room.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Session Description</label>
              <textarea name="description" class="form-control" rows="2" placeholder="What topics will be covered?"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand" style="background:var(--danger)">
            <i class="fa fa-calendar-plus me-1"></i>Schedule Session
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="complete">
        <input type="hidden" name="session_id" id="completeId">
        <div class="modal-header">
          <h5 class="modal-title">End Session & Save Recording</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Recording URL</label>
          <input type="url" name="recording_url" id="completeRec" class="form-control" placeholder="https://youtube.com/watch?v=...">
          <div class="form-text">Upload your session recording to YouTube or Google Drive, then paste the link here. Students will see a Watch Recording button.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand"><i class="fa fa-check me-1"></i>Mark Complete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('completeModal').addEventListener('show.bs.modal', function(e) {
  document.getElementById('completeId').value = e.relatedTarget.dataset.id;
  document.getElementById('completeRec').value = e.relatedTarget.dataset.rec || '';
});
</script>
<style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}</style>
</body>
</html>
