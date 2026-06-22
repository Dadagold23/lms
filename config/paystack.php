<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

return [
    'secret_key'   => $_ENV['PAYSTACK_SECRET_KEY']    ?? '',
    'public_key'   => $_ENV['PAYSTACK_PUBLIC_KEY']    ?? '',
    'callback_url' => $_ENV['PAYSTACK_CALLBACK_URL']  ?? '',
    'webhook_url'  => $_ENV['PAYSTACK_WEBHOOK_URL']   ?? '',
];
