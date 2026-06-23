<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail.php';
require_once __DIR__ . '/includes/email_templates.php';

requireInstructorLogin();
$insSession = $_SESSION['instructor'];
$insId = (int)($insSession['id'] ?? 0);

// Fetch current details from DB
$stmt = $pdo->prepare("SELECT * FROM lms_instructors WHERE id = ? LIMIT 1");
$stmt->execute([$insId]);
$i = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$i) {
    exit('Instructor profile not found.');
}

$flashSuccess = $_SESSION['instructor_profile_success'] ?? null;
$flashError = $_SESSION['instructor_profile_error'] ?? null;
unset($_SESSION['instructor_profile_success'], $_SESSION['instructor_profile_error']);

// ─── Actions Handler ───
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullName       = trim((string)($_POST['full_name'] ?? ''));
        $phone          = trim((string)($_POST['phone'] ?? ''));
        $specialization = trim((string)($_POST['specialization'] ?? ''));
        $qualification  = trim((string)($_POST['qualification'] ?? ''));
        $experienceYrs  = (int)($_POST['experience_years'] ?? 0);
        $bio            = trim((string)($_POST['bio'] ?? ''));
        
        $linkedinUrl    = trim((string)($_POST['linkedin_url'] ?? ''));
        $twitterUrl     = trim((string)($_POST['twitter_url'] ?? ''));
        $xUrl           = trim((string)($_POST['x_url'] ?? ''));
        $instagramUrl   = trim((string)($_POST['instagram_url'] ?? ''));
        $websiteUrl     = trim((string)($_POST['website_url'] ?? ''));

        if ($fullName === '' || $specialization === '') {
            $_SESSION['instructor_profile_error'] = 'Name and Specialization are required.';
            redirect('instructor_profile.php');
        }

        // Handle photo upload
        $photoFile = $i['photo'];
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                if ($_FILES['photo']['size'] <= 2 * 1024 * 1024) {
                    $uploadDir = __DIR__ . '/uploads/instructors/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $newFile = 'instructors/' . uniqid('instructor_', true) . '.' . $ext;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $newFile)) {
                        // Delete old photo if exists
                        if ($photoFile && file_exists(__DIR__ . '/uploads/' . $photoFile)) {
                            @unlink(__DIR__ . '/uploads/' . $photoFile);
                        }
                        $photoFile = $newFile;
                    }
                } else {
                    $_SESSION['instructor_profile_error'] = 'Profile photo exceeds 2MB limit.';
                    redirect('instructor_profile.php');
                }
            } else {
                $_SESSION['instructor_profile_error'] = 'Invalid image format. Please upload JPG or PNG.';
                redirect('instructor_profile.php');
            }
        }

        try {
            $stmtUpdate = $pdo->prepare("
                UPDATE lms_instructors
                SET full_name = ?,
                    phone = ?,
                    specialization = ?,
                    qualification = ?,
                    experience_years = ?,
                    bio = ?,
                    photo = ?,
                    linkedin_url = ?,
                    twitter_url = ?,
                    x_url = ?,
                    instagram_url = ?,
                    website_url = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([
                $fullName, $phone ?: null, $specialization, $qualification ?: null, $experienceYrs, $bio ?: null, $photoFile,
                $linkedinUrl ?: null, $twitterUrl ?: null, $xUrl ?: null, $instagramUrl ?: null, $websiteUrl ?: null,
                $insId
            ]);

            $_SESSION['instructor']['full_name'] = $fullName; // update session name
            $_SESSION['instructor_profile_success'] = 'Profile updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['instructor_profile_error'] = 'Failed to update profile: ' . $e->getMessage();
        }
        redirect('instructor_profile.php');
    }

    if ($action === 'update_password') {
        $currPassword = (string)($_POST['current_password'] ?? '');
        $newPassword  = (string)($_POST['new_password'] ?? '');
        $confirmPwd   = (string)($_POST['confirm_password'] ?? '');

        if ($currPassword === '' || $newPassword === '' || $confirmPwd === '') {
            $_SESSION['instructor_profile_error'] = 'All password fields are required.';
            redirect('instructor_profile.php');
        }

        if (!password_verify($currPassword, $i['password'])) {
            $_SESSION['instructor_profile_error'] = 'Your current password is incorrect.';
            redirect('instructor_profile.php');
        }

        if ($newPassword !== $confirmPwd) {
            $_SESSION['instructor_profile_error'] = 'New passwords do not match.';
            redirect('instructor_profile.php');
        }

        if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $_SESSION['instructor_profile_error'] = 'New password must be at least 8 characters, include a number and an uppercase letter.';
            redirect('instructor_profile.php');
        }

        try {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmtPwd = $pdo->prepare("UPDATE lms_instructors SET password = ? WHERE id = ?");
            $stmtPwd->execute([$newHash, $insId]);
            $_SESSION['instructor_profile_success'] = 'Password updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['instructor_profile_error'] = 'Failed to update password: ' . $e->getMessage();
        }
        redirect('instructor_profile.php');
    }

    if ($action === 'send_verification') {
        if ((int)$i['is_email_verified'] === 1) {
            $_SESSION['instructor_profile_success'] = 'Your email address is already verified.';
            redirect('instructor_profile.php');
        }

        // Generate verification token
        $token = bin2hex(random_bytes(32));
        try {
            $pdo->prepare("UPDATE lms_instructors SET verification_token = ? WHERE id = ?")
                ->execute([$token, $insId]);

            // Send setup email which acts as verification
            $mailContent = emailInstructorWelcomeSetup($i['full_name'], $i['email'], $token);
            $mailSent = send_mail($i['email'], 'Verify Your Email — Grafix@Mirror LMS', $mailContent);

            if ($mailSent) {
                $_SESSION['instructor_profile_success'] = 'Verification email sent successfully. Please check your inbox.';
            } else {
                $_SESSION['instructor_profile_error'] = 'Failed to send verification email. Please check SMTP settings.';
            }
        } catch (Throwable $e) {
            $_SESSION['instructor_profile_error'] = 'Failed to trigger verification email: ' . $e->getMessage();
        }
        redirect('instructor_profile.php');
    }
}

