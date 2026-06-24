<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

/* ======================
   BOOTSTRAP
====================== */
startSecureSession();

/* ======================
   METHOD + CSRF GUARD
====================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

verifyCsrf($_POST['_csrf'] ?? '');

/* ======================
   HELPERS (LOCAL)
====================== */
function clean(string $v): string {
    return trim(strip_tags($v));
}

function uploadImage(string $field, string $uploadDir): ?string
{
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        $_SESSION['register_error'] = 'Invalid image format. Use JPG or PNG.';
        redirect('register.php');
    }

    // extra safety: limit size (3MB)
    if (($_FILES[$field]['size'] ?? 0) > (3 * 1024 * 1024)) {
        $_SESSION['register_error'] = 'Image too large. Max 3MB.';
        redirect('register.php');
    }

    $name = uniqid($field . '_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $name)) {
        $_SESSION['register_error'] = 'Upload failed. Try again.';
        redirect('register.php');
    }
    return $name;
}

/* ======================
   UPLOAD DIR
====================== */
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ======================
   COLLECT INPUT
====================== */
$firstName   = clean($_POST['first_name'] ?? '');
$lastName    = clean($_POST['last_name'] ?? '');
$otherNames  = clean($_POST['other_names'] ?? '');
$emailRaw    = $_POST['email'] ?? '';
$email       = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$phone       = clean($_POST['phone'] ?? '');
$dob         = $_POST['dob'] ?? '';
$gender      = clean($_POST['gender'] ?? '');
$address     = clean($_POST['address'] ?? '');

$nationalityIso2 = clean($_POST['nationality_iso2'] ?? '');
$residenceIso2   = clean($_POST['residence_iso2'] ?? '');
$stateId         = (int)($_POST['state_id'] ?? 0);
$lgaId           = (int)($_POST['lga_id'] ?? 0);

$courseId    = (int)($_POST['course_id'] ?? 0);
$paymentOpt  = $_POST['payment_option'] ?? '';
$kycType     = clean($_POST['kyc_type'] ?? '');
$kycNumber   = clean($_POST['kyc_number'] ?? '');

$password    = (string)($_POST['password'] ?? '');
$confirmPwd  = (string)($_POST['confirm_password'] ?? '');

/* ======================
   VALIDATE INPUT
====================== */
if ($firstName === '' || $lastName === '') {
    $_SESSION['register_error'] = 'First name and last name are required.';
    redirect('register.php');
}

if (!$email) {
    $_SESSION['register_error'] = 'Invalid email address.';
    redirect('register.php');
}

if (strlen($phone) < 7) {
    $_SESSION['register_error'] = 'Invalid phone number.';
    redirect('register.php');
}

if (!$dob || strtotime($dob) === false || strtotime($dob) > time()) {
    $_SESSION['register_error'] = 'Invalid date of birth.';
    redirect('register.php');
}

if (!in_array($paymentOpt, ['full', 'installment'], true)) {
    $_SESSION['register_error'] = 'Invalid payment option.';
    redirect('register.php');
}

if ($password !== $confirmPwd) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    redirect('register.php');
}

