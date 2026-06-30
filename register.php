<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
startSecureSession();
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user'])) redirect('dashboard.php');

$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
$year = date('Y');

$countries = $pdo->query("SELECT iso2, name FROM ref_countries ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$courses   = $pdo->query("SELECT id, title, price FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$selectedCourseId = (int)($_GET['course_id'] ?? 0);
$refToken = $_GET['ref_token'] ?? '';
$referral = null;
$prefilledFirstName = '';
$prefilledLastName = '';
$isAffiliate = false;
$affiliateCourseId = 0;
$affiliateCourses = [];
$selectedAffiliateCourse = null;

if ($refToken !== '') {
    $stmt = $pdo->prepare("SELECT * FROM lms_affiliate_referrals WHERE referral_token = ? AND status = 'pending_enrollment'");
    $stmt->execute([$refToken]);
    $referral = $stmt->fetch();
    if ($referral) {
        $isAffiliate = true;
        $selectedCourseId = (int)$referral['course_id'];
        $parts = explode(' ', trim($referral['pupil_name']), 2);
        $prefilledFirstName = $parts[0] ?? '';
        $prefilledLastName = $parts[1] ?? '';

        // Load affiliate course list
        $affiliateCourses = $pdo->query("SELECT id, title, price FROM lms_affiliate_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

        // Try to match the referral's normal course to an affiliate course by title
        $refCourseTitle = '';
        if ($selectedCourseId > 0) {
            $cStmt = $pdo->prepare("SELECT title FROM lms_courses WHERE id = ? LIMIT 1");
            $cStmt->execute([$selectedCourseId]);
            $refCourseTitle = (string)($cStmt->fetchColumn() ?: '');
        }
        foreach ($affiliateCourses as $ac) {
            if (strtolower($ac['title']) === strtolower($refCourseTitle)) {
                $affiliateCourseId = (int)$ac['id'];
                $selectedAffiliateCourse = $ac;
                break;
            }
        }
        // Fallback: first course
        if ($affiliateCourseId === 0 && !empty($affiliateCourses)) {
            $affiliateCourseId = (int)$affiliateCourses[0]['id'];
            $selectedAffiliateCourse = $affiliateCourses[0];
        }
    }
}
$publicNavCourses = $courses;
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Student Registration';
$seoDesc    = 'Register for a professional technology course at Mirror Age Concepts. Choose from Data Science, AI, Web Development, Cybersecurity, Cloud Computing and more.';
$seoNoIndex = false;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css?v=20260624-auth-fix" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<!-- NAVBAR -->
<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="container py-4" style="max-width:860px">

  <!-- Header -->
  <div class="text-center mb-4">
    <!-- Winding pathway SVG -->
    <svg class="svg-float mb-3" width="100%" height="100" viewBox="0 0 600 100" xmlns="http://www.w3.org/2000/svg" style="max-width: 500px; margin: 0 auto; display: block;">
      <defs>
        <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#4f46e5" />
          <stop offset="50%" stop-color="#a855f7" />
          <stop offset="100%" stop-color="#06b6d4" />
        </linearGradient>
      </defs>
      <!-- Connection Line -->
      <path d="M 100 50 Q 200 15, 300 50 T 500 50" fill="none" stroke="url(#lineGrad)" stroke-width="4" stroke-linecap="round" stroke-dasharray="8 6" />
      
      <!-- Node 1: Register -->
      <g transform="translate(100, 50)">
        <circle cx="0" cy="0" r="22" fill="#4f46e5" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="15" fill="#4f46e5" />
        <!-- Head -->
        <circle cx="0" cy="-3.5" r="4" fill="#ffffff" />
        <!-- Shoulders -->
        <path d="M -6.5,5.5 C -6.5,2.5 -3.5,2.2 0,2.2 C 3.5,2.2 6.5,2.5 6.5,5.5 Z" fill="#ffffff" />
        <text x="0" y="34" font-family="'Inter', sans-serif" font-size="11" font-weight="700" fill="#4f46e5" text-anchor="middle">1. Register</text>
      </g>
      
      <!-- Node 2: Learn -->
      <g transform="translate(300, 50)">
        <circle cx="0" cy="0" r="22" fill="#a855f7" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="15" fill="#a855f7" />
        <!-- Open Book -->
        <path d="M -6.5,-5 L 0,-1.5 L 6.5,-5 L 6.5,3.5 L 0,7 L -6.5,3.5 Z M 0,-1.5 L 0,7" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <text x="0" y="34" font-family="'Inter', sans-serif" font-size="11" font-weight="700" fill="#a855f7" text-anchor="middle">2. Learn Skills</text>
      </g>
      
      <!-- Node 3: Graduate -->
      <g transform="translate(500, 50)">
        <circle cx="0" cy="0" r="22" fill="#06b6d4" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="15" fill="#06b6d4" />
        <!-- Graduation Cap -->
        <path d="M -8.5,-2.5 L 0,-6.5 L 8.5,-2.5 L 0,1.5 Z" fill="#ffffff" />
        <path d="M -4.5,-0.5 L -4.5,3 C -4.5,4.5 4.5,4.5 4.5,3 L 4.5,-0.5" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" />
        <line x1="8.5" y1="-2.5" x2="8.5" y2="4.5" stroke="#ffffff" stroke-width="1.2" />
        <circle cx="8.5" cy="4.5" r="1.5" fill="#ffffff" />
        <text x="0" y="34" font-family="'Inter', sans-serif" font-size="11" font-weight="700" fill="#06b6d4" text-anchor="middle">3. Graduate</text>
      </g>
    </svg>
    <h1 class="page-title" style="font-size:1.75rem">Student Registration</h1>
    <p class="text-muted">Fill in your details to enroll in a course at Mirror Age Concepts</p>
  </div>

  <?php if ($error): ?>
    <div class="lms-alert lms-alert-danger mb-4">
      <i class="fa fa-exclamation-circle"></i> <?= e($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($isAffiliate): ?>
  <div class="lms-card mb-4" style="background:linear-gradient(135deg,#0d1b2a,#1a3a5c);color:#fff;border:2px solid #0d9488;">
    <div class="d-flex align-items-center gap-3">
      <div style="width:48px;height:48px;background:rgba(13,148,136,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="fa fa-graduation-cap text-info fs-4"></i>
      </div>
      <div>
        <div class="fw-bold fs-6 text-white"><i class="fa fa-link me-1 text-info"></i>Affiliate Student Registration</div>
        <div class="small text-white-50">You were referred by an affiliate partner. Your course and class level will be assigned based on your date of birth.</div>
      </div>
    </div>
    <div id="ageRangeBanner" class="mt-3 p-2 rounded" style="background:rgba(13,148,136,0.15);display:none;">
      <i class="fa fa-info-circle text-info me-1"></i>
      <span id="ageRangeText" class="small text-white"></span>
    </div>
  </div>
  <?php endif; ?>

  <form method="post" action="register_handler.php" enctype="multipart/form-data" autocomplete="off" id="regForm">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <?php if ($referral): ?>
      <input type="hidden" name="ref_token" value="<?= e($refToken) ?>">
      <input type="hidden" name="is_affiliate" value="1">
      <input type="hidden" name="affiliate_course_id" id="hAffiliateCourseId" value="<?= $affiliateCourseId ?>">
    <?php endif; ?>

    <!-- PERSONAL INFO -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-user me-2"></i>Personal Information</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">First Name <span style="color:var(--danger)">*</span></label>
          <input name="first_name" class="form-control" placeholder="John" required <?= $referral ? 'value="' . e($prefilledFirstName) . '"' : '' ?>>
        </div>
        <div class="col-md-4">
          <label class="form-label">Last Name <span style="color:var(--danger)">*</span></label>
          <input name="last_name" class="form-control" placeholder="Doe" required <?= $referral ? 'value="' . e($prefilledLastName) . '"' : '' ?>>
        </div>
        <div class="col-md-4">
          <label class="form-label">Other Names</label>
          <input name="other_names" class="form-control" placeholder="Middle name (optional)">
        </div>
        <div class="col-md-4">
          <label class="form-label">Date of Birth <span style="color:var(--danger)">*</span></label>
          <input type="date" name="dob" class="form-control" required <?= $referral ? 'value="' . e($referral['pupil_dob']) . '"' : '' ?>>
        </div>
        <div class="col-md-8 d-none" id="affiliateClassSelectorContainer">
          <label class="form-label d-block fw-bold text-info">Class Level Placement <span style="color:var(--danger)">*</span></label>
          <div class="d-flex flex-wrap gap-3 mt-2" id="affiliateClassOptions">
            <!-- Dynamically populated checkboxes -->
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Gender <span style="color:var(--danger)">*</span></label>
          <select name="gender" class="form-select" required>
            <option value="">Select gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Phone Number <span style="color:var(--danger)">*</span></label>
          <input type="tel" name="phone" class="form-control" placeholder="+234 800 000 0000" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address <span style="color:var(--danger)">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required <?= $referral ? 'value="' . e($referral['pupil_email']) . '" readonly' : '' ?>>
        </div>
        <div class="col-md-6">
          <label class="form-label">Residential Address <span style="color:var(--danger)">*</span></label>
          <input name="address" class="form-control" placeholder="Street, City" required>
        </div>
      </div>
    </div>

    <!-- LOCATION -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-map-marker-alt me-2"></i>Location</div>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Nationality <span style="color:var(--danger)">*</span></label>
          <select name="nationality_iso2" class="form-select" required>
            <option value="">Select</option>
            <?php foreach ($countries as $ct): ?>
              <option value="<?= e($ct['iso2']) ?>"><?= e($ct['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Country of Residence <span style="color:var(--danger)">*</span></label>
          <select name="residence_iso2" id="residenceSelect" class="form-select" required>
            <option value="">Select</option>
            <?php foreach ($countries as $ct): ?>
              <option value="<?= e($ct['iso2']) ?>" <?= $ct['iso2'] === 'NG' ? 'selected' : '' ?>>
                <?= e($ct['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">State <span style="color:var(--danger)">*</span></label>
          <select id="stateSelect" name="state_id" class="form-select" required>
            <option value="">Select State</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">LGA <span style="color:var(--danger)">*</span></label>
          <select id="lgaSelect" name="lga_id" class="form-select" required>
            <option value="">Select LGA</option>
          </select>
        </div>
      </div>
    </div>

    <!-- COURSE -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-book me-2"></i>Course Selection</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Select Course <span style="color:var(--danger)">*</span></label>
          <?php if ($isAffiliate && !empty($affiliateCourses)): ?>
            <!-- Affiliate path: show affiliate courses, locked to matched course -->
            <select name="affiliate_course_id_select" id="courseSelect" class="form-select" <?= $affiliateCourseId > 0 ? 'disabled' : '' ?>>
              <?php foreach ($affiliateCourses as $ac): ?>
                <option value="<?= (int)$ac['id'] ?>" data-price="<?= e((string)$ac['price']) ?>" <?= (int)$ac['id'] === $affiliateCourseId ? 'selected' : '' ?>>
                  <?= e($ac['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="small text-muted mt-1"><i class="fa fa-info-circle"></i> Affiliate curriculum course — assigned by partner referral.</div>
            <!-- course_id is still needed for normal enrollment fallback (Higher track) -->
            <input type="hidden" name="course_id" value="<?= (int)$referral['course_id'] ?>">
          <?php elseif ($referral): ?>
            <select name="course_id_disabled" id="courseSelect" class="form-select" disabled>
              <?php foreach ($courses as $c): ?>
                <option value="<?= (int)$c['id'] ?>" data-price="<?= e((string)$c['price']) ?>" <?= (int)$c['id'] === $selectedCourseId ? 'selected' : '' ?>>
                  <?= e($c['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="course_id" value="<?= (int)$referral['course_id'] ?>">
          <?php else: ?>
            <select name="course_id" id="courseSelect" class="form-select" required>
              <option value="">Choose a course</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= (int)$c['id'] ?>" data-price="<?= e((string)$c['price']) ?>" <?= (int)$c['id'] === $selectedCourseId ? 'selected' : '' ?>>
                  <?= e($c['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </div>
        <div class="col-md-3">
          <label class="form-label">Course Fee</label>
          <input id="coursePrice" class="form-control" placeholder="Auto-filled" readonly style="background:var(--surface)">
        </div>
        <div class="col-md-3">
          <label class="form-label">Payment Option <span style="color:var(--danger)">*</span></label>
          <select name="payment_option" class="form-select" required>
            <option value="">Select</option>
            <option value="full">Full Payment</option>
            <option value="installment">Installment Plan</option>
          </select>
        </div>
      </div>
    </div>

    <?php if (!$isAffiliate): ?>
    <!-- KYC -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-id-card me-2"></i>Identity Verification (KYC)</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">ID Type <span style="color:var(--danger)">*</span></label>
          <select name="kyc_type" class="form-select" required>
            <option value="">Select ID type</option>
            <option value="NIN">NIN</option>
            <option value="International Passport">International Passport</option>
            <option value="Voter Card">Voter Card</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">ID Number <span style="color:var(--danger)">*</span></label>
          <input name="kyc_number" class="form-control" placeholder="Enter ID number" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Passport Photo <span style="color:var(--danger)">*</span></label>
          <input type="file" name="passport" class="form-control" accept="image/*" required>
          <div style="font-size:.75rem;color:var(--muted);margin-top:.25rem">JPG/PNG, max 3MB</div>
        </div>
        <div class="col-md-2">
          <label class="form-label">Signature <span style="color:var(--danger)">*</span></label>
          <input type="file" name="signature" class="form-control" accept="image/*" required>
          <div style="font-size:.75rem;color:var(--muted);margin-top:.25rem">JPG/PNG, max 3MB</div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- PASSWORD -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-lock me-2"></i>Account Security</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Password <span style="color:var(--danger)">*</span></label>
          <div class="position-relative">
            <input type="password" name="password" id="regPwd" class="form-control" placeholder="Min 8 chars" required>
            <button type="button" onclick="togglePwd('regPwd',this)"
              style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:0">
              <i class="fa fa-eye"></i>
            </button>
          </div>
          <div class="pwd-bar" id="pwdBar"></div>
          <div class="pwd-label text-muted" id="pwdLabel">Min 8 chars, 1 uppercase, 1 number</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password <span style="color:var(--danger)">*</span></label>
          <div class="position-relative">
            <input type="password" name="confirm_password" id="regPwdC" class="form-control" placeholder="Repeat password" required>
            <button type="button" onclick="togglePwd('regPwdC',this)"
              style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:0">
              <i class="fa fa-eye"></i>
            </button>
          </div>
          <div class="pwd-label" id="matchLabel" style="font-size:.75rem;margin-top:.25rem"></div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <p style="font-size:.85rem;color:var(--muted);margin:0">
        Already have an account? <a href="login.php" style="font-weight:600">Login here</a>
      </p>
      <button type="submit" class="btn-brand" style="padding:.75rem 2.5rem;font-size:1rem">
        <i class="fa fa-user-plus"></i> Submit Registration
      </button>
    </div>

  </form>
</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}/* Course price and select controls */
const courseSelect = document.getElementById('courseSelect');
const coursePrice = document.getElementById('coursePrice');

function updateCoursePrice() {
  if (!courseSelect) return;
  let p = courseSelect.options[courseSelect.selectedIndex]?.dataset?.price || '';
  
  if (isAffiliateReg) {
    const clField = document.getElementById('hClassLevel');
    const level = clField ? clField.value : '';
    if (level.startsWith('JSS') || level.startsWith('SSS')) {
      if (p !== '') {
        p = Math.min(Number(p), 5000.0);
      }
    }
  }

  coursePrice.value = p
    ? '₦' + Number(p).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})
    : '';
}

/* ── Affiliate Age Routing ── */
const dobInput = document.querySelector('input[name="dob"]');
const ageRangeBanner = document.getElementById('ageRangeBanner');
const ageRangeText = document.getElementById('ageRangeText');
const isAffiliateReg = <?= $isAffiliate ? 'true' : 'false' ?>;

function computeAgeRange() {
  if (!dobInput || !isAffiliateReg) return;
  const dob = new Date(dobInput.value);
  if (isNaN(dob.getTime())) { 
    if (ageRangeBanner) ageRangeBanner.style.display='none'; 
    const selectorContainer = document.getElementById('affiliateClassSelectorContainer');
    if (selectorContainer) selectorContainer.classList.add('d-none');
    return; 
  }
  const today = new Date();
  let age = today.getFullYear() - dob.getFullYear();
  const m = today.getMonth() - dob.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;

  let rangeLabel = '', classLevel = '';
  const selectorContainer = document.getElementById('affiliateClassSelectorContainer');
  const optionsDiv = document.getElementById('affiliateClassOptions');

  if (age <= 11) {
    const defaultVal = age <= 8 ? 'JSS1' : age === 9 ? 'JSS2' : 'JSS3';
    rangeLabel = `Age ${age} → You fall in the JSS track (Junior Secondary School). Choose your class level below.`;
    classLevel = defaultVal;

    if (selectorContainer && optionsDiv) {
      selectorContainer.classList.remove('d-none');
      optionsDiv.innerHTML = `
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_jss1" value="JSS1" ${defaultVal === 'JSS1' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_jss1">JSS 1</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_jss2" value="JSS2" ${defaultVal === 'JSS2' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_jss2">JSS 2</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_jss3" value="JSS3" ${defaultVal === 'JSS3' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_jss3">JSS 3</label>
        </div>
      `;
    }
  } else if (age >= 12 && age <= 17) {
    const defaultVal = age <= 13 ? 'SSS1' : age <= 15 ? 'SSS2' : 'SSS3';
    rangeLabel = `Age ${age} → You fall in the SSS track (Senior Secondary School). Choose your class level below.`;
    classLevel = defaultVal;

    if (selectorContainer && optionsDiv) {
      selectorContainer.classList.remove('d-none');
      optionsDiv.innerHTML = `
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_sss1" value="SSS1" ${defaultVal === 'SSS1' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_sss1">SSS 1</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_sss2" value="SSS2" ${defaultVal === 'SSS2' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_sss2">SSS 2</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input affiliate-class-cb" type="checkbox" name="affiliate_class_level_cb" id="cb_sss3" value="SSS3" ${defaultVal === 'SSS3' ? 'checked' : ''}>
          <label class="form-check-label text-dark" for="cb_sss3">SSS 3</label>
        </div>
      `;
    }
  } else {
    rangeLabel = `Age ${age} → You will be enrolled in the Higher Institution track — full LMS course curriculum.`;
    classLevel = 'Higher';
    if (selectorContainer) selectorContainer.classList.add('d-none');
  }

  if (ageRangeBanner) { ageRangeBanner.style.display='block'; }
  if (ageRangeText) ageRangeText.textContent = rangeLabel;

  // We embed class_level as a separate hidden field
  let clField = document.getElementById('hClassLevel');
  if (!clField) {
    clField = document.createElement('input');
    clField.type = 'hidden';
    clField.name = 'computed_class_level';
    clField.id = 'hClassLevel';
    dobInput.closest('form').appendChild(clField);
  }
  clField.value = classLevel;

  // Enforce single checkbox selection on change
  if (optionsDiv) {
    const cbs = optionsDiv.querySelectorAll('.affiliate-class-cb');
    cbs.forEach(cb => {
      cb.addEventListener('change', function() {
        if (this.checked) {
          cbs.forEach(other => {
            if (other !== this) other.checked = false;
          });
          clField.value = this.value;
          updateCoursePrice();
        } else {
          // If trying to uncheck the only checked checkbox, force it to stay checked
          const checkedCount = optionsDiv.querySelectorAll('.affiliate-class-cb:checked').length;
          if (checkedCount === 0) {
            this.checked = true;
          }
        }
      });
    });
  }
  updateCoursePrice();
}

if (dobInput) {
  dobInput.addEventListener('change', computeAgeRange);
  dobInput.addEventListener('input', computeAgeRange);
  if (dobInput.value) computeAgeRange();
}

if (courseSelect) { courseSelect.addEventListener('change', updateCoursePrice); updateCoursePrice(); }

/* Password strength */
const pwd = document.getElementById('regPwd');
const pwdC = document.getElementById('regPwdC');
const bar = document.getElementById('pwdBar');
const lbl = document.getElementById('pwdLabel');
const matchLbl = document.getElementById('matchLabel');

pwd.addEventListener('input', () => {
  const v = pwd.value;
  bar.className = 'pwd-bar';
  if (!v) { lbl.textContent = 'Min 8 chars, 1 uppercase, 1 number'; lbl.style.color=''; return; }
  if (v.length < 6) { bar.classList.add('weak'); lbl.textContent = 'Too weak'; lbl.style.color='var(--danger)'; }
  else if (/[A-Z]/.test(v) && /\d/.test(v) && v.length >= 8) { bar.classList.add('strong'); lbl.textContent = 'Strong password ✓'; lbl.style.color='var(--success)'; }
  else { bar.classList.add('medium'); lbl.textContent = 'Medium — add uppercase & number'; lbl.style.color='var(--warning)'; }
});

pwdC.addEventListener('input', () => {
  if (!pwdC.value) { matchLbl.textContent = ''; return; }
  if (pwd.value === pwdC.value) { matchLbl.textContent = 'Passwords match ✓'; matchLbl.style.color='var(--success)'; }
  else { matchLbl.textContent = 'Passwords do not match'; matchLbl.style.color='var(--danger)'; }
});

document.getElementById('regForm').addEventListener('submit', e => {
  if (pwd.value !== pwdC.value) { e.preventDefault(); alert('Passwords do not match.'); }
});

/* Geo AJAX */
const residenceSelect = document.getElementById('residenceSelect');
const stateSelect = document.getElementById('stateSelect');
const lgaSelect = document.getElementById('lgaSelect');

function resetSelect(sel, label) { sel.innerHTML = `<option value="">${label}</option>`; }
async function fetchJson(url) {
  const r = await fetch(url, { headers: { Accept: 'application/json' } });
  return r.ok ? r.json() : null;
}
async function loadStates(iso2) {
  stateSelect.innerHTML = '<option value="">Loading...</option>';
  resetSelect(lgaSelect, 'Select LGA');
  const d = await fetchJson('ajax_states.php?country_iso2=' + encodeURIComponent(iso2));
  resetSelect(stateSelect, 'Select State');
  (d?.states || []).forEach(s => { const o = new Option(s.name, s.id); stateSelect.add(o); });
}
async function loadLgas(stateId) {
  lgaSelect.innerHTML = '<option value="">Loading...</option>';
  const d = await fetchJson('ajax_lgas.php?state_id=' + encodeURIComponent(stateId));
  resetSelect(lgaSelect, 'Select LGA');
  (d?.lgas || []).forEach(l => { const o = new Option(l.name, l.id); lgaSelect.add(o); });
}
residenceSelect.addEventListener('change', () => {
  const v = residenceSelect.value;
  if (!v) { resetSelect(stateSelect,'Select State'); resetSelect(lgaSelect,'Select LGA'); return; }
  loadStates(v);
});
stateSelect.addEventListener('change', () => {
  const v = stateSelect.value;
  if (!v) { resetSelect(lgaSelect,'Select LGA'); return; }
  loadLgas(v);
});
function initGeo() {
  if (residenceSelect.value) loadStates(residenceSelect.value);
}
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initGeo);
} else {
  initGeo();
}
</script>
</body>
</html>
