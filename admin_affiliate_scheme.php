<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'];

$flash = $_SESSION['admin_aff_scheme_flash'] ?? null;
unset($_SESSION['admin_aff_scheme_flash']);

/* ======================
   LOAD FILTERS & DEFAULTS
 ====================== */
$affiliateCourses = $pdo->query("SELECT id, title FROM lms_affiliate_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

$selectedCourseId   = (int)($_GET['course_id'] ?? ($affiliateCourses[0]['id'] ?? 0));
$selectedClassLevel = trim((string)($_GET['class_level'] ?? 'JSS1'));
$selectedTerm       = trim((string)($_GET['term'] ?? '1st'));

$allowedLevels = ['JSS1','JSS2','JSS3','SSS1','SSS2','SSS3'];
$allowedTerms  = ['1st','2nd','3rd'];

if (!in_array($selectedClassLevel, $allowedLevels, true)) $selectedClassLevel = 'JSS1';
if (!in_array($selectedTerm, $allowedTerms, true)) $selectedTerm = '1st';

/* ======================
   HANDLE POST ACTIONS
 ====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    $id         = (int)($_POST['id'] ?? 0);
    $courseId   = (int)($_POST['course_id'] ?? 0);
    $classLevel     = trim((string)($_POST['class_level'] ?? ''));
    $term           = trim((string)($_POST['term'] ?? ''));
    $weekNum        = (int)($_POST['week_number'] ?? 0);
    $topic          = trim((string)($_POST['topic'] ?? ''));
    $objectives     = trim((string)($_POST['objectives'] ?? ''));
    $activities     = trim((string)($_POST['activities'] ?? ''));
    $lectureContent = trim((string)($_POST['lecture_content'] ?? ''));
    $quizJson       = trim((string)($_POST['quiz_json'] ?? ''));

    if ($action === 'create') {
        if ($courseId <= 0 || !in_array($classLevel, $allowedLevels, true) || !in_array($term, $allowedTerms, true) || $weekNum <= 0 || $topic === '') {
            $_SESSION['admin_aff_scheme_flash'] = 'Required fields are missing or invalid.';
            redirect("admin_affiliate_scheme.php?course_id={$courseId}&class_level={$classLevel}&term={$term}");
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO lms_affiliate_scheme_of_work (course_id, class_level, term, week_number, topic, objectives, activities, lecture_content, quiz_json)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$courseId, $classLevel, $term, $weekNum, $topic, $objectives, $activities, $lectureContent ?: null, $quizJson ?: null]);
            $_SESSION['admin_aff_scheme_flash'] = "Scheme of Work for Week {$weekNum} created successfully.";
        } catch (Throwable $e) {
            $_SESSION['admin_aff_scheme_flash'] = 'Error creating SOW: ' . $e->getMessage();
        }
        redirect("admin_affiliate_scheme.php?course_id={$courseId}&class_level={$classLevel}&term={$term}");
    }

    if ($action === 'update') {
        if ($id <= 0 || $courseId <= 0 || !in_array($classLevel, $allowedLevels, true) || !in_array($term, $allowedTerms, true) || $weekNum <= 0 || $topic === '') {
            $_SESSION['admin_aff_scheme_flash'] = 'Required fields are missing or invalid.';
            redirect("admin_affiliate_scheme.php?course_id={$courseId}&class_level={$classLevel}&term={$term}");
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE lms_affiliate_scheme_of_work
                SET course_id = ?, class_level = ?, term = ?, week_number = ?, topic = ?, objectives = ?, activities = ?, lecture_content = ?, quiz_json = ?
                WHERE id = ?
            ");
            $stmt->execute([$courseId, $classLevel, $term, $weekNum, $topic, $objectives, $activities, $lectureContent ?: null, $quizJson ?: null, $id]);
            $_SESSION['admin_aff_scheme_flash'] = "Scheme of Work for Week {$weekNum} updated successfully.";
        } catch (Throwable $e) {
            $_SESSION['admin_aff_scheme_flash'] = 'Error updating SOW: ' . $e->getMessage();
        }
        redirect("admin_affiliate_scheme.php?course_id={$courseId}&class_level={$classLevel}&term={$term}");
    }

    if ($action === 'delete') {
        if ($id <= 0) {
            $_SESSION['admin_aff_scheme_flash'] = 'Invalid SOW record ID.';
            redirect("admin_affiliate_scheme.php?course_id={$selectedCourseId}&class_level={$selectedClassLevel}&term={$selectedTerm}");
        }
        try {
            $pdo->prepare("DELETE FROM lms_affiliate_scheme_of_work WHERE id = ?")->execute([$id]);
            $_SESSION['admin_aff_scheme_flash'] = 'Scheme of work record deleted successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_aff_scheme_flash'] = 'Error deleting SOW: ' . $e->getMessage();
        }
        redirect("admin_affiliate_scheme.php?course_id={$selectedCourseId}&class_level={$selectedClassLevel}&term={$selectedTerm}");
    }
}

/* ======================
   LOAD SCHEME DETAILS
 ====================== */
$weeks = [];
if ($selectedCourseId > 0) {
    $stmt = $pdo->prepare("
        SELECT * FROM lms_affiliate_scheme_of_work
        WHERE course_id = ? AND class_level = ? AND term = ?
        ORDER BY week_number ASC
    ");
    $stmt->execute([$selectedCourseId, $selectedClassLevel, $selectedTerm]);
    $weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$editId = (int)($_GET['edit'] ?? 0);
$editWeek = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM lms_affiliate_scheme_of_work WHERE id = ? LIMIT 1");
    $stmt->execute([$editId]);
    $editWeek = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Manage Scheme of Work';
$seoDesc    = 'Admin management dashboard for Grafix@Mirror LMS affiliate schemes of work (syllabi).';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body>

<nav class="lms-nav lms-nav-admin">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem; color:#fff;" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
      </button>
      <div class="brand">
        <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
        <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:#94a3b8">
        <i class="fa fa-user-shield me-1"></i><?= e($admin['full_name'] ?? 'Admin') ?>
      </span>
      <a href="admin_logout.php" style="font-size:.82rem;color:#f87171;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar" id="sidebar">
    <div class="nav-section">Overview</div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="analytics.php" class="nav-link"><i class="fa fa-chart-bar"></i> Analytics</a>
    <div class="nav-section">Management</div>
    <a href="admin_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="admin_instructors.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Instructors</a>
    <a href="admin_partners.php" class="nav-link"><i class="fa fa-handshake"></i> Affiliate</a>
    <a href="admin_affiliate_courses.php" class="nav-link"><i class="fa fa-book-open"></i> Affiliate Courses</a>
    <a href="admin_affiliate_scheme.php" class="nav-link active"><i class="fa fa-scroll"></i> Scheme of Work</a>
    <a href="admin_enrollment_assignments.php" class="nav-link"><i class="fa fa-user-tag"></i> Assignments</a>
    <a href="admin_student_performance.php" class="nav-link"><i class="fa fa-graduation-cap"></i> Student Performance</a>
    <a href="cert_settings.php" class="nav-link"><i class="fa fa-certificate"></i> Certificate</a>
    <a href="admin_badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="admin_payment_approval.php" class="nav-link"><i class="fa fa-credit-card"></i> Payments</a>
    <a href="finance_report.php" class="nav-link"><i class="fa fa-file-invoice-dollar"></i> Finance Report</a>
    <a href="bulk_import.php" class="nav-link"><i class="fa fa-upload"></i> Bulk Import</a>
    <div class="nav-section">Tools</div>
    <a href="admin_live_sessions.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions</a>
    <a href="admin_switch.php" class="nav-link"><i class="fa fa-exchange-alt"></i> Switch User</a>
    <a href="reminders.php" class="nav-link"><i class="fa fa-bell"></i> Reminders</a>
    <a href="whatsapp_messages.php" class="nav-link"><i class="fab fa-whatsapp"></i> Messages</a>
    <a href="create_admin.php" class="nav-link"><i class="fa fa-user-plus"></i> Create Admin</a>
    <a href="admin_change_password.php" class="nav-link"><i class="fa fa-key"></i> Change Password</a>
    <div class="nav-section">Portal</div>
    <a href="admin_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">
    <div class="page-title mb-4">Manage Scheme of Work</div>

    <?php if ($flash): ?>
      <div class="alert alert-info border-0 shadow-sm p-3 mb-4" style="border-radius: 12px;">
        <i class="fa fa-info-circle me-1"></i> <?= e($flash) ?>
      </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <div class="lms-card shadow-sm p-4 mb-4">
      <form method="get" class="row g-3">
        <div class="col-md-5">
          <label class="form-label fw-semibold">Select Affiliate Course</label>
          <select name="course_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Choose Course --</option>
            <?php foreach ($affiliateCourses as $ac): ?>
              <option value="<?= (int)$ac['id'] ?>" <?= $selectedCourseId === (int)$ac['id'] ? 'selected' : '' ?>><?= e($ac['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Class Level</label>
          <select name="class_level" class="form-select" onchange="this.form.submit()">
            <?php foreach ($allowedLevels as $lvl): ?>
              <option value="<?= e($lvl) ?>" <?= $selectedClassLevel === $lvl ? 'selected' : '' ?>><?= e($lvl) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Term</label>
          <select name="term" class="form-select" onchange="this.form.submit()">
            <?php foreach ($allowedTerms as $t): ?>
              <option value="<?= e($t) ?>" <?= $selectedTerm === $t ? 'selected' : '' ?>><?= e($t) ?> Term</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-dark w-100 py-2"><i class="fa fa-filter"></i></button>
        </div>
      </form>
    </div>

    <div class="row g-4">
      <!-- FORM -->
      <div class="col-lg-4">
        <div class="lms-card shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-dark"><?= $editWeek ? 'Edit Week Details' : 'Add Scheme Week' ?></h5>
          <form method="post">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="<?= $editWeek ? 'update' : 'create' ?>">
            <input type="hidden" name="course_id" value="<?= $selectedCourseId ?>">
            <input type="hidden" name="class_level" value="<?= $selectedClassLevel ?>">
            <input type="hidden" name="term" value="<?= $selectedTerm ?>">
            <?php if ($editWeek): ?>
              <input type="hidden" name="id" value="<?= (int)$editWeek['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label fw-semibold">Week Number <span class="text-danger">*</span></label>
              <input type="number" min="1" max="52" class="form-control py-2" name="week_number" required value="<?= e($editWeek['week_number'] ?? (count($weeks) + 1)) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Topic <span class="text-danger">*</span></label>
              <input class="form-control py-2" name="topic" required value="<?= e($editWeek['topic'] ?? '') ?>" placeholder="e.g. Introduction to Scratch Coding">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Objectives (HTML/Text)</label>
              <textarea class="form-control" name="objectives" rows="4" placeholder="e.g. <ul><li>Define block programming</li></ul>"><?= e($editWeek['objectives'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Activities (HTML/Text)</label>
              <textarea class="form-control" name="activities" rows="3" placeholder="e.g. <ul><li>Demonstrate Scratch drag and drop</li></ul>"><?= e($editWeek['activities'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Lecture Content (Markdown/HTML)</label>
              <textarea class="form-control" name="lecture_content" rows="6" placeholder="Write rich lecture notes here..."><?= e($editWeek['lecture_content'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Quiz JSON Schema</label>
              <textarea class="form-control" name="quiz_json" rows="6" style="font-family: monospace; font-size: .8rem;" placeholder='{
  "title": "Topic Quiz",
  "instructions": "Select correct options",
  "pass_score": 50,
  "questions": [
    {
      "question": "What is 2+2?",
      "option_a": "3",
      "option_b": "4",
      "option_c": "5",
      "option_d": "6",
      "correct_option": "B"
    }
  ]
}'><?= e($editWeek['quiz_json'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
              <?php if ($editWeek): ?>
                <a href="admin_affiliate_scheme.php?course_id=<?= $selectedCourseId ?>&class_level=<?= $selectedClassLevel ?>&term=<?= $selectedTerm ?>" class="btn btn-outline-secondary px-3 fw-semibold" style="border-radius: 8px;">Cancel</a>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary px-3 fw-semibold" style="border-radius: 8px;">
                <?= $editWeek ? 'Save changes' : 'Add Week' ?>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- LIST -->
      <div class="col-lg-8">
        <div class="lms-card shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-dark">Syllabus Grid &mdash; <?= e($selectedClassLevel) ?> (<?= e($selectedTerm) ?> Term)</h5>

          <div class="table-responsive" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            <table class="table align-middle" style="margin-bottom:0;">
              <thead class="table-light">
                <tr>
                  <th style="width:70px">Week</th>
                  <th>Topic & objectives</th>
                  <th>Activities</th>
                  <th class="text-end" style="width:100px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($weeks)): ?>
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No weeks defined for this selection. Use the form to add a week.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($weeks as $w): ?>
                    <tr>
                      <td class="text-center fw-bold text-primary">W<?= (int)$w['week_number'] ?></td>
                      <td>
                        <div class="fw-bold text-dark mb-1"><?= e($w['topic']) ?></div>
                        <div class="text-muted small"><?= $w['objectives'] ?></div>
                      </td>
                      <td class="text-muted small"><?= $w['activities'] ?></td>
                      <td class="text-end">
                        <div class="btn-group gap-1">
                          <a href="admin_affiliate_scheme.php?course_id=<?= $selectedCourseId ?>&class_level=<?= $selectedClassLevel ?>&term=<?= $selectedTerm ?>&edit=<?= (int)$w['id'] ?>" class="btn btn-outline-primary btn-sm px-2" style="border-radius: 6px;" title="Edit">
                            <i class="fa fa-edit"></i>
                          </a>
                          <form method="post" class="d-inline" onsubmit="confirmDeleteSow(event, this);">
                            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$w['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-2" style="border-radius: 6px;" title="Delete">
                              <i class="fa fa-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDeleteSow(event, form) {
  event.preventDefault();
  Swal.fire({
    title: 'Delete Week SOW?',
    text: 'Are you sure you want to delete this week SOW? This cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}
</script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});
</script>
</body>
</html>
