<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$courses = $pdo->query("
  SELECT id, title, slug, price, level, intro_video, created_at
  FROM lms_courses
  ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Courses</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Courses</h4>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary btn-sm" href="instructor_dashboard.php">Dashboard</a>
    <a class="btn btn-primary btn-sm" href="instructor_upload_course.php">+ Add Course</a>
  </div>
</div>

<div class="card p-3">
  <table class="table table-sm align-middle mb-0">
    <thead>
      <tr>
        <th>Title</th>
        <th>Slug</th>
        <th>Price</th>
        <th>Level</th>
        <th>Intro Video</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($courses as $c): ?>
      <tr>
        <td><?= e($c['title']) ?></td>
        <td><?= e($c['slug']) ?></td>
        <td><?= formatMoney($c['price']) ?></td>
        <td><?= e($c['level']) ?></td>
        <td><?= !empty($c['intro_video']) ? 'Yes' : 'No' ?></td>
        <td><?= e(date('Y-m-d', strtotime((string)$c['created_at']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
