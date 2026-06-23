<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'];

$flashSuccess = $_SESSION['admin_ins_success'] ?? null;
$flashError = $_SESSION['admin_ins_error'] ?? null;
unset($_SESSION['admin_ins_success'], $_SESSION['admin_ins_error']);

// ─── Actions Handler ───
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $fullName       = trim((string)($_POST['full_name'] ?? ''));
        $email          = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone          = trim((string)($_POST['phone'] ?? ''));
        $specialization = trim((string)($_POST['specialization'] ?? ''));
        $qualification  = trim((string)($_POST['qualification'] ?? ''));
        $experienceYrs  = (int)($_POST['experience_years'] ?? 0);
        $linkedinUrl    = trim((string)($_POST['linkedin_url'] ?? ''));
        $bio            = trim((string)($_POST['bio'] ?? ''));
        $password       = (string)($_POST['password'] ?? '');

        if ($fullName === '' || !$email || $password === '' || $specialization === '') {
            $_SESSION['admin_ins_error'] = 'Please fill out all required fields (Name, valid Email, Password, Specialization).';
            redirect('admin_instructors.php');
        }

        // Check duplicate email
        $check = $pdo->prepare("SELECT id FROM lms_instructors WHERE email=? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['admin_ins_error'] = 'An instructor account with this email already exists.';
            redirect('admin_instructors.php');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO lms_instructors 
                    (full_name, email, phone, password, bio, specialization, qualification, experience_years, linkedin_url, status, availability_status, created_at)
                VALUES (?,?,?,?,?,?,?,?,?, 'active', 'available', NOW())
            ");
            $stmt->execute([
                $fullName, $email, $phone ?: null, $hash, $bio ?: null, $specialization, $qualification ?: null, $experienceYrs, $linkedinUrl ?: null
            ]);

            $_SESSION['admin_ins_success'] = 'Instructor registered successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_ins_error'] = 'Failed to register instructor: ' . $e->getMessage();
        }
        redirect('admin_instructors.php');
    }

    if ($action === 'update') {
        $insId          = (int)($_POST['instructor_id'] ?? 0);
        $fullName       = trim((string)($_POST['full_name'] ?? ''));
        $phone          = trim((string)($_POST['phone'] ?? ''));
        $specialization = trim((string)($_POST['specialization'] ?? ''));
        $qualification  = trim((string)($_POST['qualification'] ?? ''));
        $experienceYrs  = (int)($_POST['experience_years'] ?? 0);
        $linkedinUrl    = trim((string)($_POST['linkedin_url'] ?? ''));
        $bio            = trim((string)($_POST['bio'] ?? ''));
        $status         = trim((string)($_POST['status'] ?? 'active'));
        $availability   = trim((string)($_POST['availability_status'] ?? 'available'));

        if ($insId <= 0 || $fullName === '' || $specialization === '') {
            $_SESSION['admin_ins_error'] = 'Invalid request. Name and Specialization are required.';
            redirect('admin_instructors.php');
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE lms_instructors 
                SET full_name = ?, phone = ?, specialization = ?, qualification = ?, experience_years = ?, linkedin_url = ?, bio = ?, status = ?, availability_status = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $fullName, $phone ?: null, $specialization, $qualification ?: null, $experienceYrs, $linkedinUrl ?: null, $bio ?: null, $status, $availability, $insId
            ]);

            $_SESSION['admin_ins_success'] = 'Instructor profile updated successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_ins_error'] = 'Failed to update profile: ' . $e->getMessage();
        }
        redirect('admin_instructors.php');
    }

    if ($action === 'assign_course') {
        $insId    = (int)($_POST['instructor_id'] ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);

        if ($insId <= 0 || $courseId <= 0) {
            $_SESSION['admin_ins_error'] = 'Invalid course assignment params.';
            redirect('admin_instructors.php');
        }

        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO lms_instructor_courses (instructor_id, course_id) VALUES (?,?)");
            $stmt->execute([$insId, $courseId]);
            $_SESSION['admin_ins_success'] = 'Course assigned successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_ins_error'] = 'Failed to assign course: ' . $e->getMessage();
        }
        redirect('admin_instructors.php');
    }

    if ($action === 'unassign_course') {
        $insId    = (int)($_POST['instructor_id'] ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);

        if ($insId <= 0 || $courseId <= 0) {
            $_SESSION['admin_ins_error'] = 'Invalid parameters for unassignment.';
            redirect('admin_instructors.php');
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM lms_instructor_courses WHERE instructor_id = ? AND course_id = ?");
            $stmt->execute([$insId, $courseId]);
            $_SESSION['admin_ins_success'] = 'Course unassigned successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_ins_error'] = 'Failed to unassign course: ' . $e->getMessage();
        }
        redirect('admin_instructors.php');
    }

    if ($action === 'delete') {
        $insId = (int)($_POST['instructor_id'] ?? 0);

        if ($insId <= 0) {
            $_SESSION['admin_ins_error'] = 'Invalid instructor selection.';
            redirect('admin_instructors.php');
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM lms_instructors WHERE id = ?");
            $stmt->execute([$insId]);
            $_SESSION['admin_ins_success'] = 'Instructor deleted successfully.';
        } catch (Throwable $e) {
            $_SESSION['admin_ins_error'] = 'Failed to delete instructor: ' . $e->getMessage();
        }
        redirect('admin_instructors.php');
    }
}

