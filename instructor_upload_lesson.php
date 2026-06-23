<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$flash = $_SESSION['ins_lesson_flash'] ?? null;
unset($_SESSION['ins_lesson_flash']);

$insId = (int)($_SESSION['instructor']['id'] ?? 0);
$coursesStmt = $pdo->prepare("
    SELECT c.id, c.title 
    FROM lms_courses c
    JOIN lms_instructor_courses ic ON ic.course_id = c.id
    WHERE ic.instructor_id = ?
    ORDER BY c.title
");
$coursesStmt->execute([$insId]);
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $courseId = (int)($_POST['course_id'] ?? 0);
    $title    = trim((string)($_POST['title'] ?? ''));
    $content  = trim((string)($_POST['content'] ?? ''));
    $sort     = (int)($_POST['sort_order'] ?? 0);
    $pub      = (int)($_POST['is_published'] ?? 1);

    if ($courseId <= 0 || $title === '') {
        $_SESSION['ins_lesson_flash'] = 'Select a course and enter lesson title.';
        redirect('instructor_upload_lesson.php');
    }

    // Verify course belongs to this instructor
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id = ? AND course_id = ?");
    $checkStmt->execute([$insId, $courseId]);
    if ((int)$checkStmt->fetchColumn() === 0) {
        $_SESSION['ins_lesson_flash'] = 'Access denied: you are not assigned to this course.';
        redirect('instructor_upload_lesson.php');
    }

    $stmt = $pdo->prepare("
        INSERT INTO lms_lessons (course_id, title, content, sort_order, is_published, created_at)
        VALUES (?,?,?,?,?, NOW())
    ");
    $stmt->execute([$courseId, $title, $content, $sort, $pub]);

    $_SESSION['ins_lesson_flash'] = 'Lesson uploaded successfully.';
    redirect('instructor_upload_lesson.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Upload Lesson | Instructor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<h4 class="mb-3">Upload Lesson</h4>

<?php if ($flash): ?>
  <div class="alert alert-info"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card p-4">
<form method="post">
  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

  <div class="mb-2">
    <label class="form-label">Course</label>
    <select class="form-select" name="course_id" required>
      <option value="">-- Select Course --</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= e($c['title'] ?? '') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-2">
    <label class="form-label">Lesson Title</label>
    <input class="form-control" name="title" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Lesson Content</label>
    <textarea class="form-control" rows="6" name="content"></textarea>
  </div>

  <div class="row g-2">
    <div class="col-md-6 mb-2">
      <label class="form-label">Sort Order</label>
      <input type="number" class="form-control" name="sort_order" value="0">
    </div>
    <div class="col-md-6 mb-2">
      <label class="form-label">Publish</label>
      <select class="form-select" name="is_published">
        <option value="1">Published</option>
        <option value="0">Draft</option>
      </select>
    </div>
  </div>

  <button class="btn btn-primary w-100">Save Lesson</button>
</form>
</div>
</body>
</html>
