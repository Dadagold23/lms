<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

/* ── Handle switch action ── */
if (isset($_GET['student_id'])) {
    $studentId = (int)$_GET['student_id'];
    if ($studentId <= 0) {
        http_response_code(400);
        exit('Invalid student ID.');
    }

    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, role, status FROM lms_students WHERE id=? LIMIT 1");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        http_response_code(404);
        exit('Student not found.');
    }
    if (($student['status'] ?? '') !== 'active') {
        http_response_code(403);
        exit('Cannot impersonate a suspended or inactive student.');
    }
    if (($student['role'] ?? 'student') !== 'student') {
        http_response_code(403);
        exit('You can only impersonate student accounts.');
    }

    // Backup admin session
    if (empty($_SESSION['admin_backup'])) {
        $_SESSION['admin_backup'] = $_SESSION['admin'] ?? $_SESSION['user'] ?? [];
    }

    $_SESSION['user'] = [
        'id'         => (int)$student['id'],
        'first_name' => (string)$student['first_name'],
        'last_name'  => (string)$student['last_name'],
        'email'      => (string)$student['email'],
        'role'       => 'student',
        'switched'   => 1,
    ];

    redirect('dashboard.php');
}

/* ── Handle return to admin ── */
if (isset($_GET['return'])) {
    if (!empty($_SESSION['admin_backup'])) {
        $_SESSION['admin'] = $_SESSION['admin_backup'];
        unset($_SESSION['admin_backup'], $_SESSION['user']);
    }
    redirect('admin_dashboard.php');
}

/* ── Student list ── */
$search = trim($_GET['q'] ?? '');
$params = [];
$where  = '';
if ($search !== '') {
    $where = "WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
}

$students = $pdo->prepare("
    SELECT id, first_name, last_name, email, status, created_at
    FROM lms_students
    {$where}
    ORDER BY created_at DESC
    LIMIT 100
");
$students->execute($params);
$students = $students->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Switch User';
$seoDesc    = 'Admin user impersonation tool — Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav lms-nav-admin">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="admin_dashboard.php" class="brand text-decoration-none" style="color:#fff">
      <i class="fa fa-shield-alt me-2"></i> Admin Panel
    </a>
    <a href="admin_dashboard.php" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.3)">
      <i class="fa fa-arrow-left me-1"></i> Back to Admin
    </a>
  </div>
</nav>

<div class="container py-4" style="max-width:860px">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <h4 class="page-title mb-0"><i class="fa fa-user-secret me-2"></i>Impersonate Student</h4>
    <div class="lms-alert lms-alert-warning mb-0" style="font-size:.82rem">
      <i class="fa fa-exclamation-triangle me-1"></i>
      You will be logged in as the selected student. Use <strong>Return to Admin</strong> to switch back.
    </div>
  </div>

  <form method="get" class="d-flex gap-2 mb-4">
    <input type="text" name="q" value="<?= e($search) ?>" class="form-control" placeholder="Search by name or email...">
    <button class="btn-brand"><i class="fa fa-search"></i></button>
    <?php if ($search): ?><a href="admin_switch.php" class="btn-ghost">Clear</a><?php endif; ?>
  </form>

  <?php if (empty($students)): ?>
    <div class="lms-alert lms-alert-info"><i class="fa fa-info-circle"></i> No students found.</div>
  <?php else: ?>
    <div class="lms-card p-0" style="overflow:hidden">
      <table class="lms-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Email</th>
            <th>Status</th>
            <th>Registered</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
            <tr>
              <td class="fw-semibold"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
              <td class="text-muted"><?= e($s['email']) ?></td>
              <td>
                <?php $st = $s['status'] ?? 'active'; ?>
                <span class="badge-<?= $st === 'active' ? 'success' : 'danger' ?>"><?= e(ucfirst($st)) ?></span>
              </td>
              <td class="text-muted" style="font-size:.82rem"><?= e(date('d M Y', strtotime($s['created_at']))) ?></td>
              <td>
                <?php if ($st === 'active'): ?>
                  <a href="admin_switch.php?student_id=<?= (int)$s['id'] ?>"
                     class="btn-brand" style="font-size:.8rem;padding:.35rem .8rem"
                     onclick="return confirm('Switch to <?= e(addslashes($s['first_name'])) ?>?')">
                    <i class="fa fa-sign-in-alt me-1"></i> Switch
                  </a>
                <?php else: ?>
                  <span class="text-muted" style="font-size:.8rem">Inactive</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
