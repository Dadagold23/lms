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

http_response_code(500);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = '500 Server Error';
$seoDesc    = 'An internal server error occurred on Grafix@Mirror LMS.';
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
      <circle cx="250" cy="250" r="180" fill="#f59e0b" fill-opacity="0.1" class="svg-glow"/>
      
      <!-- Big Gear -->
      <g class="svg-float">
        <circle cx="200" cy="200" r="60" fill="none" stroke="#f59e0b" stroke-width="12" />
        <circle cx="200" cy="200" r="20" fill="none" stroke="#f59e0b" stroke-width="8" />
        <!-- Teeth -->
        <path d="M200,120 L200,140 M200,260 L200,280 M120,200 L140,200 M260,200 L280,200 M143,143 L157,157 M243,243 L257,257 M143,257 L157,243 M243,143 L257,157" stroke="#f59e0b" stroke-width="12" stroke-linecap="round" />
      </g>
      
      <!-- Small Gear -->
      <g class="svg-float" style="animation-delay: -3s;">
        <circle cx="310" cy="290" r="40" fill="none" stroke="#eab308" stroke-width="10" />
        <circle cx="310" cy="290" r="12" fill="none" stroke="#eab308" stroke-width="6" />
        <!-- Teeth -->
        <path d="M310,235 L310,250 M310,330 L310,345 M255,290 L270,290 M350,290 L365,290 M271,251 L282,262 M338,318 L349,329 M271,329 L282,318 M338,262 L349,251" stroke="#eab308" stroke-width="10" stroke-linecap="round" />
      </g>
      
      <!-- Warning exclamation mark inside small shield -->
      <g transform="translate(370, 160)" class="svg-float" style="animation-delay: -1.5s;">
        <polygon points="0,-25 22,15 -22,15" fill="#ef4444" stroke="#ffffff" stroke-width="3" stroke-linejoin="round" />
        <text x="0" y="8" font-family="sans-serif" font-size="24" font-weight="900" fill="#ffffff" text-anchor="middle">!</text>
      </g>
    </svg>

    <div class="error-code" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">500</div>
    <h1 class="error-title">Internal Server Error</h1>
    <p class="error-desc">Oops! Something went wrong on our servers. We have logged this incident and our technical team is investigating it. Please try again later.</p>
    
    <div class="error-actions">
      <a href="index.php" class="btn-brand d-inline-flex align-items-center justify-content-center" style="padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-home me-2"></i>Go to Homepage
      </a>
      <a href="contact_us.php" class="btn-outline-brand d-inline-flex align-items-center justify-content-center" style="border: 1px solid var(--border); color: var(--dark); padding: .65rem 1.5rem; font-size: .95rem;">
        <i class="fa fa-envelope me-2"></i>Contact Support
      </a>
    </div>
  </div>
</div>

<?php $footerYear = $year; require __DIR__ . '/includes/public_footer.php'; ?>

</body>
</html>
