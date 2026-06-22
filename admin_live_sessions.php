<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/includes/live_session_tools.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$adminId = (int)($_SESSION['admin']['id'] ?? $_SESSION['user']['id'] ?? 0);

$ok  = $_SESSION['session_ok']  ?? null;
$err = $_SESSION['session_err'] ?? null;
unset($_SESSION['session_ok'], $_SESSION['session_err']);

/* ── Handle create/edit ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'edit') {
        $title       = trim($_POST['title'] ?? '');
        $courseId    = (int)($_POST['course_id'] ?? 0);
        $instrId     = (int)($_POST['instructor_id'] ?? 0) ?: null;
        $desc        = trim($_POST['description'] ?? '');
        $anydesk     = trim($_POST['anydesk_id'] ?? '');
        $meetLink    = trim($_POST['meeting_link'] ?? '');
        $scheduledAt = trim($_POST['scheduled_at'] ?? '');
        $duration    = (int)($_POST['duration_minutes'] ?? 60);
        $recUrl      = trim($_POST['recording_url'] ?? '');
        $status      = $_POST['status'] ?? 'scheduled';
        $maxStudents = (int)($_POST['max_students'] ?? 0) ?: null;

        if ($title === '' || $courseId <= 0 || $scheduledAt === '') {
            $_SESSION['session_err'] = 'Title, course, and scheduled date/time are required.';
            redirect('admin_live_sessions.php');
        }

        if ($action === 'create') {
            $pdo->prepare("INSERT INTO lms_live_sessions (course_id,instructor_id,title,description,anydesk_id,meeting_link,scheduled_at,duration_minutes,recording_url,status,max_students,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$courseId,$instrId,$title,$desc,$anydesk,$meetLink,$scheduledAt,$duration,$recUrl,$status,$maxStudents,$adminId]);
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
                $scheduledAt,
                $meetLink
            );
            $_SESSION['admin_live_notify_course_id'] = $courseId;
            $_SESSION['admin_live_notify_session_id'] = $sessionId;
            $_SESSION['admin_live_notify_count'] = $notifiedCount;
            $_SESSION['session_ok'] = "Session created successfully. {$notifiedCount} student notification" . ($notifiedCount === 1 ? '' : 's') . ' sent.';
        } else {
            $sid = (int)($_POST['session_id'] ?? 0);
            $pdo->prepare("UPDATE lms_live_sessions SET course_id=?,instructor_id=?,title=?,description=?,anydesk_id=?,meeting_link=?,scheduled_at=?,duration_minutes=?,recording_url=?,status=?,max_students=? WHERE id=?")
                ->execute([$courseId,$instrId,$title,$desc,$anydesk,$meetLink,$scheduledAt,$duration,$recUrl,$status,$maxStudents,$sid]);
            $_SESSION['session_ok'] = 'Session updated.';
        }
        redirect('admin_live_sessions.php');
    }

    if ($action === 'delete') {
        $sid = (int)($_POST['session_id'] ?? 0);
        $pdo->exec("DELETE FROM lms_live_sessions WHERE id={$sid}");
        $_SESSION['session_ok'] = 'Session deleted.';
        redirect('admin_live_sessions.php');
    }

    if ($action === 'go_live') {
        $sid = (int)($_POST['session_id'] ?? 0);
        $pdo->exec("UPDATE lms_live_sessions SET status='live' WHERE id={$sid}");
        $_SESSION['session_ok'] = 'Session is now LIVE.';
        redirect('admin_live_sessions.php');
    }

    if ($action === 'complete') {
        $sid    = (int)($_POST['session_id'] ?? 0);
        $recUrl = trim($_POST['recording_url'] ?? '');
        $pdo->prepare("UPDATE lms_live_sessions SET status='completed', recording_url=? WHERE id=?")
            ->execute([$recUrl, $sid]);
        $_SESSION['session_ok'] = 'Session marked as completed.';
        redirect('admin_live_sessions.php');
    }

    /* ── Admin override actions ── */
    if ($action === 'force_live') {
        // Admin forces a scheduled session live (instructor is late/absent)
        $sid    = (int)($_POST['session_id'] ?? 0);
        $note   = trim($_POST['admin_note'] ?? 'Forced live by admin');
        $pdo->exec("UPDATE lms_live_sessions SET status='live' WHERE id={$sid}");
        // Log the override
        try {
            $pdo->prepare("INSERT INTO lms_activity_logs (student_id, action, description, created_at) VALUES (?,?,?,NOW())")
                ->execute([0, 'admin_force_live', "Admin forced session #{$sid} live. Note: {$note}"]);
        } catch (Throwable $e) {}
        $_SESSION['session_ok'] = "Session #{$sid} forced LIVE. Instructor notified.";
        redirect('admin_live_sessions.php');
    }

    if ($action === 'force_cancel') {
        // Admin cancels a session (instructor no-show)
        $sid  = (int)($_POST['session_id'] ?? 0);
        $note = trim($_POST['admin_note'] ?? 'Cancelled by admin');
        $pdo->exec("UPDATE lms_live_sessions SET status='cancelled' WHERE id={$sid}");
        try {
            $pdo->prepare("INSERT INTO lms_activity_logs (student_id, action, description, created_at) VALUES (?,?,?,NOW())")
                ->execute([0, 'admin_cancel_session', "Admin cancelled session #{$sid}. Note: {$note}"]);
        } catch (Throwable $e) {}
        $_SESSION['session_ok'] = "Session #{$sid} cancelled.";
        redirect('admin_live_sessions.php');
    }

    if ($action === 'reassign') {
        // Admin reassigns session to a different instructor
        $sid     = (int)($_POST['session_id'] ?? 0);
        $newInst = (int)($_POST['new_instructor_id'] ?? 0) ?: null;
        $pdo->prepare("UPDATE lms_live_sessions SET instructor_id=? WHERE id=?")
            ->execute([$newInst, $sid]);
        $_SESSION['session_ok'] = 'Session reassigned to new instructor.';
        redirect('admin_live_sessions.php');
    }
}

