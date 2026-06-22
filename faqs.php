<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$publicNavActive = 'faqs';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$faqs = [
    ['How do I register for a course?', 'Click Get Started, fill in your personal details, choose a course, and submit the registration form.'],
    ['Can I pay in installments?', 'Yes. Where installment payment is available, you can choose it during registration or course enrollment.'],
    ['Where do I find my lessons after payment?', 'After your enrollment is active, log in and open your dashboard. Your enrolled courses and lesson links will appear there.'],
    ['Do I receive a certificate?', 'Yes. Certificates are available for eligible students after completing the required course activities and assessments.'],
    ['Can I change my selected course?', 'Contact support as soon as possible. The team will review your enrollment and advise on the next step.'],
    ['I forgot my login details. What should I do?', 'Use the help page contact details so support can verify your account and assist you.'],
];
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'FAQs';
$seoDesc = 'Frequently asked questions about Grafix@Mirror courses, registration, payments, and certificates.';
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
  <div class="text-center mb-5">
    <span class="badge-brand d-inline-block mb-3">Questions</span>
    <h1 class="page-title" style="font-size:2.2rem">Frequently Asked Questions</h1>
    <p class="text-muted">Quick answers about registration, payments, learning access, and certificates.</p>
  </div>

  <div class="lms-card" style="max-width:900px;margin:0 auto">
    <div class="accordion" id="faqList">
      <?php foreach ($faqs as $idx => $faq): ?>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button <?= $idx === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $idx ?>">
              <?= e($faq[0]) ?>
            </button>
          </h2>
          <div id="faq<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqList">
            <div class="accordion-body text-muted"><?= e($faq[1]) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
