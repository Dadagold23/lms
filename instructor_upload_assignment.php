<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$flash = $_SESSION['ins_assign_flash'] ?? null;
unset($_SESSION['ins_assign_flash']);

$attachDir = __DIR__ . '/uploads/assignments/';
if (!is_dir($attachDir)) mkdir($attachDir, 0755, true);

$insId = (int)($_SESSION['instructor']['id'] ?? 0);

$coursesStmt = $pdo->prepare("
    SELECT c.id, c.title 
    FROM lms_courses c
    JOIN lms_instructor_courses ic ON ic.course_id = c.id
    WHERE ic.instructor_id = ?
    ORDER BY c.title
");
$coursesStmt->execute([$insId]);
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

$lessonsStmt = $pdo->prepare("
    SELECT l.id, l.course_id, l.title 
    FROM lms_lessons l
    JOIN lms_instructor_courses ic ON ic.course_id = l.course_id
    WHERE ic.instructor_id = ?
    ORDER BY l.id DESC
");
$lessonsStmt->execute([$insId]);
$lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);

function uploadAssignmentFile(string $field, string $dir): ?string {
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo((string)$_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf','doc','docx','png','jpg','jpeg'], true)) {
        $_SESSION['ins_assign_flash'] = 'Invalid attachment type. Use pdf/doc/docx/jpg/png.';
        redirect('instructor_upload_assignment.php');
    }
    $name = uniqid('ass_', true) . '.' . $ext;
    move_uploaded_file((string)$_FILES[$field]['tmp_name'], $dir . $name);
    return 'uploads/assignments/' . $name;
}

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $courseId = (int)($_POST['course_id'] ?? 0);
    $lessonId = (int)($_POST['lesson_id'] ?? 0);
    $title    = trim((string)($_POST['title'] ?? ''));
    $instr    = trim((string)($_POST['instructions'] ?? ''));
    $due      = trim((string)($_POST['due_date'] ?? ''));
    $pub      = (int)($_POST['is_published'] ?? 1);

    if ($courseId <= 0 || $title === '') {
        $_SESSION['ins_assign_flash'] = 'Select course and enter assignment title.';
        redirect('instructor_upload_assignment.php');
    }

    // Verify course belongs to this instructor
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id = ? AND course_id = ?");
    $checkStmt->execute([$insId, $courseId]);
    if ((int)$checkStmt->fetchColumn() === 0) {
        $_SESSION['ins_assign_flash'] = 'Access denied: you are not assigned to this course.';
        redirect('instructor_upload_assignment.php');
    }

    $attachment = uploadAssignmentFile('attachment', $attachDir);

    $dueDate = null;
    if ($due !== '' && strtotime($due) !== false) {
        $dueDate = date('Y-m-d', strtotime($due));
    }

    $stmt = $pdo->prepare("
        INSERT INTO lms_assignments (course_id, lesson_id, title, instructions, due_date, attachment_path, is_published, created_at)
        VALUES (?,?,?,?,?,?,?, NOW())
    ");
    $stmt->execute([
        $courseId,
        $lessonId > 0 ? $lessonId : null,
        $title,
        $instr,
        $dueDate,
        $attachment,
        $pub
    ]);

    $assignmentId = (int)$pdo->lastInsertId();
    if ($pub === 1) {
        $courseTitle = '';
        foreach ($courses as $course) {
            if ((int)$course['id'] === $courseId) {
                $courseTitle = (string)($course['title'] ?? '');
                break;
            }
        }
        $dueLabel = $dueDate ? date('d M Y', strtotime($dueDate)) : 'No due date yet';
        $message = "A new assignment has been published for {$courseTitle}: {$title}. Due date: {$dueLabel}.";
        createCourseStudentNotification(
            $pdo,
            $courseId,
            'assignment',
            'New assignment available',
            $message,
            'assignments.php'
        );
        $_SESSION['ins_assign_notify_course_id'] = $courseId;
        $_SESSION['ins_assign_notify_assignment_id'] = $assignmentId;
    }

    $_SESSION['ins_assign_flash'] = 'Assignment uploaded successfully.';
    redirect('instructor_upload_assignment.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Upload Assignment | Instructor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<h4 class="mb-3">Upload Assignment</h4>

<?php if ($flash): ?>
  <div class="alert alert-info"><?= e($flash) ?></div>
<?php endif; ?>
<?php
$notifyCourseId = (int)($_SESSION['ins_assign_notify_course_id'] ?? 0);
$notifyAssignmentId = (int)($_SESSION['ins_assign_notify_assignment_id'] ?? 0);
unset($_SESSION['ins_assign_notify_course_id'], $_SESSION['ins_assign_notify_assignment_id']);
?>
<?php if ($notifyCourseId > 0 && $notifyAssignmentId > 0): ?>
  <div class="alert alert-success d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span>Student notifications were created for this assignment.</span>
    <a class="btn btn-success btn-sm" href="whatsapp_course_notify.php?course_id=<?= $notifyCourseId ?>&kind=assignment&assignment_id=<?= $notifyAssignmentId ?>">
      <i class="fab fa-whatsapp me-1"></i>Open WhatsApp Links
    </a>
  </div>
<?php endif; ?>

<div class="card p-4">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

  <div class="mb-2">
    <label class="form-label">Course</label>
    <select class="form-select" name="course_id" required>
      <option value="">-- Select Course --</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= e($c['title'] ?? '') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-2">
    <label class="form-label">Lesson (optional)</label>
    <select class="form-select" name="lesson_id">
      <option value="0">-- None --</option>
      <?php foreach ($lessons as $l): ?>
        <option value="<?= (int)$l['id'] ?>">[Course #<?= (int)$l['course_id'] ?>] <?= e($l['title'] ?? '') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-2">
    <label class="form-label">Assignment Title</label>
    <input class="form-control" name="title" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Instructions</label>
    <textarea class="form-control" rows="6" name="instructions"></textarea>
  </div>

  <div class="row g-2">
    <div class="col-md-6 mb-2">
      <label class="form-label">Due Date (optional)</label>
      <input type="date" class="form-control" name="due_date">
    </div>
    <div class="col-md-6 mb-2">
      <label class="form-label">Publish</label>
      <select class="form-select" name="is_published">
        <option value="1">Published</option>
        <option value="0">Draft</option>
      </select>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Attachment (optional: pdf/doc/docx/jpg/png)</label>
    <input type="file" class="form-control" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
  </div>

  <button class="btn btn-primary w-100">Save Assignment</button>
</form>
</div>
</body>
</html>
