<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("
    SELECT iso2, name
    FROM ref_countries
    ORDER BY name
");

echo json_encode(['ok' => true, 'countries' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
