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
    // If the URL is already absolute or root-relative, redirect directly
    if (preg_match('~^(https?:)?//~i', $url) || strpos($url, '/') === 0) {
        header("Location: {$url}");
    } else {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($base, '/');
        header("Location: {$base}/{$url}");
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
        session_start();
    }

    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['_csrf'];
}

function verifyCsrf($token): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
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
