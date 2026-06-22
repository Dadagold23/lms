<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/live_session_tools.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$input = json_decode($raw ?: '[]', true);
if (!is_array($input)) {
    $input = [];
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !empty($input['csrf'])) {
    verifyCsrf((string)$input['csrf']);
}

$sessionId = (int)($input['session_id'] ?? 0);
$participantKey = trim((string)($input['participant_key'] ?? ''));
$action = trim((string)($input['action'] ?? ''));

if ($sessionId <= 0 || $participantKey === '' || $action === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid request']);
    exit;
}

$userId = (int)($_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? $_SESSION['instructor']['id'] ?? 0);
$studentId = (int)($_SESSION['user']['id'] ?? 0);
$isAdmin = !empty($_SESSION['admin']) || (($_SESSION['user']['role'] ?? '') === 'admin');
$isInstructor = !empty($_SESSION['instructor']) || (($_SESSION['user']['role'] ?? '') === 'instructor');

if ($userId <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Authentication required']);
    exit;
}

if ($isInstructor) {
    $stmt = $pdo->prepare("
        SELECT s.id
        FROM lms_live_sessions s
        WHERE s.id = ? AND s.instructor_id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId, $userId]);
} elseif ($isAdmin) {
    $stmt = $pdo->prepare("SELECT id FROM lms_live_sessions WHERE id = ? LIMIT 1");
    $stmt->execute([$sessionId]);
} else {
    $stmt = $pdo->prepare("
        SELECT s.id
        FROM lms_live_sessions s
        JOIN lms_enrollments e ON e.course_id = s.course_id
        WHERE s.id = ? AND e.student_id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId, $studentId]);
}

if (!$stmt->fetchColumn()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Access denied']);
    exit;
}

if (!liveSessionChatTableExists($pdo)) {
    // The chat helper is the cheapest place to check that the hybrid migration ran.
    // Participants/signals need the same migration.
}

$participantRole = $isAdmin ? 'admin' : ($isInstructor ? 'instructor' : 'student');
$displayName = trim((string)($input['display_name'] ?? ''));
if ($displayName === '') {
    $displayName = trim((string)(
        ($_SESSION['user']['first_name'] ?? $_SESSION['instructor']['full_name'] ?? $_SESSION['admin']['full_name'] ?? '')
        . ' '
        . ($_SESSION['user']['last_name'] ?? '')
    ));
}
if ($displayName === '') {
    $displayName = ucfirst($participantRole);
}

function json_ok(array $data = []): void
{
    echo json_encode(['ok' => true] + $data);
    exit;
}

function ensureLiveTables(PDO $pdo): void
{
    $needed = ['lms_session_participants', 'lms_session_signals', 'lms_session_chat_messages'];
    foreach ($needed as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table));
        if (!$stmt->fetchColumn()) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Live classroom migration not yet applied.']);
            exit;
        }
    }
}

ensureLiveTables($pdo);

if ($action === 'join' || $action === 'heartbeat') {
    $pdo->prepare("
        INSERT INTO lms_session_participants (session_id, participant_key, user_id, display_name, role, last_seen_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            user_id = VALUES(user_id),
            display_name = VALUES(display_name),
            role = VALUES(role),
            last_seen_at = NOW()
    ")->execute([$sessionId, $participantKey, $userId, $displayName, $participantRole]);
    $pdo->prepare("DELETE FROM lms_session_participants WHERE session_id = ? AND last_seen_at < DATE_SUB(NOW(), INTERVAL 20 SECOND)")
        ->execute([$sessionId]);
    json_ok();
}

if ($action === 'leave') {
    $pdo->prepare("DELETE FROM lms_session_participants WHERE session_id = ? AND participant_key = ?")
        ->execute([$sessionId, $participantKey]);
    $pdo->prepare("DELETE FROM lms_session_signals WHERE session_id = ? AND (from_key = ? OR to_key = ?)")
        ->execute([$sessionId, $participantKey, $participantKey]);
    json_ok();
}

if ($action === 'participants') {
    $pdo->prepare("DELETE FROM lms_session_participants WHERE session_id = ? AND last_seen_at < DATE_SUB(NOW(), INTERVAL 20 SECOND)")
        ->execute([$sessionId]);
    $stmt = $pdo->prepare("
        SELECT participant_key, display_name, role
        FROM lms_session_participants
        WHERE session_id = ?
        ORDER BY role DESC, display_name ASC
    ");
    $stmt->execute([$sessionId]);
    json_ok(['participants' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if ($action === 'send_signal') {
    $toKey = trim((string)($input['to_key'] ?? ''));
    $signalType = trim((string)($input['signal_type'] ?? ''));
    $payload = (string)($input['payload'] ?? '');
    if ($toKey === '' || !in_array($signalType, ['offer', 'answer', 'ice'], true) || $payload === '') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid signal payload']);
        exit;
    }
    $pdo->prepare("
        INSERT INTO lms_session_signals (session_id, from_key, to_key, signal_type, payload)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([$sessionId, $participantKey, $toKey, $signalType, $payload]);
    json_ok();
}

if ($action === 'poll_signals') {
    $stmt = $pdo->prepare("
        SELECT id, from_key, signal_type, payload
        FROM lms_session_signals
        WHERE session_id = ? AND to_key = ?
        ORDER BY id ASC
        LIMIT 100
    ");
    $stmt->execute([$sessionId, $participantKey]);
    $signals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($signals)) {
        $ids = implode(',', array_map('intval', array_column($signals, 'id')));
        $pdo->exec("DELETE FROM lms_session_signals WHERE id IN ({$ids})");
    }
    json_ok(['signals' => $signals]);
}

if ($action === 'send_chat') {
    $message = trim((string)($input['message'] ?? ''));
    if ($message === '') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Message is required']);
        exit;
    }
    $pdo->prepare("
        INSERT INTO lms_session_chat_messages (session_id, student_id, instructor_id, sender_name, sender_role, message)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([
        $sessionId,
        $participantRole === 'student' ? $studentId : null,
        $participantRole === 'instructor' ? $userId : null,
        $displayName,
        $participantRole,
        $message
    ]);
    json_ok();
}

if ($action === 'chat_messages') {
    $afterId = (int)($input['after_id'] ?? 0);
    $stmt = $pdo->prepare("
        SELECT id, sender_name, sender_role, message, created_at, student_id, instructor_id
        FROM lms_session_chat_messages
        WHERE session_id = ? AND id > ?
        ORDER BY id ASC
        LIMIT 100
    ");
    $stmt->execute([$sessionId, $afterId]);
    $messages = array_map(static function(array $row): array {
        return [
            'id' => (int)$row['id'],
            'sender_name' => (string)$row['sender_name'],
            'sender_role' => (string)$row['sender_role'],
            'message_html' => nl2br(e((string)$row['message'])),
            'time_label' => date('H:i', strtotime((string)$row['created_at'])),
            'is_self' => (($row['sender_role'] === 'student' && (int)($row['student_id'] ?? 0) === $studentId)
                || ($row['sender_role'] === 'instructor' && $isInstructor && (int)($row['instructor_id'] ?? 0) === $userId)
                || ($row['sender_role'] === 'admin' && $isAdmin)),
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    json_ok(['messages' => $messages]);
}

http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'Unknown action']);
