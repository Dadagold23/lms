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
    <img src="assets/img/error-404.svg" alt="Not Found Error Illustration" class="img-fluid svg-float mb-4" style="max-width: 180px;">

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
