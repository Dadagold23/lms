<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'];

$flash = $_SESSION['admin_aff_course_flash'] ?? null;
unset($_SESSION['admin_aff_course_flash']);

/* ======================
   HANDLE POST ACTIONS
 ====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    $id        = (int)($_POST['id'] ?? 0);
    $title     = trim((string)($_POST['title'] ?? ''));
    $slug      = trim((string)($_POST['slug'] ?? ''));
    $desc      = trim((string)($_POST['description'] ?? ''));
    $shortDesc = trim((string)($_POST['short_description'] ?? ''));
    $price     = (float)($_POST['price'] ?? 0);
    $level     = trim((string)($_POST['level'] ?? 'beginner'));
    $category  = trim((string)($_POST['category'] ?? ''));
    $isActive  = isset($_POST['is_active']) ? 1 : 0;

    if ($action === 'create') {
        if ($title === '' || $slug === '' || $price <= 0) {
            $_SESSION['admin_aff_course_flash'] = 'Title, slug, and price are required.';
            redirect('admin_affiliate_courses.php');
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO lms_affiliate_courses (title, slug, description, short_description, price, level, category, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$title, $slug, $desc, $shortDesc, $price, $level, $category, $isActive]);
            $_SESSION['admin_aff_course_flash'] = 'Affiliate course created successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_aff_course_flash'] = 'Error creating course: ' . $e->getMessage();
        }
        redirect('admin_affiliate_courses.php');
    }

    if ($action === 'update') {
        if ($id <= 0 || $title === '' || $slug === '' || $price <= 0) {
            $_SESSION['admin_aff_course_flash'] = 'Invalid data. Title, slug, and price are required.';
            redirect('admin_affiliate_courses.php');
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE lms_affiliate_courses
                SET title = ?, slug = ?, description = ?, short_description = ?, price = ?, level = ?, category = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $slug, $desc, $shortDesc, $price, $level, $category, $isActive, $id]);
            $_SESSION['admin_aff_course_flash'] = 'Affiliate course updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_aff_course_flash'] = 'Error updating course: ' . $e->getMessage();
        }
        redirect('admin_affiliate_courses.php');
    }

    if ($action === 'delete') {
        if ($id <= 0) {
            $_SESSION['admin_aff_course_flash'] = 'Invalid course ID.';
            redirect('admin_affiliate_courses.php');
        }
        try {
            $pdo->prepare("DELETE FROM lms_affiliate_courses WHERE id = ?")->execute([$id]);
            $_SESSION['admin_aff_course_flash'] = 'Affiliate course deleted successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_aff_course_flash'] = 'Error deleting course: ' . $e->getMessage();
        }
        redirect('admin_affiliate_courses.php');
    }
}

/* ======================
   LOAD COURSES
 ====================== */
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM lms_affiliate_courses
        WHERE title LIKE ? OR category LIKE ? OR level LIKE ?
        ORDER BY created_at DESC
    ");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $courses = $pdo->query("SELECT * FROM lms_affiliate_courses ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$editId = (int)($_GET['edit'] ?? 0);
$editCourse = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM lms_affiliate_courses WHERE id = ? LIMIT 1");
    $stmt->execute([$editId]);
    $editCourse = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Manage Affiliate Courses';
