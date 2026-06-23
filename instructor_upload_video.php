<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$flash = $_SESSION['ins_video_flash'] ?? null;
unset($_SESSION['ins_video_flash']);

$videoDir = __DIR__ . '/uploads/videos/';
if (!is_dir($videoDir)) mkdir($videoDir, 0755, true);

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

$lessonsStmt = $pdo->prepare("
    SELECT l.id, l.course_id, l.title 
    FROM lms_lessons l
    JOIN lms_instructor_courses ic ON ic.course_id = l.course_id
    WHERE ic.instructor_id = ?
    ORDER BY l.id DESC
");
$lessonsStmt->execute([$insId]);
$lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $courseId   = (int)($_POST['course_id'] ?? 0);
    $lessonId   = (int)($_POST['lesson_id'] ?? 0);
    $title      = trim((string)($_POST['title'] ?? ''));
    $dur        = (int)($_POST['duration_seconds'] ?? 0);
    $pub        = (int)($_POST['is_published'] ?? 1);
    $videoUrl   = trim((string)($_POST['video_url'] ?? ''));

    if ($courseId <= 0 || $title === '') {
        $_SESSION['ins_video_flash'] = 'Select course and enter video title.';
        redirect('instructor_upload_video.php');
    }

    // Verify course belongs to this instructor
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id = ? AND course_id = ?");
    $checkStmt->execute([$insId, $courseId]);
    if ((int)$checkStmt->fetchColumn() === 0) {
        $_SESSION['ins_video_flash'] = 'Access denied: you are not assigned to this course.';
        redirect('instructor_upload_video.php');
    }

    // Prefer URL input; fall back to file upload
    $videoPath = '';
    if ($videoUrl !== '') {
        if (!preg_match('~^https?://~i', $videoUrl)) {
            $_SESSION['ins_video_flash'] = 'Invalid URL. Must start with http:// or https://';
            redirect('instructor_upload_video.php');
        }
        $videoPath = $videoUrl;
    } elseif (!empty($_FILES['video_file']['name']) && ($_FILES['video_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo((string)$_FILES['video_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4','webm','mov'], true)) {
            $_SESSION['ins_video_flash'] = 'Invalid video format. Use mp4/webm/mov.';
            redirect('instructor_upload_video.php');
        }
        $name = uniqid('vid_', true) . '.' . $ext;
        move_uploaded_file((string)$_FILES['video_file']['tmp_name'], $videoDir . $name);
        $videoPath = 'uploads/videos/' . $name;
    } else {
        $_SESSION['ins_video_flash'] = 'Provide a video URL or upload a video file.';
        redirect('instructor_upload_video.php');
    }

    $stmt = $pdo->prepare("
        INSERT INTO lms_videos (course_id, lesson_id, title, video_path, duration_seconds, is_published, created_at)
        VALUES (?,?,?,?,?,?, NOW())
    ");
    $stmt->execute([
        $courseId,
        $lessonId > 0 ? $lessonId : null,
        $title,
        $videoPath,
        $dur > 0 ? $dur : null,
        $pub
    ]);

    $_SESSION['ins_video_flash'] = 'Video uploaded successfully.';
    redirect('instructor_upload_video.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Upload Video | Instructor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<h4 class="mb-3">Upload Video</h4>

<?php if ($flash): ?>
  <div class="alert alert-info"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card p-4">
<form method="post" enctype="multipart/form-data">
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
    <label class="form-label">Lesson (optional)</label>
    <select class="form-select" name="lesson_id">
      <option value="0">-- None --</option>
      <?php foreach ($lessons as $l): ?>
        <option value="<?= (int)$l['id'] ?>">[Course #<?= (int)$l['course_id'] ?>] <?= e($l['title'] ?? '') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-2">
    <label class="form-label">Video Title</label>
    <input class="form-control" name="title" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Duration (seconds, optional)</label>
    <input type="number" class="form-control" name="duration_seconds" value="">
  </div>

  <div class="mb-2">
    <label class="form-label">YouTube / External URL <span class="text-muted">(paste link)</span></label>
    <input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
    <div class="form-text">Paste a YouTube or any video URL. Leave blank if uploading a file below.</div>
  </div>

  <div class="mb-2">
    <label class="form-label">— OR — Upload Video File</label>
    <input type="file" class="form-control" name="video_file" accept="video/mp4,video/webm,video/quicktime">
    <div class="form-text">MP4, WebM or MOV. Max size depends on server config.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Publish</label>
    <select class="form-select" name="is_published">
      <option value="1">Published</option>
      <option value="0">Draft</option>
    </select>
  </div>

  <button class="btn btn-primary w-100">Save Video</button>
</form>
</div>
</body>
</html>
