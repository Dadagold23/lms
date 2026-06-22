<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$courseId  = (int)($_GET['course_id'] ?? 0);

if ($courseId <= 0) { http_response_code(400); exit('Missing course_id'); }

/* ── Enrollment check ── */
$en = $pdo->prepare("SELECT e.id AS enrollment_id, e.paid_amount, c.price, c.title FROM lms_enrollments e JOIN lms_courses c ON c.id=e.course_id WHERE e.student_id=? AND e.course_id=? LIMIT 1");
$en->execute([$studentId, $courseId]);
$enroll = $en->fetch(PDO::FETCH_ASSOC);
if (!$enroll) { http_response_code(404); exit('Enrollment not found.'); }

$paid  = (float)($enroll['paid_amount'] ?? 0);
$price = (float)($enroll['price'] ?? 0);
if ($paid < $price) { http_response_code(403); exit('Complete payment to download certificate.'); }

/* ── Exam check ── */
$examOk = $pdo->prepare("SELECT COALESCE(MAX(r.percent),0) AS best_score, COALESCE(MAX(CASE WHEN r.status='pass' THEN 1 ELSE 0 END),0) AS exam_passed FROM lms_exams ex LEFT JOIN lms_exam_results r ON r.exam_id=ex.id AND r.student_id=? WHERE ex.course_id=? AND ex.is_published=1");
$examOk->execute([$studentId, $courseId]);
$examRow    = $examOk->fetch(PDO::FETCH_ASSOC);
$bestScore  = (float)($examRow['best_score']  ?? 0);
$examPassed = (int)($examRow['exam_passed']   ?? 0);
if (!$examPassed) { http_response_code(403); exit("Pass the exam (50%+) to download your certificate. Best score: ".number_format($bestScore,1)."%."); }

/* ── Issue certificate ── */
$stmt = $pdo->prepare("SELECT certificate_code, issued_at FROM lms_certificates WHERE student_id = ? AND course_id = ? LIMIT 1");
$stmt->execute([$studentId, $courseId]);
$existingCert = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingCert) {
    $code     = $existingCert['certificate_code'];
    $issuedAt = $existingCert['issued_at'];
} else {
    $code     = strtoupper(bin2hex(random_bytes(6)));
    $issuedAt = date('Y-m-d H:i:s');
    $pdo->prepare("INSERT INTO lms_certificates (student_id,course_id,certificate_code,issued_at) VALUES (?,?,?,?)")
        ->execute([$studentId, $courseId, $code, $issuedAt]);
}

/* ── Student name ── */
$st = $pdo->prepare("SELECT first_name, last_name FROM lms_students WHERE id=? LIMIT 1");
$st->execute([$studentId]);
$s = $st->fetch(PDO::FETCH_ASSOC);
$firstName = trim($s['first_name'] ?? '');
$lastName  = trim($s['last_name'] ?? '');
$rawName   = trim($firstName . ' ' . $lastName);

// Auto capitalization function for block letters
function formatStudentName(string $name): string {
    // Check if the name is in block letters (all uppercase)
    if ($name === mb_strtoupper($name, 'UTF-8')) {
        // Convert to Title Case
        $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
        // Fix names starting with O' (e.g. O'connor -> O'Connor)
        $name = preg_replace_callback("/\bO'[a-z]/iu", fn($m) => mb_strtoupper($m[0], 'UTF-8'), $name);
        // Fix hyphenated names (e.g. Smith-jones -> Smith-Jones)
        $name = preg_replace_callback("/-[a-z]/iu", fn($m) => mb_strtoupper($m[0], 'UTF-8'), $name);
        // Fix Mc names (e.g. Mcdonald -> McDonald)
        $name = preg_replace_callback("/\bMc[a-z]/iu", fn($m) => 'Mc' . mb_strtoupper(mb_substr($m[0], 2, 1, 'UTF-8'), 'UTF-8'), $name);
    }
    return $name;
}

$name        = formatStudentName($rawName);
$courseTitle = (string)($enroll['title'] ?? 'Course');
$issuedDate  = date('F j, Y', strtotime($issuedAt));

/* ── Load cert settings ── */
$certSettings = [];
try {
    foreach ($pdo->query("SELECT `key`,`value` FROM lms_settings WHERE `key` LIKE 'cert_%'")->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $certSettings[$r['key']] = $r['value'];
    }
} catch (Throwable $e) {}

