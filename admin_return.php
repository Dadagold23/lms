<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['admin_backup'])) {
  $_SESSION['admin'] = $_SESSION['admin_backup'];
  unset($_SESSION['admin_backup'], $_SESSION['user']);
}

redirect('admin_dashboard.php');
