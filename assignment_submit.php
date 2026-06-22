<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$assignmentId = (int)($_GET['assignment_id'] ?? ($_POST['assignment_id'] ?? 0));
if ($assignmentId <= 0) exit('Invalid assignment');

$ass = $pdo->prepare("
  SELECT a.id, a.title, a.course_id, c.title AS course_title
  FROM lms_assignments a
  JOIN lms_courses c ON c.id=a.course_id
  WHERE a.id=?
");
$ass->execute([$assignmentId]);
$ass = $ass->fetch(PDO::FETCH_ASSOC);
if (!$ass) exit('Invalid assignment');

$uploadDir = __DIR__ . '/uploads/submissions/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$flash = $_SESSION['submit_flash'] ?? null;
unset($_SESSION['submit_flash']);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $note = trim((string)($_POST['note'] ?? ''));

    $filePath = null;
    if (!empty($_FILES['file']['name']) && ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo((string)$_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf','doc','docx','jpg','jpeg','png'], true)) {
            $_SESSION['submit_flash'] = 'Invalid file type. Use pdf/doc/docx/jpg/png.';
            redirect('assignment_submit.php?assignment_id='.$assignmentId);
        }
        $name = uniqid('sub_', true) . '.' . $ext;
        move_uploaded_file((string)$_FILES['file']['tmp_name'], $uploadDir . $name);
        $filePath = 'uploads/submissions/' . $name;
    }

    $stmt = $pdo->prepare("
      INSERT INTO lms_assignment_submissions (assignment_id, student_id, file_path, note)
      VALUES (?,?,?,?)
      ON DUPLICATE KEY UPDATE file_path=VALUES(file_path), note=VALUES(note), submitted_at=NOW()
    ");
    $stmt->execute([$assignmentId, $studentId, $filePath, $note]);

    $_SESSION['submit_flash'] = 'Submitted successfully.';
    redirect('assignment_submit.php?assignment_id='.$assignmentId);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Submit Assignment';
$seoDesc    = 'Submit your assignment at Grafix@Mirror LMS — Mirror Age Concepts.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
      <a href="assignments.php?course_id=3" class="btn-ghost"><i class="fa fa-arrow-left me-1"></i>Assignments</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:680px">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.82rem">
      <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="assignments.php?course_id=<?= (int)$ass['course_id'] ?>"><?= e($ass['course_title']) ?></a></li>
      <li class="breadcrumb-item active">Submit Assignment</li>
    </ol>
  </nav>

  <div class="lms-card">
    <h5 class="mb-1" style="font-weight:700"><i class="fa fa-upload me-2" style="color:var(--brand)"></i><?= e($ass['title']) ?></h5>
    <div class="text-muted mb-4" style="font-size:.85rem"><i class="fa fa-book me-1"></i><?= e($ass['course_title']) ?></div>

    <?php if ($flash): ?>
      <div class="lms-alert lms-alert-success mb-3"><i class="fa fa-check-circle me-1"></i><?= e($flash) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="assignment_id" value="<?= (int)$assignmentId ?>">

      <div class="mb-3">
        <label class="form-label">Upload File <span style="color:var(--danger)">*</span></label>
        <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        <div class="form-text">Accepted: PDF, DOC, DOCX, JPG, PNG — max 10MB</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Note <span class="text-muted">(optional)</span></label>
        <textarea name="note" class="form-control" rows="4" placeholder="Add any notes or comments for your instructor..."></textarea>
      </div>

      <div class="d-flex gap-3">
        <button type="submit" class="btn-brand">
          <i class="fa fa-paper-plane me-1"></i> Submit Assignment
        </button>
        <a href="assignments.php?course_id=<?= (int)$ass['course_id'] ?>" class="btn-ghost">Cancel</a>
      </div>
    </form>
  </div>

</div>
</body>
</html>
