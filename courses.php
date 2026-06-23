<?php
declare(strict_types=1);

/* ======================
   BOOTSTRAP
====================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

/* ======================
   AUTH
====================== */
requireLogin();

/* ======================
   FETCH COURSES
====================== */
$stmt = $pdo->query("
    SELECT id, title, description, price, intro_video, created_at
    FROM lms_courses
    ORDER BY id DESC
");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   FLASH
====================== */
$okMsg = $_SESSION['ok'] ?? null;
$errMsg = $_SESSION['err'] ?? null;
unset($_SESSION['ok'], $_SESSION['err']);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Browse Courses';
$seoDesc    = 'Browse all professional technology courses at Mirror Age Concepts — Data Science, AI, Machine Learning, Web Development, Cybersecurity, Cloud Computing and more.';
$seoNoIndex = false;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body{background:#f7fbff;font-family:Inter,system-ui}
  .card{border-radius:14px}
  .video-box{max-height:180px;object-fit:cover}
</style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <span class="navbar-brand fw-bold text-primary">Available Courses</span>
    <div class="ms-auto d-flex gap-2">
      <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <?php if ($okMsg): ?>
    <div class="alert alert-success"><?= e($okMsg) ?></div>
  <?php endif; ?>

  <?php if ($errMsg): ?>
    <div class="alert alert-danger"><?= e($errMsg) ?></div>
  <?php endif; ?>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Choose a course</h4>
    <form method="get" class="d-flex gap-2">
      <input
        type="search"
        name="q"
        value="<?= e($_GET['q'] ?? '') ?>"
        class="form-control form-control-sm"
        placeholder="Search courses..."
        style="max-width:260px"
      >
      <button class="btn btn-sm btn-primary">Search</button>
    </form>
  </div>

  <?php
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') {
        $courses = array_values(array_filter($courses, function($c) use ($q) {
            $t = mb_strtolower((string)($c['title'] ?? ''));
            $d = mb_strtolower((string)($c['description'] ?? ''));
            $qq = mb_strtolower($q);
            return str_contains($t, $qq) || str_contains($d, $qq);
        }));
    }
  ?>

  <?php if (empty($courses)): ?>
    <div class="card p-4">
      <h6 class="mb-1">No courses found</h6>
      <p class="text-muted mb-0">
        <?php if ($q !== ''): ?>
          Try a different search term.
        <?php else: ?>
          Admin needs to add courses in the Course Management page.
        <?php endif; ?>
      </p>
    </div>
  <?php else: ?>

    <div class="row g-4">
      <?php foreach ($courses as $c): ?>
        <?php
          $courseId = (int)($c['id'] ?? 0);
          $price = (float)($c['price'] ?? 0);
          $desc = (string)($c['description'] ?? '');
          $intro = (string)($c['intro_video'] ?? '');
          $introPath = $intro !== '' ? ('uploads/' . $intro) : '';
        ?>
        <div class="col-md-4">
          <div class="card h-100 p-3 shadow-sm">

            <?php if ($intro !== ''): ?>
              <div class="mb-2">
                <video class="w-100 rounded video-box" controls preload="metadata">
                  <source src="<?= e($introPath) ?>" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </div>
            <?php else: ?>
              <div class="mb-2 p-4 bg-light rounded text-center text-muted small">
                No intro video uploaded
              </div>
            <?php endif; ?>

            <h6 class="mb-1"><?= e((string)($c['title'] ?? 'Untitled')) ?></h6>

            <?php if ($desc !== ''): ?>
              <p class="small text-muted mb-2">
                <?= e(mb_strimwidth($desc, 0, 140, '…')) ?>
              </p>
            <?php else: ?>
              <p class="small text-muted mb-2">No description yet.</p>
            <?php endif; ?>

            <div class="fw-bold mb-3">Fee: <?= formatMoney($price) ?></div>

            <div class="d-grid gap-2 mt-auto">
              <!-- enroll should be POST (safer), but keeping your flow -->
              <form method="post" action="course_register.php" class="d-grid">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="course_id" value="<?= (int)$c['id'] ?>">
                <select name="payment_option" class="form-select form-select-sm mb-2">
                  <option value="full">Full Payment</option>
                  <option value="installment">Installment Plan</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Enroll Now</button>
              </form>
              
              <a href="<?= e(courseUrl($c)) ?>" class="btn btn-outline-secondary btn-sm">
                View Details
              </a>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