// ─── Fetch Search, Filter & Data ───
$search = trim((string)($_GET['q'] ?? ''));
$statusFilter = trim((string)($_GET['status'] ?? ''));
$availFilter = trim((string)($_GET['availability'] ?? ''));

$sql = "
    SELECT i.*, 
      (SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id = i.id) AS course_count,
      (SELECT COUNT(l.id) FROM lms_lessons l JOIN lms_instructor_courses ic ON ic.course_id = l.course_id WHERE ic.instructor_id = i.id) AS lesson_count,
      (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id = i.id) AS total_submissions,
      (SELECT COUNT(s.id) FROM lms_assignment_submissions s JOIN lms_assignments a ON a.id = s.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id = i.id AND s.score IS NOT NULL) AS graded_submissions
    FROM lms_instructors i
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (i.full_name LIKE ? OR i.email LIKE ? OR i.specialization LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($statusFilter !== '') {
    $sql .= " AND i.status = ?";
    $params[] = $statusFilter;
}

if ($availFilter !== '') {
    $sql .= " AND i.availability_status = ?";
    $params[] = $availFilter;
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all courses for mapping drop-downs
$courses = $pdo->query("SELECT id, title FROM lms_courses ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all assigned courses for easy listing
$assignedCoursesRaw = $pdo->query("
    SELECT ic.instructor_id, c.id AS course_id, c.title 
    FROM lms_instructor_courses ic
    JOIN lms_courses c ON c.id = ic.course_id
")->fetchAll(PDO::FETCH_ASSOC);

$assignedMap = [];
foreach ($assignedCoursesRaw as $row) {
    $assignedMap[(int)$row['instructor_id']][] = [
        'id' => (int)$row['course_id'],
        'title' => $row['title']
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructors Manager';
$seoDesc    = 'Manage instructor availability, course assignments, and view their grading performance.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body { background: #f7fbff; font-family: Inter, system-ui; }
  .navbar-admin { background: #0f172a !important; }
  .card-instructor {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: transform .2s, box-shadow .2s;
  }
  .card-instructor:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  .avatar-wrap {
    position: relative;
    width: 68px;
    height: 68px;
    flex-shrink: 0;
  }
  .avatar-img {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    object-fit: cover;
    background: #e2e8f0;
    border: 2px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }
  .avatar-placeholder {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: var(--brand-light);
    color: var(--brand);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.5rem;
    border: 2px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }
  .badge-avail {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
  }
  .badge-avail-available { background: var(--success); }
  .badge-avail-busy { background: var(--warning); }
  .badge-avail-leave { background: var(--danger); }
  .course-pill {
    background: var(--brand-light);
    color: var(--brand);
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    margin: 2px;
  }
  .course-pill-remove {
    cursor: pointer;
    color: var(--danger);
    font-weight: bold;
    display: inline-flex;
    align-items: center;
  }
  .course-pill-remove:hover {
    color: #991b1b;
  }
  .metric-label { font-size: 0.76rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
  .metric-val { font-size: 1.1rem; font-weight: 800; color: var(--dark); }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top shadow-sm py-2">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="admin_dashboard.php">
      <div style="width:30px;height:30px;background:rgba(255,255,255,.15);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.8rem">A</div>
      <span>Admin <span class="text-indigo-300" style="color:#a5b4fc">Panel</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item"><a class="nav-link text-white small" href="admin_dashboard.php"><i class="fa fa-th-large me-1"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-white small active" href="admin_instructors.php"><i class="fa fa-chalkboard-teacher me-1"></i>Instructors</a></li>
        <li class="nav-item"><a class="nav-link text-white small" href="admin_courses.php"><i class="fa fa-book me-1"></i>Courses</a></li>
        <li class="nav-item"><a class="btn btn-danger btn-sm text-white ms-2" href="admin_logout.php"><i class="fa fa-sign-out-alt me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">

  <!-- Alert System -->
  <?php if ($flashSuccess): ?>
    <div class="alert alert-success"><?= e($flashSuccess) ?></div>
  <?php endif; ?>
  <?php if ($flashError): ?>
    <div class="alert alert-danger"><?= e($flashError) ?></div>
  <?php endif; ?>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0">Instructors Management</h3>
      <p class="text-muted small mb-0">Monitor instructor profiles, status, availability, and course metrics.</p>
    </div>
    <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalRegister">
      <i class="fa fa-user-plus"></i> Register Instructor
    </button>
  </div>

  <!-- SEARCH & FILTER BAR -->
  <div class="card p-3 mb-4 border-0 shadow-sm" style="border-radius:12px;">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
          <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Search by name, email, specialization..." value="<?= e($search) ?>">
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="">-- All Statuses --</option>
          <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
          <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
      </div>
      <div class="col-md-3 col-sm-6">
        <select name="availability" class="form-select" onchange="this.form.submit()">
          <option value="">-- All Availabilities --</option>
          <option value="available" <?= $availFilter === 'available' ? 'selected' : '' ?>>Available</option>
          <option value="busy" <?= $availFilter === 'busy' ? 'selected' : '' ?>>Busy</option>
          <option value="leave" <?= $availFilter === 'leave' ? 'selected' : '' ?>>On Leave</option>
        </select>
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
      </div>
    </form>
  </div>

  <!-- INSTRUCTOR LIST GRID -->
  <?php if (empty($instructors)): ?>
    <div class="card p-5 text-center shadow-sm">
      <div class="text-muted mb-2"><i class="fa fa-users fa-3x"></i></div>
      <h5>No instructors found matching filters.</h5>
      <p class="text-muted small">Try modifying search tags or registering a new instructor.</p>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($instructors as $i):
        $insId = (int)$i['id'];
        $insCourses = $assignedMap[$insId] ?? [];
        $initials = '';
        if (!empty($i['full_name'])) {
            $words = explode(' ', $i['full_name']);
            $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
        }
        
        $subGraded = (int)$i['graded_submissions'];
        $subTotal = (int)$i['total_submissions'];
        $percentGraded = $subTotal > 0 ? (int)round(($subGraded / $subTotal) * 100) : 0;
        
        // Availability formatting
        $avail = (string)($i['availability_status'] ?? 'available');
        $availText = match($avail) {
          'busy' => 'Busy',
          'leave' => 'On Leave',
          default => 'Available'
        };
        $availBadge = match($avail) {
          'busy' => 'badge-avail-busy',
          'leave' => 'badge-avail-leave',
          default => 'badge-avail-available'
        };

        // Status badge
        $st = (string)($i['status'] ?? 'active');
        $statusBadge = match($st) {
          'active' => 'badge-success',
          'suspended' => 'badge-danger',
          default => 'badge-warning'
        };
      ?>
        <div class="col-lg-6">
          <div class="card-instructor p-4 h-100">
            <div class="d-flex align-items-start gap-3">
              <!-- Avatar -->
              <div class="avatar-wrap">
                <?php if (!empty($i['photo'])): ?>
                  <img src="uploads/<?= e($i['photo']) ?>" alt="<?= e($i['full_name']) ?>" class="avatar-img">
                <?php else: ?>
                  <div class="avatar-placeholder"><?= $initials ?></div>
                <?php endif; ?>
                <span class="badge-avail <?= $availBadge ?>" title="Status: <?= $availText ?>"></span>
              </div>

              <!-- General Info -->
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                  <h5 class="fw-bold mb-0 text-truncate" style="max-width:180px;"><?= e($i['full_name']) ?></h5>
                  <span class="badge <?= $statusBadge ?>"><?= ucfirst($st) ?></span>
                </div>
                <div class="text-indigo-600 fw-semibold small mb-1"><?= e($i['specialization']) ?></div>
                <div class="text-muted small mb-2"><i class="fa fa-envelope me-1"></i><?= e($i['email']) ?></div>
                <div class="text-muted small"><i class="fa fa-phone me-1"></i><?= e($i['phone'] ?? 'No phone added') ?></div>
              </div>
            </div>

            <hr class="my-3 text-muted" style="opacity:0.15;">

            <!-- Performance Metrics -->
            <div class="row g-2 text-center mb-3">
              <div class="col-4">
                <div class="metric-label">Courses</div>
                <div class="metric-val"><?= (int)$i['course_count'] ?></div>
              </div>
              <div class="col-4">
                <div class="metric-label">Lessons</div>
                <div class="metric-val"><?= (int)$i['lesson_count'] ?></div>
              </div>
              <div class="col-4">
                <div class="metric-label">Graded</div>
                <div class="metric-val"><?= $subGraded ?> / <?= $subTotal ?></div>
              </div>
            </div>

            <!-- Grading Performance Bar -->
            <div class="mb-3">
              <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Grading Completion Rate</span>
                <span class="fw-bold text-dark"><?= $percentGraded ?>%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-indigo" role="progressbar" style="width: <?= $percentGraded ?>%; background-color: var(--brand);" aria-valuenow="<?= $percentGraded ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>

            <!-- Assigned Courses Section -->
            <div class="mb-3">
              <div class="small fw-semibold text-dark mb-1">Assigned Courses:</div>
              <div class="d-flex flex-wrap align-items-center">
                <?php if (empty($insCourses)): ?>
                  <span class="text-muted small italic">No courses assigned yet.</span>
                <?php else: ?>
                  <?php foreach ($insCourses as $c): ?>
                    <span class="course-pill">
                      <?= e($c['title']) ?>
                      <form method="post" action="admin_instructors.php" class="d-inline" onsubmit="return confirm('Remove course assignment?')">
                        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="action" value="unassign_course">
                        <input type="hidden" name="instructor_id" value="<?= $insId ?>">
                        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="course-pill-remove border-0 bg-transparent p-0"><i class="fa fa-times-circle"></i></button>
                      </form>
                    </span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $insId ?>">
                <i class="fa fa-edit me-1"></i> Edit Profile
              </button>
              <button class="btn btn-outline-primary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalAssign<?= $insId ?>">
                <i class="fa fa-plus me-1"></i> Assign Course
              </button>
              <form method="post" action="admin_instructors.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this instructor? This action is permanent!')">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="instructor_id" value="<?= $insId ?>">
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete instructor"><i class="fa fa-trash"></i></button>
              </form>
            </div>
          </div>
        </div>

        <!-- EDIT INSTRUCTOR MODAL -->
        <div class="modal fade" id="modalEdit<?= $insId ?>" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Instructor Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post" action="admin_instructors.php">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="instructor_id" value="<?= $insId ?>">
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Full Name</label>
                      <input type="text" name="full_name" class="form-control" value="<?= e($i['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Phone</label>
                      <input type="text" name="phone" class="form-control" value="<?= e($i['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Specialization</label>
                      <input type="text" name="specialization" class="form-control" value="<?= e($i['specialization'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Qualification</label>
                      <input type="text" name="qualification" class="form-control" value="<?= e($i['qualification'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Experience (Years)</label>
                      <input type="number" name="experience_years" class="form-control" value="<?= (int)($i['experience_years'] ?? 0) ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Status</label>
                      <select name="status" class="form-select">
                        <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="suspended" <?= $st === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        <option value="pending" <?= $st === 'pending' ? 'selected' : '' ?>>Pending</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Availability Status</label>
                      <select name="availability_status" class="form-select">
                        <option value="available" <?= $avail === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="busy" <?= $avail === 'busy' ? 'selected' : '' ?>>Busy</option>
                        <option value="leave" <?= $avail === 'leave' ? 'selected' : '' ?>>On Leave</option>
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label">LinkedIn Profile URL</label>
                      <input type="url" name="linkedin_url" class="form-control" value="<?= e($i['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                    </div>
                    <div class="col-12">
                      <label class="form-label">Bio (Brief Summary)</label>
                      <textarea name="bio" rows="4" class="form-control"><?= e($i['bio'] ?? '') ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- ASSIGN COURSE MODAL -->
        <div class="modal fade" id="modalAssign<?= $insId ?>" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fw-bold">Assign Course to <?= e($i['full_name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post" action="admin_instructors.php">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="assign_course">
                <input type="hidden" name="instructor_id" value="<?= $insId ?>">
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Select Course</label>
                    <select name="course_id" class="form-select" required>
                      <option value="">-- Choose Course --</option>
                      <?php 
                      // Filter courses to only show ones NOT already assigned to this instructor
                      $assignedIds = array_column($insCourses, 'id');
                      foreach ($courses as $c):
                        if (in_array((int)$c['id'], $assignedIds, true)) continue;
                      ?>
                        <option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Assign</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- REGISTER INSTRUCTOR MODAL -->
<div class="modal fade" id="modalRegister" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Register New Instructor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="admin_instructors.php">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="create">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name *</label>
              <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address *</label>
              <input type="email" name="email" class="form-control" placeholder="johndoe@example.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" placeholder="+234...">
            </div>
            <div class="col-md-6">
              <label class="form-label">Specialization *</label>
              <input type="text" name="specialization" class="form-control" placeholder="Python for Data Science" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Highest Qualification</label>
              <input type="text" name="qualification" class="form-control" placeholder="M.Sc. Computer Science">
            </div>
            <div class="col-md-3">
              <label class="form-label">Experience (Years)</label>
              <input type="number" name="experience_years" class="form-control" placeholder="3" min="0">
            </div>
            <div class="col-md-3">
              <label class="form-label">Password *</label>
              <input type="password" name="password" class="form-control" required placeholder="Min 8 chars">
            </div>
            <div class="col-12">
              <label class="form-label">LinkedIn Profile URL</label>
              <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/username">
            </div>
            <div class="col-12">
              <label class="form-label">Short Bio</label>
              <textarea name="bio" rows="3" class="form-control" placeholder="Introduce the instructor's background..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Register Instructor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
