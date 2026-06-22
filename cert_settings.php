<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

if (!hasRole('Admin')) {
    http_response_code(403);
    exit('Access denied');
}

/* ── Ensure settings table exists ── */
$pdo->exec("CREATE TABLE IF NOT EXISTS `lms_settings` (
    `key` varchar(100) NOT NULL,
    `value` text DEFAULT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$ok  = $_SESSION['cert_ok']  ?? null;
$err = $_SESSION['cert_err'] ?? null;
unset($_SESSION['cert_ok'], $_SESSION['cert_err']);

/* ── Handle save ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['_csrf'] ?? '');

    $directorName  = trim($_POST['director_name']  ?? '');
    $orgSubtitle   = trim($_POST['org_subtitle']   ?? '');
    $certFooter    = trim($_POST['cert_footer']    ?? '');

    // Handle signature upload
    $sigFile = null;
    if (!empty($_FILES['signature']['name']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['signature']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'], true)) {
            $_SESSION['cert_err'] = 'Signature must be JPG or PNG.';
            redirect('cert_settings.php');
        }
        if ($_FILES['signature']['size'] > 2 * 1024 * 1024) {
            $_SESSION['cert_err'] = 'Signature image too large (max 2MB).';
            redirect('cert_settings.php');
        }
        $uploadDir = __DIR__ . '/uploads/cert/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $sigFile = 'cert/signature_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['signature']['tmp_name'], __DIR__ . '/uploads/' . $sigFile)) {
            $_SESSION['cert_err'] = 'Upload failed. Try again.';
            redirect('cert_settings.php');
        }
    }

    // Handle logo upload
    $logoFile = null;
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','svg','webp'], true)) {
            $_SESSION['cert_err'] = 'Logo must be JPG, PNG, SVG or WebP.';
            redirect('cert_settings.php');
        }
        if ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
            $_SESSION['cert_err'] = 'Logo image too large (max 2MB).';
            redirect('cert_settings.php');
        }
        $uploadDir = __DIR__ . '/uploads/cert/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $logoFile = 'cert/logo_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/uploads/' . $logoFile)) {
            $_SESSION['cert_err'] = 'Logo upload failed. Try again.';
            redirect('cert_settings.php');
        }
    }

    // Handle partner logo uploads (up to 3)
    $partnerFiles = [];
    for ($p = 1; $p <= 3; $p++) {
        $field = 'partner_logo_' . $p;
        if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','svg','webp'], true)) {
                $_SESSION['cert_err'] = "Partner logo {$p} must be JPG, PNG, SVG or WebP.";
                redirect('cert_settings.php');
            }
            if ($_FILES[$field]['size'] > 2 * 1024 * 1024) {
                $_SESSION['cert_err'] = "Partner logo {$p} too large (max 2MB).";
                redirect('cert_settings.php');
            }
            $uploadDir = __DIR__ . '/uploads/cert/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $partnerFiles[$p] = 'cert/partner_' . $p . '_' . time() . '.' . $ext;
            if (!move_uploaded_file($_FILES[$field]['tmp_name'], __DIR__ . '/uploads/' . $partnerFiles[$p])) {
                $_SESSION['cert_err'] = "Partner logo {$p} upload failed.";
                redirect('cert_settings.php');
            }
        }
        // Handle remove checkbox
        if (!empty($_POST['remove_partner_' . $p])) {
            $partnerFiles[$p] = '';
        }
    }

    $upsert = $pdo->prepare("INSERT INTO lms_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
    $upsert->execute(['cert_director_name', $directorName]);
    $upsert->execute(['cert_org_subtitle',  $orgSubtitle]);
    $upsert->execute(['cert_footer',        $certFooter]);
    if ($sigFile)  $upsert->execute(['cert_signature', $sigFile]);
    if ($logoFile) $upsert->execute(['cert_logo',      $logoFile]);
    for ($p = 1; $p <= 3; $p++) {
        if (array_key_exists($p, $partnerFiles)) {
            $upsert->execute(['cert_partner_logo_' . $p, $partnerFiles[$p]]);
        }
    }

    $_SESSION['cert_ok'] = 'Certificate settings saved.';
    redirect('cert_settings.php');
}

/* ── Load current settings ── */
$settings = [];
foreach ($pdo->query("SELECT `key`,`value` FROM lms_settings WHERE `key` LIKE 'cert_%'")->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $settings[$r['key']] = $r['value'];
}
$directorName = $settings['cert_director_name'] ?? 'Director, Mirror Age Concepts';
$orgSubtitle  = $settings['cert_org_subtitle']  ?? 'Professional Technology Training Institute';
$certFooter   = $settings['cert_footer']        ?? 'This certificate is awarded in recognition of successful completion of the course requirements.';
$sigPath      = $settings['cert_signature']     ?? '';
$logoPath     = $settings['cert_logo']          ?? '';
$sigUrl       = (!empty($sigPath)  && file_exists(__DIR__.'/uploads/'.$sigPath))  ? 'uploads/'.$sigPath  : '';
$logoUrl      = (!empty($logoPath) && file_exists(__DIR__.'/uploads/'.$logoPath)) ? 'uploads/'.$logoPath : '';

$partnerLogos = [];
for ($p = 1; $p <= 3; $p++) {
    $path = $settings['cert_partner_logo_' . $p] ?? '';
    $partnerLogos[$p] = (!empty($path) && file_exists(__DIR__.'/uploads/'.$path)) ? 'uploads/'.$path : '';
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Certificate Settings';
$seoDesc    = 'Configure certificate appearance and upload signature/logo at Grafix@Mirror LMS admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav lms-nav-admin">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
      <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
    </div>
    <a href="admin_dashboard.php" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.3)">
      <i class="fa fa-arrow-left me-1"></i>Dashboard
    </a>
  </div>
</nav>

<div class="container py-4" style="max-width:760px">

  <h4 class="page-title mb-4"><i class="fa fa-certificate me-2"></i>Certificate Settings</h4>

  <?php if ($ok): ?>
    <div class="lms-alert lms-alert-success mb-4"><i class="fa fa-check-circle me-1"></i><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="lms-alert lms-alert-danger mb-4"><i class="fa fa-exclamation-circle me-1"></i><?= e($err) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-user-tie me-2"></i>Signatory Details</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Director / Signatory Name</label>
          <input name="director_name" class="form-control" value="<?= e($directorName) ?>" placeholder="e.g. Abdullateef Matanmi">
          <div class="form-text">Appears below the signature line on the certificate.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Organisation Subtitle</label>
          <input name="org_subtitle" class="form-control" value="<?= e($orgSubtitle) ?>" placeholder="e.g. Professional Technology Training Institute">
        </div>
        <div class="col-12">
          <label class="form-label">Certificate Footer Text</label>
          <textarea name="cert_footer" class="form-control" rows="2"><?= e($certFooter) ?></textarea>
          <div class="form-text">Small text at the bottom of the certificate.</div>
        </div>
      </div>
    </div>

    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-image me-2"></i>LMS Logo on Certificate</div>

      <?php if (!empty($logoUrl)): ?>
        <div class="mb-3">
          <div class="text-muted mb-2" style="font-size:.85rem">Current logo:</div>
          <div style="background:#0d0b14;padding:1rem;border-radius:8px;display:inline-block">
            <img src="<?= e($logoUrl) ?>" alt="Current logo"
                 style="max-height:60px;max-width:200px;object-fit:contain">
          </div>
        </div>
      <?php endif; ?>

      <label class="form-label">Upload Logo Image</label>
      <input type="file" name="logo" class="form-control" accept="image/jpeg,image/png,image/svg+xml,image/webp">
      <div class="form-text">
        PNG (transparent background), SVG, WebP or JPG. Max 2MB.
        Recommended: <strong>transparent PNG, 300 × 100px</strong> — will appear at the top of the certificate.
      </div>
    </div>

    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-signature me-2"></i>Signature Image</div>

      <?php if (!empty($sigPath) && file_exists(__DIR__ . '/uploads/' . $sigPath)): ?>
        <div class="mb-3">
          <div class="text-muted mb-2" style="font-size:.85rem">Current signature:</div>
          <div style="background:#1a1a1a;padding:1rem;border-radius:8px;display:inline-block">
            <img src="uploads/<?= e($sigPath) ?>" alt="Current signature"
                 style="max-height:80px;max-width:300px;filter:invert(1)">
          </div>
        </div>
      <?php endif; ?>

      <label class="form-label">Upload Signature Image</label>
      <input type="file" name="signature" class="form-control" accept="image/jpeg,image/png">
      <div class="form-text">
        JPG or PNG, max 2MB. Use a <strong>transparent background PNG</strong> for best results.
        The signature will appear white on the dark certificate background.
        Recommended size: 400 × 120px.
      </div>

      <div class="lms-alert lms-alert-info mt-3" style="font-size:.85rem">
        <i class="fa fa-lightbulb me-1"></i>
        <strong>Tip:</strong> Sign on white paper, photograph or scan it, then use
        <a href="https://www.remove.bg" target="_blank">remove.bg</a> (free) to remove the background.
        Save as PNG with transparency.
      </div>
    </div>

    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-handshake me-2"></i>Partner Logos (In Partnership With)</div>
      <p class="text-muted mb-3" style="font-size:.85rem">
        Upload up to 3 partner organisation logos. These appear on the certificate in an "In Partnership With" section.
        Use transparent PNG or SVG for best results. Recommended size: <strong>200 × 80px</strong>.
      </p>
      <?php for ($p = 1; $p <= 3; $p++): ?>
      <div class="border rounded p-3 mb-3">
        <div class="fw-semibold mb-2" style="font-size:.88rem">Partner <?= $p ?></div>
        <?php if (!empty($partnerLogos[$p])): ?>
          <div class="mb-2 d-flex align-items-center gap-3">
            <div style="background:#0d0b14;padding:.75rem;border-radius:8px;display:inline-block">
              <img src="<?= e($partnerLogos[$p]) ?>" alt="Partner <?= $p ?> logo"
                   style="max-height:50px;max-width:160px;object-fit:contain">
            </div>
            <label class="d-flex align-items-center gap-2 text-danger" style="font-size:.82rem;cursor:pointer">
              <input type="checkbox" name="remove_partner_<?= $p ?>" value="1"> Remove this logo
            </label>
          </div>
        <?php endif; ?>
        <input type="file" name="partner_logo_<?= $p ?>" class="form-control form-control-sm"
               accept="image/jpeg,image/png,image/svg+xml,image/webp">
        <div class="form-text">PNG (transparent), SVG, WebP or JPG. Max 2MB.</div>
      </div>
      <?php endfor; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <a href="admin_dashboard.php" class="btn-ghost">Cancel</a>
      <button type="submit" class="btn-brand">
        <i class="fa fa-save me-1"></i> Save Settings
      </button>
    </div>
  </form>

  <!-- Preview link -->
  <?php
  $previewRow = $pdo->query("SELECT e.course_id FROM lms_enrollments e JOIN lms_exam_results r ON r.student_id=e.student_id WHERE r.status='pass' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
  if ($previewRow):
    $previewCid = (int)$previewRow['course_id'];
  ?>
  <div class="lms-card mt-4 text-center">
    <div class="text-muted mb-2" style="font-size:.88rem">Preview how the certificate looks:</div>
    <a href="certificate_download.php?course_id=<?= $previewCid ?>"
       target="_blank" class="btn-outline-brand">
      <i class="fa fa-eye me-1"></i> Preview Certificate
    </a>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
