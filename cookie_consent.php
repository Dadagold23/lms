<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

startSecureSession();

header('Content-Type: application/json; charset=utf-8');

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$csrf = (string)($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (empty($_SESSION['_csrf']) || !hash_equals((string)$_SESSION['_csrf'], $csrf)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$choice = (string)($_POST['choice'] ?? '');
if (!in_array($choice, ['accepted', 'necessary'], true)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid consent choice']);
    exit;
}

setcookie('lms_cookie_consent', $choice, [
    'expires' => time() + (60 * 60 * 24 * 180),
    'path' => '/',
    'secure' => isHttpsRequest(),
    'httponly' => true,
    'samesite' => 'Lax',
]);

echo json_encode(['ok' => true]);
