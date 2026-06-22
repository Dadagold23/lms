<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$countryIso2 = strtoupper(trim((string)($_GET['country_iso2'] ?? '')));
if ($countryIso2 === '') {
    echo json_encode(['ok' => true, 'states' => []]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, name
    FROM ref_states
    WHERE country_iso2 = ?
    ORDER BY name
");
$stmt->execute([$countryIso2]);

echo json_encode(['ok' => true, 'states' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
