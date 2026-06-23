<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

if (!isPost()) {
    redirect('courses.php');
}

verifyCsrf($_POST['_csrf'] ?? '');

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$courseId  = (int)($_POST['course_id'] ?? 0);
$paymentType = trim((string)($_POST['payment_option'] ?? 'full'));

if ($studentId <= 0 || $courseId <= 0) {
    exit('Invalid request');
}

if (!in_array($paymentType, ['full', 'installment'], true)) {
    $paymentType = 'full';
}

// Find instructor for this course
$stmtIns = $pdo->prepare("
    SELECT instructor_id 
    FROM lms_instructor_courses ic
    JOIN lms_instructors i ON ic.instructor_id = i.id
    WHERE ic.course_id = ? AND i.status = 'active'
    ORDER BY (i.availability_status = 'available') DESC, i.id ASC
    LIMIT 1
");
$stmtIns->execute([$courseId]);
$assignedInstructorId = $stmtIns->fetchColumn();

$assignedIdVal = $assignedInstructorId ? (int)$assignedInstructorId : null;
$needsAssignVal = $assignedInstructorId ? 0 : 1;

// create enrollment if not exists
$stmt = $pdo->prepare("
  INSERT INTO lms_enrollments (student_id, course_id, paid_amount, payment_type, status, assigned_instructor_id, needs_instructor_assignment, created_at)
  VALUES (?, ?, 0, ?, 'active', ?, ?, NOW())
  ON DUPLICATE KEY UPDATE
    status = IF(status = '' OR status IS NULL, 'active', status),
    payment_type = IF(COALESCE(paid_amount, 0) <= 0, VALUES(payment_type), payment_type),
    assigned_instructor_id = IF(assigned_instructor_id IS NULL, VALUES(assigned_instructor_id), assigned_instructor_id),
    needs_instructor_assignment = IF(assigned_instructor_id IS NULL, VALUES(needs_instructor_assignment), needs_instructor_assignment)
");
$stmt->execute([$studentId, $courseId, $paymentType, $assignedIdVal, $needsAssignVal]);

// fetch enrollment id
$en = $pdo->prepare("SELECT id FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
$en->execute([$studentId, $courseId]);
$enrollmentId = (int)$en->fetchColumn();

redirect('pay.php?enrollment_id=' . $enrollmentId);
