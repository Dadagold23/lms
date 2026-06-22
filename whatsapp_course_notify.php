<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/config/db.php';

$isAdmin = !empty($_SESSION['admin']) || (($_SESSION['user']['role'] ?? '') === 'admin');
$isInstructor = !empty($_SESSION['instructor']) || (($_SESSION['user']['role'] ?? '') === 'instructor');

if (!$isAdmin && !$isInstructor) {
    requireAdminLogin();
}

$courseId = (int)($_GET['course_id'] ?? 0);
$kind = trim((string)($_GET['kind'] ?? 'general'));
$assignmentId = (int)($_GET['assignment_id'] ?? 0);
$sessionId = (int)($_GET['session_id'] ?? 0);

if ($courseId <= 0) {
    http_response_code(400);
    exit('Invalid course ID.');
}

$courseStmt = $pdo->prepare("SELECT id, title FROM lms_courses WHERE id = ? LIMIT 1");
$courseStmt->execute([$courseId]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    http_response_code(404);
    exit('Course not found.');
}

$message = 'Hello, this is an update from Grafix@Mirror LMS for your course.';
$pageTitle = 'Course WhatsApp Notifications';

if ($kind === 'assignment' && $assignmentId > 0) {
    $stmt = $pdo->prepare("
        SELECT title, due_date
        FROM lms_assignments
        WHERE id = ? AND course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$assignmentId, $courseId]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($assignment) {
        $dueLabel = !empty($assignment['due_date']) ? date('d M Y', strtotime((string)$assignment['due_date'])) : 'No due date yet';
        $message = "Hello, a new assignment has been published for " . (string)$course['title'] . ": "
            . (string)$assignment['title'] . ". Due date: {$dueLabel}. Please log in to your LMS dashboard to view it.";
        $pageTitle = 'Assignment WhatsApp Notifications';
    }
}

if ($kind === 'live_session' && $sessionId > 0) {
    $stmt = $pdo->prepare("
        SELECT title, scheduled_at
        FROM lms_live_sessions
        WHERE id = ? AND course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId, $courseId]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($session) {
        $timeLabel = date('D d M Y, g:ia', strtotime((string)$session['scheduled_at']));
        $message = "Hello, a live session has been scheduled for " . (string)$course['title'] . ": "
            . (string)$session['title'] . " on {$timeLabel}. Please log in to your LMS dashboard to join.";
        $pageTitle = 'Live Session WhatsApp Notifications';
    }
}

$recipients = studentNotificationWhatsappRecipients($pdo, $courseId);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = $pageTitle;
$seoDesc = 'Open course-specific WhatsApp notification links for enrolled students.';
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
<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white text-decoration-none" href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'instructor_dashboard.php'; ?>">Grafix@Mirror</a>
    <div class="ms-auto d-flex gap-2">
      <?php if ($isAdmin): ?>
        <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        <a href="admin_logout.php" class="btn btn-danger btn-sm">Logout</a>
      <?php else: ?>
        <a href="instructor_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        <a href="instructor_logout.php" class="btn btn-danger btn-sm">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:1000px">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h4 class="page-title mb-1"><i class="fab fa-whatsapp me-2" style="color:#25d366"></i><?= e($pageTitle) ?></h4>
      <p class="text-muted mb-0" style="font-size:.88rem">Course: <strong><?= e((string)$course['title']) ?></strong></p>
    </div>
    <span class="badge-info"><?= count($recipients) ?> enrolled student<?= count($recipients) !== 1 ? 's' : '' ?></span>
  </div>

  <div class="lms-card mb-4">
    <div style="font-weight:700;margin-bottom:.5rem">Message Preview</div>
    <div class="p-3 rounded" style="background:#f8fafc;border:1px solid var(--border)"><?= e($message) ?></div>
  </div>

  <?php if (empty($recipients)): ?>
    <div class="lms-alert lms-alert-info">
      <i class="fa fa-info-circle me-1"></i>No enrolled students found for this course.
    </div>
  <?php else: ?>
    <div class="lms-card p-0" style="overflow:hidden">
      <table class="lms-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Phone</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recipients as $recipient): ?>
            <?php
            $personalizedMessage = str_replace('Hello,', 'Hello ' . ($recipient['name'] !== '' ? $recipient['name'] . ',' : ','), $message);
            $whatsAppUrl = $recipient['phone'] !== ''
                ? 'https://wa.me/' . $recipient['phone'] . '?text=' . urlencode($personalizedMessage)
                : '';
            ?>
            <tr>
              <td>
                <div style="font-weight:600"><?= e($recipient['name'] !== '' ? $recipient['name'] : 'Student') ?></div>
                <div style="font-size:.78rem;color:var(--muted)"><?= e($recipient['email']) ?></div>
              </td>
              <td>
                <?php if ($recipient['phone'] !== ''): ?>
                  <span style="color:var(--success)"><?= e($recipient['phone']) ?></span>
                <?php else: ?>
                  <span style="color:var(--danger)">No phone number</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($whatsAppUrl !== ''): ?>
                  <a href="<?= e($whatsAppUrl) ?>" target="_blank" class="btn btn-sm btn-success" style="background:#25d366;border:none">
                    <i class="fab fa-whatsapp me-1"></i>Send
                  </a>
                <?php else: ?>
                  <span class="badge-warning">Missing phone</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