$directorName = $certSettings['cert_director_name'] ?? 'Director, Mirror Age Concepts';
$orgSubtitle  = $certSettings['cert_org_subtitle']  ?? 'Professional Technology Training Institute';
$certFooter   = $certSettings['cert_footer']        ?? 'This certificate is awarded in recognition of successful completion of the course requirements.';
$sigPath      = $certSettings['cert_signature']     ?? '';
$sigUrl       = (!empty($sigPath) && file_exists(__DIR__.'/uploads/'.$sigPath)) ? 'uploads/'.e($sigPath) : '';
$logoPath     = $certSettings['cert_logo']          ?? '';
$logoUrl      = (!empty($logoPath) && file_exists(__DIR__.'/uploads/'.$logoPath)) ? 'uploads/'.e($logoPath) : '';

/* ── Partner logos ── */
$partnerLogos = [];
for ($p = 1; $p <= 3; $p++) {
    $path = $certSettings['cert_partner_logo_' . $p] ?? '';
    if (!empty($path) && file_exists(__DIR__.'/uploads/'.$path)) {
        $partnerLogos[] = 'uploads/' . $path;
    }
}
if (empty($partnerLogos)) {
    $partnerLogos = [
        'assets/img/pt-mac.png',
        'assets/img/white jpeg.jpeg',
        'assets/img/pt-agwe.png'
    ];
}

/* ── QR verify URL ── */
$proto     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$base      = $proto.'://'.($_SERVER['HTTP_HOST'] ?? 'localhost').rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$verifyUrl = $base.'/certificate_verify.php?code='.urlencode($code);
$qrImg     = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data='.urlencode($verifyUrl);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Certificate of Completion';
$seoDesc    = 'Your official Certificate of Completion from Mirror Age Concepts — Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<title>Certificate — <?= e($courseTitle) ?></title>
<!-- Exact fonts from the Canva template -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
/* ── Reset ── */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

@font-face {
  font-family: 'White Chick';
  src: url('assets/fonts/WhiteChick.ttf') format('truetype');
  font-weight: normal;
  font-style: normal;
}

body {
  background: linear-gradient(rgba(13, 16, 33, 0.85), rgba(13, 16, 33, 0.85)), url('assets/img/lms_background.png') no-repeat center center fixed;
  background-size: cover;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem 1rem;
  font-family: 'Cormorant Garamond', Georgia, serif;
  color: #f8fafc;
}

/* ── Action bar ── */
.actions {
  display: flex;
  gap: .75rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  justify-content: center;
}
.btn {
  padding: .55rem 1.4rem;
  border-radius: 6px;
  font-size: .88rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'Cormorant Garamond', serif;
}
.btn-print { background: #c9a227; color: #000; font-weight: 700; }
.btn-print:hover { background: #b08d1f; }
.btn-back  { background: transparent; color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15); }
.btn-back:hover { background: rgba(255,255,255,0.05); }

/* ── Social Sharing ── */
.share-box-cert {
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 2rem;
  width: 1024px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}
.share-label-cert {
  color: #e2e8f0;
  font-size: .88rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: .5rem;
}
.share-buttons-cert {
  display: flex;
  align-items: center;
  gap: .75rem;
  flex-wrap: wrap;
}
.share-btn-primary {
  background: #1877f2;
  color: #fff !important;
  font-weight: 700;
  font-size: .82rem;
  padding: .5rem 1.1rem;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  transition: filter .2s;
  box-shadow: 0 4px 12px rgba(24,119,242,0.3);
  border: none;
}
.share-btn-primary:hover { filter: brightness(1.1); }
.share-btn-secondary {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  color: #94a3b8 !important;
  font-size: .8rem;
  padding: .5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  transition: all .2s;
  cursor: pointer;
}
.share-btn-secondary:hover { background: rgba(255,255,255,0.12); color: #fff !important; }

/* ══ CERTIFICATE CONTAINER ══ */
.cert {
  width: 1024px;
  height: 725px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 32px 90px rgba(0,0,0,.95);
  border-radius: 6px;
  background-color: #0d1021 !important;
  /* Cache buster on background image to reflect edits instantly */
  background: #0d1021 url('assets/img/og-certificate-download.png?v=<?= time() ?>') no-repeat center center;
  background-size: cover;
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}

/* Hide CSS decorations as they are pre-printed on the background image */
.bg-glow,
.top-line,
.bottom-line,
.left-bar,
.right-bar,
.cert-border-outer,
.cert-border-inner,
.corner-ornament,
.gold-line,
.gold-line-short {
  display: none !important;
}

/* Hide title text because we are using the graphic title instead */
.cert-title,
.cert-title-of {
  display: none !important;
}

.cert-title-img {
  position: absolute;
  top: 98px;
  left: 50%;
  transform: translateX(-50%);
  width: 580px;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 10px rgba(201, 162, 39, 0.2));
  z-index: 5;
}

/* ── Absolute positioned content sections ── */

/* 1. Org Branding (above pre-printed title) */
.cert-header-block {
  position: absolute;
  top: 35px;
  left: 0;
  width: 1024px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1px;
}
.org-h1 {
  font-family: 'Cinzel', serif;
  font-size: 16.5px;
  font-weight: 700;
  letter-spacing: .25em;
  color: #c9a227;
  text-transform: uppercase;
  line-height: 1.1;
  margin-bottom: 2px;
}
.org-h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 12px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(200,180,255,.55);
  line-height: 1.1;
}
.org-websites {
  font-family: 'Cormorant Garamond', serif;
  font-size: 9px;
  letter-spacing: .15em;
  color: rgba(200,180,255,.35);
  text-transform: uppercase;
  margin-top: 1px;
}

