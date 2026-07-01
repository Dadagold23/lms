<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| GENERAL HELPERS (PHP 7.4+ SAFE)
|--------------------------------------------------------------------------
*/

function e($value): string
{
    if ($value === null) $value = '';
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    if ($url === '') {
        $url = 'index.php';
    }
    
    // Resolve URL target
    $target = $url;
    if (!preg_match('~^(https?:)?//~i', $url) && strpos($url, '/') !== 0) {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($base, '/');
        $target = "{$base}/{$url}";
    }

    if (PHP_SAPI === 'cli') {
        echo "REDIRECT: " . $target . "\n";
    } else {
        header("Location: {$target}");
    }
    exit;
}

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    $forwardedProto = strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    return $forwardedProto === 'https';
}

function startSecureSession(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isHttpsRequest(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', isHttpsRequest(), true);
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    if (isHttpsRequest()) {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

function formatMoney($amount): string
{
    if ($amount === null || $amount === '') {
        return '₦0.00';
    }
    return '₦' . number_format((float)$amount, 2);
}

/**
 * Generate a clean, slug-based URL for a course page.
 * Accepts a course array (with 'slug' and/or 'id' keys) or a plain slug string.
 * Falls back to ?id= style if no slug is available.
 *
 * Usage:
 *   courseUrl($course)               → course/web-development-bootcamp
 *   courseUrl('web-development')     → course/web-development
 *   courseUrl($course, true)         → /course/web-development-bootcamp  (absolute path)
 */
function courseUrl(array|string $course, bool $absolute = false): string
{
    if (is_string($course)) {
        $slug = trim($course);
        $url  = $slug !== '' ? 'course/' . rawurlencode($slug) : 'course.php';
    } else {
        $slug = trim((string)($course['slug'] ?? ''));
        $id   = (int)($course['id'] ?? 0);
        if ($slug !== '') {
            $url = 'course/' . rawurlencode($slug);
        } elseif ($id > 0) {
            $url = 'course.php?id=' . $id;
        } else {
            $url = 'course.php';
        }
    }
    return $absolute ? '/' . $url : $url;
}

function appBaseUrl(): string
{
    $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($base === '/') {
        return '/';
    }
    return rtrim($base, '/') . '/';
}

function appAbsoluteUrl(string $path = ''): string
{
    // Try environment variable loaded from .env
    $envUrl = trim((string)($_ENV['APP_URL'] ?? ''));
    if ($envUrl !== '') {
        // Strip out public suffix if present to match lms root structure
        $envUrl = str_replace('/public/', '/', $envUrl);
        $envUrl = str_replace('/public', '', $envUrl);
        
        // Dynamically replace localhost with actual requested host (e.g. local IP 192.168.x.x or tunnel)
        // so that QR codes scanned by mobile devices resolve to the correct server IP/domain.
        $reqHost = $_SERVER['HTTP_HOST'] ?? '';
        if ($reqHost !== '' && $reqHost !== 'localhost') {
            if (stripos($envUrl, '://localhost') !== false) {
                $envUrl = str_ireplace('://localhost', '://' . $reqHost, $envUrl);
            }
        }
        
        return rtrim($envUrl, '/') . '/' . ltrim($path, '/');
    }

    // Dynamic fallback
    $protocol = isHttpsRequest() ? 'https://' : 'http://';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base     = appBaseUrl();
    return $protocol . $host . $base . ltrim($path, '/');
}

function isPost(): bool
{
    return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/
function csrfToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        startSecureSession();
    }

    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['_csrf'];
}

function verifyCsrf($token): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        startSecureSession();
    }

    $t = (string)$token;
    if (!isset($_SESSION['_csrf']) || !hash_equals((string)$_SESSION['_csrf'], $t)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

/*
|--------------------------------------------------------------------------
| FILE HELPERS
|--------------------------------------------------------------------------
*/
function extOf(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isImage(string $filename): bool
{
    return in_array(extOf($filename), ['jpg','jpeg','png'], true);
}

/*
|--------------------------------------------------------------------------
| CSRF FIELD (HTML hidden input)
|--------------------------------------------------------------------------
*/
function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/*
|--------------------------------------------------------------------------
| YOUTUBE EMBED
| Accepts: full URL, short URL, or embed URL → returns embed URL or null
|--------------------------------------------------------------------------
*/
function youtubeEmbedUrl(string $url): ?string
{
    $url = trim($url);
    if ($url === '') return null;

    // Already an embed URL
    if (str_contains($url, 'youtube.com/embed/')) return $url;

    // youtu.be/ID
    if (preg_match('~youtu\.be/([A-Za-z0-9_\-]{11})~', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // youtube.com/watch?v=ID
    if (preg_match('~[?&]v=([A-Za-z0-9_\-]{11})~', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| INTRO VIDEO RENDERER
| Returns HTML for either a YouTube embed or a local <video> tag
|--------------------------------------------------------------------------
*/
function renderIntroVideo(string $src, string $cssClass = 'w-100 rounded'): string
{
    $src = trim($src);
    if ($src === '') return '';

    $ytEmbed = youtubeEmbedUrl($src);
    if ($ytEmbed !== null) {
        return '<div class="ratio ratio-16x9"><iframe src="' . htmlspecialchars($ytEmbed, ENT_QUOTES, 'UTF-8')
             . '" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"'
             . ' class="' . htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8') . '" style="border:0"></iframe></div>';
    }

    // Local file
    $fileSrc = htmlspecialchars((str_starts_with($src, 'http') ? $src : 'uploads/' . ltrim($src, '/')), ENT_QUOTES, 'UTF-8');
    return '<video class="' . htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8') . '" controls preload="metadata" style="max-height:360px;object-fit:contain;background:#000">'
         . '<source src="' . $fileSrc . '" type="video/mp4">'
         . 'Your browser does not support video.</video>';
}

/*
|--------------------------------------------------------------------------
| AUTO-AWARD CREDENTIALS (BADGES & CERTIFICATES)
|--------------------------------------------------------------------------
*/
function autoAwardCredentials(int $studentId, PDO $pdo): void
{
    if ($studentId <= 0) return;

    // Find all courses where student has fully paid AND passed the exam
    $eligibleStmt = $pdo->prepare("
        SELECT c.id AS course_id
        FROM lms_courses c
        INNER JOIN lms_enrollments e ON e.course_id = c.id AND e.student_id = ?
        LEFT JOIN lms_exams ex ON ex.course_id = c.id AND ex.is_published = 1
        LEFT JOIN lms_exam_results r ON r.exam_id = ex.id AND r.student_id = ?
        WHERE (e.paid_amount >= c.price OR e.status = 'paid')
        GROUP BY c.id
        HAVING COALESCE(MAX(CASE WHEN r.status='pass' THEN 1 ELSE 0 END), 0) = 1
    ");
    $eligibleStmt->execute([$studentId, $studentId]);
    $eligibleCourses = $eligibleStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($eligibleCourses)) return;

    // 1. Auto-award badges
    $awardedBadgesStmt = $pdo->prepare("SELECT course_id FROM lms_student_badges WHERE student_id = ?");
    $awardedBadgesStmt->execute([$studentId]);
    $awardedBadges = $awardedBadgesStmt->fetchAll(PDO::FETCH_COLUMN);

    $badgesToAward = array_diff($eligibleCourses, $awardedBadges);
    if (!empty($badgesToAward)) {
        $insertBadge = $pdo->prepare("
            INSERT IGNORE INTO lms_student_badges (student_id, course_id, share_token, earned_at)
            VALUES (?, ?, ?, NOW())
        ");
        foreach ($badgesToAward as $courseId) {
            $token = bin2hex(random_bytes(16));
            $insertBadge->execute([$studentId, (int)$courseId, $token]);
        }
    }

    // 2. Auto-award certificates
    $awardedCertsStmt = $pdo->prepare("SELECT course_id FROM lms_certificates WHERE student_id = ?");
    $awardedCertsStmt->execute([$studentId]);
    $awardedCerts = $awardedCertsStmt->fetchAll(PDO::FETCH_COLUMN);

    $certsToAward = array_diff($eligibleCourses, $awardedCerts);
    if (!empty($certsToAward)) {
        $insertCert = $pdo->prepare("
            INSERT IGNORE INTO lms_certificates (student_id, course_id, certificate_code, issued_at)
            VALUES (?, ?, ?, NOW())
        ");
        foreach ($certsToAward as $courseId) {
            $code = strtoupper(bin2hex(random_bytes(6)));
            $insertCert->execute([$studentId, (int)$courseId, $code]);
        }
    }
}

/**
 * Renders the brand logo as a high-quality inline SVG.
 * Allows custom sizing and additional CSS classes.
 */
function getBrandLogoSvg(int $size = 40, string $class = ''): string
{
    $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' : '';
    return '
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="' . $size . '" height="' . $size . '"' . $classAttr . ' style="display:inline-block; vertical-align:middle; flex-shrink:0;">
      <defs>
        <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#4f46e5" />
          <stop offset="100%" stop-color="#3b82f6" />
        </linearGradient>
        <linearGradient id="dotGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#38bdf8" />
          <stop offset="100%" stop-color="#06b6d4" />
        </linearGradient>
      </defs>
      <rect width="100" height="100" rx="26" fill="url(#logoGrad)" />
      <path d="M70,50 C70,61.05 61.05,70 50,70 C38.95,70 30,61.05 30,50 C30,38.95 38.95,30 50,30 C58.28,30 65.36,35.03 68.32,42.18 L55.6,42.18 C54.34,39.06 51.42,37.05 48,37.05 C42.17,37.05 37.45,41.77 37.45,47.6 C37.45,53.43 42.17,58.15 48,58.15 C52.75,58.15 56.76,55 58.15,50.7 L48,50.7 L48,43.2 L70,43.2 L70,50 Z" fill="#ffffff" />
      <circle cx="78" cy="22" r="8" fill="url(#dotGrad)" />
    </svg>';
}

