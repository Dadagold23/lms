<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    die("Error: Missing auto-login token.");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM lms_students WHERE autologin_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Error: Invalid or expired auto-login token.");
    }

    if (($student['status'] ?? 'active') !== 'active') {
        die("Error: Your student account is currently " . htmlspecialchars((string)$student['status']) . ".");
    }

    // Securely regenerate session ID
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'             => (int)$student['id'],
        'first_name'     => (string)$student['first_name'],
        'last_name'      => (string)$student['last_name'],
        'email'          => (string)$student['email'],
        'role'           => 'student',
        'course'         => (string)($student['course'] ?? ''),
        'course_price'   => (float)($student['course_price'] ?? 0.0),
        'payment_option' => (string)($student['payment_option'] ?? 'full'),
    ];

    header("Location: dashboard.php");
    exit;

} catch (Throwable $e) {
    error_log("Autologin failed: " . $e->getMessage());
    die("An unexpected error occurred during auto-login. Please contact support.");
}
