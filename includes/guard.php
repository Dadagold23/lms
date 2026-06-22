<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function requireLogin(): void
{
    startSecureSession();

    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }
}

function requireAdminLogin(): void
{
    startSecureSession();

    // support either role-based OR separate admin session
    if (!empty($_SESSION['admin'])) return;

    if (!empty($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        return;
    }

    http_response_code(403);
    exit('Access denied');
}

function hasRole(string $role): bool
{
    startSecureSession();

    if (!empty($_SESSION['user']) && isset($_SESSION['user']['role'])) {
        return strcasecmp((string)$_SESSION['user']['role'], $role) === 0;
    }

    // Support legacy/fallback $_SESSION['admin'] for Admin role
    if (strcasecmp($role, 'admin') === 0 && !empty($_SESSION['admin'])) {
        return true;
    }

    // Support legacy/fallback $_SESSION['instructor'] for Instructor role
    if (strcasecmp($role, 'instructor') === 0 && !empty($_SESSION['instructor'])) {
        return true;
    }

    return false;
}

function requireAdmin(): void
{
    requireAdminLogin();
}

function requireInstructorLogin(): void
{
    startSecureSession();

    // support either role-based OR separate instructor session
    if (!empty($_SESSION['instructor'])) return;

    if (!empty($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'instructor') {
        return;
    }

    http_response_code(403);
    exit('Access denied');
}

// optional: student-only page guard
function requireStudentLogin(): void
{
    startSecureSession();

    if (!empty($_SESSION['user']) && (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] === 'student')) {
        return;
    }

    http_response_code(403);
    exit('Access denied');
}
