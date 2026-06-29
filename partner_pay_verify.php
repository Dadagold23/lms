<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$partnerId   = (int)($_SESSION['partner_id'] ?? 0);
$partnerType = (string)($_SESSION['partner_type'] ?? '');

if ($partnerId <= 0 || empty($partnerType)) {
    exit('Unauthorized partner session.');
}

$reference = trim((string)($_GET['reference'] ?? ''));
if ($reference === '') {
    exit('Invalid transaction reference.');
}

// Fetch pending payment
$stmtPay = $pdo->prepare("SELECT * FROM lms_payments WHERE reference = ? AND channel = 'campaign' LIMIT 1");
$stmtPay->execute([$reference]);
$payment = $stmtPay->fetch(PDO::FETCH_ASSOC);
if (!$payment) {
    exit('Payment record not found.');
}

if (($payment['status'] ?? '') === 'success') {
    // Already processed, redirect
    redirect("unitary_academy/" . $partnerType . "/index.php?payment_success=1");
}

// Verify with Paystack
$paystack = require __DIR__ . '/config/paystack.php';
$paystackSecretKey = $paystack['secret_key'];

$verifyUrl = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $verifyUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$paystackSecretKey}",
        "Content-Type: application/json",
    ],
]);

$response = curl_exec($ch);
$err      = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    error_log('Partner Paystack verify HTTP error: ' . $err);
    exit('Verification request failed. Please try again.');
}

$data = json_decode($response, true);
if ($httpCode !== 200 || !is_array($data) || empty($data['status'])) {
    exit('Paystack verification failed.');
}

if (($data['status'] !== true) || empty($data['data'])) {
    exit('Payment not verified.');
}

$payData  = $data['data'];
$psStatus = (string)($payData['status'] ?? 'failed');
$amountPaid = (float)((int)($payData['amount'] ?? 0) / 100);

if ($psStatus !== 'success' || $amountPaid <= 0) {
    $pdo->prepare("UPDATE lms_payments SET status='failed' WHERE reference = ?")
        ->execute([$reference]);
    exit('Payment not successful.');
}

// Process successful payment inside transaction
$pdo->beginTransaction();
try {
    // Lock payment and update success
    $stmtUpdPay = $pdo->prepare("UPDATE lms_payments SET status = 'success' WHERE id = ?");
    $stmtUpdPay->execute([(int)$payment['id']]);

    // Fetch enrollment
    $enrollmentId = (int)$payment['enrollment_id'];
    $stmtEnroll = $pdo->prepare("
        SELECT e.*, c.price AS original_price 
        FROM lms_enrollments e 
        JOIN lms_courses c ON c.id = e.course_id 
        WHERE e.id = ? 
        LIMIT 1
    ");
    $stmtEnroll->execute([$enrollmentId]);
    $enroll = $stmtEnroll->fetch(PDO::FETCH_ASSOC);

    if (!$enroll) {
        throw new RuntimeException('Enrollment record not found.');
    }

    $studentId = (int)$enroll['student_id'];

    // Determine capped course price
    $stmtStud = $pdo->prepare("SELECT is_affiliate, affiliate_class_range FROM lms_students WHERE id = ? LIMIT 1");
    $stmtStud->execute([$studentId]);
    $student = $stmtStud->fetch(PDO::FETCH_ASSOC);

    $coursePrice = (float)$enroll['original_price'];
    $isAff = !empty($student['is_affiliate']);
    $classRange = $student['affiliate_class_range'] ?? '';
    if ($isAff && ($classRange === 'JSS' || $classRange === 'SSS')) {
        $coursePrice = min($coursePrice, 5000.0);
    }

    // Unlock the student enrollment
    $stmtUpdEnroll = $pdo->prepare("UPDATE lms_enrollments SET paid_amount = ?, status = 'paid' WHERE id = ?");
    $stmtUpdEnroll->execute([$coursePrice, $enrollmentId]);

    // Complete referral transaction status if matching
    $stmtRef = $pdo->prepare("UPDATE lms_affiliate_referrals SET status = 'enrolled' WHERE pupil_email = (SELECT email FROM lms_students WHERE id = ?) AND partner_id = ?");
    $stmtRef->execute([$studentId, $partnerId]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('Partner Paystack verify save failed: ' . $e->getMessage());
    exit('Payment verification succeeded, but final processing failed. Please contact support.');
}

// Redirect partner to dashboard with success query parameter
redirect("unitary_academy/" . $partnerType . "/index.php?payment_success=1");
