<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

/**
 * Shared branded HTML email wrapper.
 */
function emailWrap(string $title, string $body): string
{
    $appName = 'Grafix@Mirror LMS';
    $appUrl  = 'http://localhost/lms';
    $year    = date('Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title}</title>
</head>
<body style="margin:0;padding:0;background:#f0f4ff;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;padding:32px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(79,70,229,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:32px 40px;text-align:center;">
            <div style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:12px;padding:10px 20px;margin-bottom:12px;">
              <span style="color:#fff;font-size:22px;font-weight:800;letter-spacing:-0.5px;">G</span>
              <span style="color:#c7d2fe;font-size:18px;font-weight:600;">rafix@Mirror</span>
            </div>
            <p style="margin:6px 0 0;color:#c7d2fe;font-size:13px;font-weight:500;letter-spacing:0.5px;">MIRROR AGE CONCEPTS</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:40px 40px 32px;">
            {$body}
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;padding:24px 40px;border-top:1px solid #e2e8f0;text-align:center;">
            <p style="margin:0 0 6px;color:#94a3b8;font-size:12px;">© {$year} Mirror Age Concepts. All rights reserved.</p>
            <p style="margin:0;color:#94a3b8;font-size:12px;">
              <a href="{$appUrl}" style="color:#4f46e5;text-decoration:none;">Visit LMS Portal</a>
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
}

/**
 * Student welcome email after successful registration.
 */
function emailStudentWelcome(
    string $firstName,
    string $lastName,
    string $email,
    string $courseTitle,
    string $loginUrl = ''
): string {
    if ($loginUrl === '') {
        $loginUrl = appAbsoluteUrl('login.php');
    }
    $fullName   = htmlspecialchars(trim($firstName . ' ' . $lastName));
    $courseSafe = htmlspecialchars($courseTitle);
    $emailSafe  = htmlspecialchars($email);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:26px;font-weight:800;">Welcome aboard, {$fullName}! 🎉</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      Your student account has been successfully created at <strong>Grafix@Mirror LMS</strong>.
      You are now enrolled in your selected course and ready to start learning!
    </p>

    <div style="background:#f0f4ff;border-left:4px solid #4f46e5;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0 0 6px;color:#4f46e5;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;">Your Account</p>
      <p style="margin:0 0 4px;color:#1e293b;font-size:15px;"><strong>Email:</strong> {$emailSafe}</p>
      <p style="margin:0;color:#1e293b;font-size:15px;"><strong>Enrolled Course:</strong> {$courseSafe}</p>
    </div>

    <p style="margin:0 0 24px;color:#64748b;font-size:14px;line-height:1.6;">
      Login to your dashboard to access your lessons, assignments, and track your progress.
    </p>

    <div style="text-align:center;margin-bottom:28px;">
      <a href="{$loginUrl}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:700;letter-spacing:0.3px;">
        Access My Dashboard →
      </a>
    </div>

    <p style="margin:0;color:#94a3b8;font-size:13px;line-height:1.6;">
      If you have any questions, don't hesitate to reach out to our support team.
      We're excited to have you with us!
    </p>
HTML;

    return emailWrap('Welcome to Grafix@Mirror LMS', $body);
}

/**
 * Instructor welcome email sent when admin creates an instructor account.
 * Includes login credentials.
 */
function emailInstructorWelcome(
    string $fullName,
    string $email,
    string $plainPassword,
    string $loginUrl = ''
): string {
    if ($loginUrl === '') {
        $loginUrl = appAbsoluteUrl('instructor_login.php');
    }
    $nameSafe  = htmlspecialchars($fullName);
    $emailSafe = htmlspecialchars($email);
    $pwdSafe   = htmlspecialchars($plainPassword);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:26px;font-weight:800;">Hello, {$nameSafe}! 👋</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      An instructor account has been created for you on <strong>Grafix@Mirror LMS</strong>.
      You can start managing your courses and students right away.
    </p>

    <div style="background:#fef3c7;border-left:4px solid #d97706;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0 0 10px;color:#92400e;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;">⚠️ Your Login Credentials</p>
      <p style="margin:0 0 6px;color:#1e293b;font-size:15px;"><strong>Email:</strong> {$emailSafe}</p>
      <p style="margin:0 0 10px;color:#1e293b;font-size:15px;"><strong>Temporary Password:</strong> <code style="background:#fff;padding:2px 8px;border-radius:4px;font-size:14px;">{$pwdSafe}</code></p>
      <p style="margin:0;color:#92400e;font-size:13px;">Please change your password immediately after your first login.</p>
    </div>

    <div style="text-align:center;margin-bottom:28px;">
      <a href="{$loginUrl}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:700;">
        Login to Instructor Portal →
      </a>
    </div>

    <p style="margin:0;color:#94a3b8;font-size:13px;line-height:1.6;">
      If you did not expect this email or have any questions, please contact the administrator immediately.
    </p>
HTML;

    return emailWrap('Your Instructor Account — Grafix@Mirror LMS', $body);
}

/**
 * Instructor registration confirmation (self-registered, pending review).
 */
function emailInstructorRegistered(
    string $fullName,
    string $email,
    string $loginUrl = ''
): string {
    if ($loginUrl === '') {
        $loginUrl = appAbsoluteUrl('instructor_login.php');
    }
    $nameSafe  = htmlspecialchars($fullName);
    $emailSafe = htmlspecialchars($email);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:26px;font-weight:800;">Application Received, {$nameSafe}!</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      Thank you for applying to become an instructor at <strong>Grafix@Mirror LMS</strong>.
      Your application has been received and is currently under review.
    </p>

    <div style="background:#f0fdf4;border-left:4px solid #16a34a;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0 0 6px;color:#15803d;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;">✓ Application Status</p>
      <p style="margin:0 0 4px;color:#1e293b;font-size:15px;"><strong>Name:</strong> {$nameSafe}</p>
      <p style="margin:0 0 4px;color:#1e293b;font-size:15px;"><strong>Email:</strong> {$emailSafe}</p>
      <p style="margin:0;color:#1e293b;font-size:15px;"><strong>Status:</strong> <span style="color:#d97706;font-weight:600;">Pending Review</span></p>
    </div>

    <p style="margin:0 0 20px;color:#64748b;font-size:14px;line-height:1.6;">
      Our team will review your application and notify you once a decision has been made.
      This typically takes 1–3 business days.
    </p>

    <p style="margin:0;color:#94a3b8;font-size:13px;line-height:1.6;">
      If you have questions about your application, please contact us at
      <a href="mailto:support@mirrorageconcepts.com" style="color:#4f46e5;">support@mirrorageconcepts.com</a>.
    </p>
HTML;

    return emailWrap('Application Received — Grafix@Mirror LMS', $body);
}

/**
 * Admin notification when a new instructor self-registers.
 */
function emailAdminNotifyNewInstructor(
    string $instructorName,
    string $instructorEmail,
    string $specialization,
    string $adminUrl = ''
): string {
    if ($adminUrl === '') {
        $adminUrl = appAbsoluteUrl('admin_instructors.php');
    }
    $nameSafe  = htmlspecialchars($instructorName);
    $emailSafe = htmlspecialchars($instructorEmail);
    $specSafe  = htmlspecialchars($specialization);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:24px;font-weight:800;">New Instructor Application 📋</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      A new instructor has applied for an account on <strong>Grafix@Mirror LMS</strong>.
    </p>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0 0 8px;color:#1e293b;font-size:15px;"><strong>Name:</strong> {$nameSafe}</p>
      <p style="margin:0 0 8px;color:#1e293b;font-size:15px;"><strong>Email:</strong> {$emailSafe}</p>
      <p style="margin:0;color:#1e293b;font-size:15px;"><strong>Specialization:</strong> {$specSafe}</p>
    </div>

    <div style="text-align:center;margin-bottom:20px;">
      <a href="{$adminUrl}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:12px 32px;border-radius:8px;font-size:15px;font-weight:700;">
        Review Application →
      </a>
    </div>
HTML;

    return emailWrap('New Instructor Application — Admin Notice', $body);
}

/**
 * Notification to student that their enrollment needs instructor assignment.
 */
function emailStudentEnrollmentPending(
    string $firstName,
    string $courseTitle,
    string $loginUrl = ''
): string {
    if ($loginUrl === '') {
        $loginUrl = appAbsoluteUrl('dashboard.php');
    }
    $nameSafe   = htmlspecialchars($firstName);
    $courseSafe = htmlspecialchars($courseTitle);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:24px;font-weight:800;">Enrollment Confirmed, {$nameSafe}!</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      You have been successfully enrolled in <strong>{$courseSafe}</strong>.
      An instructor will be assigned to your course shortly by our team.
    </p>

    <div style="background:#fff7ed;border-left:4px solid #f59e0b;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0;color:#92400e;font-size:14px;line-height:1.5;">
        ⏳ Your course currently has no assigned instructor. We will notify you once an instructor has been assigned and your course materials are ready to access.
      </p>
    </div>

    <div style="text-align:center;margin-bottom:20px;">
      <a href="{$loginUrl}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:12px 32px;border-radius:8px;font-size:15px;font-weight:700;">
        Go to My Dashboard →
      </a>
    </div>
HTML;

    return emailWrap('Enrollment Confirmed — Grafix@Mirror LMS', $body);
}

/**
 * Setup link email for existing instructors (Option 1).
 */
function emailInstructorWelcomeSetup(
    string $fullName,
    string $email,
    string $token,
    string $setupUrl = ''
): string {
    if ($setupUrl === '') {
        $setupUrl = appAbsoluteUrl('instructor_reset_password.php');
    }
    $nameSafe  = htmlspecialchars($fullName);
    $emailSafe = htmlspecialchars($email);
    $fullUrl   = $setupUrl . '?token=' . urlencode($token);

    $body = <<<HTML
    <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:26px;font-weight:800;">Welcome to Grafix@Mirror LMS, {$nameSafe}! 🎉</h2>
    <p style="margin:0 0 20px;color:#64748b;font-size:15px;line-height:1.6;">
      Your instructor account is ready. Before you can log in, please verify your email address and choose a secure password to activate your portal profile.
    </p>

    <div style="background:#f0f4ff;border-left:4px solid #4f46e5;border-radius:8px;padding:18px 20px;margin-bottom:24px;">
      <p style="margin:0;color:#1e293b;font-size:15px;"><strong>Registered Email:</strong> {$emailSafe}</p>
    </div>

    <div style="text-align:center;margin-bottom:28px;">
      <a href="{$fullUrl}" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:700;letter-spacing:0.3px;">
        Verify Email & Setup Account →
      </a>
    </div>

    <p style="margin:0 0 20px;color:#94a3b8;font-size:13px;line-height:1.6;word-break:break-all;">
      If the button above does not work, copy and paste this URL into your web browser:<br>
      <a href="{$fullUrl}" style="color:#4f46e5;text-decoration:underline;">{$fullUrl}</a>
    </p>

    <p style="margin:0;color:#94a3b8;font-size:13px;line-height:1.6;">
      If you did not request this account setup email, please ignore it or contact system support.
    </p>
HTML;

    return emailWrap('Verify Email & Setup Instructor Account — Grafix@Mirror LMS', $body);
}
