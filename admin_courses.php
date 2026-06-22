<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$admin = $_SESSION['admin'];

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$flash = $_SESSION['admin_course_flash'] ?? null;
unset($_SESSION['admin_course_flash']);

/* ======================
   HANDLE POST ACTIONS
====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $action = $_POST['action'] ?? '';

    // common fields
    $id    = (int)($_POST['id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $slug  = trim((string)($_POST['slug'] ?? ''));
    $desc  = trim((string)($_POST['description'] ?? ''));
    $short = trim((string)($_POST['short_description'] ?? ''));
    $level = trim((string)($_POST['level'] ?? 'beginner'));
    $price = (float)($_POST['price'] ?? 0);

    // intro video upload (optional)
    $introVideoName = null;
    if (!empty($_FILES['intro_video']['name']) && ($_FILES['intro_video']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo((string)$_FILES['intro_video']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4','mov','webm'], true)) {
            $_SESSION['admin_course_flash'] = 'Invalid video type. Use mp4/mov/webm.';
            redirect('admin_courses.php');
        }
        $introVideoName = uniqid('intro_', true) . '.' . $ext;
        move_uploaded_file((string)$_FILES['intro_video']['tmp_name'], $uploadDir . $introVideoName);
    }

    if ($action === 'create') {
        if ($title === '' || $slug === '' || $price <= 0) {
            $_SESSION['admin_course_flash'] = 'Title, slug, and price are required.';
            redirect('admin_courses.php');
        }

        $stmt = $pdo->prepare("
            INSERT INTO lms_courses (title, slug, description, short_description, price, level, intro_video, created_at)
            VALUES (?,?,?,?,?,?,?, NOW())
        ");
        $stmt->execute([
            $title,
            $slug,
            $desc,
            $short,
            $price,
            $level,
            $introVideoName
        ]);

        $_SESSION['admin_course_flash'] = 'Course created successfully.';
        redirect('admin_courses.php');
    }

    if ($action === 'update') {
        if ($id <= 0) {
            $_SESSION['admin_course_flash'] = 'Invalid course ID.';
            redirect('admin_courses.php');
        }

        if ($introVideoName !== null) {
            $stmt = $pdo->prepare("
                UPDATE lms_courses
                SET title=?, slug=?, description=?, short_description=?, price=?, level=?, intro_video=?
                WHERE id=?
            ");
            $stmt->execute([$title, $slug, $desc, $short, $price, $level, $introVideoName, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE lms_courses
                SET title=?, slug=?, description=?, short_description=?, price=?, level=?
                WHERE id=?
            ");
            $stmt->execute([$title, $slug, $desc, $short, $price, $level, $id]);
        }

        $_SESSION['admin_course_flash'] = 'Course updated.';
        redirect('admin_courses.php?edit=' . $id);
    }

    if ($action === 'delete') {
        if ($id <= 0) {
            $_SESSION['admin_course_flash'] = 'Invalid course ID.';
            redirect('admin_courses.php');
        }
        $pdo->prepare("DELETE FROM lms_courses WHERE id=?")->execute([$id]);
        $_SESSION['admin_course_flash'] = 'Course deleted.';
        redirect('admin_courses.php');
    }
}

/* ======================
   LOAD EDIT COURSE
====================== */
$editId = (int)($_GET['edit'] ?? 0);
$editCourse = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM lms_courses WHERE id=?");
    $stmt->execute([$editId]);
    $editCourse = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ======================
   LIST + SEARCH
====================== */
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM lms_courses
        WHERE title LIKE ? OR slug LIKE ? OR level LIKE ?
        ORDER BY created_at DESC
    ");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $courses = $pdo->query("SELECT * FROM lms_courses ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Manage Courses';
$seoDesc    = 'Manage courses at Grafix@Mirror LMS — Mirror Age Concepts admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{background:#f7fbff;font-family:Inter,system-ui}
  .card{border-radius:14px}
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white text-decoration-none" href="admin_dashboard.php">Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_payment_approval.php" class="btn btn-warning btn-sm">Manual Approvals</a>
      <a href="admin_logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <?php if ($flash): ?>
    <div class="alert alert-info"><?= e($flash) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card p-4">
        <h5 class="mb-3"><?= $editCourse ? 'Edit Course' : 'Create Course' ?></h5>

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
          <input type="hidden" name="action" value="<?= $editCourse ? 'update' : 'create' ?>">
          <?php if ($editCourse): ?>
            <input type="hidden" name="id" value="<?= (int)$editCourse['id'] ?>">
          <?php endif; ?>

          <div class="mb-2">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" required value="<?= e($editCourse['title'] ?? '') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Slug</label>
            <input class="form-control" name="slug" required value="<?= e($editCourse['slug'] ?? '') ?>">
            <div class="form-text">example: graphic-design</div>
          </div>

          <div class="mb-2">
            <label class="form-label">Short Description</label>
            <input class="form-control" name="short_description" value="<?= e($editCourse['short_description'] ?? '') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" name="description"><?= e($editCourse['description'] ?? '') ?></textarea>
          </div>

          <div class="row g-2">
            <div class="col-md-6 mb-2">
              <label class="form-label">Price (₦)</label>
              <input type="number" class="form-control" name="price" required value="<?= e((string)($editCourse['price'] ?? '')) ?>">
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Level</label>
              <select class="form-select" name="level">
                <?php
                  $lv = (string)($editCourse['level'] ?? 'beginner');
                  foreach (['beginner','intermediate','advanced'] as $opt):
                ?>
                  <option value="<?= e($opt) ?>" <?= $lv === $opt ? 'selected' : '' ?>><?= e(ucfirst($opt)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Intro Video (mp4/mov/webm)</label>
            <input type="file" class="form-control" name="intro_video" accept="video/mp4,video/webm,video/quicktime">
            <?php if (!empty($editCourse['intro_video'])): ?>
              <div class="form-text">Current: <?= e($editCourse['intro_video']) ?></div>
            <?php endif; ?>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary"><?= $editCourse ? 'Update' : 'Create' ?></button>
          </div>
        </form>

        <?php if ($editCourse): ?>
          <form method="post" class="mt-3" onsubmit="return confirm('Delete this course?');">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$editCourse['id'] ?>">
            <button class="btn btn-outline-danger w-100">Delete Course</button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">All Courses</h5>
          <form class="d-flex gap-2" method="get">
            <input class="form-control form-control-sm" name="q" value="<?= e($q) ?>" placeholder="Search...">
            <button class="btn btn-sm btn-outline-primary">Search</button>
          </form>
        </div>

        <?php if (empty($courses)): ?>
          <p class="text-muted">No courses yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Slug</th>
                  <th>Price</th>
                  <th>Level</th>
                  <th>Video</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($courses as $c): ?>
                <tr>
                  <td><?= e($c['title'] ?? '') ?></td>
                  <td><?= e($c['slug'] ?? '') ?></td>
                  <td><?= formatMoney($c['price'] ?? 0) ?></td>
                  <td><?= e($c['level'] ?? '') ?></td>
                  <td><?= !empty($c['intro_video']) ? 'Yes' : 'No' ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="admin_courses.php?edit=<?= (int)$c['id'] ?>">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

</div>
</body>
</html>
