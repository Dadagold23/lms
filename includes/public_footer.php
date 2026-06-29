<?php
require_once __DIR__ . '/helpers.php';

$footerYear = $footerYear ?? date('Y');
$newsletterOk = $_SESSION['newsletter_ok'] ?? null;
$newsletterError = $_SESSION['newsletter_error'] ?? null;
unset($_SESSION['newsletter_ok'], $_SESSION['newsletter_error']);

// Dynamically determine the root prefix for links when loaded from subdirectories of any depth
$rootPrefix = '';
$dir = dirname($_SERVER['SCRIPT_FILENAME'] ?? '');
for ($i = 0; $i < 4; $i++) {
    if (file_exists($dir . '/includes/public_nav.php')) {
        break;
    }
    $rootPrefix .= '../';
    $dir = dirname($dir);
}
?>
<footer class="public-footer">
  <div class="container">
    <div class="public-footer__grid">
      <div class="public-footer__brand">
        <div class="public-footer__title">Mirror Age Concepts</div>
        <p>Practical digital, creative, and business training through Grafix@Mirror LMS.</p>
        <a href="mailto:info@mirrorageconcepts.com">info@mirrorageconcepts.com</a>
      </div>

      <div>
        <div class="public-footer__heading">Quick Access</div>
        <nav class="public-footer__links" aria-label="Footer quick access">
          <a href="<?= $rootPrefix ?>index.php">Home</a>
          <a href="<?= $rootPrefix ?>about_us.php">About Us</a>
          <a href="<?= $rootPrefix ?>register.php">Available Courses</a>
          <a href="<?= $rootPrefix ?>unitary_academy/index.php">Affiliate</a>
          <a href="<?= $rootPrefix ?>faqs.php">FAQs</a>
          <a href="<?= $rootPrefix ?>help.php">Help?</a>
          <a href="<?= $rootPrefix ?>contact_us.php">Contact Us</a>
        </nav>
      </div>

      <div>
        <div class="public-footer__heading">Portals</div>
        <nav class="public-footer__links" aria-label="Footer portal links">
          <a href="<?= $rootPrefix ?>login.php">Student Login</a>
          <a href="<?= $rootPrefix ?>instructor_login.php">Instructor Portal</a>
          <a href="<?= $rootPrefix ?>cookie_policy.php">Cookie Policy</a>
        </nav>
      </div>

      <div>
        <div class="public-footer__heading">Newsletter</div>
        <p class="public-footer__note">Get course updates, registration reminders, and learning tips.</p>
        <?php if ($newsletterOk): ?>
          <div class="public-footer__message public-footer__message--ok"><?= e($newsletterOk) ?></div>
        <?php elseif ($newsletterError): ?>
          <div class="public-footer__message public-footer__message--error"><?= e($newsletterError) ?></div>
        <?php endif; ?>
        <form class="public-footer__form" method="post" action="<?= $rootPrefix ?>newsletter_subscribe.php">
          <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
          <label class="visually-hidden" for="newsletterEmail">Email address</label>
          <input id="newsletterEmail" type="email" name="email" placeholder="Email address" required>
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>

    <div class="public-footer__bottom">
      <span>RC 3639510</span>
      <span>&copy; <?= e((string)$footerYear) ?> Mirror Age Concepts</span>
    </div>
  </div>
</footer>

