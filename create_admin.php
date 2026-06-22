<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

requireAdmin();

$siteName = "Mirror Age Concepts";
$title    = "Create Admin | Mirror LMS";

$success = $_SESSION['flash_success'] ?? null;
$error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim((string)($_POST['first_name'] ?? ''));
    $lastName  = trim((string)($_POST['last_name'] ?? ''));
    $email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password  = (string)($_POST['password'] ?? '');

    if ($firstName === '' || $lastName === '' || !$email) {
        $_SESSION['flash_error'] = 'Please fill all required fields correctly.';
        redirect('create_admin.php');
    }

    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $_SESSION['flash_error'] = 'Password must be at least 8 characters, include a number and uppercase letter.';
        redirect('create_admin.php');
    }

    // Check duplicate email
    $check = $pdo->prepare("SELECT id FROM lms_users WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    if ($check->fetchColumn()) {
        $_SESSION['flash_error'] = 'Email already exists.';
        redirect('create_admin.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->beginTransaction();
    try {
        // Create user
        $stmt = $pdo->prepare("
            INSERT INTO lms_users (first_name, last_name, email, password, status, created_at)
            VALUES (?, ?, ?, ?, 'active', NOW())
        ");
        $stmt->execute([$firstName, $lastName, $email, $hash]);
        $newUserId = (int)$pdo->lastInsertId();

        // Map as admin
        $stmt = $pdo->prepare("INSERT INTO lms_admins (user_id, created_at) VALUES (?, NOW())");
        $stmt->execute([$newUserId]);

        $pdo->commit();
        $_SESSION['flash_success'] = 'Admin account created successfully.';
        redirect('create_admin.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = 'Failed to create admin. Please try again.';
        redirect('create_admin.php');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | <?= e($siteName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{font-family:Inter,system-ui;background:#f7fbff}
  .card{border-radius:14px}
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">Admin Panel</span>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:720px;">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-1">Create Admin</h4>
    <p class="text-muted small mb-3">Create a new admin account for the platform.</p>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= e((string)$success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= e((string)$error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">First Name</label>
          <input class="form-control" name="first_name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Last Name</label>
          <input class="form-control" name="last_name" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Email</label>
          <input class="form-control" name="email" type="email" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Password</label>
          <input class="form-control" name="password" type="password" required>
          <div class="form-text">Min 8 chars, include uppercase + number.</div>
        </div>
      </div>

      <div class="d-grid mt-3">
        <button class="btn btn-primary btn-lg">Create Admin</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
