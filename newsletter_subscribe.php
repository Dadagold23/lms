<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

startSecureSession();

function newsletterReturn(): void
{
    $fallback = 'index.php#newsletter';
    $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');

    if ($referer !== '') {
        $parts = parse_url($referer);
        $path = basename((string)($parts['path'] ?? ''));
        $allowed = ['index.php', 'register.php', 'login.php', 'about_us.php', 'faqs.php', 'help.php', 'cookie_policy.php', 'contact_us.php'];
        if (in_array($path, $allowed, true)) {
            redirect($referer . (str_contains($referer, '#') ? '' : '#newsletter'));
        }
    }

    redirect($fallback);
}

if (!isPost()) {
    newsletterReturn();
}

verifyCsrf($_POST['_csrf'] ?? '');

$email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['newsletter_error'] = 'Please enter a valid email address.';
    newsletterReturn();
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS lms_newsletter_subscribers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(190) NOT NULL UNIQUE,
        status ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
        source VARCHAR(60) NOT NULL DEFAULT 'footer',
        subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        unsubscribed_at DATETIME DEFAULT NULL,
        updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_newsletter_status (status),
        KEY idx_newsletter_subscribed_at (subscribed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$stmt = $pdo->prepare("
    INSERT INTO lms_newsletter_subscribers (email, status, source, subscribed_at)
    VALUES (?, 'active', 'footer', NOW())
    ON DUPLICATE KEY UPDATE status = 'active', unsubscribed_at = NULL, updated_at = NOW()
");
$stmt->execute([$email]);

$_SESSION['newsletter_ok'] = 'Thank you for subscribing.';
newsletterReturn();
