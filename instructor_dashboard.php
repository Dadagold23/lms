<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();
$ins = $_SESSION['instructor'];
$insId = (int)($ins['id'] ?? 0);

$coursesCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id={$insId}")->fetchColumn();
$lessonsCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons l JOIN lms_instructor_courses ic ON ic.course_id = l.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$videosCount  = (int)$pdo->query("SELECT COUNT(*) FROM lms_videos v JOIN lms_instructor_courses ic ON ic.course_id = v.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$assignCount  = (int)$pdo->query("SELECT COUNT(*) FROM lms_assignments a JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$subsCount    = (int)$pdo->query("SELECT COUNT(*) FROM lms_assignment_submissions sub JOIN lms_assignments a ON a.id = sub.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();

$stats = [
  'courses' => $coursesCount,
  'lessons' => $lessonsCount,
  'videos'  => $videosCount,
  'assign'  => $assignCount,
  'subs'    => $subsCount,
];

// Live sessions for this instructor (or all if admin-level)
$liveSessions = $pdo->query("
    SELECT s.id, s.title, s.scheduled_at, s.status, s.anydesk_id, s.meeting_link,
           c.title AS course_title,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    WHERE s.instructor_id={$insId}
      AND s.status IN ('scheduled','live')
    ORDER BY s.scheduled_at ASC LIMIT 5
")->fetchAll();

$recentSubs = $pdo->query("
    SELECT sub.id, sub.submitted_at, sub.file_path,
           a.title AS assignment_title,
           s.first_name, s.last_name
    FROM lms_assignment_submissions sub
    JOIN lms_assignments a ON a.id = sub.assignment_id
    JOIN lms_instructor_courses ic ON ic.course_id = a.course_id
    JOIN lms_students s ON s.id = sub.student_id
    WHERE ic.instructor_id={$insId}
    ORDER BY sub.submitted_at DESC LIMIT 8
")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Dashboard';
$seoDesc    = 'Instructor dashboard — manage courses, lessons, videos, assignments and live sessions at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body>

<nav class="lms-nav lms-nav-instructor">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">I</div>
      <span style="color:#fff">Instructor <span style="color:#c7d2fe">Portal</span></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:rgba(255,255,255,.7)">
        <i class="fa fa-chalkboard-teacher me-1"></i><?= e($ins['full_name'] ?? 'Instructor') ?>
      </span>
      <a href="instructor_logout.php" style="font-size:.82rem;color:#fca5a5;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar">
    <div class="nav-section">Overview</div>
    <a href="instructor_dashboard.php" class="nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>
    <div class="nav-section">Content</div>
    <a href="instructor_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="instructor_lessons.php" class="nav-link"><i class="fa fa-file-alt"></i> Lessons</a>
    <a href="instructor_videos.php" class="nav-link"><i class="fa fa-video"></i> Videos</a>
    <a href="instructor_assignments.php" class="nav-link"><i class="fa fa-tasks"></i> Assignments</a>
    <div class="nav-section">Upload</div>
    <a href="instructor_upload_course.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Course</a>
    <a href="instructor_upload_lesson.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Lesson</a>
    <a href="instructor_upload_video.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Video</a>
    <a href="instructor_upload_assignment.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Assignment</a>
    <div class="nav-section">Live Teaching</div>
    <a href="instructor_live_sessions.php" class="nav-link" style="color:var(--danger)">
      <i class="fa fa-video"></i> Live Sessions
      <?php if (!empty($liveSessions)): ?>
        <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.1rem .45rem;font-size:.7rem;margin-left:auto"><?= count($liveSessions) ?></span>
      <?php endif; ?>
    </a>
    <div class="nav-section">Grading</div>
    <a href="instructor_grade_assignment.php" class="nav-link"><i class="fa fa-star"></i> Grade Submissions</a>
    <div class="nav-section">Portal</div>
    <a href="instructor_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">

    <div class="page-title mb-4">Instructor Dashboard</div>

    <!-- STATS -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa fa-book"></i></div>
          <div><div class="stat-value"><?= $stats['courses'] ?></div><div class="stat-label">Courses</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
          <div><div class="stat-value"><?= $stats['lessons'] ?></div><div class="stat-label">Lessons</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon cyan"><i class="fa fa-video"></i></div>
          <div><div class="stat-value"><?= $stats['videos'] ?></div><div class="stat-label">Videos</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon amber"><i class="fa fa-tasks"></i></div>
          <div><div class="stat-value"><?= $stats['assign'] ?></div><div class="stat-label">Assignments</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa fa-inbox"></i></div>
          <div><div class="stat-value"><?= $stats['subs'] ?></div><div class="stat-label">Submissions</div></div>
        </div>
      </div>
    </div>

    <!-- QUICK UPLOAD -->
    <div class="lms-card mb-4">
      <div class="section-title">Quick Actions</div>
      <div class="d-flex flex-wrap gap-2">
        <a href="instructor_live_sessions.php" class="btn-brand" style="background:var(--danger)">
          <i class="fa fa-broadcast-tower"></i> Schedule Live Session
        </a>
        <a href="instructor_upload_course.php" class="btn-brand"><i class="fa fa-plus"></i> New Course</a>
        <a href="instructor_upload_lesson.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Lesson</a>
        <a href="instructor_upload_video.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Video</a>
        <a href="instructor_upload_assignment.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Assignment</a>
        <a href="instructor_grade_assignment.php" class="btn-ghost"><i class="fa fa-star"></i> Grade Submissions</a>
      </div>
    </div>

    <!-- UPCOMING LIVE SESSIONS -->
    <div class="lms-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0"><i class="fa fa-video me-2" style="color:var(--danger)"></i>My Live Sessions</div>
        <a href="instructor_live_sessions.php" style="font-size:.8rem;color:var(--brand)">Manage all →</a>
      </div>
      <?php if (empty($liveSessions)): ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-2">
          <p class="text-muted mb-0" style="font-size:.88rem">No upcoming sessions. Schedule one to start live tutoring.</p>
          <a href="instructor_live_sessions.php" class="btn-brand" style="background:var(--danger);font-size:.85rem">
            <i class="fa fa-plus me-1"></i> Schedule Session
          </a>
        </div>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="lms-table">
            <thead><tr><th>Session</th><th>Course</th><th>Scheduled</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($liveSessions as $s): ?>
              <tr>
                <td>
                  <div style="font-weight:600"><?= e($s['title']) ?></div>
                  <?php if (!empty($s['anydesk_id'])): ?>
                    <div style="font-size:.75rem;color:var(--muted)"><i class="fa fa-desktop me-1"></i>AnyDesk: <strong><?= e($s['anydesk_id']) ?></strong></div>
                  <?php endif; ?>
                </td>
                <td style="font-size:.85rem"><?= e($s['course_title']) ?></td>
                <td style="font-size:.82rem"><?= e(date('d M Y H:i', strtotime($s['scheduled_at']))) ?></td>
                <td>
                  <?php if ($s['status'] === 'live'): ?>
                    <span style="color:var(--danger);font-weight:700;font-size:.8rem;animation:pulse 1.5s infinite">● LIVE NOW</span>
                  <?php else: ?>
                    <span class="badge-info">Scheduled</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="instructor_live_sessions.php?id=<?= (int)$s['id'] ?>" class="btn-outline-brand" style="font-size:.78rem;padding:.25rem .7rem">
                    <i class="fa fa-edit"></i> Manage
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- RECENT SUBMISSIONS -->
    <div class="lms-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">Recent Submissions</div>
        <a href="instructor_grade_assignment.php" style="font-size:.8rem;color:var(--brand)">Grade all →</a>
      </div>
      <?php if (empty($recentSubs)): ?>
        <p class="text-muted" style="font-size:.88rem">No submissions yet.</p>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="lms-table">
            <thead><tr><th>Student</th><th>Assignment</th><th>Submitted</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($recentSubs as $sub): ?>
              <tr>
                <td style="font-weight:600"><?= e(($sub['first_name']??'').' '.($sub['last_name']??'')) ?></td>
                <td><?= e($sub['assignment_title']??'') ?></td>
                <td style="color:var(--muted)"><?= e(date('d M Y', strtotime((string)$sub['submitted_at']))) ?></td>
                <td>
                  <a href="instructor_grade_assignment.php?submission_id=<?= (int)$sub['id'] ?>" class="btn-outline-brand" style="font-size:.78rem;padding:.25rem .7rem">
                    Grade
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

</body>
</html>
