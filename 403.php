<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$year = date('Y');
try {
    $publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $publicNavCourses = [];
}

http_response_code(403);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = '403 Forbidden';
$seoDesc    = 'Access Denied to this resource on Grafix@Mirror LMS.';
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

<div class="error-page-wrap">
  <div class="error-panel">
    
    <!-- SVG illustration -->
    <svg class="svg-float mb-4" width="180" height="180" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="250" cy="250" r="180" fill="#ef4444" fill-opacity="0.1" class="svg-glow"/>
      
      <!-- Shield -->
      <path d="M250,90 L370,130 L370,260 C370,350 250,410 250,410 C250,410 130,350 130,260 L130,130 Z" fill="#1e1b4b" stroke="#ef4444" stroke-width="8" stroke-linejoin="round" />
      
      <!-- Lock Body -->
      <rect x="200" y="220" width="100" height="80" rx="14" fill="#ef4444" />
      <!-- Lock Shackle -->
      <path d="M220,220 L220,185 C220,165 233,150 250,150 C267,150 280,165 280,185 L280,220" fill="none" stroke="#ef4444" stroke-width="8" />
      <!-- Keyhole -->
      <circle cx="250" cy="255" r="8" fill="#1e1b4b" />
      <path d="M247,255 L253,255 L256,280 L244,280 Z" fill="#1e1b4b" />
    </svg>

    <div class="error-code" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">403</div>
    <h1 class="error-title">Access Forbidden</h1>
    <p class="error-desc">You do not have the required permissions to access this page or resource. Please log in with a different account or return to safety.</p>
    
    <div class="error-actions">
      <a href="index.php" class="btn-brand d-inline-flex align-items-center justify-content-center" style="padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-home me-2"></i>Go to Homepage
      </a>
      <a href="login.php" class="btn-outline-brand d-inline-flex align-items-center justify-content-center" style="border: 1px solid var(--border); color: var(--dark); padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-sign-in-alt me-2"></i>Log In Here
      </a>
    </div>
  </div>
</div>

<?php $footerYear = $year; require __DIR__ . '/includes/public_footer.php'; ?>

</body>
</html>
