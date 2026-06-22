<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $host    = $_ENV['DB_HOST']    ?? 'localhost';
    $dbname  = $_ENV['DB_NAME']    ?? '';
    $user    = $_ENV['DB_USER']    ?? 'root';
    $pass    = $_ENV['DB_PASS']    ?? '';
    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    // In production, never expose DB errors to the browser
    $isProduction = ($_ENV['APP_ENV'] ?? 'local') === 'production';
    if ($isProduction) {
        error_log('DB connection failed: ' . $e->getMessage());
        die('Service temporarily unavailable. Please try again later.');
    }
    die('Database connection failed: ' . $e->getMessage());
}
