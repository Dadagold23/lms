<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();

$flash = $_SESSION['grade_flash'] ?? null;
unset($_SESSION['grade_flash']);

if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $subId = (int)($_POST['submission_id'] ?? 0);
    $score = $_POST['score'] ?? null;
    $feedback = trim((string)($_POST['feedback'] ?? ''));

    if ($subId <= 0) {
        $_SESSION['grade_flash'] = 'Invalid submission.';
        redirect('instructor_grade_assignment.php');
    }

    $scoreVal = null;
    if ($score !== null && $score !== '') $scoreVal = (float)$score;

    // if you have instructor id, use it; otherwise keep NULL
    $graderId = null;
    if (!empty($_SESSION['instructor']['id'])) $graderId = (int)$_SESSION['instructor']['id'];
    elseif (!empty($_SESSION['user']['id'])) $graderId = (int)$_SESSION['user']['id'];

    $stmt = $pdo->prepare("
        UPDATE lms_assignment_submissions
        SET score = ?, feedback = ?, graded_by = ?, graded_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$scoreVal, $feedback, $graderId, $subId]);

    $_SESSION['grade_flash'] = 'Submission graded successfully.';
    redirect('instructor_grade_assignment.php');
}

$subs = $pdo->query("
  SELECT s.id, s.file_path, s.note, s.submitted_at, s.score, s.feedback,
         st.first_name, st.last_name, st.email,
         a.title AS assignment_title,
         c.title AS course_title
  FROM lms_assignment_submissions s
  JOIN lms_students st ON st.id = s.student_id
  JOIN lms_assignments a ON a.id = s.assignment_id
  JOIN lms_courses c ON c.id = a.course_id
  ORDER BY s.submitted_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Grade Submissions</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Grade Submissions</h4>
  <a class="btn btn-outline-primary btn-sm" href="instructor_dashboard.php">Dashboard</a>
</div>

<?php if ($flash): ?>
  <div class="alert alert-info"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card p-3">
  <?php if (empty($subs)): ?>
    <p class="text-muted mb-0">No submissions yet.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
      <thead>
        <tr>
          <th>Student</th>
          <th>Course</th>
          <th>Assignment</th>
          <th>Submitted</th>
          <th>File</th>
          <th>Score</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($subs as $s): ?>
        <tr>
          <td>
            <?= e($s['first_name'].' '.$s['last_name']) ?><br>
            <small class="text-muted"><?= e($s['email']) ?></small>
          </td>
          <td><?= e($s['course_title']) ?></td>
          <td><?= e($s['assignment_title']) ?></td>
          <td><?= e($s['submitted_at']) ?></td>
          <td>
            <?php if (!empty($s['file_path'])): ?>
              <a target="_blank" href="<?= e($s['file_path']) ?>" class="btn btn-outline-secondary btn-sm">Open</a>
            <?php endif; ?>
          </td>
          <td><?= e($s['score']) ?></td>
          <td style="min-width:280px">
            <form method="post" class="d-flex gap-2">
              <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="submission_id" value="<?= (int)$s['id'] ?>">
              <input class="form-control form-control-sm" name="score" placeholder="Score" style="max-width:90px">
              <input class="form-control form-control-sm" name="feedback" placeholder="Feedback">
              <button class="btn btn-primary btn-sm">Save</button>
            </form>
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
