<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$stateId = (int)($_GET['state_id'] ?? 0);
if ($stateId <= 0) {
    echo json_encode(['ok' => true, 'lgas' => []]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, name
    FROM ref_lgas
    WHERE state_id = ?
    ORDER BY name
");
$stmt->execute([$stateId]);

echo json_encode(['ok' => true, 'lgas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
