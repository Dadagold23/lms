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
   METHOD CHECK + CSRF
====================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

verifyCsrf($_POST['_csrf'] ?? '');

/* ======================
   INPUT
====================== */
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['login_error'] = 'Invalid login credentials.';
    redirect('login.php');
}


/* ======================
   FETCH STUDENT
   (Or Admin, since it is a unified rolebase login now)
 ====================== */
$stmt = $pdo->prepare("
    SELECT 
        id,
        first_name,
        last_name,
        email,
        password,
        role,
        status
    FROM lms_students
    WHERE email = ?
    LIMIT 1
");
$stmt->execute([$email]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    /* ======================
       VERIFY PASSWORD
     ====================== */
    if (!password_verify($password, $student['password'])) {
        $_SESSION['login_error'] = 'Incorrect email or password.';
        redirect('login.php');
    }

    if (($student['status'] ?? '') === 'suspended') {
        $_SESSION['login_error'] = 'Your account is suspended.';
        redirect('login.php');
    }

    /* ======================
       SESSION
     ====================== */
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'         => (int)$student['id'],
        'first_name' => $student['first_name'],
        'last_name'  => $student['last_name'],
        'email'      => $student['email'],
        'role'       => $student['role'] ?? 'student'
    ];

    // For compatibility, if the student is an admin, also set the admin session
    if (($student['role'] ?? 'student') === 'admin') {
        $_SESSION['admin'] = [
            'id'        => (int)$student['id'],
            'full_name' => $student['first_name'] . ' ' . $student['last_name'],
            'email'     => $student['email'],
        ];
    }

    // For compatibility, if the student is an instructor, also set the instructor session
    if (($student['role'] ?? 'student') === 'instructor') {
        $_SESSION['instructor'] = [
            'id'        => (int)$student['id'],
            'full_name' => $student['first_name'] . ' ' . $student['last_name'],
            'email'     => $student['email'],
        ];
    }

    /* ======================
       ACTIVITY LOG
     ====================== */
    $stmt = $pdo->prepare("
        INSERT INTO lms_activity_logs (student_id, action, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([
        $student['id'],
        'Logged in'
    ]);

    /* ======================
       REDIRECT
     ====================== */
    if (($student['role'] ?? 'student') === 'admin') {
        redirect('admin_dashboard.php');
    } elseif (($student['role'] ?? 'student') === 'instructor') {
        redirect('instructor_dashboard.php');
    } else {
        redirect('dashboard.php');
    }


} else {
    // Check if the user is in legacy lms_admins table
    $stmt = $pdo->prepare("
        SELECT id, full_name, email, password, status
        FROM lms_admins
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if (!password_verify($password, (string)$admin['password'])) {
            $_SESSION['login_error'] = 'Incorrect email or password.';
            redirect('login.php');
        }

        if (($admin['status'] ?? '') !== 'active') {
            $_SESSION['login_error'] = 'Admin account disabled.';
            redirect('login.php');
        }

        session_regenerate_id(true);
        
        // Populate $_SESSION['admin'] for legacy support
        $_SESSION['admin'] = [
            'id'        => (int)$admin['id'],
            'full_name' => (string)$admin['full_name'],
            'email'     => (string)$admin['email'],
        ];

        // Also populate $_SESSION['user'] with role 'admin' for rolebase check
        $names = explode(' ', (string)$admin['full_name'], 2);
        $_SESSION['user'] = [
            'id'         => (int)$admin['id'],
            'first_name' => $names[0] ?? 'Admin',
            'last_name'  => $names[1] ?? '',
            'email'      => (string)$admin['email'],
            'role'       => 'admin'
        ];

        $pdo->prepare("UPDATE lms_admins SET last_login_at = NOW() WHERE id = ?")->execute([(int)$admin['id']]);

        redirect('admin_dashboard.php');
    } else {
        // Check if the user is in legacy lms_instructors table
        $stmt = $pdo->prepare("
            SELECT id, full_name, email, password, status
            FROM lms_instructors
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $ins = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ins) {
            if (!password_verify($password, (string)$ins['password'])) {
                $_SESSION['login_error'] = 'Incorrect email or password.';
                redirect('login.php');
            }

            if (($ins['status'] ?? '') !== 'active') {
                $_SESSION['login_error'] = 'Instructor account disabled.';
                redirect('login.php');
            }

            session_regenerate_id(true);
            
            // Populate $_SESSION['instructor'] for legacy support
            $_SESSION['instructor'] = [
                'id'        => (int)$ins['id'],
                'full_name' => (string)$ins['full_name'],
                'email'     => (string)$ins['email'],
            ];

            // Also populate $_SESSION['user'] with role 'instructor' for rolebase check
            $names = explode(' ', (string)$ins['full_name'], 2);
            $_SESSION['user'] = [
                'id'         => (int)$ins['id'],
                'first_name' => $names[0] ?? 'Instructor',
                'last_name'  => $names[1] ?? '',
                'email'      => (string)$ins['email'],
                'role'       => 'instructor'
            ];

            $pdo->prepare("UPDATE lms_instructors SET last_login_at = NOW() WHERE id = ?")->execute([(int)$ins['id']]);

            redirect('instructor_dashboard.php');
        } else {
            $_SESSION['login_error'] = 'Incorrect email or password.';
            redirect('login.php');
        }
    }
}
