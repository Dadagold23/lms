<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once dirname(dirname(__DIR__)) . '/includes/helpers.php';
require_once dirname(dirname(__DIR__)) . '/config/db.php';

$publicNavActive = 'affiliate';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

$successMessage = '';
$errorMessage = '';

$partnerId = 0;
if (isset($_SESSION['partner_id']) && ($_SESSION['partner_type'] ?? '') === 'individual') {
    $partnerId = (int)$_SESSION['partner_id'];
}

$welcomeMessage = '';
if (isset($_SESSION['partner_welcome']) && $_SESSION['partner_welcome'] === true) {
    $welcomeMessage = 'Welcome to your portal, ' . ($_SESSION['partner_name'] ?? 'Partner') . '! Your application has been submitted successfully.';
    unset($_SESSION['partner_welcome']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout_partner') {
    unset($_SESSION['partner_id']);
    unset($_SESSION['partner_name']);
    unset($_SESSION['partner_email']);
    unset($_SESSION['partner_type']);
    header("Location: index.php");
    exit;
}

function uploadPortalImage(string $field, string $uploadDir): ?string
{
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return null;
    }

    if (($_FILES[$field]['size'] ?? 0) > (3 * 1024 * 1024)) {
        return null;
    }

    $name = uniqid($field . '_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $name)) {
        return null;
    }
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'load_partner') {
    $csrfToken = $_POST['_csrf'] ?? '';
    if (function_exists('csrfToken') && $csrfToken !== ($_SESSION['_csrf'] ?? '')) {
        $errorMessage = 'Invalid request token.';
    } else {
        $email = trim($_POST['partner_email'] ?? '');
        $password = $_POST['partner_password'] ?? '';
        if ($email === '' || $password === '') {
            $errorMessage = 'Please enter both your email and access password.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM lms_affiliate_partners WHERE email = ?");
            $stmt->execute([$email]);
            $partner = $stmt->fetch();
            if ($partner) {
                if ($partner['partner_type'] !== 'individual') {
                    $errorMessage = 'This partner account is registered as ' . ucfirst($partner['partner_type']) . ', not Individual.';
                } elseif (!password_verify($password, $partner['access_password'])) {
                    $errorMessage = 'Invalid email address or access password. (Note: Passwords are case-sensitive. If copy-pasting, ensure there are no trailing spaces. You can reset your password by re-submitting the application form with your email.)';
                } else {
                    $_SESSION['partner_id'] = (int)$partner['id'];
                    $_SESSION['partner_name'] = $partner['name'];
                    $_SESSION['partner_email'] = $partner['email'];
                    $_SESSION['partner_type'] = $partner['partner_type'];
                    $successMessage = 'Partner account loaded successfully!';
                    header("Location: index.php");
                    exit;
                }
            } else {
                $errorMessage = 'Invalid email address or access password. (Note: Passwords are case-sensitive. If copy-pasting, ensure there are no trailing spaces. You can reset your password by re-submitting the application form with your email.)';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refer') {
    $isAjax = isset($_GET['ajax']);
    $csrfToken = $_POST['_csrf'] ?? '';
    if (function_exists('csrfToken') && $csrfToken !== ($_SESSION['_csrf'] ?? '')) {
        $msg = 'Invalid request token.';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit;
        }
        $errorMessage = $msg;
    } else {
        $name     = trim($_POST['pupil_name'] ?? '');
        $email    = trim($_POST['pupil_email'] ?? '');
        $pupilDob = trim($_POST['pupil_dob'] ?? '');
        $courseId = (int)($_POST['course_id'] ?? 0);
        $kycType    = trim($_POST['kyc_type'] ?? '');
        $kycNumber  = trim($_POST['kyc_number'] ?? '');

        // Upload images
        $uploadDir = dirname(dirname(__DIR__)) . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $passportPhoto  = uploadPortalImage('passport', $uploadDir);
        $signaturePhoto = uploadPortalImage('signature', $uploadDir);

        if ($partnerId === 0) {
            $msg = 'Please load or register your partner account first.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $errorMessage = $msg;
        } elseif ($name === '' || $email === '' || $courseId === 0 || $kycType === '' || $kycNumber === '') {
            $msg = 'Please fill in all required fields, including KYC details.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $errorMessage = $msg;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Please enter a valid email address.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $errorMessage = $msg;
        } elseif (!$passportPhoto || !$signaturePhoto) {
            $msg = 'Valid passport photo and signature are required (JPG/PNG, max 3MB).';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $errorMessage = $msg;
        } else {
            try {
                $token = bin2hex(random_bytes(16));
                $autologinToken = bin2hex(random_bytes(16));
                $stmt = $pdo->prepare("INSERT INTO lms_affiliate_referrals (partner_id, pupil_name, pupil_email, course_id, referral_token, pupil_dob, kyc_type, kyc_number, passport, signature, autologin_token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_enrollment')");
                $stmt->execute([$partnerId, $name, $email, $courseId, $token, ($pupilDob ?: null), $kycType, $kycNumber, $passportPhoto, $signaturePhoto, $autologinToken]);
                $referralId = (int)$pdo->lastInsertId();

                // Get course title
                $cStmt = $pdo->prepare("SELECT title FROM lms_courses WHERE id = ?");
                $cStmt->execute([$courseId]);
                $courseTitle = $cStmt->fetchColumn() ?: 'Course';

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Pupil referral registered successfully!',
                        'referral' => [
                            'id' => $referralId,
                            'pupil_name' => $name,
                            'pupil_email' => $email,
                            'course_title' => $courseTitle,
                            'referral_token' => $token,
                            'passport' => $passportPhoto,
                            'signature' => $signaturePhoto,
                            'autologin_token' => $autologinToken,
                            'status' => 'pending_enrollment'
                        ]
                    ]);
                    exit;
                }
                $successMessage = 'Pupil referral registered successfully!';
            } catch (PDOException $e) {
                $msg = 'Database error: ' . $e->getMessage();
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $errorMessage = $msg;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    if ($partnerId > 0) {
        $pdo->prepare("DELETE FROM lms_affiliate_referrals WHERE partner_id = ?")->execute([$partnerId]);
        $successMessage = 'Referrals reset successfully for testing.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'partner_make_payment') {
    $referralId = (int)($_POST['referral_id'] ?? 0);
    if ($partnerId > 0 && $referralId > 0) {
        redirect("../../partner_checkout.php?referral_id=" . $referralId);
    }
}

// Load referrals from database
$referrals = [];
if ($partnerId > 0) {
    $stmt = $pdo->prepare("
        SELECT r.*, c.title AS course_title, s.id AS student_id, e.id AS enrollment_id, e.status AS enrollment_status, e.paid_amount AS enrollment_paid_amount
        FROM lms_affiliate_referrals r 
        JOIN lms_courses c ON r.course_id = c.id 
        LEFT JOIN lms_students s ON r.pupil_email = s.email
        LEFT JOIN lms_enrollments e ON (s.id = e.student_id AND (r.affiliate_course_id = e.course_id OR (r.affiliate_course_id IS NULL AND r.course_id = e.course_id)))
        WHERE r.partner_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$partnerId]);
    $referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$referralCount = count($referrals);

$commissionRate = 0;
if ($referralCount === 1) {
    $commissionRate = 10;
} elseif ($referralCount === 2) {
    $commissionRate = 15;
} elseif ($referralCount >= 3 && $referralCount <= 5) {
    $commissionRate = 20;
} elseif ($referralCount >= 6) {
    $commissionRate = 25;
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Individual Affiliate Portal';
$seoDesc = 'Manage your individual referrals and check your commission earnings in real time.';
$seoNoIndex = true;
require_once dirname(dirname(__DIR__)) . '/includes/seo.php';
?>
<title>Individual Affiliate Portal | Unitary Academy</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../../assets/css/app.css?v=20260607-nav2" rel="stylesheet">
<style>
  .portal-header {
    background: linear-gradient(135deg, #07111f 0%, #0c1e35 100%);
    color: #fff;
    padding: 3rem 0;
  }
  .stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
  }
  .stat-num {
    font-size: 2.2rem;
    font-weight: 800;
    color: #0d9488;
  }
  .form-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 2rem;
  }
</style>
</head>
<body class="bg-light">
<?php require dirname(dirname(__DIR__)) . '/includes/public_nav.php'; ?>

<main>
  <!-- HEADER -->
  <section class="portal-header">
    <div class="container text-center text-md-start">
      <div class="row align-items-center justify-content-between g-4">
        <div class="col-md-8">
          <span class="home-slider-badge mb-2">
            <i class="fa fa-user"></i> Individual Partner Track
          </span>
          <h1 class="fw-bold mb-0">Individual Affiliate Portal</h1>
          <p class="text-white-50 mb-0 mt-2">Refer students to courses and earn scaling direct commissions.</p>
        </div>
        <div class="col-md-4 text-md-end">
          <?php if ($partnerId > 0): ?>
            <div class="text-white-50 small mb-2">Logged in as: <strong class="text-white"><?= e($_SESSION['partner_name']) ?></strong></div>
            <form method="post" action="" class="d-inline">
              <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
              <input type="hidden" name="action" value="logout_partner">
              <button type="submit" class="btn btn-sm btn-outline-danger me-2 py-1 small"><i class="fa fa-sign-out"></i> Unload Account</button>
            </form>
          <?php endif; ?>
          <a href="../index.php" class="btn btn-outline-light"><i class="fa fa-arrow-left"></i> Academy Home</a>
        </div>
      </div>
    </div>
  </section>

  <!-- DASHBOARD STATS -->
  <section class="py-4">
    <div class="container">
      <?php if ($welcomeMessage !== ''): ?>
        <div class="alert alert-success d-flex align-items-center gap-3 mb-4" role="alert">
          <i class="fa fa-circle-check fs-4"></i>
          <div><?= e($welcomeMessage) ?></div>
        </div>
      <?php endif; ?>

      <?php if ($partnerId === 0): ?>
        <!-- Load Partner Account Form -->
        <div class="row justify-content-center py-5">
          <div class="col-md-6">
            <div class="form-card shadow-sm p-4 p-md-5 bg-white rounded-4">
              <div class="text-center mb-4">
                <div class="feature-icon bg-info-subtle text-info mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                  <i class="fa fa-unlock"></i>
                </div>
                <h4 class="fw-bold">Load Partner Account</h4>
                <p class="text-muted small">Enter your registered email address to access your individual affiliate dashboard.</p>
              </div>

              <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 small" role="alert">
                  <i class="fa fa-circle-exclamation"></i>
                  <div><?= e($errorMessage) ?></div>
                </div>
              <?php endif; ?>

              <?php if ($successMessage !== ''): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3 small" role="alert">
                  <i class="fa fa-circle-check"></i>
                  <div><?= e($successMessage) ?></div>
                </div>
              <?php endif; ?>

              <form method="post" action="">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
                <input type="hidden" name="action" value="load_partner">

                <div class="mb-3">
                  <label for="partner_email" class="form-label fw-semibold">Your Registered Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control py-2.5" id="partner_email" name="partner_email" required placeholder="partner@example.com">
                </div>

                <div class="mb-3">
                  <label for="partner_password" class="form-label fw-semibold">Access Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control py-2.5" id="partner_password" name="partner_password" required placeholder="••••••••">
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="show_partner_password">
                      <label class="form-check-label small text-muted" for="show_partner_password">Show Password</label>
                    </div>
                    <a href="../forgot_password.php" class="small text-decoration-none text-muted">Forgot password?</a>
                  </div>
                </div>

                <button type="submit" class="btn btn-brand w-100 py-2.5">
                  <i class="fa fa-sign-in"></i> Load Account
                </button>
              </form>

              <div class="mt-4 text-center border-top pt-3">
                <span class="text-muted small">Don't have an affiliate account? <a href="../index.php#apply" class="text-brand fw-semibold">Register as Partner</a></span>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>

      <div class="row g-4 mb-4">
        <!-- Referrals Made -->
        <div class="col-md-4">
          <div class="stat-card text-center text-md-start">
            <div class="text-muted fs-6 mb-1">Referrals Registered</div>
            <div class="stat-num" id="stat_referral_count"><?= $referralCount ?></div>
            <div class="progress mt-2" style="height: 6px;">
              <div class="progress-bar bg-brand" id="stat_progress_bar" role="progressbar" style="width: <?= min(100, $referralCount * 16.6) ?>%" aria-valuenow="<?= $referralCount ?>" aria-valuemin="0" aria-valuemax="6"></div>
            </div>
          </div>
        </div>

        <!-- Current Commission Level -->
        <div class="col-md-4">
          <div class="stat-card text-center text-md-start">
            <div class="text-muted fs-6 mb-1">Current Commission Rate</div>
            <div class="stat-num" id="stat_commission_rate"><?= $commissionRate ?>%</div>
            <div class="text-muted" id="stat_commission_desc" style="font-size: 0.8rem; margin-top: 5px;">
              <?= $referralCount === 0 ? 'Refer 1 pupil to reach 10%' : ($referralCount === 1 ? 'Refer 1 more to reach 15%' : ($referralCount === 2 ? 'Refer to reach 20%' : ($referralCount < 6 ? 'Refer to reach 25%' : 'Maximum 25% rate reached'))) ?>
            </div>
          </div>
        </div>

        <!-- Commission Bracket -->
        <div class="col-md-4">
          <div class="stat-card text-center text-md-start">
            <div class="text-muted fs-6 mb-1">Commission Bracket</div>
            <div class="stat-num text-brand">Scaling</div>
            <div class="text-muted" style="font-size: 0.8rem; margin-top: 5px;">
              Commissions scale as you refer more over time.
            </div>
          </div>
        </div>
      </div>

      <!-- MAIN CONTENT GRID -->
      <div class="row g-4">
        <!-- Referral Form -->
        <div class="col-lg-6">
          <div class="form-card shadow-sm">
            <h4 class="fw-bold mb-3"><i class="fa fa-user-plus text-brand"></i> Register a New Referral</h4>
            <p class="text-muted small">Submit details of pupils you are introducing to Mirror LMS.</p>

            <?php if ($successMessage !== ''): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= e($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <form method="post" action="" id="referralForm" enctype="multipart/form-data">
              <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
              <input type="hidden" name="action" value="refer">

              <div class="mb-3">
                <label for="pupil_name" class="form-label fw-semibold">Pupil Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control py-2" id="pupil_name" name="pupil_name" required placeholder="e.g. Samuel Adebayo">
              </div>

              <div class="mb-3">
                <label for="pupil_email" class="form-label fw-semibold">Pupil Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control py-2" id="pupil_email" name="pupil_email" required placeholder="e.g. samuel@example.com">
              </div>

              <div class="mb-3">
                <label for="pupil_dob" class="form-label fw-semibold">Date of Birth <span class="text-muted small">(Optional — auto-assigns class level)</span></label>
                <input type="date" class="form-control py-2" id="pupil_dob" name="pupil_dob" max="<?= date('Y-m-d') ?>">
                <div class="small text-muted mt-1" id="dobClassHint"></div>
              </div>

              <div class="mb-3">
                <label for="course_id" class="form-label fw-semibold">Target Course <span class="text-danger">*</span></label>
                <select class="form-select py-2" id="course_id" name="course_id" required>
                  <option value="" disabled selected>Select Course</option>
                  <?php foreach ($publicNavCourses as $course): ?>
                    <option value="<?= (int)$course['id'] ?>"><?= e($course['title']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <hr class="my-4">
              <h5 class="fw-bold mb-3 text-secondary" style="font-size: 1.1rem;"><i class="fa fa-id-card me-2 text-brand"></i>Identity Verification (KYC)</h5>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label for="kyc_type" class="form-label fw-semibold">ID Type <span class="text-danger">*</span></label>
                  <select name="kyc_type" id="kyc_type" class="form-select py-2" required>
                    <option value="" disabled selected>Select ID type</option>
                    <option value="NIN">NIN</option>
                    <option value="International Passport">International Passport</option>
                    <option value="Voter Card">Voter Card</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="kyc_number" class="form-label fw-semibold">ID Number <span class="text-danger">*</span></label>
                  <input type="text" name="kyc_number" id="kyc_number" class="form-control py-2" required placeholder="Enter ID number">
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label for="passport" class="form-label fw-semibold">Passport Photo <span class="text-danger">*</span></label>
                  <input type="file" name="passport" id="passport" class="form-control py-2" accept="image/*" required>
                  <div class="small text-muted mt-1">JPG/PNG, max 3MB</div>
                </div>
                <div class="col-md-6">
                  <label for="signature" class="form-label fw-semibold">Signature <span class="text-danger">*</span></label>
                  <input type="file" name="signature" id="signature" class="form-control py-2" accept="image/*" required>
                  <div class="small text-muted mt-1">JPG/PNG, max 3MB</div>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-brand w-100 py-2">
                  <i class="fa fa-paper-plane"></i> Submit Referral
                </button>
              </div>
            </form>

            <div class="mt-3 text-center">
              <form method="post" action="">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn btn-sm btn-link text-muted"><i class="fa fa-refresh"></i> Reset referrals for testing</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Referral List -->
        <div class="col-lg-6">
          <div class="form-card shadow-sm h-100" id="referralsLogCard">
            <h4 class="fw-bold mb-3"><i class="fa fa-list-check text-brand"></i> Referral Log</h4>
            <p class="text-muted small">Tracks your referred students and their registration status.</p>

            <?php if ($referralCount === 0): ?>
              <div class="text-center py-5 text-muted" id="referralEmptyState">
                <i class="fa fa-users fs-1 mb-3 text-light"></i>
                <p>No referrals submitted yet. Use the form to register your first pupil.</p>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Pupil</th>
                      <th>Course</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="referralTableBody">
                    <?php foreach ($referrals as $ref): ?>
                      <tr>
                        <td>
                          <div class="fw-semibold text-dark"><?= e($ref['pupil_name']) ?></div>
                          <div class="small text-muted"><?= e($ref['pupil_email']) ?></div>
                        </td>
                        <td class="small"><?= e($ref['course_title']) ?></td>
                        <td>
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1.5">
                            <?= ucfirst(str_replace('_', ' ', $ref['status'])) ?>
                          </span>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-outline-brand py-1 px-2 fw-semibold" style="font-size:0.75rem; color:#0d9488; border-color:#0d9488;"
                                  onclick="showReferralIdCard(<?= e(json_encode([
                                    'pupil_name' => $ref['pupil_name'],
                                    'pupil_email' => $ref['pupil_email'],
                                    'course_title' => $ref['course_title'] ?? 'Course',
                                    'referral_token' => $ref['referral_token'],
                                    'passport' => $ref['passport'],
                                    'signature' => $ref['signature'],
                                    'autologin_token' => $ref['autologin_token']
                                  ])) ?>)">
                            <i class="fa fa-id-card me-1"></i> ID Card
                          </button>
                           <?php if ($ref['status'] === 'enrolled' && ($ref['enrollment_status'] ?? '') === 'active'): ?>
                             <form method="post" class="d-inline ms-1" onsubmit="confirmPartnerPayment(event, this);">
                              <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
                              <input type="hidden" name="action" value="partner_make_payment">
                              <input type="hidden" name="referral_id" value="<?= (int)$ref['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-brand py-1 px-2 fw-semibold text-white" style="font-size:0.75rem; background:#0d9488; border-color:#0d9488;">
                                <i class="fa fa-credit-card me-1"></i> Make Payment
                              </button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<!-- Student ID Card Modal -->
<div class="modal fade" id="idCardModal" tabindex="-1" aria-labelledby="idCardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-dark text-white border-0 py-3">
        <h5 class="modal-title fw-bold" id="idCardModalLabel"><i class="fa fa-id-card text-info me-2"></i>Student Referral ID Card</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 bg-light text-center">
        <!-- Stacked ID Card (Front & Back) -->
        <div id="studentIdCard" class="mx-auto" style="width: 320px; display: flex; flex-direction: column; gap: 20px;">
          
          <!-- FRONT SIDE -->
          <div class="card-front p-4 position-relative shadow rounded-4 overflow-hidden text-start" style="width: 320px; min-height: 380px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; border: 2px solid #0d9488; font-family: 'Inter', sans-serif; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <!-- Card Header -->
              <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-3">
                <div>
                  <h6 class="fw-bold text-info mb-0" style="letter-spacing: 1px; font-size: 0.9rem;">UNITARY ACADEMY</h6>
                  <span class="text-muted" style="font-size: 0.65rem;">STUDENT REFERRAL ID</span>
                </div>
                <i class="fa fa-graduation-cap text-info fs-3"></i>
              </div>
              
              <!-- Card Body -->
              <div class="row g-2 align-items-center">
                <!-- Profile avatar / initials or Passport photo -->
                <div class="col-4 text-center">
                  <div class="d-flex align-items-center justify-content-center bg-secondary text-info fw-bold rounded-circle border border-info overflow-hidden" style="width: 75px; height: 75px; font-size: 1.5rem;" id="card_avatar_container">
                    <img id="card_passport" src="" alt="Passport" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <span id="card_initials">JD</span>
                  </div>
                </div>
                <!-- Bio Info -->
                <div class="col-8 ps-3" style="font-size: 0.75rem;">
                  <div class="mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">FULL NAME</span>
                    <strong class="text-white d-block text-truncate" id="card_name" style="font-size: 0.85rem;">John Doe</strong>
                  </div>
                  <div class="mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">EMAIL ADDRESS</span>
                    <span class="text-info d-block text-truncate" id="card_email">john@doe.com</span>
                  </div>
                  <div class="mb-0 d-flex justify-content-between align-items-end">
                    <div>
                      <span class="text-muted d-block" style="font-size: 0.6rem;">COURSE / TRACK</span>
                      <span class="text-white d-block text-truncate fw-semibold" id="card_course" style="max-width: 100px;">Graphic Design</span>
                    </div>
                    <div class="text-end">
                      <span class="text-muted d-block text-end" style="font-size: 0.55rem; margin-bottom: 2px;">SIGNATURE</span>
                      <div class="border rounded bg-white p-1 d-inline-block" style="height: 30px; width: 75px;">
                        <img id="card_signature" src="" alt="Signature" style="height: 100%; width: 100%; object-fit: contain;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- QR Code section -->
            <div class="border-top border-secondary pt-3 mt-3 text-center">
              <div class="mb-2" style="font-size: 0.65rem; color: #94a3b8;">SCAN TO ENROLL & ACCESS COURSES</div>
              <!-- Unique QR Code from API -->
              <img id="card_qr" src="" alt="QR Link" class="img-fluid rounded border p-1" style="background:#fff; width: 110px; height: 110px;">
              <div class="mt-2 text-info fw-mono fw-bold" id="card_token" style="font-size: 0.7rem; letter-spacing: 1px;">REF-123456</div>
            </div>
          </div>
          
          <!-- BACK SIDE -->
          <div class="card-back p-4 position-relative shadow rounded-4 overflow-hidden text-start" style="width: 320px; min-height: 380px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; border: 2px solid #0d9488; font-family: 'Inter', sans-serif; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3">
                <span class="fw-bold text-info" style="font-size: 0.8rem; letter-spacing: 1px;">CARD ACCESS & TERMS</span>
                <span class="text-white-50" style="font-size: 0.6rem;">BACK</span>
              </div>
              
              <div class="text-white-50" style="font-size: 0.65rem; line-height: 1.4;">
                <p class="mb-2">1. This card is the property of <strong>Unitary Academy</strong> and must be presented on request.</p>
                <p class="mb-2">2. It certifies that the holder is a registered student in the affiliate curriculum track.</p>
                <p class="mb-2">3. In case of lost credentials or forgotten password, scan the security barcode below to instantly authenticate and access your student dashboard.</p>
                <p class="mb-0">4. Misuse of this card or the autologin barcode will lead to immediate suspension of portal access.</p>
              </div>
            </div>
            
            <div class="pt-3 border-top border-secondary mt-3">
              <!-- Company Signature -->
              <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                  <span class="text-muted d-block" style="font-size: 0.55rem;">ISSUING AUTHORITY</span>
                  <span class="text-info fw-semibold" style="font-size: 0.65rem;">Mirror Age Concepts</span>
                </div>
                <div class="text-end">
                  <div class="d-inline-block" style="height: 25px; margin-bottom: 2px;">
                    <img id="card_org_signature" src="../../assets/img/og-sign.png" alt="Signature" style="height: 100%; object-fit: contain; transform: rotate(-5deg); filter: invert(1) brightness(1.2);">
                  </div>
                  <span class="text-muted d-block" style="font-size: 0.55rem; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 2px;">Director of Studies</span>
                </div>
              </div>
              
              <!-- Security Autologin Code -->
              <div class="text-center bg-white p-2 rounded">
                <div class="small text-dark mb-1 fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">SECURITY AUTOLOGIN CODE</div>
                <img id="card_barcode" src="" alt="Security QR Code" class="img-fluid rounded border p-1" style="background:#fff; width: 90px; height: 90px; object-fit: contain;">
                <div class="text-muted mt-1 fw-mono" id="card_barcode_text" style="font-size: 0.55rem;">*SYS-LOGIN-XYZ*</div>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Actions -->
        <div class="d-flex gap-2 justify-content-center mt-3">
          <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-brand w-50 py-2" onclick="printIdCard()"><i class="fa fa-print me-2"></i>Print Card</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require dirname(dirname(__DIR__)) . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function printIdCard() {
  const cardHtml = document.getElementById('studentIdCard').outerHTML;
  const printWindow = window.open('', '_blank', 'width=600,height=600');
  printWindow.document.write('<html><head><title>Student ID Card</title>');
  printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">');
  printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
  printWindow.document.write('<style>body { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; min-height: 100vh; background: #f8fafc; margin: 0; padding: 20px; } #studentIdCard { gap: 20px !important; } @media print { body { background: none !important; padding: 0 !important; } * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; } #studentIdCard { gap: 20px !important; page-break-inside: avoid !important; } .card-front { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; background-color: #0f172a !important; color: #ffffff !important; border: 2px solid #0d9488 !important; page-break-inside: avoid !important; } .card-back { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; background-color: #1e293b !important; color: #ffffff !important; border: 2px solid #0d9488 !important; page-break-inside: avoid !important; } .border.rounded.bg-white { background: transparent !important; background-color: transparent !important; border-color: rgba(255, 255, 255, 0.2) !important; } #card_signature { mix-blend-mode: multiply !important; } #card_org_signature { filter: invert(1) !important; } #card_qr { background: #ffffff !important; border: 1px solid #cbd5e1 !important; } .text-center.bg-white.p-2.rounded { background: #ffffff !important; background-color: #ffffff !important; } #card_barcode { background: #ffffff !important; } .text-info { color: #0d9488 !important; } .text-muted { color: #94a3b8 !important; } }</style>');
  printWindow.document.write('</head><body>');
  printWindow.document.write(cardHtml);
  printWindow.document.write('<script>window.onload = function() { window.print(); window.close(); }<\/script>');
  printWindow.document.write('</body></html>');
  printWindow.document.close();
}

