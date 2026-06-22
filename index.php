<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';
$user     = $_SESSION['user'] ?? null;
$siteName = 'Mirror Age Concepts';
$year     = date('Y');
$courses  = [
  ['🎨','Graphic Design',      'Photoshop, Illustrator, branding & print design.'],
  ['🌐','Web Design',          'HTML, CSS, Bootstrap, responsive UI/UX.'],
  ['💻','Web Development',     'PHP, MySQL, MVC, APIs, and deployment.'],
  ['📱','Mobile App Dev',      'Android fundamentals & real-world apps.'],
  ['📣','Digital Marketing',   'SEO, ads, content strategy & analytics.'],
  ['🖥️','Computer Fundamentals','ICT basics, productivity tools & digital literacy.'],
];
$publicNavActive = 'home';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$assetImagePaths = glob(__DIR__ . '/assets/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
usort($assetImagePaths, static function (string $a, string $b): int {
  $priority = ['hero-classroom.jpg', 'best-lms-software-company.jpg', 'learning-management-system-featured-image-riseuplabs.jpg'];
  $aName = basename($a);
  $bName = basename($b);
  $aRank = array_search($aName, $priority, true);
  $bRank = array_search($bName, $priority, true);
  $aRank = $aRank === false ? 99 : $aRank;
  $bRank = $bRank === false ? 99 : $bRank;
  return $aRank === $bRank ? strcasecmp($aName, $bName) : $aRank <=> $bRank;
});
$homeSlides = array_map(static function (string $path): array {
  $file = basename($path);
  $label = ucwords(trim((string)preg_replace('/[-_]+/', ' ', pathinfo($file, PATHINFO_FILENAME))));
  return ['src' => 'assets/' . $file, 'label' => $label];
}, $assetImagePaths);
$courseImages = array_slice($homeSlides, 0, max(1, count($courses)));
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Home';
$seoDesc    = 'Welcome to Grafix@Mirror LMS — Mirror Age Concepts professional technology training. Enrol in Data Science, AI, Web Development, Cybersecurity, Cloud Computing and more.';
$seoNoIndex = false;
require_once __DIR__ . '/includes/seo.php';
?>
<title>Grafix@Mirror LMS | <?= htmlspecialchars($siteName) ?></title>
<meta name="description" content="Grafix@Mirror LMS delivers digital learning, creative design training, and skill-based education.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css?v=20260607-nav2" rel="stylesheet">
<style>
  .home-hero-slider { position:relative; min-height:680px; color:#fff; overflow:hidden; background:#07111f; }
  .home-hero-slider .carousel,
  .home-hero-slider .carousel-inner,
  .home-hero-slider .carousel-item { min-height:680px; }
  .home-hero-slider .carousel-item img { width:100%; min-height:680px; object-fit:cover; filter:saturate(1.05) contrast(1.05); }
  .home-hero-slider .carousel-item::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(90deg,rgba(7,17,31,.92) 0%,rgba(7,17,31,.76) 38%,rgba(7,17,31,.3) 72%,rgba(7,17,31,.58) 100%);
  }
  .home-hero-content { position:absolute; inset:0; z-index:3; display:flex; align-items:center; padding:5rem 0 4rem; }
  .home-hero-copy { max-width:720px; }
  .home-hero-copy h1 { font-size:clamp(2.2rem,5vw,4.4rem); line-height:1.04; font-weight:800; margin:0; }
  .home-hero-copy h1 span { color:#67e8f9; }
  .home-hero-copy .lead { color:#dbeafe; max-width:610px; }
  .home-slider-badge { display:inline-flex; align-items:center; gap:.45rem; background:rgba(103,232,249,.13); border:1px solid rgba(103,232,249,.35); color:#cffafe; border-radius:999px; padding:.45rem 1rem; font-size:.82rem; font-weight:700; margin-bottom:1.2rem; }
  .home-slide-caption { position:absolute; right:2rem; bottom:2rem; z-index:4; max-width:360px; color:#fff; background:rgba(7,17,31,.56); border:1px solid rgba(255,255,255,.16); border-radius:12px; padding:1rem; backdrop-filter:blur(10px); }
  .home-slide-caption strong { display:block; font-size:.95rem; }
  .home-slide-caption span { color:#cbd5e1; font-size:.82rem; }
  .home-hero-slider .carousel-indicators { z-index:5; margin-bottom:1.5rem; }
  .home-hero-slider .carousel-indicators [data-bs-target] { width:34px; height:4px; border-radius:999px; }
  .asset-strip { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:.75rem; }
  .asset-tile { position:relative; aspect-ratio:16/10; overflow:hidden; border-radius:10px; background:#0f172a; box-shadow:var(--shadow); }
  .asset-tile img { width:100%; height:100%; object-fit:cover; transition:transform .3s ease; }
  .asset-tile:hover img { transform:scale(1.05); }
  .asset-tile span { position:absolute; left:.55rem; right:.55rem; bottom:.55rem; color:#fff; font-size:.74rem; font-weight:700; text-shadow:0 1px 8px rgba(0,0,0,.7); }
  .course-card .course-thumb.course-thumb-image { height:155px; background:#0f172a; }
  .course-thumb-image img { width:100%; height:100%; object-fit:cover; }
  @media (max-width: 768px) {
    .home-hero-slider,
    .home-hero-slider .carousel,
    .home-hero-slider .carousel-inner,
    .home-hero-slider .carousel-item,
    .home-hero-slider .carousel-item img { min-height:720px; }
    .home-hero-content { padding:4rem 0 6rem; align-items:flex-end; }
    .home-slide-caption { left:1rem; right:1rem; bottom:1rem; max-width:none; }
  }
</style>
</head>
<body class="home-page">

<!-- NAVBAR -->
<?php require __DIR__ . '/includes/public_nav.php'; ?>

<!-- HERO SLIDER -->
<section class="home-hero-slider">
  <?php if (!empty($homeSlides)): ?>
    <div id="homeAssetCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5200">
      <div class="carousel-indicators">
        <?php foreach ($homeSlides as $idx => $slide): ?>
          <button type="button" data-bs-target="#homeAssetCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <div class="carousel-inner">
        <?php foreach ($homeSlides as $idx => $slide): ?>
          <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($slide['src']) ?>" alt="<?= htmlspecialchars($slide['label']) ?>">
            <div class="home-slide-caption d-none d-md-block">
              <strong><?= htmlspecialchars($slide['label']) ?></strong>
              <span>Learning visuals from the Grafix@Mirror LMS media library.</span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#homeAssetCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#homeAssetCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  <?php endif; ?>
  <div class="home-hero-content">
    <div class="container">
      <div class="home-hero-copy">
        <div class="home-slider-badge"><i class="fa fa-layer-group"></i><?= count($homeSlides) ?> LMS visuals in rotation</div>
        <h1>Learn Skills That <span>Actually Matter</span></h1>
        <p class="lead mt-3 mb-4">
          Structured courses in design, development, and digital marketing, taught by industry professionals at Mirror Age Concepts.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <?php if (!$user): ?>
            <a href="register.php" class="btn-brand" style="padding:.75rem 1.75rem;font-size:1rem">
              <i class="fa fa-rocket"></i> Enroll Now
            </a>
            <a href="login.php" class="btn-outline-brand" style="padding:.75rem 1.75rem;font-size:1rem;border-color:rgba(255,255,255,.45);color:#fff">
              Login
            </a>
          <?php else: ?>
            <a href="dashboard.php" class="btn-brand" style="padding:.75rem 1.75rem;font-size:1rem">
              <i class="fa fa-th-large"></i> Go to Dashboard
            </a>
          <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-3 mt-4" style="color:#bfdbfe;font-size:.88rem">
          <span><i class="fa fa-check-circle me-1" style="color:#67e8f9"></i>Practical Training</span>
          <span><i class="fa fa-check-circle me-1" style="color:#67e8f9"></i>Certified Courses</span>
          <span><i class="fa fa-check-circle me-1" style="color:#67e8f9"></i>Expert Instructors</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LEGACY HERO -->
<section class="lms-hero d-none">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7" data-aos="fade-right">
        <h1>Learn Skills That <span>Actually Matter</span></h1>
        <p class="lead mt-3 mb-4">
          Structured courses in design, development, and digital marketing — taught by industry professionals at Mirror Age Concepts.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <?php if (!$user): ?>
            <a href="register.php" class="btn-brand" style="padding:.75rem 1.75rem;font-size:1rem">
              <i class="fa fa-rocket"></i> Enroll Now
            </a>
            <a href="login.php" class="btn-outline-brand" style="padding:.75rem 1.75rem;font-size:1rem;border-color:rgba(255,255,255,.4);color:#fff">
              Login
            </a>
          <?php else: ?>
            <a href="dashboard.php" class="btn-brand" style="padding:.75rem 1.75rem;font-size:1rem">
              <i class="fa fa-th-large"></i> Go to Dashboard
            </a>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-4 mt-4" style="color:#94a3b8;font-size:.85rem">
          <span><i class="fa fa-check-circle me-1" style="color:#a5b4fc"></i>Practical Training</span>
          <span><i class="fa fa-check-circle me-1" style="color:#a5b4fc"></i>Certified Courses</span>
          <span><i class="fa fa-check-circle me-1" style="color:#a5b4fc"></i>Expert Instructors</span>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left">
        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:2rem">
          <div class="d-flex flex-wrap gap-2">
            <?php foreach ($courses as $c): ?>
              <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.6rem 1rem;font-size:.82rem;color:#e2e8f0">
                <?= $c[0] ?> <?= htmlspecialchars($c[1]) ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.1);color:#94a3b8;font-size:.8rem">
            <i class="fa fa-users me-1"></i> Join hundreds of students already learning
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LMS MEDIA STRIP -->
<?php if (!empty($homeSlides)): ?>
<section class="py-4" style="background:#fff;border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
      <div>
        <div class="badge-brand d-inline-block mb-2">LMS Media Library</div>
        <h2 class="section-title mb-0">Visual learning environment</h2>
      </div>
      <span class="text-muted" style="font-size:.85rem"><?= count($homeSlides) ?> images from assets</span>
    </div>
    <div class="asset-strip">
      <?php foreach ($homeSlides as $slide): ?>
        <a class="asset-tile" href="<?= htmlspecialchars($slide['src']) ?>" target="_blank" rel="noopener">
          <img src="<?= htmlspecialchars($slide['src']) ?>" alt="<?= htmlspecialchars($slide['label']) ?>">
          <span><?= htmlspecialchars($slide['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- COURSES -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <div class="badge-brand d-inline-block mb-2">Learning Programs</div>
      <h2 class="page-title" style="font-size:2rem">Our Courses</h2>
      <p class="text-muted">Industry-relevant programs designed for real-world impact</p>
    </div>
    <div class="row g-4">
      <?php foreach ($courses as $idx => $c): ?>
      <?php $courseImage = !empty($courseImages) ? ($courseImages[$idx % count($courseImages)] ?? null) : null; ?>
      <div class="col-md-4 col-sm-6">
        <div class="course-card h-100">
          <div class="course-thumb course-thumb-image">
            <?php if ($courseImage): ?>
              <img src="<?= htmlspecialchars($courseImage['src']) ?>" alt="<?= htmlspecialchars($c[1]) ?>">
            <?php else: ?>
              <span style="font-size:2.5rem"><?= $c[0] ?></span>
            <?php endif; ?>
          </div>
          <div class="course-body">
            <div class="course-title"><?= htmlspecialchars($c[1]) ?></div>
            <p class="course-price mb-0"><?= htmlspecialchars($c[2]) ?></p>
          </div>
          <div class="course-footer">
            <a href="register.php" class="btn-brand w-100 justify-content-center" style="display:flex">Enroll Now</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="py-5" style="background:var(--brand-light)">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="badge-brand d-inline-block mb-2">Why Choose Us</div>
        <h2 class="page-title" style="font-size:1.9rem">Built for Practical Learning</h2>
        <p class="text-muted mt-2">We combine structured curriculum with hands-on projects so you graduate job-ready.</p>
      </div>
      <div class="col-lg-7">
        <div class="row g-3">
          <?php
          $features = [
            ['fa-graduation-cap','Certified Courses','Earn verifiable certificates on completion.'],
            ['fa-chalkboard-teacher','Expert Instructors','Learn from industry professionals.'],
            ['fa-credit-card','Flexible Payments','Full payment or installment plans available.'],
            ['fa-laptop','Online Access','Study at your own pace, anytime.'],
          ];
          foreach ($features as $f): ?>
          <div class="col-sm-6">
            <div class="lms-card lms-card-sm d-flex gap-3 align-items-start">
              <div class="stat-icon purple" style="width:40px;height:40px;border-radius:8px;flex-shrink:0">
                <i class="fa <?= $f[0] ?>"></i>
              </div>
              <div>
                <div style="font-weight:700;font-size:.9rem"><?= $f[1] ?></div>
                <div class="text-muted" style="font-size:.82rem"><?= $f[2] ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5" style="background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff">
  <div class="container text-center">
    <h2 style="font-weight:800;font-size:2rem">Ready to Start Learning?</h2>
    <p style="color:#c7d2fe;margin-bottom:2rem">Join Mirror Age Concepts and build skills that open doors.</p>
    <?php if (!$user): ?>
      <a href="register.php" class="btn-brand me-2" style="background:#fff;color:var(--brand);padding:.75rem 2rem;font-size:1rem">
        <i class="fa fa-user-plus"></i> Register Free
      </a>
      <a href="login.php" style="color:#c7d2fe;font-size:.95rem">Already have an account? Login →</a>
    <?php else: ?>
      <a href="dashboard.php" class="btn-brand" style="background:#fff;color:var(--brand);padding:.75rem 2rem;font-size:1rem">
        Open Dashboard
      </a>
    <?php endif; ?>
  </div>
</section>

<?php $footerYear = $year; require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