if (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password)
) {
    $_SESSION['register_error'] = 'Password must be at least 8 characters, include a number and uppercase letter.';
    redirect('register.php');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* ======================
   VALIDATE COURSE (DB)
====================== */
$courseStmt = $pdo->prepare("
    SELECT id, title, price
    FROM lms_courses
    WHERE id = ?
    LIMIT 1
");
$courseStmt->execute([$courseId]);
$courseRow = $courseStmt->fetch(PDO::FETCH_ASSOC);

if (!$courseRow) {
    $_SESSION['register_error'] = 'Invalid course selected.';
    redirect('register.php');
}

$courseTitle = (string)$courseRow['title'];
$coursePrice = (float)$courseRow['price'];

/* ======================
   DUPLICATE EMAIL
====================== */
$check = $pdo->prepare("SELECT id FROM lms_students WHERE email = ? LIMIT 1");
$check->execute([$email]);
if ($check->fetch()) {
    $_SESSION['register_error'] = 'Email already registered.';
    redirect('register.php');
}

/* ======================
   FILE UPLOADS
====================== */
$passportPhoto  = uploadImage('passport', $uploadDir);
$signaturePhoto = uploadImage('signature', $uploadDir);

if (!$passportPhoto || !$signaturePhoto) {
    $_SESSION['register_error'] = 'Passport and signature are required (JPG/PNG).';
    redirect('register.php');
}

/* ======================
   RESOLVE GEO NAMES FROM IDs / ISO2
====================== */
$nationalityName = '';
if ($nationalityIso2 !== '') {
    $r = $pdo->prepare("SELECT name FROM ref_countries WHERE iso2 = ? LIMIT 1");
    $r->execute([$nationalityIso2]);
    $nationalityName = (string)($r->fetchColumn() ?: $nationalityIso2);
}

$countryName = '';
if ($residenceIso2 !== '') {
    $r = $pdo->prepare("SELECT name FROM ref_countries WHERE iso2 = ? LIMIT 1");
    $r->execute([$residenceIso2]);
    $countryName = (string)($r->fetchColumn() ?: $residenceIso2);
}

$stateName = '';
if ($stateId > 0) {
    $r = $pdo->prepare("SELECT name FROM ref_states WHERE id = ? LIMIT 1");
    $r->execute([$stateId]);
    $stateName = (string)($r->fetchColumn() ?: '');
}

$lgaName = '';
if ($lgaId > 0) {
    $r = $pdo->prepare("SELECT name FROM ref_lgas WHERE id = ? LIMIT 1");
    $r->execute([$lgaId]);
    $lgaName = (string)($r->fetchColumn() ?: '');
}

/* ======================
   INSERT USER
   - Works with BOTH schemas:
     A) legacy columns: course/course_price/payment_option
     B) DB-driven: course_id + location ids
====================== */
$cols = $pdo->query("SHOW COLUMNS FROM lms_students")->fetchAll(PDO::FETCH_COLUMN);
$has = fn(string $c): bool => in_array($c, $cols, true);

// Build fields dynamically — only include columns that actually exist
$allFields = [
    'first_name'      => $firstName,
    'last_name'       => $lastName,
    'other_names'     => $otherNames,
    'email'           => $email,
    'phone'           => $phone,
    'dob'             => $dob,
    'gender'          => $gender,
    'address'         => $address,
    'nationality'     => $nationalityName,
    'country'         => $countryName,
    'state_of_origin' => $stateName,
    'lga'             => $lgaName,
    'kyc_type'        => $kycType,
    'kyc_number'      => $kycNumber,
    // passport/signature: try both column name variants
    'passport'        => $passportPhoto,    // actual column name
    'signature'       => $signaturePhoto,   // actual column name
    'passport_photo'  => $passportPhoto,    // legacy variant
    'signature_photo' => $signaturePhoto,   // legacy variant
    'course'          => $courseTitle,      // legacy column
    'course_price'    => $coursePrice,      // legacy column
    'payment_option'  => $paymentOpt,       // legacy column
    'password'        => $hashedPassword,
    'created_at'      => null,
];

// Filter to only columns that exist in the table
$fields = array_filter($allFields, fn($col) => $has($col), ARRAY_FILTER_USE_KEY);

// Deduplicate: if both 'passport' and 'passport_photo' exist, keep only the first match
$seen = [];
$fields = array_filter($fields, function($val, $col) use (&$seen) {
    $group = match(true) {
        in_array($col, ['passport','passport_photo'], true)  => 'passport_grp',
        in_array($col, ['signature','signature_photo'], true) => 'signature_grp',
        default => $col,
    };
    if (isset($seen[$group])) return false;
    $seen[$group] = true;
    return true;
}, ARRAY_FILTER_USE_BOTH);

/* Build SQL dynamically */
$valSqlParts = [];
$values = [];

foreach ($fields as $col => $val) {
    if ($col === 'created_at') {
        $valSqlParts[] = 'NOW()';
        continue;
    }
    $valSqlParts[] = '?';
    $values[] = $val;
}

$colSql = implode(',', array_keys($fields));
$sql = "INSERT INTO lms_students ($colSql) VALUES (" . implode(',', $valSqlParts) . ")";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute($values);
} catch (Throwable $e) {
    $_SESSION['register_error'] = 'Registration failed. Please try again.';
    redirect('register.php');
}

$userId = (int)$pdo->lastInsertId();

/* ======================
   OPTIONAL: CREATE ENROLLMENT IMMEDIATELY
   If your lms_enrollments table exists
====================== */
try {
    $enCols = $pdo->query("SHOW COLUMNS FROM lms_enrollments")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('student_id', $enCols, true) && in_array('course_id', $enCols, true)) {
        
        // Find instructor for this course
        $stmtIns = $pdo->prepare("
            SELECT instructor_id 
            FROM lms_instructor_courses ic
            JOIN lms_instructors i ON ic.instructor_id = i.id
            WHERE ic.course_id = ? AND i.status = 'active'
            ORDER BY (i.availability_status = 'available') DESC, i.id ASC
            LIMIT 1
        ");
        $stmtIns->execute([$courseId]);
        $assignedInstructorId = $stmtIns->fetchColumn();
        
        $assignedIdVal = $assignedInstructorId ? (int)$assignedInstructorId : null;
        $needsAssignVal = $assignedInstructorId ? 0 : 1;

        $enHasPT = in_array('payment_type', $enCols, true);
        if ($enHasPT) {
            $en = $pdo->prepare("
                INSERT INTO lms_enrollments (student_id, course_id, paid_amount, payment_type, status, assigned_instructor_id, needs_instructor_assignment, created_at)
                VALUES (?,?,0,?,'active',?,?,NOW())
            ");
            $en->execute([$userId, $courseId, $paymentOpt, $assignedIdVal, $needsAssignVal]);
        } else {
            $en = $pdo->prepare("
                INSERT INTO lms_enrollments (student_id, course_id, paid_amount, status, assigned_instructor_id, needs_instructor_assignment, created_at)
                VALUES (?,?,0,'active',?,?,NOW())
            ");
            $en->execute([$userId, $courseId, $assignedIdVal, $needsAssignVal]);
        }

        $enrollmentId = (int)$pdo->lastInsertId();
        if ($assignedIdVal && $enrollmentId > 0) {
            require_once __DIR__ . '/includes/student_notifications.php';
            notifyInstructorAssigned($pdo, $enrollmentId, $assignedIdVal);
        }

        // Send email confirmation using SMTP
        require_once __DIR__ . '/config/mail.php';
        require_once __DIR__ . '/includes/email_templates.php';

        if ($assignedInstructorId) {
            $mailContent = emailStudentWelcome($firstName, $lastName, $email, $courseTitle);
            $subject = 'Welcome to Grafix@Mirror LMS - Enrollment Confirmed!';
        } else {
            $mailContent = emailStudentEnrollmentPending($firstName, $courseTitle);
            $subject = 'Welcome to Grafix@Mirror LMS - Enrollment Under Review';
        }
        send_mail($email, $subject, $mailContent);
    }
} catch (Throwable $e) {
    error_log("Enrollment setup / mail failed: " . $e->getMessage());
}

/* ======================
   AUTO LOGIN
====================== */
session_regenerate_id(true);
$_SESSION['user'] = [
    'id'             => $userId,
    'first_name'     => $firstName,
    'last_name'      => $lastName,
    'email'          => $email,
    'role'           => 'student',
    // keep these for compatibility with your dashboard / older files
    'course'         => $courseTitle,
    'course_price'   => $coursePrice,
    'payment_option' => $paymentOpt,
];

/* ======================
   REDIRECT
====================== */
redirect('dashboard.php');