function showReferralIdCard(refData) {
  // Generate initials
  const names = refData.pupil_name.trim().split(/\s+/);
  let initials = '';
  if (names.length > 0) {
    initials += names[0][0].toUpperCase();
    if (names.length > 1) {
      initials += names[names.length - 1][0].toUpperCase();
    }
  }
  
  // Generate registration link
  const regLink = <?= json_encode(appAbsoluteUrl('register.php')) ?> + `?ref_token=${refData.referral_token}`;
  
  // Populate ID card modal
  document.getElementById('card_initials').textContent = initials || 'ST';
  document.getElementById('card_name').textContent = refData.pupil_name;
  document.getElementById('card_email').textContent = refData.pupil_email;
  document.getElementById('card_course').textContent = refData.course_title;
  document.getElementById('card_token').textContent = `REF-${refData.referral_token.substring(0, 8).toUpperCase()}`;
  document.getElementById('card_qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=130x130&color=0d9488&data=${encodeURIComponent(regLink)}`;
  
  // Populate Passport and Signature
  const cardPassport = document.getElementById('card_passport');
  const cardInitials = document.getElementById('card_initials');
  if (refData.passport) {
    cardPassport.src = `../../uploads/${refData.passport}`;
    cardPassport.style.display = 'block';
    cardInitials.style.display = 'none';
  } else {
    cardPassport.style.display = 'none';
    cardInitials.style.display = 'block';
  }
  
  const cardSignature = document.getElementById('card_signature');
  if (refData.signature) {
    cardSignature.src = `../../uploads/${refData.signature}`;
    cardSignature.style.display = 'block';
  } else {
    cardSignature.style.display = 'none';
  }
  
  // Populate Security QR Code
  const cardBarcode = document.getElementById('card_barcode');
  const cardBarcodeText = document.getElementById('card_barcode_text');
  if (refData.autologin_token) {
    const autologinUrl = <?= json_encode(appAbsoluteUrl('autologin.php')) ?> + `?token=${refData.autologin_token}`;
    cardBarcode.src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&color=0f172a&data=${encodeURIComponent(autologinUrl)}`;
    cardBarcodeText.textContent = `*SYS-LOGIN-${refData.autologin_token.substring(0, 12).toUpperCase()}*`;
  }
  
  // Show ID card modal
  const modal = new bootstrap.Modal(document.getElementById('idCardModal'));
  modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
  // Show password toggle
  document.getElementById('show_partner_password')?.addEventListener('change', function() {
    const pwdField = document.getElementById('partner_password');
    if (pwdField) {
      pwdField.type = this.checked ? 'text' : 'password';
    }
  });

  const referralForm = document.getElementById('referralForm');

  if (referralForm) {
    referralForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(referralForm);
      
      fetch('index.php?ajax=1', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showAlert('success', data.message);
          referralForm.reset();
          
          // Show the ID card using our new helper!
          showReferralIdCard(data.referral);
          
          // Update stats dynamically
          const countEl = document.getElementById('stat_referral_count');
          if (countEl) {
            let count = parseInt(countEl.textContent.trim()) || 0;
            count++;
            countEl.textContent = count;
            
            // Update progress bar
            const pb = document.getElementById('stat_progress_bar');
            if (pb) {
              pb.style.width = `${Math.min(100, count * 16.6)}%`;
              pb.setAttribute('aria-valuenow', count);
            }
            
            // Update commission rate and description
            const rateEl = document.getElementById('stat_commission_rate');
            const descEl = document.getElementById('stat_commission_desc');
            
            let rate = 0;
            let desc = '';
            if (count === 1) {
              rate = 10;
              desc = 'Refer 1 more to reach 15%';
            } else if (count === 2) {
              rate = 15;
              desc = 'Refer to reach 20%';
            } else if (count >= 3 && count <= 5) {
              rate = 20;
              desc = 'Refer to reach 25%';
            } else if (count >= 6) {
              rate = 25;
              desc = 'Maximum 25% rate reached';
            } else {
              rate = 0;
              desc = 'Refer 1 pupil to reach 10%';
            }
            
            if (rateEl) rateEl.textContent = `${rate}%`;
            if (descEl) descEl.textContent = desc;
          }
          
          // Append to Referral table
          const emptyState = document.getElementById('referralEmptyState');
          if (emptyState) emptyState.remove();
          
          let table = document.querySelector('#referralsLogCard .table-responsive table');
          if (!table) {
            // Re-create table if it was empty state
            const container = document.getElementById('referralsLogCard');
            const emptyP = container.querySelector('.text-center');
            if (emptyP) emptyP.remove();
            
            const div = document.createElement('div');
            div.className = 'table-responsive';
            div.innerHTML = `
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Pupil</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="referralTableBody"></tbody>
              </table>
            `;
            container.appendChild(div);
          }
          
          const tbody = document.getElementById('referralTableBody') || document.querySelector('#referralsLogCard tbody');
          if (tbody) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>
                <div class="fw-semibold text-dark">${escapeHtml(data.referral.pupil_name)}</div>
                <div class="small text-muted">${escapeHtml(data.referral.pupil_email)}</div>
              </td>
              <td class="small">
                ${escapeHtml(data.referral.course_title)}
              </td>
              <td>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill">
                  Pending Enrollment
                </span>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-brand py-1 px-2 fw-semibold" style="font-size:0.75rem; color:#0d9488; border-color:#0d9488;"
                        onclick="showReferralIdCard(${JSON.stringify(data.referral).replace(/"/g, '&quot;')})">
                  <i class="fa fa-id-card me-1"></i> ID Card
                </button>
              </td>
            `;
            tbody.insertBefore(tr, tbody.firstChild);
          }
        } else {
          showAlert('danger', data.message);
        }
      })
      .catch(err => {
        showAlert('danger', 'An unexpected error occurred.');
      });
    });
  }

  function showAlert(type, message) {
    Swal.fire({
      icon: type === 'danger' ? 'error' : (type === 'success' ? 'success' : 'info'),
      title: type === 'danger' ? 'Error' : (type === 'success' ? 'Success' : 'Notification'),
      text: message,
      confirmButtonColor: '#0d9488'
    });
  }

  function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  }
});

function confirmPartnerPayment(event, form) {
  event.preventDefault();
  Swal.fire({
    title: 'Unlock Course?',
    text: 'Unlock course for this student and deduct commission?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0d9488',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, unlock it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}
</script>
</body>
</html>
