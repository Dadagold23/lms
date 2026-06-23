<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();
$ins = $_SESSION['instructor'] ?? $_SESSION['user'] ?? null;
$insId = (int)($ins['id'] ?? 0);

$stmt = $pdo->prepare("
  SELECT v.id, v.title, v.video_path, v.is_published, v.created_at,
         c.title AS course_title
  FROM lms_videos v
  JOIN lms_courses c ON c.id = v.course_id
  JOIN lms_instructor_courses ic ON ic.course_id = c.id
  WHERE ic.instructor_id = ?
  ORDER BY v.id DESC
");
$stmt->execute([$insId]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Videos</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Videos</h4>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary btn-sm" href="instructor_dashboard.php">Dashboard</a>
    <a class="btn btn-primary btn-sm" href="instructor_upload_video.php">+ Add Video</a>
  </div>
</div>

<div class="card p-3">
  <table class="table table-sm align-middle mb-0">
    <thead>
      <tr>
        <th>Video</th>
        <th>Course</th>
        <th>Published</th>
        <th>Preview</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($videos as $v): ?>
      <tr>
        <td><?= e($v['title']) ?></td>
        <td><?= e($v['course_title']) ?></td>
        <td><?= ((int)$v['is_published'] === 1) ? 'Yes' : 'No' ?></td>
        <td>
          <?php if (!empty($v['video_path'])): ?>
            <a class="btn btn-outline-secondary btn-sm" target="_blank" href="<?= e($v['video_path']) ?>">Open</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
