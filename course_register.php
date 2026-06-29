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

// Fetch student's affiliate info
$st = $pdo->prepare("SELECT is_affiliate, affiliate_class_range FROM lms_students WHERE id = ? LIMIT 1");
$st->execute([$studentId]);
$student = $st->fetch() ?: [];
$isAffiliate = !empty($student['is_affiliate']);
$classRange  = $student['affiliate_class_range'] ?? '';
$isUnlockedAffiliate = false;

if ($isUnlockedAffiliate) {
    // Get course price
    $cStmt = $pdo->prepare("SELECT price FROM lms_courses WHERE id = ? LIMIT 1");
    $cStmt->execute([$courseId]);
    $coursePrice = (float)($cStmt->fetchColumn() ?: 0.0);
    $finalPrice = min($coursePrice, 5000.0);

    $stmt = $pdo->prepare("
      INSERT INTO lms_enrollments (student_id, course_id, paid_amount, payment_type, status, assigned_instructor_id, needs_instructor_assignment, created_at)
      VALUES (?, ?, ?, 'full', 'paid', ?, ?, NOW())
      ON DUPLICATE KEY UPDATE
        status = 'paid',
        paid_amount = ?,
        payment_type = 'full',
        assigned_instructor_id = IF(assigned_instructor_id IS NULL, VALUES(assigned_instructor_id), assigned_instructor_id),
        needs_instructor_assignment = IF(assigned_instructor_id IS NULL, VALUES(needs_instructor_assignment), needs_instructor_assignment)
    ");
    $stmt->execute([$studentId, $courseId, $finalPrice, $assignedIdVal, $needsAssignVal, $finalPrice]);

    // get enrollment id
    $en = $pdo->prepare("SELECT id FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
    $en->execute([$studentId, $courseId]);
    $enrollmentId = (int)$en->fetchColumn();

    // Check if payment already exists
    $payChk = $pdo->prepare("SELECT id FROM lms_payments WHERE student_id = ? AND enrollment_id = ? AND channel = 'campaign' LIMIT 1");
    $payChk->execute([$studentId, $enrollmentId]);
    if (!$payChk->fetch()) {
        try {
            $payStmt = $pdo->prepare("
                INSERT INTO lms_payments (student_id, enrollment_id, amount, channel, reference, status, created_at)
                VALUES (?, ?, ?, 'campaign', ?, 'success', NOW())
            ");
            $campaignRef = 'CAMP_' . bin2hex(random_bytes(6));
            $payStmt->execute([$studentId, $enrollmentId, $finalPrice, $campaignRef]);
        } catch (Throwable $e) {
            error_log("Failed to insert campaign payment record: " . $e->getMessage());
        }
    }

    if ($assignedIdVal && $enrollmentId > 0) {
        require_once __DIR__ . '/includes/student_notifications.php';
        notifyInstructorAssigned($pdo, $enrollmentId, $assignedIdVal);
    }

    redirect('dashboard.php');
} else {
    // Normal LMS payment procedure
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

    // get enrollment id
    $en = $pdo->prepare("SELECT id FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
    $en->execute([$studentId, $courseId]);
    $enrollmentId = (int)$en->fetchColumn();

    if ($assignedIdVal && $enrollmentId > 0) {
        require_once __DIR__ . '/includes/student_notifications.php';
        notifyInstructorAssigned($pdo, $enrollmentId, $assignedIdVal);
    }

    redirect('pay.php?enrollment_id=' . $enrollmentId);
}
