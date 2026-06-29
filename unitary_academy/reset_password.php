<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/config/db.php';

$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$errorMessage   = '';
$successMessage = '';
$partner = null;

if ($token === '') {
    $errorMessage = 'Invalid or missing password reset token.';
} else {
    try {
        // Validate token against DB
        $stmt = $pdo->prepare("
            SELECT * FROM lms_affiliate_partners
            WHERE reset_token = ? AND reset_token_expires > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $partner = $stmt->fetch();

        if (!$partner) {
            $errorMessage = 'This password reset link is invalid or has expired.';
        }
    } catch (PDOException $e) {
        $errorMessage = 'Database error: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $partner) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword === '' || $confirmPassword === '') {
        $errorMessage = 'All password fields are required.';
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = 'Password must be at least 8 characters long.';
    } else {
        try {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update partner password and clear token
            $stmtUpdate = $pdo->prepare("
                UPDATE lms_affiliate_partners
                SET access_password = ?, reset_token = NULL, reset_token_expires = NULL
                WHERE id = ?
            ");
            $stmtUpdate->execute([$hashed, (int)$partner['id']]);

            $successMessage = 'Your password has been reset successfully! You can now log in.';
        } catch (PDOException $e) {
            $errorMessage = 'Failed to reset password: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Reset Partner Password';
$seoDesc = 'Choose a new password for your partner account — Unitary Academy.';
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
      <i class="fa fa-shield-alt"></i>
    </div>
    <h3 class="fw-bold mb-1">Set New Password</h3>
    <p class="text-muted small">Choose a strong, secure password for your account.</p>
  </div>

  <?php if ($successMessage): ?>
    <div class="alert alert-success border-0 p-3 mb-4" style="border-radius: 10px; font-size: .85rem;">
      <i class="fa fa-check-circle me-1"></i> <?= e($successMessage) ?>
    </div>
    <a href="index.php" class="btn btn-brand w-100 fw-semibold" style="border-radius: 10px;">Go to Login</a>
  <?php else: ?>
    <?php if ($errorMessage): ?>
      <div class="alert alert-danger border-0 p-3 mb-4" style="border-radius: 10px; font-size: .85rem;">
        <i class="fa fa-exclamation-circle me-1"></i> <?= e($errorMessage) ?>
      </div>
    <?php endif; ?>

    <?php if ($partner): ?>
      <form method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">

        <div class="mb-3">
          <label class="form-label text-muted small fw-semibold">New Password</label>
          <input type="password" name="new_password" class="form-control bg-dark border-secondary text-white py-2.5" placeholder="Min. 8 characters" required style="border-radius: 10px;">
        </div>

        <div class="mb-4">
          <label class="form-label text-muted small fw-semibold">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control bg-dark border-secondary text-white py-2.5" placeholder="Repeat new password" required style="border-radius: 10px;">
        </div>

        <button type="submit" class="btn btn-brand w-100 py-2.5 fw-semibold" style="border-radius: 10px;">Update Password</button>
      </form>
    <?php else: ?>
      <a href="forgot_password.php" class="btn btn-outline-secondary w-100 fw-semibold" style="border-radius: 10px;">Request New Reset Link</a>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
