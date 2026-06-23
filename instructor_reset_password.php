<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$error = null;
$success = null;

if ($token === '') {
    $error = 'Invalid or missing security token. Please request a new setup email.';
} else {
    // Look up instructor by token
    $stmt = $pdo->prepare("SELECT * FROM lms_instructors WHERE verification_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        $error = 'The security link is invalid or has already been used. Please contact your administrator.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && $instructor) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $password = (string)($_POST['password'] ?? '');
    $confirmPwd = (string)($_POST['confirm_password'] ?? '');

    if ($password !== $confirmPwd) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'Password must be at least 8 characters, include a number and an uppercase letter.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $update = $pdo->prepare("
                UPDATE lms_instructors
                SET password = ?,
                    verification_token = NULL,
                    is_email_verified = 1
                WHERE id = ?
            ");
            $update->execute([$hash, (int)$instructor['id']]);

            // Auto-login the instructor
            session_regenerate_id(true);
            $_SESSION['instructor'] = [
                'id' => $instructor['id'],
                'full_name' => $instructor['full_name'],
                'email' => $instructor['email'],
                'role' => 'instructor'
            ];

            $_SESSION['instructor_profile_success'] = 'Welcome! Your email has been verified and password set successfully. Welcome to your profile page.';
            redirect('instructor_profile.php');
        } catch (Throwable $e) {
            $error = 'Failed to set password: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Account Setup';
$seoDesc    = 'Set up your instructor account password and verify your email.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body {
    background: radial-gradient(circle at 10% 20%, rgb(18, 23, 37) 0%, rgb(9, 11, 19) 90%);
    font-family: Inter, system-ui;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f8fafc;
  }
  .setup-card {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-width: 460px;
    width: 100%;
    overflow: hidden;
  }
  .setup-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 32px 24px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  .setup-logo {
    display: inline-flex;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.5px;
    margin-bottom: 8px;
  }
  .form-control-dark {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 10px;
    padding: 12px 16px;
  }
  .form-control-dark:focus {
    background: rgba(15, 23, 42, 0.8);
    border-color: #6366f1;
    color: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
  }
  .btn-setup {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    color: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .btn-setup:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
  }
</style>
</head>
<body>

<div class="setup-card animate__animated animate__fadeIn">
  <div class="setup-header">
    <div class="setup-logo">
      <span>G</span><span style="color:#c7d2fe;">rafix@Mirror</span>
    </div>
    <h5 class="fw-bold mb-0 mt-2 text-white">Setup Instructor Account</h5>
    <p class="text-indigo-200 small mb-0 mt-1" style="color: #c7d2fe; opacity: 0.85;">Verify email and set credentials</p>
  </div>
  
  <div class="p-4">
    <?php if ($error): ?>
      <div class="alert alert-danger border-0 p-3 mb-4" style="background: rgba(239, 68, 68, 0.15); color: #fca5a5; border-radius: 10px;">
        <i class="fa fa-exclamation-circle me-2"></i><?= e($error) ?>
      </div>
      <div class="text-center">
        <a href="instructor_login.php" class="btn btn-outline-light w-100 btn-sm py-2.5" style="border-radius: 10px;">Back to Login</a>
      </div>
    <?php else: ?>
      <div class="alert alert-success border-0 p-3 mb-4" style="background: rgba(16, 185, 129, 0.12); color: #a7f3d0; border-radius: 10px; font-size: 0.88rem;">
        <i class="fa fa-envelope-open text-success me-2"></i>Hello <strong><?= e($instructor['full_name']) ?></strong>! Your email address (<?= e($instructor['email']) ?>) is ready for verification.
      </div>
      
      <form method="post" action="instructor_reset_password.php">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        
        <div class="mb-3">
          <label class="form-label text-indigo-200 small">Choose Secure Password</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: rgba(255, 255, 255, 0.1); border-radius: 10px 0 0 10px;"><i class="fa fa-key"></i></span>
            <input type="password" name="password" class="form-control form-control-dark border-start-0" placeholder="Minimum 8 characters" required style="border-radius: 0 10px 10px 0;">
          </div>
          <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">Must include a number and an uppercase letter.</div>
        </div>
        
        <div class="mb-4">
          <label class="form-label text-indigo-200 small">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: rgba(255, 255, 255, 0.1); border-radius: 10px 0 0 10px;"><i class="fa fa-lock"></i></span>
            <input type="password" name="confirm_password" class="form-control form-control-dark border-start-0" placeholder="Re-type password" required style="border-radius: 0 10px 10px 0;">
          </div>
        </div>
        
        <button type="submit" class="btn btn-setup w-100 py-3">
          Verify Email & Activate Account <i class="fa fa-arrow-right ms-1"></i>
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
