<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/guard.php';

$currentPage = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));

// Determine Context
$isAdminContext = str_starts_with($currentPage, 'admin_') || in_array($currentPage, [
    'analytics.php', 'finance_report.php', 'bulk_import.php', 
    'reminders.php', 'whatsapp_messages.php', 'create_admin.php', 'cert_settings.php'
], true);

$isInstructorContext = str_starts_with($currentPage, 'instructor_');

if ($isAdminContext) {
    // -------------------------------------------------------------
    // ADMIN HEADER
    // -------------------------------------------------------------
    $adminSession = $_SESSION['admin'] ?? $_SESSION['user'] ?? null;
    $adminName = $adminSession['full_name'] ?? (($adminSession['first_name'] ?? 'Admin') . ' ' . ($adminSession['last_name'] ?? ''));
    ?>
    <nav class="lms-nav lms-nav-admin">
      <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
        <div class="brand">
          <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
          <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span style="font-size:.82rem;color:#94a3b8">
            <i class="fa fa-user-shield me-1"></i><?= e($adminName) ?>
          </span>
          <a href="admin_logout.php" style="font-size:.82rem;color:#f87171;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
        </div>
      </div>
    </nav>
    <?php
} elseif ($isInstructorContext) {
    // -------------------------------------------------------------
    // INSTRUCTOR HEADER
    // -------------------------------------------------------------
    $insSession = $_SESSION['instructor'] ?? $_SESSION['user'] ?? null;
    $insName = $insSession['full_name'] ?? (($insSession['first_name'] ?? 'Instructor') . ' ' . ($insSession['last_name'] ?? ''));
    ?>
    <nav class="lms-nav lms-nav-instructor">
      <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
        <div class="brand">
          <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">I</div>
          <span style="color:#fff">Instructor <span style="color:#93c5fd">Panel</span></span>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span style="font-size:.82rem;color:#bfdbfe">
            <i class="fa fa-chalkboard-teacher me-1"></i><?= e($insName) ?>
          </span>
          <a href="instructor_logout.php" style="font-size:.82rem;color:#fca5a5;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
        </div>
      </div>
    </nav>
    <?php
} else {
    // -------------------------------------------------------------
    // STUDENT/SHARED HEADER
    // -------------------------------------------------------------
    $navbarUser = $_SESSION['user'] ?? null;
    $navbarFirstName = $navbarUser['first_name'] ?? 'User';
    $isAdminImpersonating = !empty($_SESSION['admin_backup']) && !empty($_SESSION['user']['switched']);

    // Detect if this page has a sidebar (student-facing dashboards/pages)
    $sidebarPages = [
        'dashboard.php', 'videos.php', 'assignments.php', 'exams.php',
        'live_session.php', 'ai_tutor.php', 'certificate.php', 'badges.php'
    ];
    $hasSidebar = in_array($currentPage, $sidebarPages, true);
    ?>
    <nav class="lms-nav">
      <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <?php if ($hasSidebar): ?>
            <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem" aria-label="Toggle menu">
              <i class="fa fa-bars"></i>
            </button>
          <?php endif; ?>
          <a href="dashboard.php" class="brand text-decoration-none">
            <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
            <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
          </a>
        </div>
        
        <div class="d-flex align-items-center gap-3">
          <?php if ($navbarUser): ?>
            <span class="d-none d-sm-inline" style="font-size:.85rem;color:var(--muted)">
              <span id="greet"></span>, <strong><?= e($navbarFirstName) ?></strong> 👋
            </span>
            <?php if ($isAdminImpersonating): ?>
              <a href="admin_switch.php?return=1" class="btn-brand" style="background:var(--warning);color:#111827;font-size:.82rem;padding:.4rem .9rem">
                <i class="fa fa-user-shield me-1"></i><span class="d-none d-sm-inline">Return to Admin</span>
              </a>
            <?php endif; ?>
            
            <?php if (hasRole('admin')): ?>
              <a href="admin_dashboard.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem"><i class="fa fa-shield-alt me-1"></i><span class="d-none d-sm-inline">Admin Dashboard</span></a>
            <?php endif; ?>
            <?php if (hasRole('instructor')): ?>
              <a href="instructor_dashboard.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem"><i class="fa fa-chalkboard-teacher me-1"></i><span class="d-none d-sm-inline">Instructor Dashboard</span></a>
            <?php endif; ?>
            
            <a href="profile.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem"><i class="fa fa-user me-1"></i><span class="d-none d-sm-inline">Profile</span></a>
            <a href="logout.php" style="font-size:.82rem;color:var(--danger);font-weight:600"><i class="fa fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline">Logout</span></a>
          <?php else: ?>
            <a href="login.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem">Login</a>
            <a href="register.php" class="btn-brand" style="font-size:.82rem;padding:.4rem .9rem">Get Started</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
    <script>
    /* Greeting in navbar */
    const hour = new Date().getHours();
    const greetEl = document.getElementById('greet');
    if (greetEl) {
      greetEl.textContent = hour < 12 ? 'Good Morning' : hour < 17 ? 'Good Afternoon' : hour < 22 ? 'Good Evening' : 'Good Night';
    }
    </script>
    <?php
}
