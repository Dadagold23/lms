<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$userId = (int)($_SESSION['user']['id'] ?? 0);
if ($userId <= 0) redirect('login.php');

/* =====================
   HANDLE PROFILE UPDATE
===================== */
$success = $_SESSION['profile_ok'] ?? null;
$error   = $_SESSION['profile_err'] ?? null;
unset($_SESSION['profile_ok'], $_SESSION['profile_err']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_action']) && $_POST['_action'] === 'update_profile') {
    verifyCsrf($_POST['_csrf'] ?? '');

    $phone       = trim(strip_tags($_POST['phone']        ?? ''));
    $dob         = trim($_POST['dob']                     ?? '');
    $gender      = trim($_POST['gender']                  ?? '');
    $address     = trim(strip_tags($_POST['address']      ?? ''));
    $nationality = trim(strip_tags($_POST['nationality']  ?? ''));
    $country     = trim(strip_tags($_POST['country']      ?? ''));
    $state       = trim(strip_tags($_POST['state_of_origin'] ?? ''));
    $lga         = trim(strip_tags($_POST['lga']          ?? ''));
    $otherNames  = trim(strip_tags($_POST['other_names']  ?? ''));

    // Validate
    if ($phone !== '' && strlen($phone) < 7) {
        $_SESSION['profile_err'] = 'Invalid phone number.';
        redirect('profile.php');
    }
    if ($dob !== '' && (strtotime($dob) === false || strtotime($dob) > time())) {
        $_SESSION['profile_err'] = 'Invalid date of birth.';
        redirect('profile.php');
    }
    if ($gender !== '' && !in_array($gender, ['Male', 'Female'], true)) {
        $_SESSION['profile_err'] = 'Invalid gender.';
        redirect('profile.php');
    }

    // Handle passport upload
    $passportFile = null;
    if (!empty($_FILES['passport']['name']) && $_FILES['passport']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $_SESSION['profile_err'] = 'Passport must be JPG or PNG.';
            redirect('profile.php');
        }
        if ($_FILES['passport']['size'] > 3 * 1024 * 1024) {
            $_SESSION['profile_err'] = 'Passport image too large (max 3MB).';
            redirect('profile.php');
        }
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $passportFile = uniqid('passport_', true) . '.' . $ext;
        if (!move_uploaded_file($_FILES['passport']['tmp_name'], $uploadDir . $passportFile)) {
            $_SESSION['profile_err'] = 'Upload failed. Try again.';
            redirect('profile.php');
        }
    }

    // Build dynamic UPDATE — always update all text fields (empty string clears them)
    $sets   = [
        'phone = ?', 'dob = ?', 'gender = ?', 'address = ?',
        'nationality = ?', 'country = ?', 'state_of_origin = ?', 'lga = ?', 'other_names = ?',
    ];
    $params = [
        $phone ?: null,
        $dob   ?: null,
        $gender ?: null,
        $address ?: null,
        $nationality ?: null,
        $country ?: null,
        $state ?: null,
        $lga ?: null,
        $otherNames ?: null,
    ];

    if ($passportFile) {
        $sets[]   = 'passport = ?';
        $params[] = $passportFile;
    }

    $params[] = $userId;
    $pdo->prepare("UPDATE lms_students SET " . implode(', ', $sets) . " WHERE id = ?")
        ->execute($params);

    $_SESSION['profile_ok'] = 'Profile updated successfully.';
    redirect('profile.php');
}

