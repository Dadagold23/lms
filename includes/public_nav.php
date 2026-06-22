<?php
require_once __DIR__ . '/guard.php';
$publicNavCourses = $publicNavCourses ?? [];
$publicNavActive = $publicNavActive ?? '';
$publicNavUser = $_SESSION['user'] ?? null;
$currentPage = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));

if ($publicNavActive === '') {
    $publicNavActive = match ($currentPage) {
        'index.php' => 'home',
        'about_us.php' => 'about',
        'register.php' => 'courses',
        'faqs.php' => 'faqs',
        'help.php' => 'help',
        'contact_us.php' => 'contact',
        default => '',
    };
}
?>
<nav class="lms-nav public-nav">
  <div class="container public-nav__inner">
    <a href="index.php" class="brand text-decoration-none">
      <div class="public-nav__mark">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>

    <div class="public-nav__menu" id="publicNavMenu">
      <a class="public-nav__link <?= $publicNavActive === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
      <a class="public-nav__link <?= $publicNavActive === 'about' ? 'active' : '' ?>" href="about_us.php">About Us</a>

      <div class="public-nav__dropdown" data-public-nav-dropdown>
        <button class="public-nav__link public-nav__dropdown-toggle <?= $publicNavActive === 'courses' ? 'active' : '' ?>" type="button" data-public-nav-dropdown-toggle aria-expanded="false">
          Available Courses <i class="fa fa-chevron-down"></i>
        </button>
        <div class="public-nav__dropdown-menu" data-public-nav-dropdown-menu>
          <?php if (!empty($publicNavCourses)): ?>
            <?php foreach ($publicNavCourses as $course): ?>
              <a href="register.php?course_id=<?= (int)($course['id'] ?? 0) ?>">
                <?= e($course['title'] ?? 'Course') ?>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <a href="register.php">View Courses</a>
          <?php endif; ?>
        </div>
      </div>

      <a class="public-nav__link <?= $publicNavActive === 'faqs' ? 'active' : '' ?>" href="faqs.php">FAQs</a>
      <a class="public-nav__link <?= $publicNavActive === 'help' ? 'active' : '' ?>" href="help.php">Help?</a>
    </div>

    <div class="public-nav__actions">
      <?php if (!$publicNavUser): ?>
        <a href="login.php" class="btn-ghost btn-sm">Login</a>
        <a href="register.php" class="btn-brand">Get Started</a>
      <?php else: ?>
        <?php
          $dashboardUrl = 'dashboard.php';
          if (hasRole('admin')) {
              $dashboardUrl = 'admin_dashboard.php';
          } elseif (hasRole('instructor')) {
              $dashboardUrl = 'instructor_dashboard.php';
          }
        ?>
        <a href="<?= $dashboardUrl ?>" class="btn-brand">Dashboard</a>
      <?php endif; ?>
    </div>

    <button class="public-nav__toggle" type="button" data-public-nav-toggle aria-expanded="false" aria-controls="publicNavMenu" aria-label="Open navigation">
      <i class="fa fa-bars"></i>
    </button>
  </div>
</nav>
<script>
(function () {
  if (window.publicNavReady) return;
  window.publicNavReady = true;

  function closeDropdowns(except) {
    document.querySelectorAll('[data-public-nav-dropdown]').forEach(function (dropdown) {
      if (dropdown === except) return;
      dropdown.classList.remove('open');
      dropdown.querySelector('[data-public-nav-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  }

  document.querySelectorAll('[data-public-nav-toggle]').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      const menu = document.getElementById(toggle.getAttribute('aria-controls'));
      const isOpen = menu?.classList.toggle('open') || false;
      toggle.setAttribute('aria-expanded', String(isOpen));
      closeDropdowns();
    });
  });

  document.querySelectorAll('[data-public-nav-dropdown-toggle]').forEach(function (toggle) {
    toggle.addEventListener('click', function (event) {
      event.stopPropagation();
      const dropdown = toggle.closest('[data-public-nav-dropdown]');
      const isOpen = dropdown?.classList.toggle('open') || false;
      toggle.setAttribute('aria-expanded', String(isOpen));
      closeDropdowns(dropdown);
    });
  });

  document.addEventListener('click', function (event) {
    if (!event.target.closest('.public-nav')) {
      closeDropdowns();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeDropdowns();
      document.getElementById('publicNavMenu')?.classList.remove('open');
      document.querySelector('[data-public-nav-toggle]')?.setAttribute('aria-expanded', 'false');
    }
  });
}());
</script>
