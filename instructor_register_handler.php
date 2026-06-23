<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('instructor_register.php');
}
verifyCsrf($_POST['_csrf'] ?? '');

function clean(string $v): string { return trim(strip_tags($v)); }

/* ── Collect input ── */
$fullName       = clean($_POST['full_name']       ?? '');
$emailRaw       = $_POST['email']                 ?? '';
$email          = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$phone          = clean($_POST['phone']           ?? '');
$gender         = clean($_POST['gender']          ?? '');
$qualification  = clean($_POST['qualification']   ?? '');
$experienceYrs  = (int)($_POST['experience_years'] ?? 0);
$specialization = clean($_POST['specialization']  ?? '');
$bio            = trim($_POST['bio']              ?? '');
$linkedinUrl    = clean($_POST['linkedin_url']    ?? '');
$courseIds      = array_map('intval', (array)($_POST['course_ids'] ?? []));
$password       = (string)($_POST['password']       ?? '');
$confirmPwd     = (string)($_POST['confirm_password'] ?? '');

/* ── Validate ── */
if ($fullName === '') {
    $_SESSION['instructor_register_error'] = 'Full name is required.';
    redirect('instructor_register.php');
}
if (!$email) {
    $_SESSION['instructor_register_error'] = 'Invalid email address.';
    redirect('instructor_register.php');
}
if (strlen($phone) < 7) {
    $_SESSION['instructor_register_error'] = 'Invalid phone number.';
    redirect('instructor_register.php');
}
if ($qualification === '') {
    $_SESSION['instructor_register_error'] = 'Please select your highest qualification.';
    redirect('instructor_register.php');
}
if ($specialization === '') {
    $_SESSION['instructor_register_error'] = 'Field of specialization is required.';
    redirect('instructor_register.php');
}
if (strlen($bio) < 100) {
    $_SESSION['instructor_register_error'] = 'Bio must be at least 100 characters.';
    redirect('instructor_register.php');
}
if ($password !== $confirmPwd) {
    $_SESSION['instructor_register_error'] = 'Passwords do not match.';
    redirect('instructor_register.php');
}
if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $_SESSION['instructor_register_error'] = 'Password must be at least 8 characters, include 1 uppercase and 1 number.';
    redirect('instructor_register.php');
}

/* ── Duplicate check ── */
$check = $pdo->prepare("SELECT id FROM lms_instructors WHERE email=? LIMIT 1");
$check->execute([$email]);
if ($check->fetch()) {
    $_SESSION['instructor_register_error'] = 'An instructor account with this email already exists.';
    redirect('instructor_register.php');
}

/* ── Photo upload ── */
$photoFile = null;
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        $_SESSION['instructor_register_error'] = 'Profile photo must be JPG or PNG.';
        redirect('instructor_register.php');
    }
    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        $_SESSION['instructor_register_error'] = 'Profile photo too large (max 2MB).';
        redirect('instructor_register.php');
    }
    $uploadDir = __DIR__ . '/uploads/instructors/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $photoFile = 'instructors/' . uniqid('instructor_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $photoFile)) {
        $_SESSION['instructor_register_error'] = 'Photo upload failed. Please try again.';
        redirect('instructor_register.php');
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);

/* ── Insert instructor ── */
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("
        INSERT INTO lms_instructors
            (full_name, email, phone, password, bio, specialization, qualification,
             experience_years, linkedin_url, photo, status, created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,'active',NOW())
    ");
    $stmt->execute([
        $fullName, $email, $phone, $hash, $bio, $specialization,
        $qualification, $experienceYrs, $linkedinUrl ?: null, $photoFile
    ]);
    $instructorId = (int)$pdo->lastInsertId();

    /* ── Assign selected courses ── */
    if (!empty($courseIds)) {
        // Check if lms_instructor_courses table exists
        $tableExists = $pdo->query("SHOW TABLES LIKE 'lms_instructor_courses'")->fetchColumn();
        if ($tableExists) {
            $ins = $pdo->prepare("INSERT IGNORE INTO lms_instructor_courses (instructor_id, course_id) VALUES (?,?)");
            foreach ($courseIds as $cid) {
                if ($cid > 0) $ins->execute([$instructorId, $cid]);
            }
        }
    }

    $pdo->commit();

    // Send email confirmation and admin notices
    try {
        require_once __DIR__ . '/config/mail.php';
        require_once __DIR__ . '/includes/email_templates.php';

        // 1. Send confirmation to the instructor
        $insMail = emailInstructorRegistered($fullName, $email);
        send_mail($email, 'Application Received — Grafix@Mirror LMS', $insMail);

        // 2. Notify all administrators
        $adminMail = emailAdminNotifyNewInstructor($fullName, $email, $specialization);
        $stmtAdmins = $pdo->query("SELECT email FROM lms_admins");
        $adminEmails = $stmtAdmins->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($adminEmails)) {
            foreach ($adminEmails as $adminEmail) {
                if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    send_mail($adminEmail, 'New Instructor Application — Admin Notice', $adminMail);
                }
            }
        }
    } catch (Throwable $e) {
        error_log("Instructor self-reg notifications failed: " . $e->getMessage());
    }
} catch (Throwable $e) {
    $pdo->rollBack();
    $_SESSION['instructor_register_error'] = 'Registration failed. Please try again: ' . $e->getMessage();
    redirect('instructor_register.php');
}

$_SESSION['instructor_register_ok'] = 'Registration successful! Your account is pending admin review. You will be notified when approved.';
redirect('instructor_login.php');
