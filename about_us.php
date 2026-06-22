<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$publicNavActive = 'about';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'About Us';
$seoDesc = 'Learn about Mirror Age Concepts and the Grafix@Mirror learning platform.';
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
          <span class="badge-brand d-inline-block mb-3">About Mirror Age Concepts</span>
          <h1 class="page-title" style="font-size:2.35rem">Practical training for people building real careers.</h1>
          <p class="text-muted mt-3" style="font-size:1rem;line-height:1.8">
            Mirror Age Concepts helps learners gain useful digital, creative, and business skills through guided lessons, hands-on projects, and supportive instruction. Our classes are built for people who want more than theory: they want to practise, improve, and leave with work they can show.
          </p>
          <p class="text-muted" style="font-size:1rem;line-height:1.8">
            Grafix@Mirror brings that training into a structured online space where students can register, learn at their own pace, complete assignments, make payments, and track their progress from one place.
          </p>
        </div>
        <div class="col-lg-6">
          <div class="lms-card">
            <h2 class="section-title">What We Stand For</h2>
            <div class="d-grid gap-3">
              <div><strong>Practical learning</strong><p class="text-muted mb-0">Every course is shaped around skills students can use in school, work, business, or personal projects.</p></div>
              <div><strong>Clear guidance</strong><p class="text-muted mb-0">Lessons are organised so students know what to do next and how each step connects to the bigger goal.</p></div>
              <div><strong>Reliable support</strong><p class="text-muted mb-0">Students can ask questions, receive feedback, and continue learning with confidence.</p></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5" style="background:var(--surface)">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4"><div class="lms-card h-100"><h3 class="section-title">Our Approach</h3><p class="text-muted mb-0">We combine instructor-led direction with exercises, projects, and steady review so learning becomes practical and measurable.</p></div></div>
        <div class="col-md-4"><div class="lms-card h-100"><h3 class="section-title">Our Learners</h3><p class="text-muted mb-0">We serve beginners, working professionals, entrepreneurs, and young people preparing for stronger opportunities.</p></div></div>
        <div class="col-md-4"><div class="lms-card h-100"><h3 class="section-title">Our Promise</h3><p class="text-muted mb-0">We keep training useful, honest, and focused on growth students can recognise in their own work.</p></div></div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
