<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['instructor'])) redirect('instructor_dashboard.php');

$error = $_SESSION['instructor_login_error'] ?? null;
unset($_SESSION['instructor_login_error']);
$year = date('Y');
$footerYear = $year;
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Login';
$seoDesc    = 'Instructor login for Grafix@Mirror LMS — Mirror Age Concepts.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  .auth-wrap { background: linear-gradient(135deg, #eef2ff 0%, #e0f2fe 100%); }
  .logo-icon  { background: var(--brand); }
</style>
</head>
<body>

<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="auth-wrap">
  <div style="width:100%;max-width:440px">



    <div class="auth-box">
      <div class="auth-logo">
        <div class="logo-icon"><i class="fa fa-chalkboard-teacher" style="font-size:1.1rem"></i></div>
        <div class="logo-text">Instructor <span>Portal</span></div>
      </div>

      <div class="auth-title">Instructor Sign In</div>
      <div class="auth-sub">Access your teaching dashboard</div>

      <?php if ($error): ?>
        <div class="lms-alert lms-alert-danger">
          <i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="instructor_login_handler.php" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="instructor@example.com" required autofocus>
        </div>

        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="position-relative">
            <input type="password" name="password" id="insPwd" class="form-control" placeholder="••••••••" required>
            <button type="button" onclick="togglePwd('insPwd',this)"
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
        Need an account? <a href="instructor_register.php" style="font-weight:600">Register as Instructor</a>
      </div>

      <div style="border-top:1px solid var(--border);margin-top:1.5rem;padding-top:1rem;display:flex;justify-content:center;gap:1.5rem;font-size:.8rem">
        <a href="login.php" style="color:var(--muted)"><i class="fa fa-user me-1"></i>Student Login</a>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

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
