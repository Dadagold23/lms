<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$code = trim($_GET['code'] ?? '');
if ($code === '') {
    http_response_code(400);
    exit('Missing certificate code.');
}

$stmt = $pdo->prepare("
    SELECT c.certificate_code, c.issued_at,
           s.first_name, s.last_name, s.email,
           crs.id AS course_id, crs.title AS course_title
    FROM lms_certificates c
    JOIN lms_students s ON s.id = c.student_id
    JOIN lms_courses crs ON crs.id = c.course_id
    WHERE c.certificate_code = ?
    LIMIT 1
");
$stmt->execute([$code]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    exit('Certificate not found.');
}

$studentName = trim(($row['first_name'] ?? '').' '.($row['last_name'] ?? ''));
$issueDate = date('F d, Y', strtotime((string)$row['issued_at']));

$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$currentUrl = $proto . '://' . $host . $_SERVER['REQUEST_URI'];
$ogImageUrl = $proto . '://' . $host . $uri . '/assets/img/og-certificate.png';
$qrImg      = 'https://chart.googleapis.com/chart?chs=140x140&cht=qr&chld=M|0&chl=' . urlencode($currentUrl);
?>
<!doctype html>
<html lang="en">
<head>
<title>Verified Certificate: <?= e($row['course_title']) ?> — <?= e($studentName) ?> 🎓</title>
<meta name="description" content="Verified certificate issued to <?= e($studentName) ?> on Grafix@Mirror LMS. Code: <?= e($row['certificate_code']) ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e($currentUrl) ?>">
<meta property="og:title" content="Verified Certificate: <?= e($row['course_title']) ?> — <?= e($studentName) ?> 🎓">
<meta property="og:description" content="Verified certificate issued to <?= e($studentName) ?> on Grafix@Mirror LMS. Code: <?= e($row['certificate_code']) ?>">
<meta property="og:image" content="<?= e($ogImageUrl) ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?= e($currentUrl) ?>">
<meta name="twitter:title" content="Verified Certificate: <?= e($row['course_title']) ?> — <?= e($studentName) ?> 🎓">
<meta name="twitter:description" content="Verified certificate issued to <?= e($studentName) ?> on Grafix@Mirror LMS. Code: <?= e($row['certificate_code']) ?>">
<meta name="twitter:image" content="<?= e($ogImageUrl) ?>">

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
  :root {
    --bg-dark: #0a0b10;
    --card-bg: rgba(22, 28, 45, 0.45);
    --border-color: rgba(255, 255, 255, 0.08);
    --brand: #e3c162;
  }
  body {
    background-color: var(--bg-dark);
    color: #f8fafc;
    font-family: 'Inter', system-ui, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* Ambient glow backgrounds */
    background-image: 
      radial-gradient(ellipse 55% 45% at 80% 10%, rgba(124, 92, 191, 0.15) 0%, transparent 80%),
      radial-gradient(ellipse 55% 45% at 20% 90%, rgba(227, 193, 98, 0.08) 0%, transparent 80%);
    background-repeat: no-repeat;
  }
  .glass-card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  }
  .cert-icon-container {
    font-size: 4.5rem;
    color: var(--brand);
    margin-bottom: 1.5rem;
    filter: drop-shadow(0 0 15px rgba(227, 193, 98, 0.35));
  }
  /* Certificate preview — clean static card */
  .cert-preview-card {
    max-width: 420px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.12);
    background: #0f172a;
    aspect-ratio: 16/10;
    margin: 0 auto 1.5rem;
    box-shadow: 0 12px 32px rgba(0,0,0,0.6);
    overflow: hidden;
    position: relative;
  }
  .cert-preview-card img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    opacity: 0.92;
  }
  .cert-preview-badge {
    position: absolute;
    bottom: 10px; right: 12px;
    background: rgba(10,11,16,0.82);
    border: 1px solid var(--brand);
    border-radius: 6px;
    padding: 4px 10px;
    font-size: .68rem;
    font-weight: 700;
    color: var(--brand);
    letter-spacing: .06em;
    text-transform: uppercase;
  }
  /* QR Code block */
  .qr-block {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 1.1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
  }
  .qr-img-wrap {
    flex-shrink: 0;
    width: 96px; height: 96px;
    background: #fff;
    border-radius: 8px;
    padding: 6px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.4);
  }
  .qr-img-wrap img {
    width: 100%; height: 100%;
    display: block;
  }
  .qr-text { flex: 1; text-align: left; }
  .qr-text strong {
    display: block;
    font-size: .82rem;
    color: #f1f5f9;
    margin-bottom: .3rem;
  }
  .qr-text small {
    font-size: .73rem;
    color: #64748b;
    line-height: 1.45;
    word-break: break-all;
  }
  .verify-pill {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
    font-size: .8rem;
    font-weight: 700;
    padding: .35rem .9rem;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
  }
  .meta-label {
    font-size: .78rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .2rem;
  }
  .meta-value {
    font-size: .95rem;
    font-weight: 600;
    color: #f1f5f9;
  }
  .btn-action {
    border-radius: 10px;
    padding: .68rem 1.5rem;
    font-weight: 700;
    font-size: .9rem;
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
  }
  .btn-facebook-primary {
    background-color: #1877f2;
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 14px rgba(24, 119, 242, 0.35);
  }
  .btn-facebook-primary:hover {
    background-color: #166fe5;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(24, 119, 242, 0.5);
  }
  .btn-secondary-option {
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .btn-secondary-option:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
  }
  .footer-brand {
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    padding: 1.5rem 0;
    text-align: center;
    font-size: .8rem;
    color: #64748b;
  }
