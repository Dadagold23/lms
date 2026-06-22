<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$msg = $_SESSION['bulk_msg'] ?? null;
unset($_SESSION['bulk_msg']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf((string)($_POST['_csrf'] ?? ''));

  if (empty($_FILES['csv']['tmp_name'])) {
    $_SESSION['bulk_msg'] = 'Please upload a CSV file.';
    redirect('bulk_import.php');
  }

  $tmp = $_FILES['csv']['tmp_name'];
  $fh = fopen($tmp, 'r');
  if (!$fh) {
    $_SESSION['bulk_msg'] = 'Unable to read uploaded file.';
    redirect('bulk_import.php');
  }

  $header = fgetcsv($fh);
  if (!$header) {
    fclose($fh);
    $_SESSION['bulk_msg'] = 'CSV is empty.';
    redirect('bulk_import.php');
  }

  $pdo->beginTransaction();
  $count = 0;

  try {
    while (($row = fgetcsv($fh)) !== false) {
      $data = array_combine($header, $row);
      if (!$data) continue;

      $email = filter_var(trim((string)($data['email'] ?? '')), FILTER_VALIDATE_EMAIL);
      if (!$email) continue;

      $first = trim((string)($data['first_name'] ?? ''));
      $last  = trim((string)($data['last_name'] ?? ''));
      $phone = trim((string)($data['phone'] ?? ''));
      $course = trim((string)($data['course'] ?? ''));
      $payOpt = trim((string)($data['payment_option'] ?? 'full'));
      $passRaw = (string)($data['password'] ?? 'Student@123');

      $hash = password_hash($passRaw, PASSWORD_DEFAULT);

      // skip if email exists
      $chk = $pdo->prepare("SELECT id FROM lms_students WHERE email=? LIMIT 1");
      $chk->execute([$email]);
      if ($chk->fetch()) continue;

      $ins = $pdo->prepare("
        INSERT INTO lms_students (first_name,last_name,email,phone,password,course,payment_option,role,status,created_at)
        VALUES (?,?,?,?,?,?,?,?,?,NOW())
      ");
      $ins->execute([$first,$last,$email,$phone,$hash,$course,$payOpt,'student','active']);
      $count++;
    }

    fclose($fh);
    $pdo->commit();

    $_SESSION['bulk_msg'] = "Import complete. Added {$count} students.";
    redirect('bulk_import.php');
  } catch (Throwable $e) {
    $pdo->rollBack();
    fclose($fh);
    $_SESSION['bulk_msg'] = "Import failed.";
    redirect('bulk_import.php');
  }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Bulk Import';
$seoDesc    = 'Bulk import students at Grafix@Mirror LMS admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">Bulk Import</span>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:760px">
  <div class="card p-4">
    <h5 class="mb-2">Upload CSV</h5>
    <p class="text-muted small mb-3">Columns: first_name,last_name,email,phone,course,payment_option,password</p>

    <?php if ($msg): ?>
      <div class="alert alert-info small"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <div class="mb-3">
        <input class="form-control" type="file" name="csv" accept=".csv" required>
      </div>
      <button class="btn btn-primary">Import</button>
    </form>
  </div>
</div>

</body>
</html>