/* 2. Upper Content (between title and seal) */
.cert-upper-content {
  position: absolute;
  top: 175px;
  left: 150px;
  right: 150px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.cert-presented {
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(201,162,39,.8);
}
.recipient-name {
  font-family: 'White Chick', 'Dancing Script', cursive;
  font-size: 68px;
  font-weight: normal;
  color: #ffffff;
  line-height: 1.15;
  margin: 1px 0 2px;
  text-shadow: 0 2px 12px rgba(201,162,39,.22);
}
.cert-name-line {
  width: 480px;
  height: 1px;
  flex-shrink: 0;
  background: linear-gradient(90deg, transparent, rgba(201,162,39,.5) 20%, rgba(201,162,39,.7) 50%, rgba(201,162,39,.5) 80%, transparent);
  margin-bottom: 4px;
}
.cert-completed {
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(201,162,39,.8);
}
.course-name {
  font-family: 'Cinzel', serif;
  font-size: 28px;
  font-weight: 600;
  letter-spacing: .06em;
  background: linear-gradient(90deg, #a8d48a, #c9a227 50%, #c0a8e8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 2px 0;
}
.cert-course-line {
  width: 360px;
  height: 1px;
  flex-shrink: 0;
  background: linear-gradient(90deg, transparent, rgba(201,162,39,.4) 20%, rgba(201,162,39,.6) 50%, rgba(201,162,39,.4) 80%, transparent);
}

/* 3. Middle Content (below seal but above footer) */
.cert-middle-content {
  position: absolute;
  top: 485px;
  left: 100px;
  right: 100px;
  height: 125px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.cert-seal {
  position: absolute;
  top: 360px;
  left: 50%;
  transform: translateX(-50%);
  width: 120px;
  height: 120px;
  object-fit: contain;
  z-index: 10;
  filter: drop-shadow(0 4px 12px rgba(201, 162, 39, 0.25));
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.4s ease;
}
.cert-seal:hover {
  transform: translateX(-50%) scale(1.68) rotate(6deg);
  filter: drop-shadow(0 8px 16px rgba(201, 162, 39, 0.45));
}
.cert-footer-text {
  font-family: 'Cormorant Garamond', serif;
  font-size: 13px;
  font-style: italic;
  color: rgba(240, 240, 250, 0.82);
  max-width: 680px;
  line-height: 1.45;
  margin-top: 8px;
  margin-bottom: 2px;
}
.cert-footer-text strong {
  font-style: normal;
  font-weight: 600;
  color: rgba(240, 240, 250, 0.9);
}

/* Gold rules */
.gold-rule-sm {
  width: 120px;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(201,162,39,.5), transparent);
  margin: 3px 0;
}

/* Partners */
.partners-label {
  font-family: 'Cormorant Garamond', serif;
  font-size: 11px;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: #c9a227;
  margin-bottom: 6px;
}
.partners-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-top: 1px;
}
.partner-logo-img {
  height: 60px;
  max-width: 140px;
  object-fit: contain;
  padding: 6px 12px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 6px;
  border: 1px solid rgba(201, 162, 39, 0.35);
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
  opacity: 1;
}
.partner-logo-img:hover {
  background: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(201, 162, 39, 0.4);
}
.partner-divider {
  width: 1px;
  height: 30px;
  background: rgba(201, 162, 39, 0.25);
}

/* 4. Bottom row Date | Signature | QR */
.cert-bottom {
  position: absolute;
  bottom: 40px;
  left: 120px;
  right: 120px;
  height: 100px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
}
.bottom-col {
  text-align: center;
  min-width: 160px;
}
.bottom-label {
  font-family: 'Cormorant Garamond', serif;
  font-size: 10px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(201,162,39,.55);
  margin-bottom: 2px;
}
.bottom-value {
  font-family: 'Cormorant Garamond', serif;
  font-size: 14px;
  color: rgba(255,255,255,.75);
}

/* Signature */
.sig-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 190px;
}
.sig-img {
  height: 52px;
  max-width: 190px;
  object-fit: contain;
  margin-bottom: 3px;
  filter: brightness(0) invert(1);
  opacity: .85;
}
.sig-placeholder {
  height: 52px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.sig-line {
  width: 150px;
  height: 1px;
  background: linear-gradient(90deg, rgba(80,55,150,.4), rgba(201,162,39,.6), rgba(80,55,150,.4));
  margin-bottom: 2px;
}
.sig-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 13px;
  color: rgba(255,255,255,.65);
  letter-spacing: .05em;
}
.sig-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 10px;
  color: rgba(201,162,39,.45);
  letter-spacing: .1em;
  text-transform: uppercase;
}

