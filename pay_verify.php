<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/includes/payment_processing.php';
require_once __DIR__ . '/config/db.php';

requireLogin(); // student must be logged in

/* ======================
   CONFIG
====================== */
$paystack = require __DIR__ . '/config/paystack.php';
$paystackSecretKey = $paystack['secret_key'];

$reference = trim((string)($_GET['reference'] ?? ''));
if ($reference === '') {
    exit('Invalid reference.');
}

$studentId = (int)($_SESSION['user']['id'] ?? 0);
if ($studentId <= 0) {
    exit('Invalid session.');
}

/* ======================
   VERIFY WITH PAYSTACK
====================== */
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
    error_log('Paystack verify HTTP error: ' . $err);
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
$psStatus = (string)($payData['status'] ?? 'failed'); // 'success' expected

// Paystack amount is in kobo
$amountPaid = (float)((int)($payData['amount'] ?? 0) / 100);
$channel    = (string)($payData['channel'] ?? 'paystack');

if ($psStatus !== 'success' || $amountPaid <= 0) {
    $pdo->prepare("UPDATE lms_payments SET status='failed' WHERE reference = ?")
        ->execute([$reference]);
    exit('Payment not successful.');
}

/* ======================
   UPDATE PAYMENT + ENROLLMENT (TRANSACTION)
====================== */
$pdo->beginTransaction();

try {
    $payment = lockPaymentByReference($pdo, $reference);
    if (!$payment) {
        throw new RuntimeException('Payment record not found.');
    }

    if ((int)$payment['student_id'] !== $studentId) {
        throw new RuntimeException('Access denied.');
    }

    if (($payment['status'] ?? '') === 'success') {
        $pdo->commit();
        redirect('payments.php');
    }

    $enrollmentId = (int)($payment['enrollment_id'] ?? 0);
    $enroll = lockEnrollmentForPayment($pdo, $enrollmentId, $studentId);
    if (!$enroll) {
        throw new RuntimeException('Enrollment not found for this payment.');
    }

    applyPaymentSuccess($pdo, $payment, $enroll, $amountPaid, $channel);

    $pdo->commit();

} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('Paystack verify save failed: ' . $e->getMessage());
    exit('Payment verification succeeded, but final processing failed. Please contact support if your dashboard does not update shortly.');
}

/* ======================
   DONE
====================== */
$_SESSION['pay_success'] = 'Payment verified successfully.';
redirect('payments.php');
