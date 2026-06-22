<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireLogin();
$studentId = (int)($_SESSION['user']['id'] ?? 0);
if ($studentId <= 0) redirect('login.php');

// Auto-award eligible credentials (certificates and badges)
autoAwardCredentials($studentId, $pdo);


/* ── Student info ── */
$st = $pdo->prepare("SELECT first_name, last_name, email, passport FROM lms_students WHERE id=? LIMIT 1");
$st->execute([$studentId]);
$student = $st->fetch() ?: [];
$name = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));

/* ── Enrolled courses (with enrollment + payment info) ── */
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.price, c.level, c.intro_video" . workspaceCourseSelectSql($pdo, 'c') . ",
           e.id AS enrollment_id, e.paid_amount, e.payment_type,
           e.status AS enroll_status, e.next_due_date, e.access_expires_at
    FROM lms_courses c
    INNER JOIN lms_enrollments e ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$stmt->execute([$studentId]);
$myCourses = $stmt->fetchAll();

/* ── Available courses (not enrolled) ── */
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.price, c.level, c.short_description
    FROM lms_courses c
    WHERE c.is_active = 1
      AND c.id NOT IN (SELECT course_id FROM lms_enrollments WHERE student_id = ?)
    ORDER BY c.title
");
$stmt->execute([$studentId]);
$availableCourses = $stmt->fetchAll();

/* ── Stats ── */
$totalPaid = (float)$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE student_id=? AND status='success'")
    ->execute([$studentId]) ? $pdo->query("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE student_id={$studentId} AND status='success'")->fetchColumn() : 0;
$certCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_certificates WHERE student_id={$studentId}")->fetchColumn();
$badgeCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_student_badges WHERE student_id={$studentId}")->fetchColumn();
$unreadNotifications = countUnreadStudentNotifications($pdo, $studentId);
$notifications = getStudentNotifications($pdo, $studentId, 6);
markStudentNotificationsRead($pdo, $studentId);
$isAdminImpersonating = !empty($_SESSION['admin_backup']) && !empty($_SESSION['user']['switched']);

