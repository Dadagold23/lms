<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user'])) redirect('dashboard.php');

$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
$year = date('Y');

$countries = $pdo->query("SELECT iso2, name FROM ref_countries ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$courses   = $pdo->query("SELECT id, title, price FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$selectedCourseId = (int)($_GET['course_id'] ?? 0);
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

  <form method="post" action="register_handler.php" enctype="multipart/form-data" autocomplete="off" id="regForm">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

    <!-- PERSONAL INFO -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-user me-2"></i>Personal Information</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">First Name <span style="color:var(--danger)">*</span></label>
          <input name="first_name" class="form-control" placeholder="John" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Last Name <span style="color:var(--danger)">*</span></label>
          <input name="last_name" class="form-control" placeholder="Doe" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Other Names</label>
          <input name="other_names" class="form-control" placeholder="Middle name (optional)">
        </div>
        <div class="col-md-4">
          <label class="form-label">Date of Birth <span style="color:var(--danger)">*</span></label>
          <input type="date" name="dob" class="form-control" required>
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
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
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
          <select name="course_id" id="courseSelect" class="form-select" required>
            <option value="">Choose a course</option>
            <?php foreach ($courses as $c): ?>
              <option value="<?= (int)$c['id'] ?>" data-price="<?= e((string)$c['price']) ?>" <?= (int)$c['id'] === $selectedCourseId ? 'selected' : '' ?>>
                <?= e($c['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
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
}

/* Course price */
const courseSelect = document.getElementById('courseSelect');
const coursePrice = document.getElementById('coursePrice');

function updateCoursePrice() {
  const p = courseSelect.options[courseSelect.selectedIndex]?.dataset?.price || '';
  coursePrice.value = p
    ? '\u20A6' + Number(p).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})
    : '';
}

courseSelect.addEventListener('change', updateCoursePrice);
updateCoursePrice();

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
document.addEventListener('DOMContentLoaded', () => {
  if (residenceSelect.value) loadStates(residenceSelect.value);
});
</script>
</body>
</html>
