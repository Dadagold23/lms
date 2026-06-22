<?php
declare(strict_types=1);

function liveSessionEnvCsv(string $key): array
{
    $raw = trim((string)($_ENV[$key] ?? ''));
    if ($raw === '') {
        return [];
    }

    $parts = array_map('trim', explode(',', $raw));
    return array_values(array_filter($parts, static fn(string $value): bool => $value !== ''));
}

function liveSessionChatTableExists(PDO $pdo): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'lms_session_chat_messages'");
        $cache = (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function liveSessionProvider(?string $url): string
{
    $url = strtolower(trim((string)$url));
    if ($url === '') {
        return 'none';
    }
    if (str_contains($url, 'meet.google.com')) {
        return 'google_meet';
    }
    if (str_contains($url, 'zoom.us')) {
        return 'zoom';
    }
    if (str_contains($url, 'teams.microsoft.com') || str_contains($url, 'teams.live.com')) {
        return 'teams';
    }
    if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
        return 'youtube';
    }
    if (str_contains($url, 'vimeo.com')) {
        return 'vimeo';
    }
    return 'web';
}

function liveSessionProviderLabel(?string $url): string
{
    return match (liveSessionProvider($url)) {
        'google_meet' => 'Google Meet',
        'zoom' => 'Zoom',
        'teams' => 'Microsoft Teams',
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'web' => 'Web Lecture',
        default => 'No Lecture Link',
    };
}

function liveSessionIsTeams(?string $url): bool
{
    return liveSessionProvider($url) === 'teams';
}

function liveSessionEmbedUrl(?string $url): ?string
{
    $url = trim((string)$url);
    if ($url === '') {
        return null;
    }

    $provider = liveSessionProvider($url);
    if ($provider === 'youtube') {
        if (preg_match('~youtu\.be/([A-Za-z0-9_\-]{11})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('~[?&]v=([A-Za-z0-9_\-]{11})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
    }
    if ($provider === 'vimeo' && preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }

    // Try to load lecture providers inside the LMS shell. Some providers may still
    // require opening in a new tab depending on their own framing rules.
    return $url;
}

function liveSessionAnyDeskUrl(?string $value): ?string
{
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }
    if (preg_match('~^[a-z]+:~i', $value) || preg_match('~^https?://~i', $value)) {
        return $value;
    }
    return 'anydesk:' . preg_replace('/\s+/', '', $value);
}

function liveSessionIceServers(): array
{
    $servers = [];

    $stunUrls = liveSessionEnvCsv('LIVECLASS_STUN_URLS');
    if (!empty($stunUrls)) {
        $servers[] = ['urls' => $stunUrls];
    }

    $turnUrls = liveSessionEnvCsv('LIVECLASS_TURN_URLS');
    $turnUsername = trim((string)($_ENV['LIVECLASS_TURN_USERNAME'] ?? ''));
    $turnCredential = trim((string)($_ENV['LIVECLASS_TURN_CREDENTIAL'] ?? ''));
    if (!empty($turnUrls) && $turnUsername !== '' && $turnCredential !== '') {
        $servers[] = [
            'urls' => $turnUrls,
            'username' => $turnUsername,
            'credential' => $turnCredential,
        ];
    }

    return $servers;
}

function liveSessionHasTurnServer(): bool
{
    return !empty(liveSessionEnvCsv('LIVECLASS_TURN_URLS'))
        && trim((string)($_ENV['LIVECLASS_TURN_USERNAME'] ?? '')) !== ''
        && trim((string)($_ENV['LIVECLASS_TURN_CREDENTIAL'] ?? '')) !== '';
}

function liveSessionHasIceServers(): bool
{
    return !empty(liveSessionIceServers());
}

function liveSessionForceRelay(): bool
{
    return in_array(strtolower(trim((string)($_ENV['LIVECLASS_FORCE_RELAY'] ?? 'false'))), ['1', 'true', 'yes', 'on'], true);
}
