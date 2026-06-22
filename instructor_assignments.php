<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$assign = $pdo->query("
  SELECT a.id, a.title, a.due_date, a.is_published, a.created_at,
         c.title AS course_title
  FROM lms_assignments a
  JOIN lms_courses c ON c.id = a.course_id
  ORDER BY a.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Assignments</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Assignments</h4>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary btn-sm" href="instructor_dashboard.php">Dashboard</a>
    <a class="btn btn-primary btn-sm" href="instructor_upload_assignment.php">+ Add Assignment</a>
  </div>
</div>

<div class="card p-3">
  <table class="table table-sm align-middle mb-0">
    <thead>
      <tr>
        <th>Title</th>
        <th>Course</th>
        <th>Due</th>
        <th>Published</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($assign as $a): ?>
      <tr>
        <td><?= e($a['title']) ?></td>
        <td><?= e($a['course_title']) ?></td>
        <td><?= e($a['due_date']) ?></td>
        <td><?= ((int)$a['is_published'] === 1) ? 'Yes' : 'No' ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
