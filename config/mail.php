<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

function mailer_env(string $key, string $default = ''): string
{
    return trim((string)($_ENV[$key] ?? $default));
}

function smtp_expect($socket, array $allowedCodes): string
{
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (preg_match('/^\d{3} /', $line) === 1) {
            break;
        }
    }

    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $allowedCodes, true)) {
        throw new RuntimeException('SMTP error: ' . trim($response));
    }

    return $response;
}

function smtp_command($socket, string $command, array $allowedCodes): string
{
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $allowedCodes);
}

function smtp_send_mail(
    string $to,
    string $subject,
    string $message,
    string $from,
    string $fromName
): bool {
    $host = mailer_env('MAIL_HOST');
    $port = (int)mailer_env('MAIL_PORT', '587');
    $username = mailer_env('MAIL_USERNAME');
    $password = mailer_env('MAIL_PASSWORD');
    $encryption = strtolower(mailer_env('MAIL_ENCRYPTION', 'tls'));
    $timeout = (int)mailer_env('MAIL_TIMEOUT', '30');

    if ($host === '' || $username === '' || $password === '') {
        return false;
    }

    $transportHost = $encryption === 'ssl' ? 'ssl://' . $host : $host;
    $socket = @stream_socket_client(
        $transportHost . ':' . $port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT
    );

    if (!is_resource($socket)) {
        throw new RuntimeException("SMTP connection failed: {$errstr} ({$errno})");
    }

    stream_set_timeout($socket, $timeout);

    try {
        smtp_expect($socket, [220]);
        smtp_command($socket, 'EHLO localhost', [250]);

        if ($encryption === 'tls') {
            smtp_command($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('SMTP STARTTLS negotiation failed.');
            }
            smtp_command($socket, 'EHLO localhost', [250]);
        }

        smtp_command($socket, 'AUTH LOGIN', [334]);
        smtp_command($socket, base64_encode($username), [334]);
        smtp_command($socket, base64_encode($password), [235]);

        smtp_command($socket, 'MAIL FROM:<' . $from . '>', [250]);
        smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtp_command($socket, 'DATA', [354]);

        $encodedSubject = function_exists('mb_encode_mimeheader')
            ? mb_encode_mimeheader($subject, 'UTF-8')
            : '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $headers = [];
        $headers[] = 'Date: ' . date(DATE_RFC2822);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . $encodedSubject;

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $message . "\r\n.";
        fwrite($socket, $data . "\r\n");
        smtp_expect($socket, [250]);
        smtp_command($socket, 'QUIT', [221]);
    } finally {
        fclose($socket);
    }

    return true;
}

function send_mail(
    string $to,
    string $subject,
    string $message,
    string $from = ''
): bool {
    if ($from === '') {
        $from = mailer_env('MAIL_FROM', 'no-reply@mirrorageconcepts.com');
    }

    $fromName = mailer_env('MAIL_FROM_NAME', 'Mirror Age Concepts');
    $mailer = strtolower(mailer_env('MAIL_MAILER', 'mail'));

    if ($mailer === 'smtp') {
        try {
            return smtp_send_mail($to, $subject, $message, $from, $fromName);
        } catch (Throwable $e) {
            error_log('SMTP mail failure: ' . $e->getMessage());
            return false;
        }
    }

    $headers   = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type:text/html;charset=UTF-8';
    $headers[] = "From: {$fromName} <{$from}>";

    return mail($to, $subject, $message, implode("\r\n", $headers));
}
