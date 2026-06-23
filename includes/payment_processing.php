<?php
declare(strict_types=1);

function lockPaymentById(PDO $pdo, int $paymentId, ?string $status = null, ?string $channel = null): ?array
{
    $sql = "
        SELECT *
        FROM lms_payments
        WHERE id = ?
    ";
    $args = [$paymentId];

    if ($status !== null) {
        $sql .= " AND status = ?";
        $args[] = $status;
    }

    if ($channel !== null) {
        $sql .= " AND channel = ?";
        $args[] = $channel;
    }

    $sql .= " FOR UPDATE";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    return $payment ?: null;
}

function lockPaymentByReference(PDO $pdo, string $reference): ?array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM lms_payments
        WHERE reference = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$reference]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    return $payment ?: null;
}

function lockEnrollmentForPayment(PDO $pdo, int $enrollmentId, ?int $studentId = null): ?array
{
    $sql = "
        SELECT e.*, c.price
        FROM lms_enrollments e
        JOIN lms_courses c ON c.id = e.course_id
        WHERE e.id = ?
    ";
    $args = [$enrollmentId];

    if ($studentId !== null) {
        $sql .= " AND e.student_id = ?";
        $args[] = $studentId;
    }

    $sql .= " LIMIT 1 FOR UPDATE";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

    return $enrollment ?: null;
}

function resolveEnrollmentPaymentUpdate(array $enrollment, float $amountPaid): array
{
    $currentPaid = (float)($enrollment['paid_amount'] ?? 0);
    $coursePrice = (float)($enrollment['price'] ?? 0);
    $paymentType = (string)($enrollment['payment_type'] ?? 'full');
    $newPaid = $currentPaid + $amountPaid;
    $balance = max(0, $coursePrice - $newPaid);

    if ($coursePrice > 0 && $newPaid >= $coursePrice) {
        return [
            'paid_amount' => $newPaid,
            'status' => 'paid',
            'next_due_date' => null,
        ];
    }

    if ($paymentType === 'installment' && $newPaid > 0) {
        $createdAt = trim((string)($enrollment['created_at'] ?? ''));
        $existingDueDate = trim((string)($enrollment['next_due_date'] ?? ''));
        $enrolledAtTs = $createdAt !== '' ? strtotime($createdAt) : time();
        $dueDate = $existingDueDate !== ''
            ? date('Y-m-d', strtotime($existingDueDate))
            : date('Y-m-d', strtotime('+2 months', $enrolledAtTs));

        return [
            'paid_amount' => $newPaid,
            'status' => $balance > 0 ? 'installment' : 'paid',
            'next_due_date' => $balance > 0 ? $dueDate : null,
        ];
    }

    return [
        'paid_amount' => $newPaid,
        'status' => $newPaid > 0 ? 'active' : ((string)($enrollment['status'] ?? 'active') ?: 'active'),
        'next_due_date' => null,
    ];
}

function markOtherPendingPaymentsFailed(PDO $pdo, int $studentId, int $enrollmentId, int $keepPaymentId): void
{
    $pdo->prepare("
        UPDATE lms_payments
        SET status = 'failed'
        WHERE student_id = ?
          AND enrollment_id = ?
          AND status = 'pending'
          AND id <> ?
    ")->execute([$studentId, $enrollmentId, $keepPaymentId]);
}

function applyPaymentSuccess(PDO $pdo, array $payment, array $enrollment, float $amountPaid, string $channel, array $paymentExtras = []): array
{
    $paymentId = (int)($payment['id'] ?? 0);
    $studentId = (int)($payment['student_id'] ?? 0);
    $enrollmentId = (int)($payment['enrollment_id'] ?? 0);

    if ($paymentId <= 0 || $studentId <= 0 || $enrollmentId <= 0 || $amountPaid <= 0) {
        throw new RuntimeException('Invalid payment payload.');
    }

    $update = resolveEnrollmentPaymentUpdate($enrollment, $amountPaid);

    $fields = [
        'status = ?',
        'channel = ?',
        'amount = ?',
        'paid_at = NOW()',
    ];
    $args = [
        'success',
        $channel,
        $amountPaid,
    ];

    foreach ($paymentExtras as $column => $value) {
        $fields[] = "{$column} = ?";
        $args[] = $value;
    }

    $args[] = $paymentId;

    $pdo->prepare("
        UPDATE lms_payments
        SET " . implode(",\n            ", $fields) . "
        WHERE id = ?
    ")->execute($args);

    $pdo->prepare("
        UPDATE lms_enrollments
        SET paid_amount = ?,
            status = ?,
            next_due_date = ?
        WHERE id = ? AND student_id = ?
    ")->execute([
        $update['paid_amount'],
        $update['status'],
        $update['next_due_date'],
        $enrollmentId,
        $studentId,
    ]);

    // If enrollment has no instructor assigned yet, try to auto-assign one now
    if (empty($enrollment['assigned_instructor_id'])) {
        $stmtIns = $pdo->prepare("
            SELECT instructor_id 
            FROM lms_instructor_courses ic
            JOIN lms_instructors i ON ic.instructor_id = i.id
            WHERE ic.course_id = ? AND i.status = 'active'
            ORDER BY (i.availability_status = 'available') DESC, i.id ASC
            LIMIT 1
        ");
        $stmtIns->execute([(int)$enrollment['course_id']]);
        $assignedInstructorId = $stmtIns->fetchColumn();

        if ($assignedInstructorId) {
            $pdo->prepare("
                UPDATE lms_enrollments 
                SET assigned_instructor_id = ?, needs_instructor_assignment = 0
                WHERE id = ?
            ")->execute([(int)$assignedInstructorId, $enrollmentId]);
        } else {
            $pdo->prepare("
                UPDATE lms_enrollments 
                SET needs_instructor_assignment = 1
                WHERE id = ?
            ")->execute([$enrollmentId]);
        }
    }

    markOtherPendingPaymentsFailed($pdo, $studentId, $enrollmentId, $paymentId);

    return $update;
}
