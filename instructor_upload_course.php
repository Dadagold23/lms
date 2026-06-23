<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$flash = $_SESSION['ins_course_flash'] ?? null;
unset($_SESSION['ins_course_flash']);

$introDir = __DIR__ . '/uploads/intro_videos/';
if (!is_dir($introDir)) mkdir($introDir, 0755, true);

function uploadVideoFile(string $field, string $dir): ?string {
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo((string)$_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['mp4','webm','mov'], true)) {
        $_SESSION['ins_course_flash'] = 'Invalid intro video type. Use mp4/webm/mov.';
        redirect('instructor_upload_course.php');
    }
    $name = uniqid('intro_', true) . '.' . $ext;
    move_uploaded_file((string)$_FILES[$field]['tmp_name'], $dir . $name);
    return $name;
}

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $title = trim((string)($_POST['title'] ?? ''));
    $slug  = trim((string)($_POST['slug'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $short = trim((string)($_POST['short_description'] ?? ''));
    $level = trim((string)($_POST['level'] ?? 'beginner'));
    $price = (float)($_POST['price'] ?? 0);
    $workspaceType = normalizeWorkspaceType((string)($_POST['workspace_type'] ?? 'default'));
    $workspaceUrl = trim((string)($_POST['workspace_url'] ?? ''));

    if ($title === '' || $slug === '' || $price <= 0) {
        $_SESSION['ins_course_flash'] = 'Title, slug and price are required.';
        redirect('instructor_upload_course.php');
    }

    $introVideo = uploadVideoFile('intro_video', $introDir);

    if (workspaceColumnsExist($pdo)) {
        $stmt = $pdo->prepare("
            INSERT INTO lms_courses (title, slug, description, short_description, price, level, intro_video, workspace_type, workspace_url, created_at)
            VALUES (?,?,?,?,?,?,?,?,?, NOW())
        ");
        $stmt->execute([$title, $slug, $description, $short, $price, $level, $introVideo, $workspaceType, $workspaceUrl !== '' ? $workspaceUrl : null]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO lms_courses (title, slug, description, short_description, price, level, intro_video, created_at)
            VALUES (?,?,?,?,?,?,?, NOW())
        ");
        $stmt->execute([$title, $slug, $description, $short, $price, $level, $introVideo]);
    }

    $courseId = (int)$pdo->lastInsertId();
    $insId = (int)($_SESSION['instructor']['id'] ?? 0);
    if ($courseId > 0 && $insId > 0) {
        $pdo->prepare("INSERT INTO lms_instructor_courses (instructor_id, course_id) VALUES (?, ?)")
            ->execute([$insId, $courseId]);
    }

    $_SESSION['ins_course_flash'] = 'Course uploaded successfully.';
    redirect('instructor_upload_course.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Upload Course | Instructor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<h4 class="mb-3">Upload Course</h4>

<?php if ($flash): ?>
  <div class="alert alert-info"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card p-4">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

  <div class="mb-2">
    <label class="form-label">Title</label>
    <input class="form-control" name="title" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Slug (unique)</label>
    <input class="form-control" name="slug" placeholder="graphic-design" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Short Description</label>
    <input class="form-control" name="short_description">
  </div>

  <div class="mb-2">
    <label class="form-label">Description</label>
    <textarea class="form-control" rows="4" name="description"></textarea>
  </div>

  <div class="row g-2">
    <div class="col-md-6 mb-2">
      <label class="form-label">Price (₦)</label>
      <input type="number" class="form-control" name="price" required>
    </div>
    <div class="col-md-6 mb-2">
      <label class="form-label">Level</label>
      <select class="form-select" name="level">
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>
  </div>

  <div class="row g-2">
    <div class="col-md-6 mb-2">
      <label class="form-label">Workspace Type</label>
      <select class="form-select" name="workspace_type">
        <?php foreach (workspaceTypeOptions() as $value => $label): ?>
          <option value="<?= e($value) ?>"><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6 mb-2">
      <label class="form-label">Workspace URL (optional)</label>
      <input class="form-control" name="workspace_url" placeholder="https://www.photopea.com">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Intro Video (mp4/webm/mov)</label>
    <input type="file" class="form-control" name="intro_video" accept="video/mp4,video/webm,video/quicktime">
  </div>

  <button class="btn btn-primary w-100">Save Course</button>
</form>
</div>
</body>
</html>