$seoDesc    = 'Admin management dashboard for Grafix@Mirror LMS affiliate curriculum courses.';
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
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
      <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
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
  <aside class="lms-sidebar">
    <div class="nav-section">Overview</div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="analytics.php" class="nav-link"><i class="fa fa-chart-bar"></i> Analytics</a>
    <div class="nav-section">Management</div>
    <a href="admin_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="admin_instructors.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Instructors</a>
    <a href="admin_partners.php" class="nav-link"><i class="fa fa-handshake"></i> Affiliate</a>
    <a href="admin_affiliate_courses.php" class="nav-link active"><i class="fa fa-book-open"></i> Affiliate Courses</a>
    <a href="admin_affiliate_scheme.php" class="nav-link"><i class="fa fa-scroll"></i> Scheme of Work</a>
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
    <div class="page-title mb-4">Manage Affiliate Courses</div>

    <?php if ($flash): ?>
      <div class="alert alert-info border-0 shadow-sm p-3 mb-4" style="border-radius: 12px;">
        <i class="fa fa-info-circle me-1"></i> <?= e($flash) ?>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- FORM -->
      <div class="col-lg-5">
        <div class="lms-card shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-dark"><?= $editCourse ? 'Edit Course' : 'Create Affiliate Course' ?></h5>
          <form method="post">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="<?= $editCourse ? 'update' : 'create' ?>">
            <?php if ($editCourse): ?>
              <input type="hidden" name="id" value="<?= (int)$editCourse['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
              <input class="form-control py-2" name="title" required value="<?= e($editCourse['title'] ?? '') ?>" placeholder="e.g. Intro to Computer Studies">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
              <input class="form-control py-2" name="slug" required value="<?= e($editCourse['slug'] ?? '') ?>" placeholder="e.g. intro-computer-studies">
              <div class="form-text small text-muted">URL friendly identifier. Must be unique.</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Short Description</label>
              <input class="form-control py-2" name="short_description" value="<?= e($editCourse['short_description'] ?? '') ?>" placeholder="Brief one-line summary">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea class="form-control" name="description" rows="3" placeholder="Full course syllabus overview"><?= e($editCourse['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Price (₦) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control py-2" name="price" required value="<?= e($editCourse['price'] ?? '150000.00') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Level</label>
                <select class="form-select py-2" name="level">
                  <option value="beginner" <?= ($editCourse['level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                  <option value="intermediate" <?= ($editCourse['level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                  <option value="advanced" <?= ($editCourse['level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Category</label>
              <input class="form-control py-2" name="category" value="<?= e($editCourse['category'] ?? '') ?>" placeholder="e.g. IT Literacy">
            </div>

            <div class="mb-3 form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" <?= ($editCourse['is_active'] ?? 1) ? 'checked' : '' ?>>
              <label class="form-check-label fw-semibold" for="isActiveSwitch">Course is Active / Visible</label>
            </div>

            <div class="d-flex gap-2 justify-content-end">
              <?php if ($editCourse): ?>
                <a href="admin_affiliate_courses.php" class="btn btn-outline-secondary px-4 fw-semibold" style="border-radius: 8px;">Cancel</a>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px;">
                <?= $editCourse ? 'Save Changes' : 'Create Course' ?>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- LIST -->
      <div class="col-lg-7">
        <div class="lms-card shadow-sm p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-dark">Existing Affiliate Courses</h5>
            <form method="get" class="d-flex gap-2">
              <input class="form-control form-control-sm py-1 px-2" name="q" placeholder="Search..." value="<?= e($q) ?>" style="max-width: 180px;">
              <button type="submit" class="btn btn-dark btn-sm"><i class="fa fa-search"></i></button>
            </form>
          </div>

          <div class="table-responsive" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            <table class="table align-middle" style="margin-bottom:0;">
              <thead class="table-light">
                <tr>
                  <th>Course Title</th>
                  <th>Price</th>
                  <th>Level / Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($courses)): ?>
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No affiliate courses found.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($courses as $c): ?>
                    <tr>
                      <td>
                        <div class="fw-bold text-dark"><?= e($c['title']) ?></div>
                        <div class="text-muted small"><?= e($c['category']) ?> (<?= e($c['slug']) ?>)</div>
                      </td>
                      <td class="fw-bold text-indigo">₦<?= number_format((float)$c['price'], 2) ?></td>
                      <td>
                        <span class="badge bg-secondary text-capitalize small"><?= e($c['level']) ?></span>
                        <span class="badge bg-<?= $c['is_active'] ? 'success' : 'danger' ?> small">
                          <?= $c['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <div class="btn-group gap-1">
                          <a href="admin_affiliate_courses.php?edit=<?= (int)$c['id'] ?>" class="btn btn-outline-primary btn-sm px-2" style="border-radius: 6px;" title="Edit">
                            <i class="fa fa-edit"></i>
                          </a>
                          <form method="post" class="d-inline" onsubmit="confirmDeleteAffiliateCourse(event, this);">
                            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
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
function confirmDeleteAffiliateCourse(event, form) {
  event.preventDefault();
  Swal.fire({
    title: 'Delete this course?',
    text: 'Are you sure you want to delete this course? This will delete all linked schemes of work.',
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
</body>
</html>
