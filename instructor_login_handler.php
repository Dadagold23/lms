<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('instructor_login.php');
}

verifyCsrf($_POST['_csrf'] ?? '');

$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = (string)($_POST['password'] ?? '');

if (!$email || $password === '') {
  $_SESSION['instructor_login_error'] = 'Invalid login details.';
  redirect('instructor_login.php');
}

$stmt = $pdo->prepare("
  SELECT id, full_name, email, password, status
  FROM lms_instructors
  WHERE email=?
  LIMIT 1
");
$stmt->execute([$email]);
$ins = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ins || !password_verify($password, (string)$ins['password'])) {
  $_SESSION['instructor_login_error'] = 'Incorrect email or password.';
  redirect('instructor_login.php');
}

if (($ins['status'] ?? '') !== 'active') {
  $_SESSION['instructor_login_error'] = 'Instructor account disabled.';
  redirect('instructor_login.php');
}

session_regenerate_id(true);
$_SESSION['instructor'] = [
  'id' => (int)$ins['id'],
  'full_name' => (string)$ins['full_name'],
  'email' => (string)$ins['email'],
];

$pdo->prepare("UPDATE lms_instructors SET last_login_at=NOW() WHERE id=?")->execute([(int)$ins['id']]);

redirect('instructor_dashboard.php');
