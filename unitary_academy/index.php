<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/config/db.php';

$publicNavActive = 'affiliate';
$publicNavCourses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

$successMessage = '';
$errorMessage = '';
$selectedPortal = '';
$selectedPortalName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check if token helper is present
    $csrfToken = $_POST['_csrf'] ?? '';
    if (function_exists('csrfToken') && $csrfToken !== ($_SESSION['_csrf'] ?? '')) {
        $errorMessage = 'Invalid request token. Please reload and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $partnerType = trim($_POST['partner_type'] ?? '');
        $promoPlan = trim($_POST['promo_plan'] ?? '');
        $password = $_POST['access_password'] ?? '';

        if ($name === '' || $email === '' || $partnerType === '' || $promoPlan === '' || $password === '') {
            $errorMessage = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Please enter a valid email address.';
        } else {
            $portals = [
                'individual' => ['name' => 'Individual Affiliate Portal', 'path' => 'individual/index.php'],
                'organization' => ['name' => 'Organization Affiliate Portal', 'path' => 'organization/index.php'],
                'institution' => ['name' => 'Institution Affiliate Portal', 'path' => 'institution/index.php'],
                'private' => ['name' => 'Private Affiliate Portal', 'path' => 'private/index.php'],
                'government' => ['name' => 'Government Affiliate Portal', 'path' => 'government/index.php'],
            ];
            
            if (isset($portals[$partnerType])) {
                try {
                    // Hash access password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Persist partner to database
                    $stmt = $pdo->prepare("INSERT INTO lms_affiliate_partners (name, email, phone, partner_type, promo_plan, access_password, status) 
                        VALUES (?, ?, ?, ?, ?, ?, 'pending') 
                        ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), partner_type = VALUES(partner_type), promo_plan = VALUES(promo_plan), access_password = VALUES(access_password)");
                    $stmt->execute([$name, $email, $phone, $partnerType, $promoPlan, $hashedPassword]);

                    // Fetch the partner ID
                    $stmt = $pdo->prepare("SELECT id FROM lms_affiliate_partners WHERE email = ?");
                    $stmt->execute([$email]);
                    $partnerId = (int)$stmt->fetchColumn();

                    $_SESSION['partner_id'] = $partnerId;
                    $_SESSION['partner_name'] = $name;
                    $_SESSION['partner_email'] = $email;
                    $_SESSION['partner_phone'] = $phone;
                    $_SESSION['partner_type'] = $partnerType;
                    $_SESSION['partner_promo_plan'] = $promoPlan;
                    $_SESSION['partner_welcome'] = true;
                    
                    header("Location: " . $portals[$partnerType]['path']);
                    exit;
                } catch (PDOException $e) {
                    $errorMessage = 'Database error: ' . $e->getMessage();
                }
            } else {
                $errorMessage = 'Invalid partnership track selected.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle = 'Unitary Academy Affiliate Program';
$seoDesc = 'Join the Unitary Academy partner program. Share and promote courses, empower learners, and earn generous commissions.';
$seoNoIndex = false;
require_once dirname(__DIR__) . '/includes/seo.php';
?>
<title>Unitary Academy | Mirror Age Concepts</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="../assets/css/app.css?v=20260607-nav2" rel="stylesheet">
<style>
  .academy-hero {
    position: relative;
    background: linear-gradient(135deg, #07111f 0%, #0c1e35 100%);
    color: #fff;
    padding: 6rem 0;
    overflow: hidden;
  }
  .academy-hero::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(103,232,249,0.15) 0%, rgba(103,232,249,0) 70%);
    z-index: 1;
    pointer-events: none;
  }
  .academy-hero-content {
    position: relative;
    z-index: 2;
  }
  .academy-hero h1 {
    font-size: clamp(2.5rem, 5vw, 4.2rem);
    font-weight: 800;
    line-height: 1.1;
  }
  .academy-hero h1 span {
    color: #67e8f9;
  }
  .academy-hero .lead {
    color: #cbd5e1;
    font-size: 1.2rem;
    max-width: 650px;
  }
  .feature-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 2rem;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  }
  .feature-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(103,232,249,0.1);
    color: #0d9488;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1.25rem;
  }
  .portal-card {
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .portal-card:hover {
    border-color: #0d9488;
    box-shadow: 0 12px 30px rgba(13, 148, 136, 0.08);
    transform: translateY(-4px);
  }
  .step-num {
    font-size: 3rem;
    font-weight: 800;
    color: rgba(13, 148, 136, 0.15);
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  .form-section {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
  }
  .glass-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
  }
</style>
</head>
<body>
<?php require dirname(__DIR__) . '/includes/public_nav.php'; ?>

<main>
  <!-- HERO SECTION -->
  <section class="academy-hero">
    <div class="container">
      <div class="row align-items-center g-5 academy-hero-content">
        <div class="col-lg-7">
          <span class="home-slider-badge mb-3">
            <i class="fa fa-graduation-cap"></i> Unitary Academy
          </span>
          <h1>Empower Learners, <span>Earn Commissions</span></h1>
          <p class="lead mt-3">
            Partner with Grafix@Mirror LMS to promote high-demand tech and creative courses. Select your partnership track, refer students, and earn tiered discounts and commissions.
          </p>
          <div class="mt-4 d-flex flex-wrap gap-3">
            <a href="#apply" class="btn btn-brand btn-lg px-4 py-2.5">Join Affiliate Network</a>
            <a href="#portals" class="btn btn-outline-light btn-lg px-4 py-2.5">Explore Portals</a>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="glass-card p-4 text-dark shadow-lg">
            <h4 class="fw-bold mb-3">Partnership Model</h4>
            <div class="d-grid gap-2 text-muted mb-4" style="font-size: 0.9rem;">
              <div class="d-flex justify-content-between">
                <span>Individual Track:</span>
                <strong class="text-dark">10% - 25% (Scaling)</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span>Institutions / Orgs:</span>
                <strong class="text-dark">15% - 30% (Unlimited)</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span>Payout Frequency:</span>
                <strong class="text-dark">Monthly Direct Bank</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span>Support:</span>
                <strong class="text-dark">24/7 Partner Success</strong>
              </div>
            </div>
            <a href="#apply" class="btn btn-brand w-100">Register as Partner</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PORTALS DIRECTORY SECTION -->
  <section id="portals" class="py-5" style="background:#fff">
    <div class="container py-4">
      <div class="text-center max-width-600 mx-auto mb-5">
        <span class="badge-brand mb-2">Dedicated Dashboards</span>
        <h2 class="section-title">Affiliate Portals</h2>
        <p class="text-muted">Choose your pathway and access specialized resources, forms, and tools matching your partnership category.</p>
      </div>

      <div class="row g-4">
        <!-- Individual -->
        <div class="col-md-6 col-lg-4">
          <div class="portal-card">
            <div class="feature-icon bg-info-subtle text-info">
              <i class="fa fa-user"></i>
            </div>
            <h4 class="fw-bold fs-5">Individual Portal</h4>
            <p class="text-muted flex-grow-1" style="font-size: 0.9rem;">
              For single referrers or tutors. Refer students one-by-one and earn scaling commissions starting from 10% up to 25% as your referrals grow.
            </p>
            <a href="individual/index.php" class="btn btn-sm btn-outline-brand mt-3">Access Individual Portal <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- Organization -->
        <div class="col-md-6 col-lg-4">
          <div class="portal-card">
            <div class="feature-icon bg-primary-subtle text-primary">
              <i class="fa fa-sitemap"></i>
            </div>
            <h4 class="fw-bold fs-5">Organization Portal</h4>
            <p class="text-muted flex-grow-1" style="font-size: 0.9rem;">
              For corporate bodies, associations, and NGOs. Refer unlimited pupils with tiered commissions from 15% to 30%. Integrates private/public school trackers.
            </p>
            <a href="organization/index.php" class="btn btn-sm btn-outline-brand mt-3">Access Organization Portal <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- Institution -->
        <div class="col-md-6 col-lg-4">
          <div class="portal-card">
            <div class="feature-icon bg-success-subtle text-success">
              <i class="fa fa-school"></i>
            </div>
            <h4 class="fw-bold fs-5">Institution Portal</h4>
            <p class="text-muted flex-grow-1" style="font-size: 0.9rem;">
              For secondary schools, universities, and educational institutions. Features Public/Private School tracking and grade filters (JSS 1-3, SSS 1-3).
            </p>
            <a href="institution/index.php" class="btn btn-sm btn-outline-brand mt-3">Access Institution Portal <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- Private -->
        <div class="col-md-6 col-lg-6">
          <div class="portal-card">
            <div class="feature-icon bg-warning-subtle text-warning">
              <i class="fa fa-building"></i>
            </div>
            <h4 class="fw-bold fs-5">Private Affiliate Portal</h4>
            <p class="text-muted flex-grow-1" style="font-size: 0.9rem;">
              For private enterprises, commercial scholarship programs, and private training centres looking to scale student learning. High-volume tiered benefits apply.
            </p>
            <a href="private/index.php" class="btn btn-sm btn-outline-brand mt-3">Access Private Portal <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- Government -->
        <div class="col-md-6 col-lg-6">
          <div class="portal-card">
            <div class="feature-icon bg-danger-subtle text-danger">
              <i class="fa fa-landmark"></i>
            </div>
            <h4 class="fw-bold fs-5">Government Affiliate Portal</h4>
            <p class="text-muted flex-grow-1" style="font-size: 0.9rem;">
              For government agencies, ministries of education, and public empowerment programs. Track public secondary schools and candidate classes (JSS/SSS).
            </p>
            <a href="government/index.php" class="btn btn-sm btn-outline-brand mt-3">Access Government Portal <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BENEFITS SECTION -->
  <section id="benefits" class="py-5 bg-light border-top">
    <div class="container py-4">
      <div class="text-center max-width-600 mx-auto mb-5">
        <span class="badge-brand mb-2">Flexible Schemes</span>
        <h2 class="section-title">Commission & Discount Structure</h2>
        <p class="text-muted">How commissions and student discounts are structured depending on your category.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-user"></i>
            </div>
            <h4 class="fw-bold fs-5">Individual Affiliates</h4>
            <p class="text-muted">Designed for single agents. Refer students dynamically over time with scaling commissions:</p>
            <ul class="text-muted ps-3">
              <li><strong>1 Pupil Referred:</strong> 10% commission</li>
              <li><strong>2 Pupils Referred:</strong> 15% commission</li>
              <li><strong>3 – 5 Pupils Referred:</strong> 20% commission</li>
              <li><strong>6+ Pupils Referred:</strong> 25% commission</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-users"></i>
            </div>
            <h4 class="fw-bold fs-5">Institutions, Orgs, Private & Government</h4>
            <p class="text-muted">Designed for organizations referring from 1 to infinity pupils, with dynamic tiered discounts:</p>
            <ul class="text-muted ps-3">
              <li><strong>1 – 5 Pupils:</strong> 15% discount/commission</li>
              <li><strong>6 – 20 Pupils:</strong> 20% discount/commission</li>
              <li><strong>21 – 50 Pupils:</strong> 25% discount/commission</li>
              <li><strong>51+ Pupils:</strong> 30% discount/commission</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- REGISTRATION FORM -->
  <section id="apply" class="py-5 form-section">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="lms-card p-4 p-md-5 shadow-sm bg-white rounded-4">
            <div class="text-center mb-4">
              <h3 class="fw-bold">Partnership Application Form</h3>
              <p class="text-muted">Submit your application to join the Unitary Academy partner network today.</p>
            </div>

            <?php if ($successMessage !== ''): ?>
              <div class="alert alert-success d-flex align-items-center gap-3 mb-4" role="alert">
                <i class="fa fa-circle-check fs-4 text-success"></i>
                <div>
                  <?= e($successMessage) ?>
                  <?php if ($selectedPortal !== ''): ?>
                    <div class="mt-2">
                      <a href="<?= e($selectedPortal) ?>" class="btn btn-sm btn-brand">Go to <?= e($selectedPortalName) ?> <i class="fa fa-arrow-right"></i></a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
              <div class="alert alert-danger d-flex align-items-center gap-3 mb-4" role="alert">
                <i class="fa fa-triangle-exclamation fs-4 text-danger"></i>
                <div><?= e($errorMessage) ?></div>
              </div>
            <?php endif; ?>

            <?php if ($successMessage === ''): ?>
              <form method="post" action="#apply">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken() ?? '') ?>">
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Contact Person / Organization Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control py-2.5" id="name" name="name" required placeholder="e.g. John Doe / Global Tech Inc">
                  </div>
                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control py-2.5" id="email" name="email" required placeholder="e.g. john@example.com">
                  </div>
                  <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                    <input type="tel" class="form-control py-2.5" id="phone" name="phone" placeholder="e.g. +234 80 1234 5678">
                  </div>
                  <div class="col-md-6">
                    <label for="partner_type" class="form-label fw-semibold">Partnership Track <span class="text-danger">*</span></label>
                    <select class="form-select py-2.5" id="partner_type" name="partner_type" required>
                      <option value="" disabled selected>Select Partnership Type</option>
                      <option value="individual">Individual Affiliate (Scaling Referrals)</option>
                      <option value="organization">Organization Affiliate (Unlimited Referrals)</option>
                      <option value="institution">Institution Affiliate (Schools/Academies)</option>
                      <option value="private">Private Corporate Partner</option>
                      <option value="government">Government Agency / Ministry</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="access_password" class="form-label fw-semibold">Create Access Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control py-2.5" id="access_password" name="access_password" required placeholder="Create a secure passcode/password to access your portal later">
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="checkbox" id="show_access_password">
                      <label class="form-check-label small text-muted" for="show_access_password">Show Password</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label for="promo_plan" class="form-label fw-semibold">How do you plan to promote our courses? <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="promo_plan" name="promo_plan" rows="4" required placeholder="Describe your target audience, school size, campaign strategy, or number of candidates..."></textarea>
                  </div>
                </div>

                <div class="mt-4 text-center">
                  <button type="submit" class="btn btn-brand btn-lg px-5 py-2.5">Submit Application</button>
                </div>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require dirname(__DIR__) . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('show_access_password')?.addEventListener('change', function() {
  const pwdField = document.getElementById('access_password');
  if (pwdField) {
    pwdField.type = this.checked ? 'text' : 'password';
  }
});
</script>
</body>
</html>