/* QR code styling */
.qr-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}
.qr-wrap img {
  width: 60px;
  height: 60px;
  filter: invert(1);
  opacity: .65;
}
.qr-code-text {
  font-size: 10px;
  color: rgba(255,255,255,.28);
  letter-spacing: .08em;
  font-style: italic;
  font-family: 'Cormorant Garamond', serif;
}

/* Print */
@media print {
  .actions, .share-box-cert { display:none !important; }
  body { background: transparent !important; background-image: none !important; padding:0; margin:0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  .cert {
    box-shadow:none;
    width:100vw;
    height: 100vh;
    margin:0;
    background-color: #0d1021 !important;
    background-image: url('assets/img/og-certificate-download.png?v=<?= time() ?>') !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-size: cover !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  @page { size: A4 landscape; margin:0; }
}
</style>
</head>
<body>

<!-- Action buttons -->
<div class="actions">
  <button class="btn btn-print" onclick="window.print()">
    <i class="fa fa-print"></i> Print / Save as PDF
  </button>
  <a href="dashboard.php" class="btn btn-back"><i class="fa fa-arrow-left"></i> Dashboard</a>
  <a href="cert_settings.php" class="btn btn-back">
    <i class="fa fa-cog"></i> Settings
  </a>
</div>

<!-- Social sharing for Certificate -->
<div class="share-box-cert">
  <div class="share-label-cert">
    <i class="fa fa-share-nodes" style="color:#c9a227"></i>
    <span>Share Certificate on Social Media:</span>
  </div>
  <div class="share-buttons-cert">
    <?php
      $certShareUrl  = $base . '/certificate_verify.php?code=' . urlencode($code);
      $certShareText = rawurlencode("I am proud to share that I have successfully completed the course '{$courseTitle}' on Grafix@Mirror LMS and received my Certificate of Completion! Check it out: ");
    ?>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($certShareUrl) ?>" target="_blank" class="share-btn-primary">
      <i class="fab fa-facebook"></i> Share on Facebook
    </a>
    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($certShareUrl) ?>" target="_blank" class="share-btn-secondary">
      <i class="fab fa-linkedin-in"></i> LinkedIn
    </a>
    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($certShareUrl) ?>&text=<?= $certShareText ?>" target="_blank" class="share-btn-secondary">
      <i class="fab fa-x-twitter"></i> Twitter/X
    </a>
    <a href="https://api.whatsapp.com/send?text=<?= $certShareText ?>%20<?= urlencode($certShareUrl) ?>" target="_blank" class="share-btn-secondary">
      <i class="fab fa-whatsapp"></i> WhatsApp
    </a>
    <button onclick="navigator.clipboard.writeText('<?= $certShareUrl ?>').then(() => alert('Certificate link copied!'))" class="share-btn-secondary">
      <i class="fa fa-copy"></i> Copy Link
    </button>
    <button id="certWebShareBtn" class="share-btn-secondary" style="display:none;">
      <i class="fa fa-share-nodes"></i> Share via App
    </button>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     CERTIFICATE
     ══════════════════════════════════════════════════════ -->
<div class="cert">

  <!-- Structural decorations (retained in HTML to prevent deletion of tags, hidden via CSS) -->
  <div class="bg-glow"></div>
  <div class="top-line"></div>
  <div class="bottom-line"></div>
  <div class="left-bar"></div>
  <div class="right-bar"></div>
  <div class="cert-border-outer"></div>
  <div class="cert-border-inner"></div>
  <div class="corner-ornament co-tl"></div>
  <div class="corner-ornament co-tr"></div>
  <div class="corner-ornament co-bl"></div>
  <div class="corner-ornament co-br"></div>

  <!-- ── 1. Header Block: Org Branding ── -->
  <div class="cert-header-block">
    <div class="org-h1">Mirror Age Concepts &bull; Grafix@Mirror LMS</div>
    <div class="org-h2"><?= e($orgSubtitle) ?></div>
    <div class="org-websites">www.mirrorageconcepts.com &nbsp;|&nbsp; www.lms.mirrorageconcepts.com</div>
    <div class="gold-line"></div>
  </div>

  <!-- ── Title Graphic ── -->
  <img src="assets/img/og-cert-name.png" alt="Certificate of Achievement" class="cert-title-img">

  <!-- ── 2. Upper Content: Awarded details ── -->
  <div class="cert-upper-content">
    <div class="cert-presented">Awarded to:</div>
    <div class="recipient-name"><?= e($name) ?></div>
    <div class="cert-name-line"></div>

    <div class="cert-completed">For Excellence in:</div>
    <div class="course-name"><?= e($courseTitle) ?></div>
    <div class="cert-course-line"></div>

    <?php if (!empty($certFooter)): ?>
      <div class="cert-footer-text"><?= e($certFooter) ?></div>
    <?php endif; ?>
  </div>

  <!-- ── Official Seal ── -->
  <img src="assets/img/og-official-seal.png" alt="Official Seal" class="cert-seal">

  <!-- ── 3. Middle Content: Partners ── -->
  <div class="cert-middle-content">
    <?php if (!empty($partnerLogos)): ?>
      <div class="partners-label">In Partnership With</div>
      <div class="partners-row">
        <?php foreach ($partnerLogos as $i => $pLogo): ?>
          <?php if ($i > 0): ?><div class="partner-divider"></div><?php endif; ?>
          <img src="<?= e($pLogo) ?>" alt="Partner" class="partner-logo-img">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── 4. Bottom Row: Date | Signature | QR ── -->
  <div class="cert-bottom">

    <div class="bottom-col">
      <div class="gold-rule-sm" style="width:90px;margin-bottom:4px;"></div>
      <div class="bottom-label">Date of Issue</div>
      <div class="bottom-value"><?= e($issuedDate) ?></div>
      <div class="bottom-label" style="margin-top:2px;font-size:6.5px;color:rgba(201,162,39,.3)">
        www.mirrorageconcepts.com
      </div>
    </div>

    <div class="sig-area">
      <?php if (!empty($sigUrl)): ?>
        <img src="<?= $sigUrl ?>" alt="Signature" class="sig-img">
      <?php else: ?>
        <div class="sig-placeholder"></div>
      <?php endif; ?>
      <div class="sig-line"></div>
      <div class="sig-name"><?= e($directorName) ?></div>
      <div class="sig-title">Authorized Signature</div>
    </div>

    <div class="bottom-col">
      <div class="qr-wrap">
        <img src="<?= e($qrImg) ?>" alt="Verify">
        <div class="qr-code-text">Scan to verify</div>
        <div class="bottom-label" style="margin-top:1px">ID: <?= e($code) ?></div>
      </div>
    </div>

  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Fit recipient name to single line dynamically
  const nameEl = document.querySelector('.recipient-name');
  if (nameEl) {
    let fontSize = 68; // initial font size in px
    const maxAllowedWidth = 724; // maximum width inside the frame (1024px width - 300px margins)
    nameEl.style.whiteSpace = 'nowrap';
    nameEl.style.fontSize = fontSize + 'px';
    while (nameEl.scrollWidth > maxAllowedWidth && fontSize > 20) {
      fontSize -= 1;
      nameEl.style.fontSize = fontSize + 'px';
    }
  }

  // Fit course name to single line dynamically
  const courseEl = document.querySelector('.course-name');
  if (courseEl) {
    let fontSize = 28;
    const maxAllowedWidth = 724;
    courseEl.style.whiteSpace = 'nowrap';
    courseEl.style.fontSize = fontSize + 'px';
    while (courseEl.scrollWidth > maxAllowedWidth && fontSize > 14) {
      fontSize -= 0.5;
      courseEl.style.fontSize = fontSize + 'px';
    }
  }

  if (navigator.share) {
    const wsBtn = document.getElementById('certWebShareBtn');
    if (wsBtn) {
      wsBtn.style.display = 'inline-flex';
      wsBtn.addEventListener('click', function() {
        navigator.share({
          title: 'Certificate of Completion',
          text: `I completed '${<?= json_encode($courseTitle) ?>}' on Grafix@Mirror LMS!`,
          url: '<?= $certShareUrl ?>'
        }).catch(err => console.log('Error sharing:', err));
      });
    }
  }
});
</script>
</body>
</html>
