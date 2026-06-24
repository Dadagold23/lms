<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

$error = $_SESSION['instructor_register_error'] ?? null;
$ok    = $_SESSION['instructor_register_ok']    ?? null;
unset($_SESSION['instructor_register_error'], $_SESSION['instructor_register_ok']);

/* Load courses for specialization selection */
$courses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active=1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

/* Also needed by public_nav */
$publicNavCourses = $courses;

$year = date('Y');
$footerYear = $year;
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Registration';
$seoDesc    = 'Register as an instructor at Grafix@Mirror LMS — Mirror Age Concepts. Share your expertise and teach professional technology courses.';
$seoNoIndex = false;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="description" content="Register as an instructor at Grafix@Mirror LMS. Share your expertise and teach courses.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css?v=20260624-auth-fix" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<?php require __DIR__ . '/includes/public_nav.php'; ?>

<div class="container py-4" style="max-width:760px">

  <div class="text-center mb-4">
    <!-- Instructor pathway SVG -->
    <svg class="svg-float mb-3" width="100%" height="80" viewBox="0 0 500 80" xmlns="http://www.w3.org/2000/svg" style="max-width: 420px; margin: 0 auto; display: block;">
      <defs>
        <linearGradient id="insLineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#0284c7" />
          <stop offset="100%" stop-color="#10b981" />
        </linearGradient>
      </defs>
      <path d="M 100 40 L 400 40" fill="none" stroke="url(#insLineGrad)" stroke-width="3" stroke-dasharray="6 4" />
      
      <!-- Node 1: Profile -->
      <g transform="translate(100, 40)">
        <circle cx="0" cy="0" r="20" fill="#0284c7" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="14" fill="#0284c7" />
        <!-- Resume / Document -->
        <path d="M -4,-5 L 1,-5 L 4,-2 L 4,5 L -4,5 Z" fill="none" stroke="#ffffff" stroke-width="1.5" />
        <line x1="-2" y1="-1" x2="2" y2="-1" stroke="#ffffff" stroke-width="1" />
        <line x1="-2" y1="1" x2="2" y2="1" stroke="#ffffff" stroke-width="1" />
        <text x="0" y="30" font-family="'Inter', sans-serif" font-size="10" font-weight="700" fill="#0284c7" text-anchor="middle">1. Profile</text>
      </g>
      
      <!-- Node 2: Specialties -->
      <g transform="translate(250, 40)">
        <circle cx="0" cy="0" r="20" fill="#0ea5e9" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="14" fill="#0ea5e9" />
        <!-- Gears/Specialty -->
        <circle cx="0" cy="0" r="4.5" fill="none" stroke="#ffffff" stroke-width="1.8" />
        <path d="M 0,-6 L 0,-4.5 M 0,4.5 L 0,6 M -6,0 L -4.5,0 M 4.5,0 L 6,0 M -4.5,-4.5 L -3,-3 M 3,3 L 4.5,4.5 M -4.5,4.5 L -3,3 M 3,-3 L 4.5,-4.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" />
        <text x="0" y="30" font-family="'Inter', sans-serif" font-size="10" font-weight="700" fill="#0ea5e9" text-anchor="middle">2. Specialties</text>
      </g>
      
      <!-- Node 3: Approval -->
      <g transform="translate(400, 40)">
        <circle cx="0" cy="0" r="20" fill="#10b981" fill-opacity="0.12" />
        <circle cx="0" cy="0" r="14" fill="#10b981" />
        <!-- Badge/Verified Shield -->
        <path d="M -5,-4 L 0,-6.5 L 5,-4 L 5,1 C 5,3.5 0,6 0,6 C 0,6 -5,3.5 -5,1 Z" fill="none" stroke="#ffffff" stroke-width="1.5" />
        <text x="0" y="30" font-family="'Inter', sans-serif" font-size="10" font-weight="700" fill="#10b981" text-anchor="middle">3. Approval</text>
      </g>
    </svg>
    <h2 class="page-title">Instructor Registration</h2>
    <p class="text-muted">Join our team of educators. Fill in your professional details below.</p>
  </div>

  <?php if ($error): ?>
    <div class="lms-alert lms-alert-danger mb-4"><i class="fa fa-exclamation-circle me-1"></i><?= e($error) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="lms-alert lms-alert-success mb-4"><i class="fa fa-check-circle me-1"></i><?= e($ok) ?></div>
  <?php endif; ?>

  <form method="post" action="instructor_register_handler.php" enctype="multipart/form-data">
    <?= csrfField() ?>

    <!-- Personal Information -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-user me-2"></i>Personal Information</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name <span style="color:var(--danger)">*</span></label>
          <input name="full_name" class="form-control" placeholder="e.g. Bankole Adeyemi" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address <span style="color:var(--danger)">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="instructor@example.com" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number <span style="color:var(--danger)">*</span></label>
          <input type="tel" name="phone" class="form-control" placeholder="+234 801 234 5678" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="">— Select —</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Profile Photo</label>
          <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png">
          <div class="form-text">JPG or PNG, max 2MB. Professional headshot recommended.</div>
        </div>
      </div>
    </div>

    <!-- Professional Background -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-briefcase me-2"></i>Professional Background</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Highest Qualification <span style="color:var(--danger)">*</span></label>
          <select name="qualification" class="form-select" required>
            <option value="">— Select —</option>
            <option value="SSCE/WAEC">SSCE / WAEC</option>
            <option value="OND">OND</option>
            <option value="HND">HND</option>
            <option value="B.Sc / B.Tech / B.Eng">B.Sc / B.Tech / B.Eng</option>
            <option value="PGD">Postgraduate Diploma</option>
            <option value="M.Sc / MBA / M.Tech">M.Sc / MBA / M.Tech</option>
            <option value="Ph.D">Ph.D</option>
            <option value="Professional Certification">Professional Certification</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Years of Experience <span style="color:var(--danger)">*</span></label>
          <select name="experience_years" class="form-select" required>
            <option value="">— Select —</option>
            <option value="1">Less than 1 year</option>
            <option value="2">1–2 years</option>
            <option value="3">3–5 years</option>
            <option value="7">6–10 years</option>
            <option value="11">10+ years</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Field of Specialization <span style="color:var(--danger)">*</span></label>
          <input name="specialization" class="form-control"
                 placeholder="e.g. Graphic Design, Web Development, Cybersecurity, Data Analysis"
                 required>
          <div class="form-text">Your primary area of expertise. Be specific.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Professional Bio <span style="color:var(--danger)">*</span></label>
          <textarea name="bio" class="form-control" rows="4" required
                    placeholder="Describe your professional background, achievements, teaching experience, and what makes you a great instructor. Minimum 100 characters."></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">LinkedIn Profile URL</label>
          <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/yourname">
        </div>
      </div>
    </div>

    <!-- Course Assignment Preference -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-book me-2"></i>Course Teaching Preference</div>
      <p class="text-muted" style="font-size:.88rem">Select the courses you are qualified and willing to teach. Admin will review and assign you.</p>
      <div class="row g-2">
        <?php foreach ($courses as $c): ?>
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox"
                     name="course_ids[]" value="<?= (int)$c['id'] ?>"
                     id="course_<?= (int)$c['id'] ?>">
              <label class="form-check-label" for="course_<?= (int)$c['id'] ?>" style="font-size:.88rem">
                <?= e($c['title']) ?>
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Account Security -->
    <div class="lms-card mb-4">
      <div class="form-section-title"><i class="fa fa-lock me-2"></i>Account Security</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Password <span style="color:var(--danger)">*</span></label>
          <div class="position-relative">
            <input type="password" name="password" id="insPwd" class="form-control" placeholder="Min 8 chars" required>
            <button type="button" onclick="togglePwd('insPwd',this)"
              style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:0">
              <i class="fa fa-eye"></i>
            </button>
          </div>
          <div class="form-text">Min 8 chars, 1 uppercase, 1 number</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password <span style="color:var(--danger)">*</span></label>
          <input type="password" name="confirm_password" id="insPwdC" class="form-control" placeholder="Repeat password" required>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <p style="font-size:.85rem;color:var(--muted);margin:0">
        Already have an account? <a href="instructor_login.php" style="font-weight:600">Login here</a>
      </p>
      <button type="submit" class="btn-brand" style="padding:.75rem 2.5rem;font-size:1rem">
        <i class="fa fa-user-plus me-1"></i> Submit Registration
      </button>
    </div>
  </form>

</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>

<script>
function togglePwd(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}
document.querySelector('form').addEventListener('submit', function(e) {
  const p = document.getElementById('insPwd').value;
  const c = document.getElementById('insPwdC').value;
  if (p !== c) { e.preventDefault(); alert('Passwords do not match.'); }
});
</script>
</body>
</html>
