<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$user = $_SESSION['user'] ?? null;
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Cookie Policy';
$seoDesc    = 'Cookie policy for Grafix@Mirror LMS by Mirror Age Concepts.';
$seoNoIndex = false;
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

<main class="container py-5">
  <section class="lms-card" style="max-width:900px;margin:0 auto">
    <span class="badge-brand d-inline-block mb-3">Privacy</span>
    <h1 class="page-title" style="font-size:2rem">Cookie Policy</h1>
    <p class="text-muted">Last updated: June 6, 2026</p>

    <div class="mt-4">
      <h2 class="section-title">How We Use Cookies</h2>
      <p>Grafix@Mirror LMS uses cookies and similar browser storage to run the learning platform, keep user sessions secure, and remember basic preferences.</p>
    </div>

    <div class="mt-4">
      <h2 class="section-title">Essential Cookies</h2>
      <p>Essential cookies are required for login sessions, account protection, course access, payment flow continuity, and remembering your cookie choice. These cookies cannot be switched off through the cookie banner because the site depends on them to work correctly.</p>
    </div>

    <div class="mt-4">
      <h2 class="section-title">Optional Cookies</h2>
      <p>The LMS may use optional cookies only when features such as analytics, embedded content, or marketing integrations are added. If optional cookies are introduced, the site should request your consent before using them.</p>
    </div>

    <div class="mt-4">
      <h2 class="section-title">Managing Cookies</h2>
      <p>You can clear or block cookies from your browser settings. Blocking essential cookies may prevent login, course access, payments, or other LMS features from working properly.</p>
    </div>

    <div class="mt-4">
      <h2 class="section-title">Contact</h2>
      <p>For questions about cookies or privacy, contact Mirror Age Concepts at <a href="mailto:info@mirrorageconcepts.com">info@mirrorageconcepts.com</a>.</p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
