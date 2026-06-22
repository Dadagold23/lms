<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

if (($_ENV['APP_ENV'] ?? 'production') !== 'local') {
    http_response_code(404);
    exit('Not found.');
}

$input = $_GET['p'] ?? 'Admin@123';
echo password_hash($input, PASSWORD_DEFAULT);