// Initials for avatar fallback
$initials = '';
if (!empty($i['full_name'])) {
    $words = explode(' ', $i['full_name']);
    $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Profile Settings';
$seoDesc    = 'Update your biography, qualification, social links, and security settings.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
  body { background: #f8fafc; font-family: Inter, system-ui; }
  .lms-nav-instructor { background: #0f172a !important; }
  .profile-banner {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 16px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .profile-banner::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
    pointer-events: none;
  }
  .avatar-profile-wrap {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    overflow: hidden;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 2.2rem;
    color: #4f46e5;
  }
  .avatar-profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .card-settings {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
  }
</style>
</head>
<body>

<nav class="lms-nav lms-nav-instructor py-2 shadow-sm">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <a href="instructor_dashboard.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">I</div>
        <span style="color:#fff" class="fw-bold">Instructor <span style="color:#c7d2fe">Portal</span></span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:rgba(255,255,255,.7)">
        <i class="fa fa-chalkboard-teacher me-1"></i><?= e($i['full_name']) ?>
      </span>
      <a href="instructor_logout.php" style="font-size:.82rem;color:#fca5a5;font-weight:600" class="text-decoration-none"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar">
    <div class="nav-section">Overview</div>
    <a href="instructor_dashboard.php" class="nav-link"><i class="fa fa-th-large"></i> Dashboard</a>
    <div class="nav-section">Content</div>
    <a href="instructor_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="instructor_lessons.php" class="nav-link"><i class="fa fa-file-alt"></i> Lessons</a>
    <a href="instructor_videos.php" class="nav-link"><i class="fa fa-video"></i> Videos</a>
    <a href="instructor_assignments.php" class="nav-link"><i class="fa fa-tasks"></i> Assignments</a>
    <div class="nav-section">Upload</div>
    <a href="instructor_upload_course.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Course</a>
    <a href="instructor_upload_lesson.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Lesson</a>
    <a href="instructor_upload_video.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Video</a>
    <a href="instructor_upload_assignment.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Assignment</a>
    <div class="nav-section">Live Teaching</div>
    <a href="instructor_live_sessions.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-video"></i> Live Sessions</a>
    <div class="nav-section">Grading</div>
    <a href="instructor_grade_assignment.php" class="nav-link"><i class="fa fa-star"></i> Grade Submissions</a>
    <div class="nav-section">Settings</div>
    <a href="instructor_profile.php" class="nav-link active"><i class="fa fa-user-cog"></i> Profile Settings</a>
    <div class="nav-section">Portal</div>
    <a href="instructor_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="lms-main">
    
    <!-- Hero Banner -->
    <div class="profile-banner p-4 mb-4">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <div class="avatar-profile-wrap">
          <?php if (!empty($i['photo'])): ?>
            <img src="uploads/<?= e($i['photo']) ?>" alt="<?= e($i['full_name']) ?>" class="avatar-profile-img">
          <?php else: ?>
            <span><?= $initials ?></span>
          <?php endif; ?>
        </div>
        <div>
          <h3 class="fw-bold mb-1 text-white"><?= e($i['full_name']) ?></h3>
          <p class="text-indigo-200 mb-2 small" style="color: #c7d2fe;"><i class="fa fa-envelope me-1"></i><?= e($i['email']) ?></p>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-indigo-500 text-white"><?= e($i['specialization']) ?></span>
            <?php if ((int)$i['is_email_verified'] === 1): ?>
              <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Email Verified</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark"><i class="fa fa-exclamation-circle me-1"></i>Email Unverified</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Verification Card Alert if Unverified -->
    <?php if ((int)$i['is_email_verified'] !== 1): ?>
      <div class="card p-3 border-0 shadow-sm mb-4 animate__animated animate__headShake" style="background-color: #fffbeb; border-left: 5px solid #f59e0b !important; border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2.5">
            <i class="fa fa-exclamation-triangle text-warning fa-lg"></i>
            <div>
              <div class="fw-bold text-dark text-sm">Your email address is not verified yet!</div>
              <div class="text-muted small">Verify your email to secure your account and receive course enrollments notifications.</div>
            </div>
          </div>
          <form method="post" action="instructor_profile.php">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="send_verification">
            <button type="submit" class="btn btn-warning btn-sm fw-semibold"><i class="fa fa-paper-plane me-1"></i>Send Verification Email</button>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- General Settings -->
      <div class="col-lg-8">
        <div class="card-settings p-4 shadow-sm">
          <h5 class="fw-bold mb-3"><i class="fa fa-user-edit text-primary me-2"></i>Personal Biography & Background</h5>
          <hr class="mt-0 mb-4" style="opacity: 0.1;">
          
          <form method="post" action="instructor_profile.php" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="update_profile">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Full Name *</label>
                <input type="text" name="full_name" class="form-control" value="<?= e($i['full_name']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= e($i['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Highest Educational Qualification</label>
                <input type="text" name="qualification" class="form-control" value="<?= e($i['qualification'] ?? '') ?>" placeholder="e.g. M.Sc. Computer Science">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Experience (Years)</label>
                <input type="number" name="experience_years" class="form-control" value="<?= (int)($i['experience_years'] ?? 0) ?>" min="0">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Specialization / Elevation *</label>
                <input type="text" name="specialization" class="form-control" value="<?= e($i['specialization']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Profile Photo (Max 2MB)</label>
                <input type="file" name="photo" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">LinkedIn Profile URL</label>
                <input type="url" name="linkedin_url" class="form-control" value="<?= e($i['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Twitter Profile URL</label>
                <input type="url" name="twitter_url" class="form-control" value="<?= e($i['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/username">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">X (Formerly Twitter) Profile URL</label>
                <input type="url" name="x_url" class="form-control" value="<?= e($i['x_url'] ?? '') ?>" placeholder="https://x.com/username">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Instagram Profile URL</label>
                <input type="url" name="instagram_url" class="form-control" value="<?= e($i['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/username">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Website / Portfolio URL</label>
                <input type="url" name="website_url" class="form-control" value="<?= e($i['website_url'] ?? '') ?>" placeholder="https://myportfolio.com">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Biography (Bio)</label>
                <textarea name="bio" rows="6" class="form-control" placeholder="Introduce yourself, your teaching philosophy, and experience..."><?= e($i['bio'] ?? '') ?></textarea>
              </div>
            </div>

            <hr class="my-4" style="opacity: 0.1;">
            <button type="submit" class="btn btn-primary px-4"><i class="fa fa-save me-2"></i>Save Settings</button>
          </form>
        </div>
      </div>

      <!-- Security Settings -->
      <div class="col-lg-4">
        <div class="card-settings p-4 shadow-sm">
          <h5 class="fw-bold mb-3"><i class="fa fa-shield-alt text-primary me-2"></i>Change Password</h5>
          <hr class="mt-0 mb-4" style="opacity: 0.1;">

          <form method="post" action="instructor_profile.php">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="update_password">

            <div class="mb-3">
              <label class="form-label small fw-semibold">Current Password</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label small fw-semibold">New Password</label>
              <input type="password" name="new_password" class="form-control" required>
              <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">Min 8 chars, 1 uppercase, 1 number.</div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-semibold">Confirm New Password</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-outline-primary w-100"><i class="fa fa-key me-2"></i>Change Password</button>
          </form>
        </div>
      </div>
    </div>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // SweetAlert notifications logic
  <?php if ($flashSuccess): ?>
    Swal.fire({
      title: 'Success!',
      text: <?= json_encode($flashSuccess) ?>,
      icon: 'success',
      confirmButtonColor: '#4f46e5'
    });
  <?php endif; ?>

  <?php if ($flashError): ?>
    Swal.fire({
      title: 'Error!',
      text: <?= json_encode($flashError) ?>,
      icon: 'error',
      confirmButtonColor: '#ef4444'
    });
  <?php endif; ?>
</script>
</body>
</html>