/* =====================
   FETCH STUDENT
===================== */
$student = $pdo->prepare("
    SELECT id, first_name, last_name, other_names, email, phone, dob, gender,
           nationality, country, state_of_origin, lga, address,
           passport, signature, course, course_price, payment_option,
           kyc_type, kyc_number, status, role, created_at, updated_at
    FROM lms_students WHERE id = ? LIMIT 1
");
$student->execute([$userId]);
$s = $student->fetch(PDO::FETCH_ASSOC);

if (!$s) exit('Student not found.');

$fullName = trim(($s['first_name'] ?? '') . ' ' . ($s['other_names'] ? $s['other_names'] . ' ' : '') . ($s['last_name'] ?? ''));

/* =====================
   FETCH ENROLLMENTS + PAYMENT SUMMARY
===================== */
$enStmt = $pdo->prepare("
    SELECT
        e.id,
        e.course_id,
        e.paid_amount,
        e.payment_type,
        e.status,
        e.next_due_date,
        c.title,
        c.price
    FROM lms_enrollments e
    JOIN lms_courses c ON c.id = e.course_id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$enStmt->execute([$userId]);
$enrollments = $enStmt->fetchAll(PDO::FETCH_ASSOC);

/* =====================
   FETCH PAYMENT HISTORY
===================== */
$payStmt = $pdo->prepare("
    SELECT p.amount, p.channel, p.status, p.paid_at, c.title AS course_title
    FROM lms_payments p
    LEFT JOIN lms_enrollments e ON e.id = p.enrollment_id
    LEFT JOIN lms_courses c ON c.id = e.course_id
    WHERE p.student_id = ? AND p.status = 'success'
    ORDER BY p.paid_at DESC
    LIMIT 20
");
$payStmt->execute([$userId]);
$payments = $payStmt->fetchAll(PDO::FETCH_ASSOC);

/* =====================
   LOAD GEO SELECTION STATE
===================== */

// Resolve current student's stored names → IDs/ISO2 for pre-selecting dropdowns
$currentCountryIso2 = '';
$currentNatIso2     = '';
$currentStateId     = 0;
$currentLgaName     = (string)($s['lga'] ?? '');
$currentStates      = [];

if (!empty($s['country'])) {
    $r = $pdo->prepare("SELECT iso2 FROM ref_countries WHERE name = ? LIMIT 1");
    $r->execute([$s['country']]);
    $currentCountryIso2 = (string)($r->fetchColumn() ?: '');
}
if (!empty($s['nationality'])) {
    $r = $pdo->prepare("SELECT iso2 FROM ref_countries WHERE name = ? LIMIT 1");
    $r->execute([$s['nationality']]);
    $currentNatIso2 = (string)($r->fetchColumn() ?: '');
}
if ($currentCountryIso2 !== '') {
    $r = $pdo->prepare("SELECT id, name FROM ref_states WHERE country_iso2 = ? ORDER BY name");
    $r->execute([$currentCountryIso2]);
    $currentStates = $r->fetchAll(PDO::FETCH_ASSOC);
    foreach ($currentStates as $st) {
        if (strcasecmp($st['name'], (string)($s['state_of_origin'] ?? '')) === 0) {
            $currentStateId = (int)$st['id'];
            break;
        }
    }
}
if ($currentStateId > 0) {
    $r = $pdo->prepare("SELECT id, name FROM ref_lgas WHERE state_id = ? ORDER BY name");
    $r->execute([$currentStateId]);
}
$year = date('Y');
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'My Profile';
$seoDesc    = 'Manage your student profile, bio-data and payment information at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="brand text-decoration-none">
      <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn-ghost">Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:900px">

  <h4 class="page-title mb-4"><i class="fa fa-user-circle me-2"></i>My Profile</h4>

  <?php if ($success): ?>
    <div class="lms-alert lms-alert-success mb-4"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="lms-alert lms-alert-danger mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <!-- ===== BIO (read-only) ===== -->
  <div class="lms-card mb-4">
    <div class="form-section-title"><i class="fa fa-id-card me-2"></i>Personal Information</div>
    <div class="row g-4">

      <!-- Avatar + quick info -->
      <div class="col-md-3 text-center">
        <?php if (!empty($s['passport'])): ?>
          <img src="uploads/<?= e($s['passport']) ?>" alt="Passport"
               class="rounded-circle border" style="width:110px;height:110px;object-fit:cover">
        <?php else: ?>
          <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
               style="width:110px;height:110px;font-size:2.8rem;color:#fff">
            <i class="fa fa-user"></i>
          </div>
        <?php endif; ?>
        <div class="fw-bold mt-2" style="font-size:.95rem"><?= e($fullName) ?></div>
        <div class="text-muted small"><?= e($s['email'] ?? '') ?></div>
        <div class="mt-1">
          <?php
          $statusColor = match($s['status'] ?? '') {
            'active'    => 'success',
            'inactive'  => 'secondary',
            'suspended' => 'danger',
            default     => 'secondary',
          };
          ?>
          <span class="badge bg-<?= $statusColor ?>"><?= e(ucfirst($s['status'] ?? 'active')) ?></span>
          <span class="badge bg-info text-dark ms-1"><?= e(ucfirst($s['role'] ?? 'student')) ?></span>
        </div>
        <div class="text-muted mt-2" style="font-size:.75rem">
          Registered: <?= e(date('d M Y', strtotime($s['created_at'] ?? 'now'))) ?>
        </div>
        <?php if (!empty($s['updated_at'])): ?>
          <div class="text-muted" style="font-size:.75rem">
            Updated: <?= e(date('d M Y', strtotime($s['updated_at']))) ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- All bio fields -->
      <div class="col-md-9">
        <div class="row g-3">

          <?php
          $bioFields = [
            ['First Name',      $s['first_name'] ?? ''],
            ['Last Name',       $s['last_name'] ?? ''],
            ['Other Names',     $s['other_names'] ?? ''],
            ['Email',           $s['email'] ?? ''],
            ['Phone',           $s['phone'] ?? ''],
            ['Date of Birth',   $s['dob'] ? date('d M Y', strtotime($s['dob'])) : ''],
            ['Gender',          $s['gender'] ?? ''],
            ['Nationality',     $s['nationality'] ?? ''],
            ['Country',         $s['country'] ?? ''],
            ['State of Origin', $s['state_of_origin'] ?? ''],
            ['LGA',             $s['lga'] ?? ''],
            ['Address',         $s['address'] ?? ''],
          ];
          foreach ($bioFields as [$label, $val]): ?>
          <div class="col-sm-6 col-lg-4">
            <div class="small text-muted mb-1"><?= e($label) ?></div>
            <div class="fw-semibold" style="font-size:.9rem;word-break:break-word"><?= e($val ?: '—') ?></div>
          </div>
          <?php endforeach; ?>

        </div>

        <!-- KYC -->
        <hr class="my-3">
        <div class="row g-3">
          <div class="col-12"><div class="small fw-bold text-muted text-uppercase" style="letter-spacing:.05em">Identity Verification (KYC)</div></div>
          <div class="col-sm-6">
            <div class="small text-muted mb-1">KYC Type</div>
            <div class="fw-semibold" style="font-size:.9rem"><?= e($s['kyc_type'] ?: '—') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="small text-muted mb-1">KYC Number</div>
            <div class="fw-semibold" style="font-size:.9rem"><?= e($s['kyc_number'] ?: '—') ?></div>
          </div>
        </div>

        <!-- Course registration details -->
        <hr class="my-3">
        <div class="row g-3">
          <div class="col-12"><div class="small fw-bold text-muted text-uppercase" style="letter-spacing:.05em">Registration Details</div></div>
          <div class="col-sm-4">
            <div class="small text-muted mb-1">Registered Course</div>
            <div class="fw-semibold" style="font-size:.9rem"><?= e($s['course'] ?: '—') ?></div>
          </div>
          <div class="col-sm-4">
            <div class="small text-muted mb-1">Course Fee</div>
            <div class="fw-semibold" style="font-size:.9rem"><?= $s['course_price'] ? '₦' . number_format((float)$s['course_price'], 2) : '—' ?></div>
          </div>
          <div class="col-sm-4">
            <div class="small text-muted mb-1">Payment Plan</div>
            <div class="fw-semibold" style="font-size:.9rem"><?= $s['payment_option'] ? ucfirst($s['payment_option']) : '—' ?></div>
          </div>
        </div>

        <!-- Signature -->
        <?php if (!empty($s['signature'])): ?>
        <hr class="my-3">
        <div class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:.05em">Signature</div>
        <img src="uploads/<?= e($s['signature']) ?>" alt="Signature"
             style="max-height:60px;max-width:220px;border:1px solid var(--border);border-radius:6px;padding:4px;background:#fff">
        <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- ===== EDIT BIO ===== -->
  <div class="lms-card mb-4">
    <div class="form-section-title"><i class="fa fa-edit me-2"></i>Update Profile</div>
    <form method="post" enctype="multipart/form-data" id="profileForm">
      <?= csrfField() ?>
      <input type="hidden" name="_action" value="update_profile">
      <div class="row g-3">

        <div class="col-md-4">
          <label class="form-label">Other Names</label>
          <input type="text" name="other_names" class="form-control" value="<?= e($s['other_names'] ?? '') ?>" placeholder="Middle name">
        </div>
        <div class="col-md-4">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" value="<?= e($s['phone'] ?? '') ?>" placeholder="+234...">
        </div>
        <div class="col-md-4">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control" value="<?= e($s['dob'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="">— Select —</option>
            <option value="Male"   <?= ($s['gender'] ?? '') === 'Male'   ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($s['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>

        <!-- Nationality -->
        <div class="col-md-4">
          <label class="form-label">Nationality</label>
          <select name="nationality" id="natSelect" class="form-select" data-selected-iso2="<?= e($currentNatIso2) ?>">
            <option value="">— Select —</option>
            <?php /* populated via AJAX */ ?>
          </select>
        </div>

        <!-- Country of Residence -->
        <div class="col-md-4">
          <label class="form-label">Country of Residence</label>
          <select name="country" id="countrySelect" class="form-select" data-selected-iso2="<?= e($currentCountryIso2) ?>">
            <option value="">— Select —</option>
            <?php /* populated via AJAX */ ?>
          </select>
        </div>

        <!-- State of Origin -->
        <div class="col-md-4">
          <label class="form-label">State of Origin</label>
          <select name="state_of_origin" id="stateSelect" class="form-select" data-selected-id="<?= (int)$currentStateId ?>">
            <option value="">— Select State —</option>
            <?php /* populated via AJAX */ ?>
          </select>
        </div>

        <!-- LGA -->
        <div class="col-md-4">
          <label class="form-label">LGA</label>
          <select name="lga" id="lgaSelect" class="form-select" data-selected-name="<?= e($currentLgaName) ?>">
            <option value="">— Select LGA —</option>
            <?php /* populated via AJAX */ ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" value="<?= e($s['address'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Update Passport Photo</label>
          <input type="file" name="passport" class="form-control" accept="image/jpeg,image/png">
          <div class="form-text">JPG/PNG, max 3MB. Leave blank to keep current.</div>
        </div>

      </div>
      <div class="mt-3">
        <button type="submit" class="btn-brand"><i class="fa fa-save me-1"></i> Save Changes</button>
      </div>
    </form>
  </div>

  <!-- ===== ENROLLMENT & PAYMENT STATUS ===== -->
  <?php if (!empty($enrollments)): ?>
  <div class="lms-card mb-4">
    <div class="form-section-title"><i class="fa fa-book me-2"></i>Course Enrollment & Payment</div>
    <?php foreach ($enrollments as $en):
      $price   = (float)$en['price'];
      $paid    = (float)$en['paid_amount'];
      $balance = max(0, $price - $paid);
      $pct     = $price > 0 ? min(100, round($paid / $price * 100)) : 0;
      $ptype   = (string)($en['payment_type'] ?? 'full');
      $status  = (string)$en['status'];
      $dueDate = $en['next_due_date'] ?? null;

      $statusBadge = match($status) {
        'paid'        => '<span class="badge bg-success">Fully Paid</span>',
        'installment' => '<span class="badge bg-warning text-dark">Installment</span>',
        'active'      => '<span class="badge bg-info text-dark">Active</span>',
        'expired'     => '<span class="badge bg-danger">Expired</span>',
        'cancelled'   => '<span class="badge bg-secondary">Cancelled</span>',
        default       => '<span class="badge bg-secondary">' . e($status) . '</span>',
      };
    ?>
    <div class="border rounded p-3 mb-3">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div>
          <div class="fw-semibold"><?= e($en['title']) ?></div>
          <div class="text-muted small">
            Payment plan: <strong><?= $ptype === 'installment' ? 'Installment (2 payments)' : 'Full Payment' ?></strong>
          </div>
        </div>
        <?= $statusBadge ?>
      </div>

      <!-- Progress bar -->
      <div class="progress mb-2" style="height:8px">
        <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
      </div>
      <div class="d-flex justify-content-between small text-muted mb-2">
        <span>Paid: ₦<?= number_format($paid, 2) ?></span>
        <span>Total: ₦<?= number_format($price, 2) ?></span>
      </div>

      <?php if ($balance > 0): ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="small">
            Balance: <strong class="text-danger">₦<?= number_format($balance, 2) ?></strong>
            <?php if ($dueDate): ?>
              — due <strong><?= e(date('d M Y', strtotime($dueDate))) ?></strong>
            <?php endif; ?>
          </div>
          <a href="pay.php?enrollment_id=<?= (int)$en['id'] ?>" class="btn btn-sm btn-warning">
            <i class="fa fa-credit-card me-1"></i>
            <?= $ptype === 'installment' && $paid <= 0 ? 'Pay 1st Installment' : 'Pay Balance' ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- ===== PAYMENT HISTORY ===== -->
  <?php if (!empty($payments)): ?>
  <div class="lms-card mb-4">
    <div class="form-section-title"><i class="fa fa-history me-2"></i>Payment History</div>
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Course</th>
            <th>Amount</th>
            <th>Channel</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e($p['course_title'] ?? '—') ?></td>
            <td>₦<?= number_format((float)$p['amount'], 2) ?></td>
            <td><span class="badge bg-secondary"><?= e(ucfirst($p['channel'] ?? '')) ?></span></td>
            <td><?= e($p['paid_at'] ? date('d M Y H:i', strtotime($p['paid_at'])) : '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>

<footer style="text-align:center;padding:1.5rem;font-size:.8rem;color:var(--muted);border-top:1px solid var(--border);margin-top:2rem">
  © <?= e((string)$year) ?> Mirror Age Concepts · Grafix@Mirror LMS
</footer>

<script>
const natSelect = document.getElementById('natSelect');
const countrySelect = document.getElementById('countrySelect');
const stateSelect = document.getElementById('stateSelect');
const lgaSelect = document.getElementById('lgaSelect');

function resetSelect(select, label) {
  select.innerHTML = `<option value="">${label}</option>`;
}

async function fetchJson(url) {
  const response = await fetch(url, { headers: { Accept: 'application/json' } });
  return response.ok ? response.json() : null;
}

async function loadCountries(select, selectedIso2, label) {
  select.innerHTML = '<option value="">Loading...</option>';
  const data = await fetchJson('ajax_countries.php');
  resetSelect(select, label);

  (data?.countries || []).forEach((country) => {
    const option = new Option(country.name, country.name);
    option.dataset.iso2 = country.iso2;
    if (country.iso2 === selectedIso2) {
      option.selected = true;
    }
    select.add(option);
  });
}

async function loadStates(countryIso2, selectedStateId = '') {
  stateSelect.innerHTML = '<option value="">Loading...</option>';
  resetSelect(lgaSelect, 'Select LGA');

  const data = await fetchJson('ajax_states.php?country_iso2=' + encodeURIComponent(countryIso2));
  resetSelect(stateSelect, 'Select state');

  (data?.states || []).forEach((state) => {
    const option = new Option(state.name, state.name);
    option.dataset.id = state.id;
    if (String(state.id) === String(selectedStateId)) {
      option.selected = true;
    }
    stateSelect.add(option);
  });
}

async function loadLgas(stateId, selectedLgaName = '') {
  lgaSelect.innerHTML = '<option value="">Loading...</option>';
  const data = await fetchJson('ajax_lgas.php?state_id=' + encodeURIComponent(stateId));
  resetSelect(lgaSelect, 'Select LGA');

  (data?.lgas || []).forEach((lga) => {
    const option = new Option(lga.name, lga.name);
    if (lga.name.toLowerCase() === String(selectedLgaName).toLowerCase()) {
      option.selected = true;
    }
    lgaSelect.add(option);
  });
}

countrySelect.addEventListener('change', async () => {
  const option = countrySelect.options[countrySelect.selectedIndex];
  const countryIso2 = option?.dataset?.iso2 || '';

  if (!countryIso2) {
    resetSelect(stateSelect, 'Select state');
    resetSelect(lgaSelect, 'Select LGA');
    return;
  }

  await loadStates(countryIso2);
});

stateSelect.addEventListener('change', async () => {
  const option = stateSelect.options[stateSelect.selectedIndex];
  const stateId = option?.dataset?.id || '';

  if (!stateId) {
    resetSelect(lgaSelect, 'Select LGA');
    return;
  }

  await loadLgas(stateId);
});

document.addEventListener('DOMContentLoaded', async () => {
  const selectedNatIso2 = natSelect.dataset.selectedIso2 || '';
  const selectedCountryIso2 = countrySelect.dataset.selectedIso2 || '';
  const selectedStateId = stateSelect.dataset.selectedId || '';
  const selectedLgaName = lgaSelect.dataset.selectedName || '';

  await Promise.all([
    loadCountries(natSelect, selectedNatIso2, 'Select nationality'),
    loadCountries(countrySelect, selectedCountryIso2, 'Select country')
  ]);

  if (selectedCountryIso2) {
    await loadStates(selectedCountryIso2, selectedStateId);
  }

  if (selectedStateId) {
    await loadLgas(selectedStateId, selectedLgaName);
  }
});
</script>

</body>
</html>
