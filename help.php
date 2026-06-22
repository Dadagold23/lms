<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$publicNavActive = 'help';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Help';
$seoDesc = 'Get help with registration, login, payments, lessons, and certificates on Grafix@Mirror.';
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

<main>
  <section class="py-5" style="background:#fff">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="badge-brand d-inline-block mb-3">Support</span>
          <h1 class="page-title" style="font-size:2.25rem">How can we help?</h1>
          <p class="text-muted" style="line-height:1.8">Use this page when you need support with registration, login, payment confirmation, course access, assignments, exams, or certificates.</p>
          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="mailto:info@mirrorageconcepts.com" class="btn-brand"><i class="fa fa-envelope"></i> Email Support</a>
            <a href="login.php" class="btn-ghost">Student Login</a>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="lms-card">
            <h2 class="section-title">Before You Contact Support</h2>
            <ul class="text-muted mb-0" style="line-height:1.9">
              <li>Use the same email address you registered with.</li>
              <li>Include the course name if your issue is course-related.</li>
              <li>For payment issues, include the payment date and reference if available.</li>
              <li>Describe what you tried and what message appeared on screen.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5" style="background:var(--surface)">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4"><div class="lms-card h-100"><i class="fa fa-user-plus mb-3" style="color:var(--brand);font-size:1.4rem"></i><h3 class="section-title">Registration</h3><p class="text-muted mb-0">Get help choosing a course, completing your profile, or correcting registration details.</p></div></div>
        <div class="col-md-4"><div class="lms-card h-100"><i class="fa fa-credit-card mb-3" style="color:var(--brand);font-size:1.4rem"></i><h3 class="section-title">Payments</h3><p class="text-muted mb-0">Ask for help with payment confirmation, installment questions, or course activation.</p></div></div>
        <div class="col-md-4"><div class="lms-card h-100"><i class="fa fa-book-open mb-3" style="color:var(--brand);font-size:1.4rem"></i><h3 class="section-title">Learning Access</h3><p class="text-muted mb-0">Report issues with lessons, videos, assignments, exams, or certificate access.</p></div></div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
