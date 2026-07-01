<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'] ?? $_SESSION['user'] ?? [];
$adminEmail = (string)($admin['email'] ?? '');

$flashSuccess = $_SESSION['admin_pwd_success'] ?? null;
$flashError   = $_SESSION['admin_pwd_error']   ?? null;
unset($_SESSION['admin_pwd_success'], $_SESSION['admin_pwd_error']);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $currentPwd  = (string)($_POST['current_password'] ?? '');
    $newPwd      = (string)($_POST['new_password'] ?? '');
    $confirmPwd  = (string)($_POST['confirm_password'] ?? '');

    if ($currentPwd === '' || $newPwd === '' || $confirmPwd === '') {
        $_SESSION['admin_pwd_error'] = 'All password fields are required.';
        redirect('admin_change_password.php');
    }

    if ($newPwd !== $confirmPwd) {
        $_SESSION['admin_pwd_error'] = 'New passwords do not match.';
        redirect('admin_change_password.php');
    }

    if (strlen($newPwd) < 8 || !preg_match('/[A-Z]/', $newPwd) || !preg_match('/[0-9]/', $newPwd)) {
        $_SESSION['admin_pwd_error'] = 'Password must be at least 8 characters, include 1 uppercase letter and 1 number.';
        redirect('admin_change_password.php');
    }

    // Fetch current hash from lms_admins
    $stmt = $pdo->prepare("SELECT id, password FROM lms_admins WHERE email = ? LIMIT 1");
    $stmt->execute([$adminEmail]);
    $adminRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminRow) {
        $_SESSION['admin_pwd_error'] = 'Admin account not found. Please contact support.';
        redirect('admin_change_password.php');
    }

    if (!password_verify($currentPwd, (string)$adminRow['password'])) {
        $_SESSION['admin_pwd_error'] = 'Current password is incorrect.';
        redirect('admin_change_password.php');
    }

    $newHash = password_hash($newPwd, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        // Update lms_admins
        $pdo->prepare("UPDATE lms_admins SET password = ? WHERE email = ?")
            ->execute([$newHash, $adminEmail]);

        // Also update lms_students if a matching admin-role row exists (dual-table admin)
        $pdo->prepare("UPDATE lms_students SET password = ? WHERE email = ? AND role = 'admin'")
            ->execute([$newHash, $adminEmail]);

        $pdo->commit();

        $_SESSION['admin_pwd_success'] = 'Password changed successfully. Please use your new password on next login.';
    } catch (Throwable $e) {
        $pdo->rollBack();
        $_SESSION['admin_pwd_error'] = 'Failed to update password. Please try again.';
    }

    redirect('admin_change_password.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Change Admin Password';
$seoDesc    = 'Update admin account password — Grafix@Mirror LMS.';
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
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem; color:#fff;" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
      </button>
      <div class="brand">
        <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
        <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:#94a3b8">
        <i class="fa fa-user-shield me-1"></i><?= e($admin['full_name'] ?? 'Admin') ?>
      </span>
      <a href="admin_dashboard.php" style="font-size:.82rem;color:#94a3b8"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="admin_logout.php" style="font-size:.82rem;color:#f87171;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar" id="sidebar">
    <div class="nav-section">Overview</div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="analytics.php" class="nav-link"><i class="fa fa-chart-bar"></i> Analytics</a>
    <div class="nav-section">Management</div>
    <a href="admin_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="admin_instructors.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Instructors</a>
    <a href="admin_partners.php" class="nav-link"><i class="fa fa-handshake"></i> Affiliate</a>
    <a href="admin_affiliate_courses.php" class="nav-link"><i class="fa fa-book-open"></i> Affiliate Courses</a>
    <a href="admin_affiliate_scheme.php" class="nav-link"><i class="fa fa-scroll"></i> Scheme of Work</a>
    <a href="cert_settings.php" class="nav-link"><i class="fa fa-certificate"></i> Certificate</a>
    <a href="admin_badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="admin_payment_approval.php" class="nav-link"><i class="fa fa-credit-card"></i> Payments</a>
    <a href="finance_report.php" class="nav-link"><i class="fa fa-file-invoice-dollar"></i> Finance Report</a>
    <a href="bulk_import.php" class="nav-link"><i class="fa fa-upload"></i> Bulk Import</a>
    <div class="nav-section">Tools</div>
    <a href="admin_live_sessions.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions</a>
    <a href="admin_switch.php" class="nav-link"><i class="fa fa-exchange-alt"></i> Switch User</a>
    <a href="reminders.php" class="nav-link"><i class="fa fa-bell"></i> Reminders</a>
    <a href="whatsapp_messages.php" class="nav-link"><i class="fab fa-whatsapp"></i> Messages</a>
    <a href="create_admin.php" class="nav-link"><i class="fa fa-user-plus"></i> Create Admin</a>
    <a href="admin_change_password.php" class="nav-link active"><i class="fa fa-key"></i> Change Password</a>
    <div class="nav-section">Portal</div>
    <a href="admin_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main" style="max-width:560px;">

    <div class="page-title mb-1">Change Password</div>
    <p class="text-muted mb-4" style="font-size:.9rem;">Logged in as <strong><?= e($adminEmail) ?></strong></p>

    <?php if ($flashSuccess): ?>
      <div class="alert alert-success"><?= e($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="alert alert-danger"><?= e($flashError) ?></div>
    <?php endif; ?>

    <div class="lms-card">
      <form method="post" action="admin_change_password.php" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <div class="position-relative">
            <input type="password" name="current_password" id="curPwd" class="form-control" placeholder="Your current password" required>
            <button type="button" onclick="togglePwd('curPwd',this)" class="pwd-eye"><i class="fa fa-eye"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">New Password</label>
          <div class="position-relative">
            <input type="password" name="new_password" id="newPwd" class="form-control" placeholder="Min 8 chars, 1 uppercase, 1 number" required oninput="checkStrength(this.value)">
            <button type="button" onclick="togglePwd('newPwd',this)" class="pwd-eye"><i class="fa fa-eye"></i></button>
          </div>
          <div class="pwd-bar" id="pwdBar"></div>
          <div class="pwd-label text-muted" id="pwdLabel"></div>
        </div>

        <div class="mb-4">
          <label class="form-label">Confirm New Password</label>
          <div class="position-relative">
            <input type="password" name="confirm_password" id="confPwd" class="form-control" placeholder="Repeat new password" required>
            <button type="button" onclick="togglePwd('confPwd',this)" class="pwd-eye"><i class="fa fa-eye"></i></button>
          </div>
        </div>

        <button type="submit" class="btn-brand w-100 justify-content-center" style="display:flex;padding:.7rem;">
          <i class="fa fa-save me-2"></i> Update Password
        </button>
      </form>
    </div>

  </main>
</div>

<style>
.pwd-eye {
  position: absolute;
  right: .75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--muted);
  cursor: pointer;
  padding: 0;
}
</style>

<script>
function togglePwd(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}

function checkStrength(val) {
  const bar = document.getElementById('pwdBar');
  const lbl = document.getElementById('pwdLabel');
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = ['', 'weak', 'medium', 'strong', 'strong'];
  const texts  = ['', 'Weak', 'Medium', 'Strong', 'Very strong'];
  bar.className = 'pwd-bar ' + (levels[score] || '');
  lbl.textContent = texts[score] || '';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});
</script>
</body>
</html>
