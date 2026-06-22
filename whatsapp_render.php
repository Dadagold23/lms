<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$enrollmentId = (int)($_GET['enrollment_id'] ?? 0);
if ($enrollmentId <= 0) {
    http_response_code(400);
    exit('Invalid enrollment ID. Please provide a valid enrollment_id parameter.');
}

$stmt = $pdo->prepare("
  SELECT
    s.first_name, s.last_name, s.phone,
    c.title AS course_title, c.price,
    e.paid_amount, e.next_due_date
  FROM lms_enrollments e
  JOIN lms_students s ON s.id = e.student_id
  JOIN lms_courses c ON c.id = e.course_id
  WHERE e.id = ?
  LIMIT 1
");
$stmt->execute([$enrollmentId]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    http_response_code(404);
    exit('Enrollment not found. The enrollment ID may be invalid or deleted.');
}

$paid = (float)($r['paid_amount'] ?? 0);
$price = (float)($r['price'] ?? 0);
$bal = max(0, $price - $paid);

$phone = preg_replace('/\D+/', '', (string)($r['phone'] ?? ''));
if ($phone === '') {
    http_response_code(400);
    exit('No phone number on record for this student. Update their profile first.');
}
// Normalise to international format
if (str_starts_with($phone, '0')) {
    $phone = '234' . substr($phone, 1);
} elseif (strlen($phone) === 10 && !str_starts_with($phone, '234')) {
    $phone = '234' . $phone;
}

$name = trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''));
$course = (string)($r['course_title'] ?? '');
$due = (string)($r['next_due_date'] ?? '');

$msg = "Hello {$name}, this is a reminder that your next installment for {$course} is due on {$due}. "
     . "Outstanding balance: " . formatMoney($bal) . ". "
     . "Please login to your dashboard to complete payment. Thank you.";

$wa = "https://wa.me/{$phone}?text=" . urlencode($msg);
redirect($wa);
