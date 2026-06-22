<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

verifyCsrf($_POST['_csrf'] ?? '');

$sessionId = (int)($_POST['session_id'] ?? 0);
if ($sessionId <= 0 || empty($_FILES['recording']['name'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid upload request.']);
    exit;
}

$userId = (int)($_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? $_SESSION['instructor']['id'] ?? 0);
$isAdmin = !empty($_SESSION['admin']) || (($_SESSION['user']['role'] ?? '') === 'admin');
$isInstructor = !empty($_SESSION['instructor']) || (($_SESSION['user']['role'] ?? '') === 'instructor');

if ($userId <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Authentication required.']);
    exit;
}

if ($isInstructor) {
    $stmt = $pdo->prepare("SELECT id FROM lms_live_sessions WHERE id = ? AND instructor_id = ? LIMIT 1");
    $stmt->execute([$sessionId, $userId]);
} elseif ($isAdmin) {
    $stmt = $pdo->prepare("SELECT id FROM lms_live_sessions WHERE id = ? LIMIT 1");
    $stmt->execute([$sessionId]);
} else {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Only instructors or admins can upload recordings.']);
    exit;
}

if (!$stmt->fetchColumn()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Access denied for this session.']);
    exit;
}

$tmp = (string)($_FILES['recording']['tmp_name'] ?? '');
$size = (int)($_FILES['recording']['size'] ?? 0);
$ext = strtolower(pathinfo((string)$_FILES['recording']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['webm', 'mp4'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Recording must be webm or mp4.']);
    exit;
}

if ($size <= 0 || $size > 250 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Recording is empty or too large (max 250MB).']);
    exit;
}

$dir = __DIR__ . '/uploads/live_recordings/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$file = 'live_session_' . $sessionId . '_' . date('Ymd_His') . '.' . $ext;
if (!move_uploaded_file($tmp, $dir . $file)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not save recording file.']);
    exit;
}

$relativeUrl = 'uploads/live_recordings/' . $file;
$pdo->prepare("UPDATE lms_live_sessions SET recording_url = ? WHERE id = ?")
    ->execute([$relativeUrl, $sessionId]);

echo json_encode(['ok' => true, 'url' => $relativeUrl]);
