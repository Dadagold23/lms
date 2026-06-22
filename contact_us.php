<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Contact Us';
$seoDesc = 'Contact Mirror Age Concepts for course registration, payments, learning support, and general enquiries.';
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
          <span class="badge-brand d-inline-block mb-3">Contact Us</span>
          <h1 class="page-title" style="font-size:2.25rem">We are ready to help.</h1>
          <p class="text-muted" style="line-height:1.8">
            Reach out for course enquiries, registration guidance, payment support, certificate questions, or help using Grafix@Mirror LMS.
          </p>
          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="mailto:info@mirrorageconcepts.com" class="btn-brand"><i class="fa fa-envelope"></i> Send Email</a>
            <a href="register.php" class="btn-ghost">Choose a Course</a>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="lms-card">
            <h2 class="section-title">Contact Details</h2>
            <div class="d-grid gap-3">
              <div>
                <div style="font-weight:800;color:var(--dark)">Email</div>
                <a href="mailto:info@mirrorageconcepts.com">info@mirrorageconcepts.com</a>
              </div>
              <div>
                <div style="font-weight:800;color:var(--dark)">Organisation</div>
                <p class="text-muted mb-0">Mirror Age Concepts, RC 3639510</p>
              </div>
              <div>
                <div style="font-weight:800;color:var(--dark)">Support Areas</div>
                <p class="text-muted mb-0">Registration, course access, payments, assignments, exams, certificates, and account support.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
