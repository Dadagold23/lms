<?php
declare(strict_types=1);

function enrollmentAccessState(array $enrollment): array
{
    $price = (float)($enrollment['price'] ?? 0);
    $paid = (float)($enrollment['paid_amount'] ?? 0);
    $status = (string)($enrollment['status'] ?? ($enrollment['enroll_status'] ?? ''));
    $paymentType = (string)($enrollment['payment_type'] ?? 'full');
    $balance = max(0, $price - $paid);

    $accessExpiresAt = trim((string)($enrollment['access_expires_at'] ?? ''));
    $isExpired = $accessExpiresAt !== '' && strtotime($accessExpiresAt) < time();

    $nextDueDate = trim((string)($enrollment['next_due_date'] ?? ''));
    $createdAt = trim((string)($enrollment['created_at'] ?? ''));

    if ($paymentType === 'installment' && $balance > 0 && $nextDueDate === '' && $createdAt !== '') {
        $nextDueDate = date('Y-m-d', strtotime($createdAt . ' +2 months'));
    }

    $installmentDue = false;
    if ($paymentType === 'installment' && $paid > 0 && $balance > 0 && $nextDueDate !== '') {
        $installmentDue = strtotime($nextDueDate . ' 23:59:59') < time();
    }

    $isUnlocked = !$isExpired && (
        $status === 'paid'
        || ($price > 0 && $paid >= $price)
        || ($paymentType === 'installment' && $paid > 0 && !$installmentDue)
    );

    return [
        'price' => $price,
        'paid' => $paid,
        'status' => $status,
        'payment_type' => $paymentType,
        'balance' => $balance,
        'is_expired' => $isExpired,
        'next_due_date' => $nextDueDate,
        'installment_due' => $installmentDue,
        'is_unlocked' => $isUnlocked,
    ];
}
