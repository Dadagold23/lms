<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user']) || !empty($_SESSION['admin']) || !empty($_SESSION['instructor'])) {
    if (hasRole('admin')) {
        redirect('admin_dashboard.php');
    } elseif (hasRole('instructor')) {
        redirect('instructor_dashboard.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
$year = date('Y');
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Student Login';
$seoDesc    = 'Login to your Grafix@Mirror LMS student dashboard to access your courses, lessons, assignments and exams.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css?v=20260607-nav2" rel="stylesheet">
</head>
<body>

<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="auth-wrap">
  <div style="width:100%;max-width:460px">

    <div class="auth-box">
      <!-- Logo -->
      <div class="auth-logo">
        <div class="logo-icon">G</div>
        <div class="logo-text">Grafix<span>@Mirror</span> LMS</div>
      </div>

      <div class="auth-title">Welcome back</div>
      <div class="auth-sub">Sign in to your student account</div>

      <?php if ($error): ?>
        <div class="lms-alert lms-alert-danger">
          <i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="login_handler.php" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
        </div>

        <div class="mb-3">
          <label class="form-label d-flex justify-content-between">
            Password
            <a href="forgot_password.php" style="font-weight:400;font-size:.82rem">Forgot password?</a>
          </label>
          <div class="position-relative">
            <input type="password" name="password" id="loginPwd" class="form-control" placeholder="••••••••" required>
            <button type="button" onclick="togglePwd('loginPwd',this)"
              style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:0">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-brand w-100 justify-content-center" style="display:flex;padding:.7rem;font-size:.95rem">
          <i class="fa fa-sign-in-alt"></i> Sign In
        </button>
      </form>

      <div style="text-align:center;margin-top:1.25rem;font-size:.85rem;color:var(--muted)">
        Don't have an account? <a href="register.php" style="font-weight:600">Register here</a>
      </div>

      <div style="border-top:1px solid var(--border);margin-top:1.5rem;padding-top:1rem;display:flex;justify-content:center;gap:1.5rem;font-size:.8rem">
        <a href="instructor_login.php" style="color:var(--muted)"><i class="fa fa-chalkboard-teacher me-1"></i>Instructor Portal</a>
      </div>
    </div>
  </div>
</div>

<?php $footerYear = $year; require __DIR__ . '/includes/public_footer.php'; ?>

<script>
function togglePwd(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}
</script>
</body>
</html>
