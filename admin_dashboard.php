<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'];

$stats = [
  'students'               => (int)$pdo->query("SELECT COUNT(*) FROM lms_students")->fetchColumn(),
  'courses'                => (int)$pdo->query("SELECT COUNT(*) FROM lms_courses")->fetchColumn(),
  'enrollments'            => (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments")->fetchColumn(),
  'revenue'                => (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE status='success'")->fetchColumn(),
  'pending_manual'         => (int)$pdo->query("SELECT COUNT(*) FROM lms_payments WHERE status='pending' AND channel='manual'")->fetchColumn(),
  'live_upcoming'          => (int)$pdo->query("SELECT COUNT(*) FROM lms_live_sessions WHERE status IN ('scheduled','live')")->fetchColumn(),
  'unassigned_enrollments' => (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments WHERE needs_instructor_assignment = 1")->fetchColumn(),
];

$upcomingSessions = $pdo->query("
    SELECT s.id, s.title, s.scheduled_at, s.status, s.anydesk_id,
           c.title AS course_title,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    WHERE s.status IN ('scheduled','live')
    ORDER BY s.scheduled_at ASC LIMIT 5
")->fetchAll();

$recentPayments = $pdo->query("
    SELECT p.id, p.amount, p.channel, p.status, p.created_at,
           s.first_name, s.last_name
    FROM lms_payments p JOIN lms_students s ON s.id=p.student_id
    ORDER BY p.created_at DESC LIMIT 8
")->fetchAll();

$recentStudents = $pdo->query("
    SELECT id, first_name, last_name, email, created_at
    FROM lms_students ORDER BY created_at DESC LIMIT 8
")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Admin Dashboard';
$seoDesc    = 'Admin dashboard — manage students, courses, payments and live sessions at Grafix@Mirror LMS.';
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

<nav class="lms-nav lms-nav-admin">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
      <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:#94a3b8">
        <i class="fa fa-user-shield me-1"></i><?= e($admin['full_name'] ?? 'Admin') ?>
      </span>
      <a href="admin_logout.php" style="font-size:.82rem;color:#f87171;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar">
    <div class="nav-section">Overview</div>
    <a href="admin_dashboard.php" class="nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="analytics.php" class="nav-link"><i class="fa fa-chart-bar"></i> Analytics</a>
    <div class="nav-section">Management</div>
    <a href="admin_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="admin_instructors.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Instructors</a>
    <a href="admin_partners.php" class="nav-link"><i class="fa fa-handshake"></i> Affiliate/Partners</a>
    <a href="admin_enrollment_assignments.php" class="nav-link">
      <i class="fa fa-user-tag"></i> Assignments
      <?php if ($stats['unassigned_enrollments'] > 0): ?>
        <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.1rem .45rem;font-size:.7rem;margin-left:auto"><?= $stats['unassigned_enrollments'] ?></span>
      <?php endif; ?>
    </a>
    <a href="admin_student_performance.php" class="nav-link"><i class="fa fa-graduation-cap"></i> Student Performance</a>
    <a href="cert_settings.php" class="nav-link"><i class="fa fa-certificate"></i> Certificate</a>
    <a href="admin_badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="admin_payment_approval.php" class="nav-link">
      <i class="fa fa-credit-card"></i> Payments
      <?php if ($stats['pending_manual'] > 0): ?>
        <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.1rem .45rem;font-size:.7rem;margin-left:auto"><?= $stats['pending_manual'] ?></span>
      <?php endif; ?>
    </a>
    <a href="finance_report.php" class="nav-link"><i class="fa fa-file-invoice-dollar"></i> Finance Report</a>
    <a href="bulk_import.php" class="nav-link"><i class="fa fa-upload"></i> Bulk Import</a>
    <div class="nav-section">Tools</div>
    <a href="admin_live_sessions.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions
      <?php if ($stats['live_upcoming'] > 0): ?>
        <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.1rem .45rem;font-size:.7rem;margin-left:auto"><?= $stats['live_upcoming'] ?></span>
      <?php endif; ?>
    </a>
    <a href="admin_switch.php" class="nav-link"><i class="fa fa-exchange-alt"></i> Switch User</a>
    <a href="reminders.php" class="nav-link"><i class="fa fa-bell"></i> Reminders</a>
    <a href="whatsapp_messages.php" class="nav-link"><i class="fab fa-whatsapp"></i> Messages</a>
    <a href="create_admin.php" class="nav-link"><i class="fa fa-user-plus"></i> Create Admin</a>
    <a href="admin_change_password.php" class="nav-link"><i class="fa fa-key"></i> Change Password</a>
    <div class="nav-section">Portal</div>
    <a href="admin_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">

    <div class="page-title mb-4">Dashboard Overview</div>

    <!-- Alert for unassigned enrollments -->
    <?php if ($stats['unassigned_enrollments'] > 0): ?>
      <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 animate__animated animate__fadeIn" style="border-radius: 12px; background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; background-color: #fef3c7;">
            <i class="fa fa-exclamation-triangle fa-lg text-warning" style="color: #d97706 !important;"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1 text-dark">Unassigned Student Enrollments</h6>
            <p class="text-muted small mb-0">There are currently <strong><?= $stats['unassigned_enrollments'] ?></strong> active enrollment(s) with no assigned instructor. Students cannot access course lessons until assigned.</p>
          </div>
        </div>
        <a href="admin_enrollment_assignments.php" class="btn btn-warning btn-sm fw-bold px-3" style="background-color: #f59e0b; color: #fff; border: none; border-radius: 6px;">Assign Now</a>
      </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa fa-users"></i></div>
          <div>
            <div class="stat-value"><?= number_format($stats['students']) ?></div>
            <div class="stat-label">Total Students</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa fa-book"></i></div>
          <div>
            <div class="stat-value"><?= $stats['courses'] ?></div>
            <div class="stat-label">Active Courses</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon cyan"><i class="fa fa-user-check"></i></div>
          <div>
            <div class="stat-value"><?= number_format($stats['enrollments']) ?></div>
            <div class="stat-label">Enrollments</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa fa-money-bill-wave"></i></div>
          <div>
            <div class="stat-value" style="font-size:1.2rem"><?= formatMoney($stats['revenue']) ?></div>
            <div class="stat-label">Total Revenue</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa fa-video"></i></div>
          <div>
            <div class="stat-value"><?= $stats['live_upcoming'] ?></div>
            <div class="stat-label">Live Sessions</div>
          </div>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="lms-card mb-4">
      <div class="section-title">Quick Actions</div>
      <div class="d-flex flex-wrap gap-2">
        <a href="admin_courses.php" class="btn-brand"><i class="fa fa-book"></i> Manage Courses</a>
        <a href="admin_badges.php" class="btn-brand" style="background:var(--brand);color:#fff"><i class="fa fa-award"></i> Manage Badges</a>
        <a href="admin_live_sessions.php" class="btn-brand" style="background:var(--danger)"><i class="fa fa-video"></i> Live Sessions</a>
        <a href="admin_payment_approval.php" class="btn-outline-brand"><i class="fa fa-credit-card"></i> Payment Approvals</a>
        <a href="finance_report.php" class="btn-ghost"><i class="fa fa-file-invoice-dollar"></i> Finance Report</a>
        <a href="analytics.php" class="btn-ghost"><i class="fa fa-chart-bar"></i> Analytics</a>
        <a href="bulk_import.php" class="btn-ghost"><i class="fa fa-upload"></i> Bulk Import</a>
        <a href="create_admin.php" class="btn-ghost"><i class="fa fa-user-plus"></i> Create Admin</a>
      </div>
    </div>

    <!-- UPCOMING LIVE SESSIONS -->
    <?php if (!empty($upcomingSessions)): ?>
    <div class="lms-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0"><i class="fa fa-video me-2" style="color:var(--danger)"></i>Upcoming Live Sessions</div>
        <a href="admin_live_sessions.php" style="font-size:.8rem;color:var(--brand)">Manage all →</a>
      </div>
      <div style="overflow-x:auto">
        <table class="lms-table">
          <thead><tr><th>Session</th><th>Course</th><th>Scheduled</th><th>Status</th><th>Attendees</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($upcomingSessions as $s): ?>
            <tr>
              <td>
                <div style="font-weight:600"><?= e($s['title']) ?></div>
                <?php if (!empty($s['anydesk_id'])): ?>
                  <div style="font-size:.75rem;color:var(--muted)">AnyDesk: <?= e($s['anydesk_id']) ?></div>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem"><?= e($s['course_title']) ?></td>
              <td style="font-size:.82rem"><?= e(date('d M Y H:i', strtotime($s['scheduled_at']))) ?></td>
              <td>
                <?php if ($s['status'] === 'live'): ?>
                  <span style="color:var(--danger);font-weight:700;font-size:.8rem">● LIVE</span>
                <?php else: ?>
                  <span class="badge-info">Scheduled</span>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem"><?= (int)$s['attendees'] ?></td>
              <td>
                <a href="admin_live_sessions.php" class="btn-outline-brand" style="font-size:.78rem;padding:.25rem .7rem">
                  <i class="fa fa-edit"></i> Manage
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- TABLES -->
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="lms-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0">Recent Students</div>
            <a href="analytics.php" style="font-size:.8rem;color:var(--brand)">View all →</a>
          </div>
          <?php if (empty($recentStudents)): ?>
            <p class="text-muted" style="font-size:.88rem">No students yet.</p>
          <?php else: ?>
            <div style="overflow-x:auto">
              <table class="lms-table">
                <thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
                <tbody>
                <?php foreach ($recentStudents as $s): ?>
                  <tr>
                    <td style="font-weight:600"><?= e(($s['first_name']??'').' '.($s['last_name']??'')) ?></td>
                    <td style="color:var(--muted)"><?= e($s['email']??'') ?></td>
                    <td style="color:var(--muted)"><?= e(date('d M Y', strtotime((string)$s['created_at']))) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="lms-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0">Recent Payments</div>
            <a href="finance_report.php" style="font-size:.8rem;color:var(--brand)">View all →</a>
          </div>
          <?php if (empty($recentPayments)): ?>
            <p class="text-muted" style="font-size:.88rem">No payments yet.</p>
          <?php else: ?>
            <div style="overflow-x:auto">
              <table class="lms-table">
                <thead><tr><th>Student</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentPayments as $p):
                  $st = (string)($p['status']??'');
                  $badge = match($st) {
                    'success' => 'badge-success',
                    'pending' => 'badge-warning',
                    default   => 'badge-muted'
                  };
                ?>
                  <tr>
                    <td style="font-weight:600"><?= e(($p['first_name']??'').' '.($p['last_name']??'')) ?></td>
                    <td><?= formatMoney($p['amount']??0) ?></td>
                    <td><span class="<?= $badge ?>"><?= e($st) ?></span></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </main>
</div>

</body>
</html>
