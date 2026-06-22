<?php
/**
 * SEO Head Include — Grafix@Mirror LMS
 * Usage: include __DIR__ . '/includes/seo.php';
 * Override before including:
 *   $seoTitle       = 'Page Title';
 *   $seoDesc        = 'Page description';
 *   $seoKeywords    = 'keyword1, keyword2';
 *   $seoCanonical   = 'https://lms.mirrorageconcepts.com/page.php';
 *   $seoNoIndex     = true;   // for private/auth pages
 *   $seoOgImage     = 'https://lms.mirrorageconcepts.com/assets/img/og-image.jpg';
 *   $seoOgType      = 'article'; // default: website
 */
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// Inline escape — does NOT depend on helpers.php
function _h(mixed $v): string {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    startSecureSession();
}

// Load APP_URL from env if not already loaded
if (empty($_ENV['APP_URL']) && file_exists(dirname(__DIR__).'/.env')) {
    foreach (file(dirname(__DIR__).'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        if (trim($k) === 'APP_URL') { $_ENV['APP_URL'] = trim($v); break; }
    }
}

$_siteName    = 'Grafix@Mirror LMS — Mirror Age Concepts';
$_siteUrl     = rtrim($_ENV['APP_URL'] ?? 'https://lms.mirrorageconcepts.com', '/');
$_defaultDesc = 'Grafix@Mirror LMS is the official learning management system of Mirror Age Concepts — Nigeria\'s professional technology training institute. Enrol in Data Science, AI, Web Development, Cybersecurity, and more.';
$_defaultKw   = 'LMS Nigeria, tech training Nigeria, data science course Nigeria, AI course Nigeria, web development Nigeria, Mirror Age Concepts, Grafix Mirror LMS, online learning Nigeria';
$_defaultOg   = $_siteUrl . '/assets/img/og-image.svg';
$_themeColor  = '#4f46e5';

$_title     = isset($seoTitle)     ? _h($seoTitle) . ' | ' . $_siteName : $_siteName;
$_desc      = isset($seoDesc)      ? _h($seoDesc)  : $_defaultDesc;
$_kw        = isset($seoKeywords)  ? _h($seoKeywords) : $_defaultKw;
$_canonical = isset($seoCanonical) ? _h($seoCanonical) : $_siteUrl . '/' . ltrim(basename($_SERVER['PHP_SELF'] ?? ''), '/');
$_noIndex   = $seoNoIndex ?? false;
$_ogImage   = isset($seoOgImage)   ? _h($seoOgImage) : $_defaultOg;
$_ogType    = isset($seoOgType)    ? _h($seoOgType)  : 'website';

// Detect base path for favicon links (works on localhost/lms/ and production /)
$_basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
// Normalise: if running from root, basePath is empty
$_faviconBase = $_basePath !== '' ? $_basePath : '';
$_cookieConsentToken = session_status() === PHP_SESSION_ACTIVE ? csrfToken() : '';
$_hasCookieConsent = isset($_COOKIE['lms_cookie_consent']);
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Primary SEO -->
<title><?= $_title ?></title>
<meta name="description" content="<?= $_desc ?>">
<meta name="keywords" content="<?= $_kw ?>">
<meta name="author" content="Mirror Age Concepts">
<meta name="robots" content="<?= $_noIndex ? 'noindex,nofollow' : 'index,follow' ?>">
<?php if (!$_noIndex): ?>
<link rel="canonical" href="<?= $_canonical ?>">
<?php endif; ?>

<!-- Open Graph / Facebook -->
<meta property="og:type"        content="<?= $_ogType ?>">
<meta property="og:url"         content="<?= $_canonical ?>">
<meta property="og:title"       content="<?= $_title ?>">
<meta property="og:description" content="<?= $_desc ?>">
<meta property="og:image"       content="<?= $_ogImage ?>">
<meta property="og:site_name"   content="Grafix@Mirror LMS">
<meta property="og:locale"      content="en_NG">

<!-- Twitter Card -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@mirrorageconcepts">
<meta name="twitter:title"       content="<?= $_title ?>">
<meta name="twitter:description" content="<?= $_desc ?>">
<meta name="twitter:image"       content="<?= $_ogImage ?>">

<!-- Favicon -->
<link rel="icon"             type="image/svg+xml"  href="<?= $_faviconBase ?>/assets/img/favicon.svg">
<link rel="icon"             type="image/png"      href="<?= $_faviconBase ?>/assets/img/favicon-32.png" sizes="32x32">
<link rel="icon"             type="image/png"      href="<?= $_faviconBase ?>/assets/img/favicon-16.png" sizes="16x16">
<link rel="apple-touch-icon"                       href="<?= $_faviconBase ?>/assets/img/apple-touch-icon.png" sizes="180x180">
<link rel="manifest"                               href="<?= $_faviconBase ?>/assets/img/site.webmanifest">
<meta name="theme-color"    content="<?= $_themeColor ?>">
<meta name="msapplication-TileColor" content="<?= $_themeColor ?>">
<script>
window.lmsCookieConsent = {
  csrfToken: "<?= _h($_cookieConsentToken) ?>",
  endpoint: "<?= $_faviconBase ?>/cookie_consent.php",
  hasChoice: <?= $_hasCookieConsent ? 'true' : 'false' ?>
};
</script>
<script src="<?= $_faviconBase ?>/assets/js/cookie-consent.js" defer></script>

<!-- Structured Data — Organisation -->
<?php if (!$_noIndex): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "name": "Mirror Age Concepts",
  "alternateName": "Grafix@Mirror LMS",
  "url": "https://www.mirrorageconcepts.com",
  "logo": "<?= $_ogImage ?>",
  "sameAs": ["https://lms.mirrorageconcepts.com"],
  "address": { "@type": "PostalAddress", "addressCountry": "NG" },
  "description": "<?= addslashes($_defaultDesc) ?>"
}
</script>
<?php endif; ?>