/* ── Fetch data ── */
$sessions = $pdo->query("
    SELECT s.*, c.title AS course_title, i.full_name AS instructor_name,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    LEFT JOIN lms_instructors i ON i.id=s.instructor_id
    ORDER BY s.scheduled_at DESC LIMIT 100
")->fetchAll(PDO::FETCH_ASSOC);

$courses     = $pdo->query("SELECT id,title FROM lms_courses WHERE is_active=1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$instructors = $pdo->query("SELECT id,full_name FROM lms_instructors WHERE status='active' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

/* ── Monitor: sessions that should be live but aren't started yet ── */
$overdueThreshold = date('Y-m-d H:i:s', strtotime('-15 minutes'));
$overdueSessions = $pdo->query("
    SELECT s.*, c.title AS course_title, i.full_name AS instructor_name,
           TIMESTAMPDIFF(MINUTE, s.scheduled_at, NOW()) AS minutes_overdue
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    LEFT JOIN lms_instructors i ON i.id=s.instructor_id
    WHERE s.status = 'scheduled'
      AND s.scheduled_at <= '{$overdueThreshold}'
    ORDER BY s.scheduled_at ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Currently live sessions ── */
$liveSessions = $pdo->query("
    SELECT s.*, c.title AS course_title, i.full_name AS instructor_name,
           TIMESTAMPDIFF(MINUTE, s.scheduled_at, NOW()) AS minutes_running,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    LEFT JOIN lms_instructors i ON i.id=s.instructor_id
    WHERE s.status = 'live'
    ORDER BY s.scheduled_at ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Today's sessions ── */
$todaySessions = $pdo->query("
    SELECT s.*, c.title AS course_title, i.full_name AS instructor_name,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    LEFT JOIN lms_instructors i ON i.id=s.instructor_id
    WHERE DATE(s.scheduled_at) = CURDATE()
    ORDER BY s.scheduled_at ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Live Sessions Monitor';
$seoDesc    = 'Monitor and manage all live tutoring sessions at Grafix@Mirror LMS admin panel.';
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

<nav class="lms-nav lms-nav-admin">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="admin_dashboard.php" class="brand text-decoration-none" style="color:#fff">
      <i class="fa fa-shield-alt me-2"></i>Admin Panel
    </a>
    <a href="admin_dashboard.php" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.3)">
      <i class="fa fa-arrow-left me-1"></i>Dashboard
    </a>
  </div>
</nav>

<div class="container py-4" style="max-width:1100px">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <h4 class="page-title mb-0"><i class="fa fa-video me-2"></i>Live Sessions Management</h4>
    <button class="btn-brand" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fa fa-plus me-1"></i>Schedule Session
    </button>
  </div>

  <?php if ($ok): ?>
    <div class="lms-alert lms-alert-success mb-4"><i class="fa fa-check-circle me-1"></i><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="lms-alert lms-alert-danger mb-4"><i class="fa fa-exclamation-circle me-1"></i><?= e($err) ?></div>
  <?php endif; ?>
  <?php
  $notifyCourseId = (int)($_SESSION['admin_live_notify_course_id'] ?? 0);
  $notifySessionId = (int)($_SESSION['admin_live_notify_session_id'] ?? 0);
  $notifyCount = (int)($_SESSION['admin_live_notify_count'] ?? 0);
  unset($_SESSION['admin_live_notify_course_id'], $_SESSION['admin_live_notify_session_id'], $_SESSION['admin_live_notify_count']);
  ?>
  <?php if ($notifyCourseId > 0 && $notifySessionId > 0): ?>
    <div class="lms-alert lms-alert-success mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <span><?= $notifyCount ?> enrolled student<?= $notifyCount === 1 ? '' : 's' ?> received an LMS notification for this live session.</span>
      <a class="btn-brand" href="whatsapp_course_notify.php?course_id=<?= $notifyCourseId ?>&kind=live_session&session_id=<?= $notifySessionId ?>">
        <i class="fab fa-whatsapp me-1"></i>Open WhatsApp Links
      </a>
    </div>
  <?php endif; ?>

  <!-- ═══ LIVE MONITOR PANEL ═══ -->

  <!-- Overdue alert: sessions that should have started but haven't -->
  <?php if (!empty($overdueSessions)): ?>
  <div class="lms-card mb-4" style="border-color:var(--danger);border-width:2px;background:#fff5f5">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="fa fa-exclamation-triangle fa-lg" style="color:var(--danger)"></i>
      <div style="font-weight:700;font-size:1rem;color:var(--danger)">
        INSTRUCTOR ALERT — <?= count($overdueSessions) ?> Session<?= count($overdueSessions) > 1 ? 's' : '' ?> Overdue
      </div>
      <span style="font-size:.75rem;color:var(--muted);margin-left:auto">Scheduled but not started (15+ min late)</span>
    </div>
    <?php foreach ($overdueSessions as $s): ?>
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 mb-2 rounded" style="background:#fee2e2;border:1px solid #fecaca">
        <div>
          <div style="font-weight:700"><?= e($s['title']) ?></div>
          <div style="font-size:.82rem;color:var(--muted)">
            <i class="fa fa-book me-1"></i><?= e($s['course_title']) ?>
            &nbsp;·&nbsp;
            <i class="fa fa-chalkboard-teacher me-1"></i><?= e($s['instructor_name'] ?? 'No instructor assigned') ?>
            &nbsp;·&nbsp;
            <i class="fa fa-clock me-1"></i>Scheduled: <?= e(date('H:i', strtotime($s['scheduled_at']))) ?>
            &nbsp;·&nbsp;
            <strong style="color:var(--danger)"><?= (int)$s['minutes_overdue'] ?> min overdue</strong>
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <!-- Force Live -->
          <form method="post" class="d-inline">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="force_live">
            <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
            <input type="hidden" name="admin_note" value="Admin forced live — instructor was <?= (int)$s['minutes_overdue'] ?> min late">
            <button class="btn btn-sm btn-danger" onclick="return confirm('Force this session LIVE? Students will be notified.')">
              <i class="fa fa-broadcast-tower me-1"></i>Force Live
            </button>
          </form>
          <!-- Reassign -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#reassignModal"
                  data-id="<?= (int)$s['id'] ?>" data-title="<?= e($s['title']) ?>">
            <i class="fa fa-exchange-alt me-1"></i>Reassign
          </button>
          <!-- Cancel -->
          <form method="post" class="d-inline">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="force_cancel">
            <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
            <input type="hidden" name="admin_note" value="Cancelled — instructor no-show after <?= (int)$s['minutes_overdue'] ?> min">
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this session? Students will see it as cancelled.')">
              <i class="fa fa-times me-1"></i>Cancel
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Currently LIVE sessions monitor -->
  <?php if (!empty($liveSessions)): ?>
  <div class="lms-card mb-4" style="border-color:var(--success);border-width:2px">
    <div class="d-flex align-items-center gap-2 mb-3">
      <span style="width:10px;height:10px;border-radius:50%;background:var(--danger);display:inline-block;animation:pulse 1.5s infinite"></span>
      <div style="font-weight:700;font-size:1rem"><?= count($liveSessions) ?> Session<?= count($liveSessions) > 1 ? 's' : '' ?> Currently LIVE</div>
    </div>
    <div class="row g-3">
      <?php foreach ($liveSessions as $s): ?>
        <div class="col-md-6">
          <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <div style="font-weight:700"><?= e($s['title']) ?></div>
              <span style="font-size:.75rem;color:var(--success);font-weight:700">
                <?= (int)$s['minutes_running'] ?> min running
              </span>
            </div>
            <div style="font-size:.82rem;color:var(--muted)" class="mb-2">
              <i class="fa fa-book me-1"></i><?= e($s['course_title']) ?>
              &nbsp;·&nbsp;
              <i class="fa fa-chalkboard-teacher me-1"></i><?= e($s['instructor_name'] ?? 'Admin') ?>
              &nbsp;·&nbsp;
              <i class="fa fa-users me-1"></i><?= (int)$s['attendees'] ?> joined
            </div>
            <?php if (!empty($s['anydesk_id'])): ?>
              <div style="font-size:.82rem;margin-bottom:.5rem">
                <i class="fa fa-desktop me-1" style="color:var(--brand)"></i>
                AnyDesk ID: <strong><?= e($s['anydesk_id']) ?></strong>
                <a href="anydesk:<?= e($s['anydesk_id']) ?>" class="btn btn-sm btn-outline-primary ms-2" style="font-size:.75rem;padding:.15rem .5rem">
                  <i class="fa fa-eye me-1"></i>Monitor
                </a>
              </div>
            <?php endif; ?>
            <?php if (!empty($s['meeting_link'])): ?>
              <a href="live_session_room.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-success" style="font-size:.78rem">
                <i class="fa fa-door-open me-1"></i>Monitor in LMS
              </a>
            <?php endif; ?>
            <!-- Admin end session -->
            <form method="post" class="d-inline ms-1">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="complete">
              <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
              <input type="hidden" name="recording_url" value="">
              <button class="btn btn-sm btn-outline-secondary" style="font-size:.78rem"
                      onclick="return confirm('End this live session?')">
                <i class="fa fa-stop me-1"></i>End Session
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Today's schedule -->
  <?php if (!empty($todaySessions)): ?>
  <div class="lms-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div style="font-weight:700"><i class="fa fa-calendar-day me-2" style="color:var(--brand)"></i>Today's Schedule</div>
      <span style="font-size:.8rem;color:var(--muted)"><?= date('l, d F Y') ?></span>
    </div>
    <div class="d-flex flex-column gap-2">
      <?php foreach ($todaySessions as $s):
        $statusColor = match($s['status']) {
            'live'      => 'var(--danger)',
            'completed' => 'var(--success)',
            'cancelled' => 'var(--muted)',
            default     => 'var(--brand)',
        };
        $isPast = strtotime($s['scheduled_at']) < time() && $s['status'] === 'scheduled';
      ?>
        <div class="d-flex align-items-center gap-3 p-2 rounded <?= $isPast ? '' : '' ?>"
             style="background:<?= $isPast ? '#fff7ed' : 'var(--surface)' ?>;border:1px solid <?= $isPast ? '#fed7aa' : 'var(--border)' ?>">
          <div style="width:60px;text-align:center;font-weight:700;font-size:.9rem;color:<?= $statusColor ?>">
            <?= date('H:i', strtotime($s['scheduled_at'])) ?>
          </div>
          <div class="flex-grow-1">
            <div style="font-weight:600;font-size:.9rem"><?= e($s['title']) ?></div>
            <div style="font-size:.78rem;color:var(--muted)">
              <?= e($s['course_title']) ?> &nbsp;·&nbsp;
              <?= e($s['instructor_name'] ?? '<span style="color:var(--danger)">No instructor</span>') ?>
              &nbsp;·&nbsp; <?= (int)$s['attendees'] ?> joined
            </div>
          </div>
          <div>
            <?php if ($s['status'] === 'live'): ?>
              <span style="color:var(--danger);font-weight:700;font-size:.8rem">● LIVE</span>
            <?php elseif ($s['status'] === 'completed'): ?>
              <span class="badge-success">Done</span>
            <?php elseif ($s['status'] === 'cancelled'): ?>
              <span class="badge-muted">Cancelled</span>
            <?php elseif ($isPast): ?>
              <span class="badge-warning">Not started</span>
            <?php else: ?>
              <span class="badge-info">Upcoming</span>
            <?php endif; ?>
          </div>
          <?php if ($isPast && $s['status'] === 'scheduled'): ?>
            <form method="post" class="d-inline">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="force_live">
              <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
              <input type="hidden" name="admin_note" value="Admin override">
              <button class="btn btn-sm btn-danger" style="font-size:.75rem" onclick="return confirm('Force live?')">
                Force Live
              </button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (empty($sessions)): ?>
    <div class="lms-alert lms-alert-info"><i class="fa fa-info-circle me-1"></i>No sessions scheduled yet.</div>
  <?php else: ?>
    <div class="lms-card p-0" style="overflow:hidden">
      <table class="lms-table">
        <thead>
          <tr>
            <th>Session</th>
            <th>Course</th>
            <th>Instructor</th>
            <th>Scheduled</th>
            <th>Status</th>
            <th>Attendees</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sessions as $s):
            $statusColor = match($s['status']) {
                'live'      => 'var(--danger)',
                'scheduled' => 'var(--brand)',
                'completed' => 'var(--success)',
                default     => 'var(--muted)',
            };
          ?>
            <tr>
              <td>
                <div style="font-weight:600"><?= e($s['title']) ?></div>
                <?php if (!empty($s['anydesk_id'])): ?>
                  <div style="font-size:.75rem;color:var(--muted)"><i class="fa fa-desktop me-1"></i>AnyDesk: <?= e($s['anydesk_id']) ?></div>
                <?php endif; ?>
                <?php if (!empty($s['meeting_link'])): ?>
                  <div style="font-size:.75rem;color:var(--muted)"><i class="fa fa-users-viewfinder me-1"></i><?= e(liveSessionProviderLabel($s['meeting_link'])) ?></div>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem"><?= e($s['course_title']) ?></td>
              <td style="font-size:.85rem"><?= e($s['instructor_name'] ?? '—') ?></td>
              <td style="font-size:.82rem"><?= e(date('d M Y H:i', strtotime($s['scheduled_at']))) ?></td>
              <td>
                <span style="font-size:.78rem;font-weight:600;color:<?= $statusColor ?>;text-transform:uppercase">
                  <?= $s['status'] === 'live' ? '● LIVE' : ucfirst($s['status']) ?>
                </span>
              </td>
              <td style="font-size:.85rem"><?= (int)$s['attendees'] ?></td>
              <td>
                <div class="d-flex gap-1 flex-wrap">
                  <?php if ($s['status'] === 'scheduled'): ?>
                    <form method="post" class="d-inline">
                      <?= csrfField() ?>
                      <input type="hidden" name="action" value="go_live">
                      <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
                      <button class="btn btn-sm btn-danger" onclick="return confirm('Go LIVE now?')">
                        <i class="fa fa-broadcast-tower"></i> Go Live
                      </button>
                    </form>
                  <?php elseif ($s['status'] === 'live'): ?>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal"
                            data-id="<?= (int)$s['id'] ?>" data-rec="<?= e($s['recording_url'] ?? '') ?>">
                      <i class="fa fa-check"></i> Complete
                    </button>
                  <?php endif; ?>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                          data-session='<?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>'>
                    <i class="fa fa-edit"></i>
                  </button>
                  <form method="post" class="d-inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="session_id" value="<?= (int)$s['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this session?')">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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
          <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Schedule Live Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= sessionFormFields($courses, $instructors) ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand"><i class="fa fa-save me-1"></i>Schedule</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="session_id" id="editSessionId">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= sessionFormFields($courses, $instructors, 'edit_') ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand"><i class="fa fa-save me-1"></i>Update</button>
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
        <input type="hidden" name="session_id" id="completeSessionId">
        <div class="modal-header">
          <h5 class="modal-title">Mark Session Complete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Recording URL (YouTube, Google Drive, etc.)</label>
          <input type="url" name="recording_url" id="completeRecUrl" class="form-control" placeholder="https://youtube.com/watch?v=...">
          <div class="form-text">Students will see a Watch Recording button after the session.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand"><i class="fa fa-check me-1"></i>Mark Complete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="reassign">
        <input type="hidden" name="session_id" id="reassignId">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-exchange-alt me-2"></i>Reassign Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted" style="font-size:.88rem">
            Reassigning: <strong id="reassignTitle"></strong>
          </p>
          <label class="form-label">Assign to Instructor</label>
          <select name="new_instructor_id" class="form-select" required>
            <option value="">— Select Instructor —</option>
            <?php foreach ($instructors as $i): ?>
              <option value="<?= (int)$i['id'] ?>"><?= e($i['full_name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text mt-2">The new instructor will see this session in their dashboard.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-brand"><i class="fa fa-check me-1"></i>Reassign</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function(e) {
  const s = JSON.parse(e.relatedTarget.dataset.session);
  this.querySelector('#editSessionId').value = s.id;
  ['title','description','anydesk_id','meeting_link','duration_minutes','recording_url','max_students'].forEach(f => {
    const el = this.querySelector('[name="edit_'+f+'"]') || this.querySelector('[name="'+f+'"]');
    if (el) el.value = s[f] || '';
  });
  const dt = s.scheduled_at ? s.scheduled_at.replace(' ','T').substring(0,16) : '';
  const dtEl = this.querySelector('[name="edit_scheduled_at"]');
  if (dtEl) dtEl.value = dt;
  const cEl = this.querySelector('[name="edit_course_id"]');
  if (cEl) cEl.value = s.course_id;
  const iEl = this.querySelector('[name="edit_instructor_id"]');
  if (iEl) iEl.value = s.instructor_id || '';
  const stEl = this.querySelector('[name="edit_status"]');
  if (stEl) stEl.value = s.status;
});
document.getElementById('completeModal').addEventListener('show.bs.modal', function(e) {
  document.getElementById('completeSessionId').value = e.relatedTarget.dataset.id;
  document.getElementById('completeRecUrl').value = e.relatedTarget.dataset.rec || '';
});
document.getElementById('reassignModal').addEventListener('show.bs.modal', function(e) {
  document.getElementById('reassignId').value = e.relatedTarget.dataset.id;
  document.getElementById('reassignTitle').textContent = e.relatedTarget.dataset.title;
});

// Auto-refresh monitor every 60 seconds
setTimeout(() => location.reload(), 60000);
</script>
<style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}</style>
</body>
</html>
<?php
function sessionFormFields(array $courses, array $instructors, string $prefix = ''): string {
    $html = '<div class="row g-3">';
    $html .= '<div class="col-12"><label class="form-label">Session Title *</label><input name="'.$prefix.'title" class="form-control" required placeholder="e.g. Week 3 Live Tutoring — Web Design"></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Course *</label><select name="'.$prefix.'course_id" class="form-select" required><option value="">— Select Course —</option>';
    foreach ($courses as $c) $html .= '<option value="'.(int)$c['id'].'">'.htmlspecialchars($c['title'],ENT_QUOTES).'</option>';
    $html .= '</select></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Instructor</label><select name="'.$prefix.'instructor_id" class="form-select"><option value="">— None —</option>';
    foreach ($instructors as $i) $html .= '<option value="'.(int)$i['id'].'">'.htmlspecialchars($i['full_name'],ENT_QUOTES).'</option>';
    $html .= '</select></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Scheduled Date & Time *</label><input type="datetime-local" name="'.$prefix.'scheduled_at" class="form-control" required></div>';
    $html .= '<div class="col-md-3"><label class="form-label">Duration (mins)</label><input type="number" name="'.$prefix.'duration_minutes" class="form-control" value="60" min="15" max="480"></div>';
    $html .= '<div class="col-md-3"><label class="form-label">Max Students</label><input type="number" name="'.$prefix.'max_students" class="form-control" placeholder="Unlimited" min="1"></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Remote Support ID (optional)</label><input name="'.$prefix.'anydesk_id" class="form-control" placeholder="Optional AnyDesk ID or support note"><div class="form-text">Use only when the instructor needs separate remote support.</div></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Microsoft Teams Channel Link</label><input type="url" name="'.$prefix.'meeting_link" class="form-control" placeholder="https://teams.microsoft.com/l/meetup-join/..."><div class="form-text">Paste a Teams meeting link to run the session through Teams from the LMS room.</div></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Recording URL</label><input type="url" name="'.$prefix.'recording_url" class="form-control" placeholder="https://youtube.com/..."></div>';
    $html .= '<div class="col-md-6"><label class="form-label">Status</label><select name="'.$prefix.'status" class="form-select"><option value="scheduled">Scheduled</option><option value="live">Live</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>';
    $html .= '<div class="col-12"><label class="form-label">Description</label><textarea name="'.$prefix.'description" class="form-control" rows="2" placeholder="What will be covered in this session?"></textarea></div>';
    $html .= '</div>';
    return $html;
}
