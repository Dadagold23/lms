<?php
session_start();
require_once __DIR__.'/includes/guard.php';
require_once __DIR__.'/includes/helpers.php';
require_once __DIR__.'/config/db.php';

requireLogin();
requireRole('admin');

/* ADD COURSE */
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $stmt = $pdo->prepare("
        INSERT INTO lms_courses (title, price, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([trim($_POST['title']), (float)$_POST['price']]);
}

$courses = $pdo->query("SELECT * FROM lms_courses ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html>
<head>
<title>Course Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h4>Courses</h4>

<form method="post" class="row g-2 mb-3">
  <div class="col">
    <input name="title" class="form-control" placeholder="Course title" required>
  </div>
  <div class="col">
    <input name="price" class="form-control" placeholder="Price" required>
  </div>
  <div class="col">
    <button class="btn btn-primary">Add</button>
  </div>
</form>

<table class="table table-sm">
<tr><th>Title</th><th>Price</th></tr>
<?php foreach ($courses as $c): ?>
<tr>
<td><?= e($c['title']) ?></td>
<td><?= formatMoney((float)$c['price']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="admin_dashboard.php" class="btn btn-secondary btn-sm">Back</a>
</body>
</html>