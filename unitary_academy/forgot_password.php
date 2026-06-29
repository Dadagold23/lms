<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/mail.php';

$successMessage = '';
$errorMessage   = '';
$resetLinkDebug = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['_csrf'] ?? '');

    $email = trim((string)($_POST['email'] ?? ''));

    if ($email === '') {
        $errorMessage = 'Please enter your registered email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        try {
            // Check if partner exists
            $stmt = $pdo->prepare("SELECT id, name FROM lms_affiliate_partners WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $partner = $stmt->fetch();

            if ($partner) {
                // Generate secure token and expiration
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

                // Store in database
                $stmtUpdate = $pdo->prepare("
                    UPDATE lms_affiliate_partners
                    SET reset_token = ?, reset_token_expires = ?
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$token, $expires, (int)$partner['id']]);

                // Construct reset link
                $httpProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $resetLink = "{$httpProtocol}://{$host}/lms/unitary_academy/reset_password.php?token={$token}";

                // Send email
                $subject = "Password Reset Request — Grafix@Mirror Partner Portal";
                $message = "Hello " . e($partner['name']) . ",\n\n"
                         . "You requested a password reset for your partner account.\n"
                         . "Please click the link below to set a new password. The link is valid for 1 hour:\n\n"
                         . $resetLink . "\n\n"
                         . "If you did not request this, please ignore this email.\n\n"
                         . "Best regards,\n"
                         . "Partner Success Team\n"
                         . "Grafix@Mirror LMS";

                send_mail($email, $subject, $message);

                $successMessage = 'A password reset link has been generated and sent to your email address.';
                
                // For local xampp testing/dev mode convenience, expose the link on screen
                $resetLinkDebug = $resetLink;
            } else {
                // Return same success message to prevent user enumeration security issues, or friendly warning
                $errorMessage = 'No registered partner account was found with that email address.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Partner Password Recovery';
$seoDesc = 'Recover your partner account password — Unitary Academy.';
$seoNoIndex = true;
require_once dirname(__DIR__) . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../assets/css/app.css" rel="stylesheet">
<style>
  body { background: #0f172a; font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card-reset { background: #1e293b; border: 1px solid #334155; border-radius: 20px; max-width: 450px; width: 100%; padding: 2.5rem; color: #f8fafc; }
  .btn-brand { background: #0d9488; color: #fff; border: none; }
  .btn-brand:hover { background: #0f766e; color: #fff; }
</style>
</head>
<body>

<div class="card-reset shadow-lg">
  <div class="text-center mb-4">
    <div style="width: 48px; height: 48px; background: rgba(13, 148, 136, 0.2); color: #2dd4bf; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
      <i class="fa fa-key"></i>
    </div>
    <h3 class="fw-bold mb-1">Partner Password Reset</h3>
    <p class="text-muted small">Enter your email and we'll send you a password reset link.</p>
  </div>

  <?php if ($successMessage): ?>
    <div class="alert alert-success border-0 p-3 mb-4" style="border-radius: 10px; font-size: .85rem;">
      <i class="fa fa-check-circle me-1"></i> <?= e($successMessage) ?>
    </div>
    <?php if ($resetLinkDebug): ?>
      <div class="p-3 bg-dark border border-secondary rounded mb-4 text-start" style="font-size: .8rem; word-break: break-all;">
        <span class="text-info fw-bold d-block mb-1"><i class="fa fa-info-circle"></i> Local Dev Preview:</span>
        <a href="<?= e($resetLinkDebug) ?>" class="text-warning text-decoration-none"><?= e($resetLinkDebug) ?></a>
      </div>
    <?php endif; ?>
    <a href="index.php" class="btn btn-outline-secondary w-100 fw-semibold" style="border-radius: 10px;">Back to Portals</a>
  <?php else: ?>
    <?php if ($errorMessage): ?>
      <div class="alert alert-danger border-0 p-3 mb-4" style="border-radius: 10px; font-size: .85rem;">
        <i class="fa fa-exclamation-circle me-1"></i> <?= e($errorMessage) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

      <div class="mb-4">
        <label class="form-label text-muted small fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control bg-dark border-secondary text-white py-2.5" placeholder="name@organization.com" required style="border-radius: 10px;">
      </div>

      <button type="submit" class="btn btn-brand w-100 py-2.5 fw-semibold mb-3" style="border-radius: 10px;">Send Reset Link</button>
      <a href="index.php" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" style="border-radius: 10px;">Cancel</a>
    </form>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
