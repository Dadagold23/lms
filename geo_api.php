<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? '';

try {

    // /geo_api.php?type=states&country_iso2=NG
    if ($type === 'states') {
        $iso2 = strtoupper(trim((string)($_GET['country_iso2'] ?? '')));

        if ($iso2 === '' || strlen($iso2) !== 2) {
            echo json_encode(['states' => []]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT id, name, code
            FROM ref_states
            WHERE country_iso2 = ?
            ORDER BY name
        ");
        $stmt->execute([$iso2]);

        echo json_encode(['states' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // /geo_api.php?type=lgas&state_id=1
    if ($type === 'lgas') {
        $stateId = (int)($_GET['state_id'] ?? 0);
        if ($stateId <= 0) {
            echo json_encode(['lgas' => []]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT id, name
            FROM ref_lgas
            WHERE state_id = ?
            ORDER BY name
        ");
        $stmt->execute([$stateId]);

        echo json_encode(['lgas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // /geo_api.php?type=cities&state_id=1  (optional if you later seed cities)
    if ($type === 'cities') {
        $stateId = (int)($_GET['state_id'] ?? 0);
        if ($stateId <= 0) {
            echo json_encode(['cities' => []]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT id, name
            FROM ref_cities
            WHERE state_id = ?
            ORDER BY name
        ");
        $stmt->execute([$stateId]);

        echo json_encode(['cities' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    echo json_encode([]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
