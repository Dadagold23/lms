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

http_response_code(404);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = '404 Page Not Found';
$seoDesc    = 'The requested page was not found on Grafix@Mirror LMS.';
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
      <circle cx="250" cy="250" r="180" fill="#4f46e5" fill-opacity="0.1" class="svg-glow"/>
      
      <!-- Magnifying Glass -->
      <circle cx="210" cy="210" r="80" stroke="#4f46e5" stroke-width="8" fill="none" />
      <line x1="266" y1="266" x2="370" y2="370" stroke="#4f46e5" stroke-width="12" stroke-linecap="round" />
      
      <!-- Tiny space debris / stars -->
      <circle cx="100" cy="120" r="4" fill="#38bdf8" />
      <circle cx="380" cy="150" r="6" fill="#a855f7" />
      <circle cx="340" cy="280" r="3" fill="#6366f1" />
      <circle cx="120" cy="320" r="5" fill="#eab308" />
      
      <!-- Lost page -->
      <g class="svg-float" style="animation-delay: -3s;">
        <rect x="175" y="165" width="70" height="90" rx="10" fill="#1e1b4b" stroke="#6366f1" stroke-width="3" />
        <text x="210" y="222" font-family="'Inter', sans-serif" font-size="44" font-weight="900" fill="#a5b4fc" text-anchor="middle">?</text>
      </g>
    </svg>

    <div class="error-code">404</div>
    <h1 class="error-title">Page Not Found</h1>
    <p class="error-desc">We can't find the page you're looking for. It might have been moved, deleted, or never existed in the first place.</p>
    
    <div class="error-actions">
      <a href="index.php" class="btn-brand d-inline-flex align-items-center justify-content-center" style="padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-home me-2"></i>Go to Homepage
      </a>
      <a href="dashboard.php" class="btn-outline-brand d-inline-flex align-items-center justify-content-center" style="border: 1px solid var(--border); color: var(--dark); padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-th-large me-2"></i>Go to Dashboard
      </a>
    </div>
  </div>
</div>

<?php $footerYear = $year; require __DIR__ . '/includes/public_footer.php'; ?>

</body>
</html>
