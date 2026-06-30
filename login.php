<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
startSecureSession();
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
<link href="assets/css/app.css?v=20260624-auth-fix" rel="stylesheet">
</head>
<body>

<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="auth-grid-wrap">
  <div class="auth-grid-container">
    
    <!-- Left Column: SVG Illustration -->
    <div class="auth-left-illustration">
      <div class="auth-illustration-content">
        <svg class="svg-float" width="280" height="280" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- background glow -->
          <circle cx="250" cy="250" r="180" fill="#4f46e5" fill-opacity="0.15" class="svg-glow"/>
          
          <!-- main platform shape -->
          <rect x="120" y="160" width="260" height="200" rx="20" fill="#1e1b4b" stroke="#4f46e5" stroke-width="6" />
          <rect x="150" y="190" width="200" height="110" rx="8" fill="#0f172a" />
          
          <!-- graduation cap -->
          <path d="M250,90 L380,140 L250,190 L120,140 Z" fill="#6366f1" />
          <path d="M190,165 L190,220 C190,235 310,235 310,220 L310,165" fill="none" stroke="#6366f1" stroke-width="6" stroke-linecap="round" />
          <path d="M340,155 L340,240 C340,245 330,250 330,250" fill="none" stroke="#f59e0b" stroke-width="4" />
          <circle cx="330" cy="250" r="6" fill="#f59e0b" />
          
          <!-- mock browser dots -->
          <circle cx="170" cy="210" r="6" fill="#ef4444" />
          <circle cx="190" cy="210" r="6" fill="#eab308" />
          <circle cx="210" cy="210" r="6" fill="#22c55e" />
          
          <!-- code lines -->
          <rect x="170" y="235" width="100" height="8" rx="4" fill="#475569" />
          <rect x="170" y="255" width="140" height="8" rx="4" fill="#475569" />
          <rect x="170" y="275" width="70" height="8" rx="4" fill="#475569" />
          
          <!-- floating chart card -->
          <g class="svg-float" style="animation-delay: -2s;">
            <rect x="330" y="260" width="100" height="90" rx="14" fill="#0c4a6e" stroke="#0ea5e9" stroke-width="3" />
            <path d="M350,320 L370,300 L390,310 L410,280" fill="none" stroke="#38bdf8" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="410" cy="280" r="4" fill="#38bdf8" />
          </g>
          
          <!-- floating palette card -->
          <g class="svg-float" style="animation-delay: -4s;">
            <circle cx="90" cy="280" r="50" fill="#2e1065" stroke="#a855f7" stroke-width="3" />
            <circle cx="75" cy="265" r="10" fill="#ec4899" />
            <circle cx="105" cy="265" r="10" fill="#3b82f6" />
            <circle cx="75" cy="295" r="10" fill="#eab308" />
            <circle cx="105" cy="295" r="10" fill="#10b981" />
          </g>
        </svg>
        <h3>Start Your Tech Journey</h3>
        <p>Access your student portal to continue learning and building your digital skills portfolio.</p>
      </div>
    </div>

    <!-- Right Column: Login Form -->
    <div class="auth-right-form-wrap">
      <div class="auth-box">
        <!-- Logo -->
        <div class="auth-logo">
          <?= getBrandLogoSvg(38) ?>
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
