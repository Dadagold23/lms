<?php
declare(strict_types=1);

/**
 * Paystack Webhook Handler
 * URL: https://mirrorageconcepts.com/lms/paystack_webhook.php
 * Set this in your Paystack dashboard under Settings > API Keys & Webhooks
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/paystack.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/payment_processing.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Read raw body
$body = file_get_contents('php://input');
if (empty($body)) {
    http_response_code(400);
    exit('Empty body');
}

// Verify Paystack signature
$paystack   = require __DIR__ . '/config/paystack.php';
$secretKey  = $paystack['secret_key'];
$signature  = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
$expected   = hash_hmac('sha512', $body, $secretKey);

if (!hash_equals($expected, $signature)) {
    http_response_code(401);
    exit('Invalid signature');
}

$event = json_decode($body, true);
if (!is_array($event)) {
    http_response_code(400);
    exit('Invalid JSON');
}

$eventType = (string)($event['event'] ?? '');
$data      = $event['data'] ?? [];

// Log webhook (optional — comment out in production if noisy)
$isProduction = ($_ENV['APP_ENV'] ?? 'local') === 'production';
if (!$isProduction) {
    $logLine = date('Y-m-d H:i:s') . " | {$eventType} | " . json_encode([
        'reference' => (string)($data['reference'] ?? ''),
        'status' => (string)($data['status'] ?? ''),
        'amount' => (int)($data['amount'] ?? 0),
    ]) . "\n";
    @file_put_contents(__DIR__ . '/logs/paystack_webhook.log', $logLine, FILE_APPEND);
}

/* ── Handle charge.success ── */
if ($eventType === 'charge.success') {
    $reference  = (string)($data['reference'] ?? '');
    $amountKobo = (int)($data['amount'] ?? 0);
    $amountNgn  = $amountKobo / 100;
    $channel    = (string)($data['channel'] ?? 'paystack');

    if ($reference === '') {
        http_response_code(200); // Always 200 to Paystack
        exit('No reference');
    }

    $pdo->beginTransaction();
    try {
        $payment = lockPaymentByReference($pdo, $reference);
        if (!$payment) {
            $pdo->rollBack();
            http_response_code(200);
            exit('Payment not found');
        }

        if (($payment['status'] ?? '') === 'success') {
            $pdo->commit();
            http_response_code(200);
            exit('Already processed');
        }

        $studentId    = (int)($payment['student_id'] ?? 0);
        $enrollmentId = (int)($payment['enrollment_id'] ?? 0);
        $enroll = lockEnrollmentForPayment($pdo, $enrollmentId, $studentId);
        if (!$enroll) {
            throw new RuntimeException('Enrollment not found for payment.');
        }

        applyPaymentSuccess($pdo, $payment, $enroll, $amountNgn, $channel);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Paystack webhook processing failed: ' . $e->getMessage());
        http_response_code(500);
        exit('Processing error');
    }
}

/* ── Handle transfer.success / transfer.failed (if using Paystack Transfers) ── */
if (in_array($eventType, ['transfer.success', 'transfer.failed'], true)) {
    // Log only — extend as needed
}

// Always respond 200 to Paystack
http_response_code(200);
echo json_encode(['status' => true, 'event' => $eventType]);