$firstName = $student['first_name'] ?? 'Student';
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'My Dashboard';
$seoDesc    = 'Your Grafix@Mirror LMS student dashboard — view enrolled courses, track progress, access lessons, videos, assignments and exams.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="description" content="Student dashboard — manage your enrolled courses, payments and certificates at Grafix@Mirror LMS.">
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="lms-nav">
  <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
      </button>
      <a href="dashboard.php" class="brand text-decoration-none">
        <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
        <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-sm-inline" style="font-size:.85rem;color:var(--muted)">
        <span id="greet"></span>, <strong><?= e($firstName) ?></strong> 👋
      </span>
      <?php if ($isAdminImpersonating): ?>
        <a href="admin_switch.php?return=1" class="btn-brand" style="background:var(--warning);color:#111827;font-size:.82rem;padding:.4rem .9rem">
          <i class="fa fa-user-shield me-1"></i><span class="d-none d-sm-inline">Return to Admin</span>
        </a>
      <?php endif; ?>
      <a href="profile.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem"><i class="fa fa-user me-1"></i><span class="d-none d-sm-inline">Profile</span></a>
      <a href="logout.php" style="font-size:.82rem;color:var(--danger);font-weight:600"><i class="fa fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline">Logout</span></a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar" id="sidebar">
    <div class="nav-section">Student</div>
    <a href="dashboard.php" class="nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>

    <!-- My Courses accordion -->
    <button class="sidebar-acc-btn" id="myCoursesToggle" aria-expanded="false" aria-controls="myCoursesList">
      <i class="fa fa-book-open icon"></i> My Courses
      <i class="fa fa-chevron-down chev"></i>
    </button>
    <div class="sidebar-courses" id="myCoursesList" role="region">
      <?php foreach ($myCourses as $c): ?>
        <a href="course.php?id=<?= (int)$c['id'] ?>" class="sidebar-course-item">
          <i class="fa fa-circle" style="color:var(--brand)"></i><?= e($c['title']) ?>
        </a>
      <?php endforeach; ?>
      <?php if (empty($myCourses)): ?>
        <div class="sidebar-course-item" style="font-style:italic;color:var(--muted)">No enrollments yet</div>
      <?php endif; ?>
    </div>

    <!-- Available Courses accordion -->
    <button class="sidebar-acc-btn" id="availToggle" aria-expanded="false" aria-controls="availList">
      <i class="fa fa-graduation-cap icon"></i> Available Courses
      <i class="fa fa-chevron-down chev"></i>
    </button>
    <div class="sidebar-courses" id="availList" role="region">
      <?php foreach ($availableCourses as $c): ?>
        <a href="course.php?id=<?= (int)$c['id'] ?>" class="sidebar-course-item">
          <i class="fa fa-circle" style="color:var(--success)"></i><?= e($c['title']) ?>
        </a>
      <?php endforeach; ?>
      <?php if (empty($availableCourses)): ?>
        <div class="sidebar-course-item" style="font-style:italic;color:var(--muted)">All courses enrolled</div>
      <?php endif; ?>
    </div>

    <div class="nav-section">Learning</div>
    <a href="videos.php" class="nav-link"><i class="fa fa-play-circle"></i> Videos</a>
    <a href="assignments.php" class="nav-link"><i class="fa fa-tasks"></i> Assignments</a>
    <a href="exams.php" class="nav-link"><i class="fa fa-pen-alt"></i> Exams</a>
    <a href="live_session.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions</a>
    <a href="ai_tutor.php" class="nav-link" style="color:var(--brand)"><i class="fa fa-robot"></i> AI Tutor <span style="font-size:.65rem;background:var(--brand);color:#fff;padding:.1rem .4rem;border-radius:99px;margin-left:.25rem">24/7</span></a>

    <div class="nav-section">Account</div>
    <a href="payments.php" class="nav-link"><i class="fa fa-credit-card"></i> Payments</a>
    <a href="certificate.php" class="nav-link"><i class="fa fa-certificate"></i> Certificates</a>
    <a href="badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="profile.php" class="nav-link"><i class="fa fa-user-cog"></i> Profile</a>

    <div class="nav-section">Portal</div>
    <a href="logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">

    <?php if ($isAdminImpersonating): ?>
      <div class="lms-alert lms-alert-warning mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="fa fa-user-secret me-1"></i>You are viewing the portal as this student.</span>
        <a href="admin_switch.php?return=1" class="btn-ghost" style="font-size:.82rem;padding:.4rem .8rem">
          <i class="fa fa-arrow-left me-1"></i>Return to Admin
        </a>
      </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa fa-book-open"></i></div>
          <div>
            <div class="stat-value"><?= count($myCourses) ?></div>
            <div class="stat-label">Enrolled Courses</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa fa-graduation-cap"></i></div>
          <div>
            <div class="stat-value"><?= count($availableCourses) ?></div>
            <div class="stat-label">Available Courses</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon amber"><i class="fa fa-wallet"></i></div>
          <div>
            <div class="stat-value" style="font-size:1.1rem"><?= formatMoney($totalPaid) ?></div>
            <div class="stat-label">Total Paid</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <a href="certificate.php" class="text-decoration-none text-dark">
          <div class="stat-card">
            <div class="stat-icon cyan"><i class="fa fa-certificate"></i></div>
            <div>
              <div class="stat-value"><?= $certCount ?></div>
              <div class="stat-label">Certificates</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <a href="badges.php" class="text-decoration-none text-dark">
          <div class="stat-card">
            <div class="stat-icon purple" style="background:rgba(227,193,98,0.12); color:#e3c162"><i class="fa fa-award"></i></div>
            <div>
              <div class="stat-value"><?= $badgeCount ?></div>
              <div class="stat-label">Badges</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa fa-bell"></i></div>
          <div>
            <div class="stat-value"><?= $unreadNotifications ?></div>
            <div class="stat-label">New Alerts</div>
          </div>
        </div>
      </div>
    </div>

    <div class="lms-card mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="section-title mb-0"><i class="fa fa-bell me-2" style="color:var(--brand)"></i>Notifications</div>
        <span style="font-size:.8rem;color:var(--muted)"><?= count($notifications) ?> recent</span>
      </div>
      <?php if (empty($notifications)): ?>
        <div class="lms-alert lms-alert-info mb-0">
          <i class="fa fa-info-circle me-1"></i>No notifications yet.
        </div>
      <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($notifications as $notice): ?>
            <div class="p-3 rounded" style="border:1px solid var(--border);background:#fff">
              <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div style="font-weight:700"><?= e($notice['title']) ?></div>
                <span class="badge-<?= $notice['type'] === 'assignment' ? 'warning' : 'info' ?>">
                  <?= $notice['type'] === 'assignment' ? 'Assignment' : 'Live Session' ?>
                </span>
              </div>
              <div style="font-size:.88rem;color:var(--dark);margin-top:.35rem"><?= e($notice['message']) ?></div>
              <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-2">
                <span style="font-size:.76rem;color:var(--muted)"><?= e(date('d M Y, g:ia', strtotime((string)$notice['created_at']))) ?></span>
                <?php if (!empty($notice['action_url'])): ?>
                  <a href="<?= e((string)$notice['action_url']) ?>" class="btn-ghost" style="font-size:.78rem;padding:.35rem .8rem">
                    <i class="fa fa-arrow-right me-1"></i>Open
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- MY ENROLLED COURSES -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="section-title mb-0">My Enrolled Courses</div>
    </div>

    <?php if (empty($myCourses)): ?>
      <div class="lms-alert lms-alert-info mb-4">
        <i class="fa fa-info-circle"></i>
        You haven't enrolled in any course yet. Click <strong>Available Courses</strong> in the sidebar to browse.
      </div>
    <?php else: ?>
      <div class="row g-3 mb-4">
      <?php foreach ($myCourses as $c):
        $c = workspaceCourseRow((array)$c);
        $access  = enrollmentAccessState($c);
        $paid    = (float)($c['paid_amount'] ?? 0);
        $price   = (float)($c['price'] ?? 0);
        $ptype   = (string)($c['payment_type'] ?? 'full');
        $status  = (string)($c['enroll_status'] ?? '');
        $expired = (bool)$access['is_expired'];
        $installmentDue = (bool)$access['installment_due'];
        $balance = max(0, $price - $paid);
        $pct     = $price > 0 ? min(100, round($paid / $price * 100)) : 100;

        if ($expired) {
          $badge  = '<span class="badge-danger">Expired</span>';
          $action = '<a class="btn-ghost w-100 text-center d-block" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'">Renew Access</a>';
        } elseif ($status === 'paid' || ($price > 0 && $paid >= $price)) {
          $badge  = '<span class="badge-success">Unlocked ✓</span>';
          $action = '<div class="d-grid gap-2">'
                  . '<a class="btn-brand w-100 justify-content-center d-flex" href="' . e(workspaceLaunchUrl($c)) . '"><i class="fa ' . e(workspaceTypeIcon($c)) . ' me-1"></i>Launch ' . e(workspaceTypeLabel($c)) . '</a>'
                  . '<a class="btn-ghost w-100 text-center d-block" href="course.php?id='.(int)$c['id'].'">Course Details</a>'
                  . '</div>';
        } elseif ($ptype === 'installment' && $paid > 0 && !$installmentDue) {
          $badge  = '<span class="badge-success">Active on Installment</span>';
          $action = '<div class="d-grid gap-2">'
                  . '<a class="btn-brand w-100 justify-content-center d-flex" href="' . e(workspaceLaunchUrl($c)) . '"><i class="fa ' . e(workspaceTypeIcon($c)) . ' me-1"></i>Launch ' . e(workspaceTypeLabel($c)) . '</a>'
                  . '<a class="btn-ghost w-100 text-center d-block" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'">Pay Balance Later</a>'
                  . '</div>';
        } elseif ($ptype === 'installment' && $installmentDue) {
          $badge  = '<span class="badge-danger">2nd Payment Due</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--warning);color:#000" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Balance ('.formatMoney($balance).')</a>';
        } elseif ($status === 'installment' || ($paid > 0 && $paid < $price)) {
          $badge  = '<span class="badge-warning">Installment</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--warning);color:#000" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Balance ('.formatMoney($balance).')</a>';
        } else {
          $badge  = '<span class="badge-muted">Pending Payment</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--success)" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Now</a>';
        }
      ?>
        <div class="col-md-6 col-xl-4">
          <div class="course-card h-100">
            <div class="course-thumb">
              <i class="fa fa-book-open"></i>
            </div>
            <div class="course-body">
              <div class="course-title"><?= e($c['title']) ?></div>
              <div class="course-price d-flex align-items-center justify-content-between mt-1">
                <span><?= formatMoney($price) ?></span>
                <span class="badge-muted" style="font-size:.7rem"><?= e(ucfirst($c['level'] ?? '')) ?></span>
              </div>
              <div class="mt-2 mb-1"><?= $badge ?></div>
              <!-- Payment progress -->
              <div class="progress mt-2" style="height:5px">
                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
              </div>
              <div class="d-flex justify-content-between" style="font-size:.72rem;color:var(--muted);margin-top:.2rem">
                <span>Paid: <?= formatMoney($paid) ?></span>
                <span><?= $pct ?>%</span>
              </div>
              <?php if (!empty($c['next_due_date']) && $balance > 0): ?>
                <div style="font-size:.75rem;color:var(--danger);margin-top:.3rem">
                  <i class="fa fa-clock me-1"></i>Next due: <?= e(date('d M Y', strtotime($c['next_due_date']))) ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="course-footer"><?= $action ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>
</div>

<script>
/* Greeting */
const h = new Date().getHours();
const el = document.getElementById('greet');
if (el) el.textContent = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : h < 22 ? 'Good Evening' : 'Good Night';

/* Sidebar mobile toggle */
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});

/* Accordion helper — works with max-height CSS transition */
function initAccordion(btnId, panelId) {
  const btn   = document.getElementById(btnId);
  const panel = document.getElementById(panelId);
  if (!btn || !panel) return;
  btn.addEventListener('click', function () {
    const isOpen = panel.classList.toggle('open');
    btn.classList.toggle('open', isOpen);
    btn.setAttribute('aria-expanded', String(isOpen));
  });
}
initAccordion('myCoursesToggle', 'myCoursesList');
initAccordion('availToggle',     'availList');
</script>
</body>
</html>