</style>
</head>
<body>

<!-- TOP BRAND BAR -->
<header class="container py-3">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div style="width:30px;height:30px;background:var(--brand);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#0f172a;font-weight:800;font-size:.85rem">G</div>
      <span class="fw-bold" style="font-size:1rem;color:#f8fafc;letter-spacing:.05em">Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </div>
    <span class="text-muted small">Official Credential Validation</span>
  </div>
</header>

<main class="container py-5 my-auto" style="max-width: 680px;">
  <div class="glass-card p-4 p-md-5 text-center">
    
    <!-- Verified Certificate Pill -->
    <div class="mb-4">
      <span class="verify-pill">
        <i class="fa fa-circle-check"></i> Verified Certificate
      </span>
    </div>

    <!-- Certificate Preview (static, clean) -->
    <div class="cert-preview-card mb-4">
      <img src="assets/img/og-certificate.png" alt="<?= e($row['course_title']) ?> Certificate">
      <div class="cert-preview-badge"><i class="fa fa-certificate me-1"></i> Verified</div>
    </div>

    <!-- Header Credentials -->
    <h3 class="fw-bold mb-1" style="color:var(--brand);">Certificate of Completion</h3>
    <p class="text-white-50 mb-4" style="font-size:.9rem; max-width: 480px; margin:0 auto;">
      Successfully awarded to the student below in recognition of completing all curriculum, evaluation, and assessment benchmarks.
    </p>

    <hr style="border-color: rgba(255,255,255,0.08);" class="my-4">

    <!-- Credentials Metadata Grid -->
    <div class="row g-3 text-start mb-4">
      <div class="col-6 col-sm-6">
        <div class="meta-label">Recipient</div>
        <div class="meta-value"><?= e($studentName) ?></div>
      </div>
      <div class="col-6 col-sm-6">
        <div class="meta-label">Course Completed</div>
        <div class="meta-value"><?= e($row['course_title']) ?></div>
      </div>
      <div class="col-6 col-sm-6">
        <div class="meta-label">Issued On</div>
        <div class="meta-value"><?= e($issueDate) ?></div>
      </div>
      <div class="col-6 col-sm-6">
        <div class="meta-label">Certificate ID</div>
        <div class="meta-value" style="font-family: monospace; font-size:.88rem; color:var(--brand);"><?= e($row['certificate_code']) ?></div>
      </div>
    </div>

    <!-- QR Code Verification Block -->
    <div class="qr-block text-start">
      <div class="qr-img-wrap">
        <img src="<?= e($qrImg) ?>" alt="Scan to verify certificate">
      </div>
      <div class="qr-text">
        <strong><i class="fa fa-qrcode me-1" style="color:var(--brand)"></i> Scan to Verify</strong>
        <small>Scan this QR code with any device to instantly verify the authenticity of this certificate on Grafix@Mirror LMS.</small>
        <div class="mt-2">
          <span style="font-size:.68rem; color:#475569; font-family:monospace; word-break:break-all;"><?= e($currentUrl) ?></span>
        </div>
      </div>
    </div>

    <!-- Share block with Facebook as Primary -->
    <div class="mb-4 text-start p-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
      <h6 class="text-white fw-bold mb-3" style="font-size:.85rem; letter-spacing:0.02em;"><i class="fa fa-share-nodes text-warning me-1"></i> SHARE THIS CERTIFICATE:</h6>
      
      <!-- Primary Share Target (Facebook) -->
      <div class="d-grid mb-3">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-action btn-facebook-primary">
          <i class="fab fa-facebook"></i> Share on Facebook (Primary)
        </a>
      </div>

      <!-- Secondary Share Options -->
      <div class="row g-2" id="certShareRow">
        <div class="col-6 col-sm-4 cert-share-col">
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-action btn-secondary-option w-100 py-2" style="font-size:.78rem">
            <i class="fab fa-linkedin-in"></i> LinkedIn
          </a>
        </div>
        <div class="col-6 col-sm-4 cert-share-col">
          <?php 
            $shareText = rawurlencode("I have successfully earned my Certificate of Completion for " . $row['course_title'] . " on Grafix@Mirror LMS! Validate here: ");
          ?>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= $shareText ?>" target="_blank" class="btn-action btn-secondary-option w-100 py-2" style="font-size:.78rem">
            <i class="fab fa-x-twitter"></i> Twitter/X
          </a>
        </div>
        <div class="col-12 col-sm-4 cert-share-col">
          <button onclick="navigator.clipboard.writeText('<?= $currentUrl ?>').then(() => alert('Verification link copied!'))" class="btn-action btn-secondary-option w-100 py-2" style="font-size:.78rem; cursor:pointer;">
            <i class="fa fa-copy"></i> Copy Link
          </button>
        </div>
        <div class="col-6 col-sm-4 d-none cert-share-col" id="certWebShareCol">
          <button id="certWebShareBtn" class="btn-action btn-secondary-option w-100 py-2" style="font-size:.78rem; cursor:pointer; background:rgba(168, 85, 247, 0.1); border-color: rgba(168, 85, 247, 0.3); color: #c084fc !important;">
            <i class="fa fa-share-nodes"></i> Share via App
          </button>
        </div>
      </div>
    </div>

    <!-- Portal Action -->
    <div class="d-grid">
      <a href="course.php?id=<?= (int)$row['course_id'] ?>" class="btn-action btn-secondary-option" style="border-color: rgba(227,193,98,0.2); color:var(--brand)">
        <i class="fa fa-graduation-cap"></i> Explore Grafix@Mirror LMS
      </a>
    </div>

  </div>
</main>

<footer class="footer-brand">
  <div class="container">
    <div>Mirror Age Concepts &copy; <?= date('Y') ?> &nbsp;|&nbsp; Grafix@Mirror Learning Portal</div>
    <div class="small mt-1 text-muted">This credential is digitally verified and validated against real LMS enrollment records.</div>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (navigator.share) {
    const webShareCol = document.getElementById('certWebShareCol');
    const cols = document.querySelectorAll('.cert-share-col');
    if (webShareCol) {
      webShareCol.classList.remove('d-none');
      cols.forEach(c => {
        c.classList.remove('col-sm-4', 'col-12');
        c.classList.add('col-6', 'col-sm-3');
      });
      document.getElementById('certWebShareBtn').addEventListener('click', function() {
        navigator.share({
          title: document.title,
          text: `Check out my verified Certificate of Completion on Grafix@Mirror LMS!`,
          url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
      });
    }
  }
});
</script>
</body>
</html>
