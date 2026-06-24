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
<link href="assets/css/app.css?v=20260624-auth-fix" rel="stylesheet">
<style>
  .auth-wrap { background: linear-gradient(135deg, #eef2ff 0%, #e0f2fe 100%); }
  .logo-icon  { background: var(--brand); }
</style>
</head>
<body>

<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="auth-grid-wrap">
  <div class="auth-grid-container" style="max-width: 1040px;">
    
    <!-- Left Column: Instructor SVG Illustration -->
    <div class="auth-left-illustration" style="background: linear-gradient(135deg, #020617 0%, #0369a1 70%, #0f172a 100%);">
      <div class="auth-illustration-content">
        <svg class="svg-float" width="280" height="280" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- background glow -->
          <circle cx="250" cy="250" r="180" fill="#0ea5e9" fill-opacity="0.15" class="svg-glow"/>
          
          <!-- classroom board/monitor -->
          <rect x="110" y="140" width="280" height="190" rx="16" fill="#1e293b" stroke="#0ea5e9" stroke-width="5" />
          <rect x="130" y="160" width="240" height="115" rx="8" fill="#0f172a" />
          
          <!-- monitor stand -->
          <path d="M220,330 L280,330 L290,380 L210,380 Z" fill="#334155" />
          <rect x="180" y="380" width="140" height="12" rx="6" fill="#475569" />
          
          <!-- lightbulb/creativity symbol -->
          <g class="svg-float" style="animation-delay: -1s;">
            <circle cx="250" cy="110" r="35" fill="#fef08a" fill-opacity="0.2" />
            <path d="M250,70 C233.43,70 220,83.43 220,100 C220,111.85 228.6,120.35 232.7,126 L267.3,126 C271.4,120.35 280,111.85 280,100 C280,83.43 266.57,70 250,70 Z" fill="#facc15" />
            <rect x="238" y="128" width="24" height="6" rx="2" fill="#94a3b8" />
            <rect x="241" y="136" width="18" height="6" rx="2" fill="#64748b" />
          </g>
          
          <!-- chart graphics on board -->
          <rect x="150" y="180" width="60" height="8" rx="4" fill="#38bdf8" />
          <rect x="150" y="196" width="120" height="6" rx="3" fill="#475569" />
          
          <!-- grid representation -->
          <path d="M150,220 L150,260 L260,260" fill="none" stroke="#475569" stroke-width="2" />
          <path d="M150,250 Q180,220 210,240 T260,210" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" />
          
          <!-- floating tools -->
          <!-- floating star/badge -->
          <g class="svg-float" style="animation-delay: -3s;">
            <rect x="340" y="240" width="90" height="80" rx="12" fill="#0f172a" stroke="#f59e0b" stroke-width="2" />
            <polygon points="385,255 389,266 401,266 391,273 395,285 385,278 375,285 379,273 369,266 381,266" fill="#facc15" />
            <rect x="360" y="295" width="50" height="6" rx="3" fill="#475569" />
          </g>
          
          <!-- floating analytics piece -->
          <g class="svg-float" style="animation-delay: -5s;">
            <circle cx="90" cy="240" r="40" fill="#0f172a" stroke="#a855f7" stroke-width="2" />
            <!-- pie piece -->
            <path d="M90,240 L90,210 A30,30 0 0,1 120,240 Z" fill="#a855f7" />
            <circle cx="90" cy="240" r="18" fill="#0f172a" />
          </g>
        </svg>
        <h3>Empower the Next Generation</h3>
        <p>Log in to manage your classes, review assignments, host live sessions, and track student success metrics.</p>
      </div>
    </div>

    <!-- Right Column: Login Form -->
    <div class="auth-right-form-wrap">
      <div class="auth-box">
        <div class="auth-logo">
          <?= getBrandLogoSvg(38) ?>
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
