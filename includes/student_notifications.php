<?php
declare(strict_types=1);

function ensureStudentNotificationsTable(PDO $pdo): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lms_student_notifications (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            student_id INT UNSIGNED NOT NULL,
            course_id INT UNSIGNED DEFAULT NULL,
            type ENUM('assignment','live_session') NOT NULL,
            title VARCHAR(190) NOT NULL,
            message TEXT NOT NULL,
            action_url VARCHAR(255) DEFAULT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_student_read_created (student_id, is_read, created_at),
            KEY idx_course_type (course_id, type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $ready = true;
}

function createCourseStudentNotification(
    PDO $pdo,
    int $courseId,
    string $type,
    string $title,
    string $message,
    ?string $actionUrl = null
): int {
    ensureStudentNotificationsTable($pdo);

    $stmt = $pdo->prepare("
        INSERT INTO lms_student_notifications (student_id, course_id, type, title, message, action_url)
        SELECT e.student_id, e.course_id, ?, ?, ?, ?
        FROM lms_enrollments e
        WHERE e.course_id = ?
    ");
    $stmt->execute([$type, $title, $message, $actionUrl, $courseId]);

    return (int)$stmt->rowCount();
}

function createLiveSessionStudentNotifications(
    PDO $pdo,
    int $courseId,
    int $sessionId,
    string $courseTitle,
    string $sessionTitle,
    string $scheduledAt,
    ?string $meetingLink = null
): int {
    $sessionTime = date('D d M Y, g:ia', strtotime($scheduledAt));
    $normalizedMeetingLink = strtolower(trim((string)$meetingLink));
    $roomLabel = (str_contains($normalizedMeetingLink, 'teams.microsoft.com') || str_contains($normalizedMeetingLink, 'teams.live.com'))
        ? 'Microsoft Teams channel room'
        : 'native LMS classroom';
    $message = "A live session has been scheduled for {$courseTitle}: {$sessionTitle} on {$sessionTime}. "
        . "Join from your LMS dashboard through the {$roomLabel}.";

    return createCourseStudentNotification(
        $pdo,
        $courseId,
        'live_session',
        'New live session scheduled',
        $message,
        'live_session.php?join=' . $sessionId
    );
}

function getStudentNotifications(PDO $pdo, int $studentId, int $limit = 8): array
{
    ensureStudentNotificationsTable($pdo);
    $limit = max(1, min(50, $limit));

    $stmt = $pdo->prepare("
        SELECT id, course_id, type, title, message, action_url, is_read, created_at
        FROM lms_student_notifications
        WHERE student_id = ?
        ORDER BY created_at DESC, id DESC
        LIMIT {$limit}
    ");
    $stmt->execute([$studentId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function countUnreadStudentNotifications(PDO $pdo, int $studentId): int
{
    ensureStudentNotificationsTable($pdo);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lms_student_notifications WHERE student_id = ? AND is_read = 0");
    $stmt->execute([$studentId]);
    return (int)$stmt->fetchColumn();
}

function markStudentNotificationsRead(PDO $pdo, int $studentId): void
{
    ensureStudentNotificationsTable($pdo);
    $stmt = $pdo->prepare("UPDATE lms_student_notifications SET is_read = 1 WHERE student_id = ? AND is_read = 0");
    $stmt->execute([$studentId]);
}

function normalizeWhatsappPhone(?string $phone): string
{
    $phone = preg_replace('/\D+/', '', (string)$phone);
    if ($phone === '') {
        return '';
    }
    if (str_starts_with($phone, '0')) {
        return '234' . substr($phone, 1);
    }
    if (strlen($phone) === 10 && !str_starts_with($phone, '234')) {
        return '234' . $phone;
    }
    return $phone;
}

function studentNotificationWhatsappRecipients(PDO $pdo, int $courseId): array
{
    $stmt = $pdo->prepare("
        SELECT
            s.id AS student_id,
            s.first_name,
            s.last_name,
            s.phone,
            s.email,
            c.title AS course_title
        FROM lms_enrollments e
        JOIN lms_students s ON s.id = e.student_id
        JOIN lms_courses c ON c.id = e.course_id
        WHERE e.course_id = ?
        ORDER BY s.first_name ASC, s.last_name ASC
    ");
    $stmt->execute([$courseId]);

    return array_map(static function (array $row): array {
        $phone = normalizeWhatsappPhone((string)($row['phone'] ?? ''));
        return [
            'student_id' => (int)$row['student_id'],
            'name' => trim((string)($row['first_name'] ?? '') . ' ' . (string)($row['last_name'] ?? '')),
            'email' => (string)($row['email'] ?? ''),
            'phone' => $phone,
            'course_title' => (string)($row['course_title'] ?? ''),
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
}

function notifyInstructorAssigned(PDO $pdo, int $enrollmentId, int $instructorId): void
{
    try {
        // Fetch details
        $stmt = $pdo->prepare("
            SELECT 
                s.id AS student_id,
                s.first_name AS student_first_name,
                CONCAT(s.first_name, ' ', s.last_name) AS student_name,
                s.email AS student_email,
                c.id AS course_id,
                c.title AS course_title,
                i.full_name AS instructor_name,
                i.email AS instructor_email
            FROM lms_enrollments e
            JOIN lms_students s ON s.id = e.student_id
            JOIN lms_courses c ON c.id = e.course_id
            JOIN lms_instructors i ON i.id = ?
            WHERE e.id = ?
            LIMIT 1
        ");
        $stmt->execute([$instructorId, $enrollmentId]);
        $details = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$details) {
            return;
        }

        // 1. Add database notification for the student
        try {
            $pdo->exec("ALTER TABLE lms_student_notifications MODIFY COLUMN type ENUM('assignment','live_session','instructor') NOT NULL");
        } catch (Throwable $e) {}

        $notifStmt = $pdo->prepare("
            INSERT INTO lms_student_notifications (student_id, course_id, type, title, message, action_url)
            VALUES (?, ?, 'instructor', 'Instructor Assigned', ?, 'dashboard.php')
        ");
        $notifMsg = "Your course instructor has been assigned! You can now contact and learn with " . $details['instructor_name'] . ".";
        $notifStmt->execute([(int)$details['student_id'], (int)$details['course_id'], $notifMsg]);

        // 2. Send email to student
        require_once __DIR__ . '/../config/mail.php';
        require_once __DIR__ . '/email_templates.php';

        $studentMail = emailStudentInstructorAssigned(
            $details['student_first_name'],
            $details['course_title'],
            $details['instructor_name'],
            $details['instructor_email']
        );
        send_mail($details['student_email'], 'Instructor Assigned — Grafix@Mirror LMS', $studentMail);

        // 3. Send email to active administrators
        $adminStmt = $pdo->query("SELECT first_name, last_name, email FROM lms_admins WHERE status='active' LIMIT 10");
        $admins = $adminStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($admins as $admin) {
            $adminName = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
            if ($adminName === '') {
                $adminName = 'Admin';
            }
            $adminMail = emailAdminInstructorAssigned(
                $adminName,
                $details['student_name'],
                $details['course_title'],
                $details['instructor_name']
            );
            send_mail($admin['email'], 'Instructor Assignment Confirmation — Admin', $adminMail);
        }
    } catch (Throwable $e) {
        // Log to prevent breaking registration/payment flows
        error_log("Failed to send instructor assignment notifications: " . $e->getMessage());
    }
}
