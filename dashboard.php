<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
startSecureSession();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/includes/student_notifications.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireLogin();
$studentId = (int)($_SESSION['user']['id'] ?? 0);
if ($studentId <= 0) redirect('login.php');

// Auto-award eligible credentials (certificates and badges)
autoAwardCredentials($studentId, $pdo);


/* ── Student info ── */
$st = $pdo->prepare("SELECT * FROM lms_students WHERE id=? LIMIT 1");
$st->execute([$studentId]);
$student = $st->fetch() ?: [];
$name = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));

$isAffiliate = !empty($student['is_affiliate']);
$affiliateClassRange = $student['affiliate_class_range'] ?? '';
$affiliateClassLevel = $student['affiliate_class_level'] ?? '';
$affiliateCourseId   = (int)($student['affiliate_course_id'] ?? 0);
$affiliateCourse     = null;
$affiliateScheme     = [];
$affiliatePartner    = null;

if ($isAffiliate && ($affiliateClassLevel === '' || $affiliateClassRange === '')) {
    $dobVal = $student['dob'] ?? '';
    if ($dobVal !== '') {
        try {
            $dobDate = new DateTime($dobVal);
            $today   = new DateTime();
            $age     = (int)$today->diff($dobDate)->y;
            
            if ($age >= 6 && $age <= 11) {
                $affiliateClassRange = 'JSS';
                $affiliateClassLevel = 'JSS1';
            } elseif ($age >= 12 && $age <= 19) {
                $affiliateClassRange = 'SSS';
                $affiliateClassLevel = 'SSS1';
            } else {
                $affiliateClassRange = 'Higher';
                $affiliateClassLevel = 'Higher';
            }
            
            // Persist the computed values so they are permanently fixed
            $updateStmt = $pdo->prepare("
                UPDATE lms_students 
                SET affiliate_class_range = ?, affiliate_class_level = ? 
                WHERE id = ?
            ");
            $updateStmt->execute([$affiliateClassRange, $affiliateClassLevel, $studentId]);
        } catch (Throwable $e) {
            error_log("Failed to auto-resolve affiliate class: " . $e->getMessage());
        }
    }
}

if ($isAffiliate && $affiliateCourseId > 0) {
    $acStmt = $pdo->prepare("SELECT * FROM lms_affiliate_courses WHERE id = ? LIMIT 1");
    $acStmt->execute([$affiliateCourseId]);
    $affiliateCourse = $acStmt->fetch() ?: null;

    // Load current term scheme (1st term by default)
    if ($affiliateCourse && in_array($affiliateClassLevel, ['JSS1','JSS2','JSS3','SSS1','SSS2','SSS3'], true)) {
        $sowStmt = $pdo->prepare("
            SELECT week_number, topic, objectives, activities
            FROM lms_affiliate_scheme_of_work
            WHERE course_id = ? AND class_level = ?
            ORDER BY
              CASE term WHEN '1st' THEN 1 WHEN '2nd' THEN 2 WHEN '3rd' THEN 3 END,
              week_number ASC
        ");
        $sowStmt->execute([$affiliateCourseId, $affiliateClassLevel]);
        $sowRaw = $sowStmt->fetchAll(PDO::FETCH_ASSOC);
        // Group by term
        foreach ($sowRaw as $row) {
            // We need term info - re-fetch with term
        }
        // Simpler: fetch all grouped
        $sowStmt2 = $pdo->prepare("
            SELECT term, week_number, topic, objectives
            FROM lms_affiliate_scheme_of_work
            WHERE course_id = ? AND class_level = ?
            ORDER BY
              CASE term WHEN '1st' THEN 1 WHEN '2nd' THEN 2 WHEN '3rd' THEN 3 END,
              week_number ASC
        ");
        $sowStmt2->execute([$affiliateCourseId, $affiliateClassLevel]);
        foreach ($sowStmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $affiliateScheme[$row['term']][] = $row;
        }
    }

    // Find the referring partner via referral record
    $refStmt = $pdo->prepare("
        SELECT p.name AS partner_name, p.partner_type, r.status, r.created_at, r.referral_token
        FROM lms_affiliate_referrals r
        JOIN lms_affiliate_partners p ON p.id = r.partner_id
        WHERE r.pupil_email = ?
        ORDER BY r.created_at DESC
        LIMIT 1
    ");
    $refStmt->execute([$student['email'] ?? '']);
    $affiliatePartner = $refStmt->fetch() ?: null;
}

$firstName = $student['first_name'] ?? 'Student';
/* ── Enrolled courses (with enrollment + payment info) ── */
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.slug, c.price, c.level, c.intro_video" . workspaceCourseSelectSql($pdo, 'c') . ",
           e.id AS enrollment_id, e.paid_amount, e.payment_type,
           e.status AS enroll_status, e.next_due_date, e.access_expires_at,
           e.assigned_instructor_id,
           ins.full_name AS instructor_name, ins.photo AS instructor_photo
    FROM lms_courses c
    INNER JOIN lms_enrollments e ON e.course_id = c.id
    LEFT JOIN lms_instructors ins ON e.assigned_instructor_id = ins.id
    WHERE e.student_id = ?
    ORDER BY e.created_at ASC
");
$stmt->execute([$studentId]);
$myCourses = $stmt->fetchAll();

/* ── Available courses (not enrolled) ── */
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.slug, c.price, c.level, c.short_description
    FROM lms_courses c
    WHERE c.is_active = 1
      AND c.id NOT IN (SELECT course_id FROM lms_enrollments WHERE student_id = ?)
    ORDER BY c.title
");
$stmt->execute([$studentId]);
$availableCourses = $stmt->fetchAll();

/* ── Stats ── */
$totalPaid = (float)$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE student_id=? AND status='success'")
    ->execute([$studentId]) ? $pdo->query("SELECT COALESCE(SUM(amount),0) FROM lms_payments WHERE student_id={$studentId} AND status='success'")->fetchColumn() : 0;
$certCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_certificates WHERE student_id={$studentId}")->fetchColumn();
$badgeCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_student_badges WHERE student_id={$studentId}")->fetchColumn();
$unreadNotifications = countUnreadStudentNotifications($pdo, $studentId);
$notifications = getStudentNotifications($pdo, $studentId, 6);
markStudentNotificationsRead($pdo, $studentId);
$isAdminImpersonating = !empty($_SESSION['admin_backup']) && !empty($_SESSION['user']['switched']);

$firstName = $student['first_name'] ?? 'Student';
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'My Dashboard';
$seoDesc    = 'Your Grafix@Mirror LMS student dashboard — view enrolled courses, track progress, access lessons, videos, assignments and exams.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="description" content="Student dashboard — manage your enrolled courses, payments and certificates at Grafix@Mirror LMS.">
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
.instructor-tag {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(15, 23, 42, 0.82);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: #fff;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.72rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border: 1px solid rgba(255, 255, 255, 0.1);
  z-index: 2;
  transition: all 0.2s ease-in-out;
}
.instructor-tag:hover {
  transform: scale(1.05);
  background: rgba(79, 70, 229, 0.95);
  border-color: rgba(255, 255, 255, 0.25);
  box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
}
.instructor-tag-avatar {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid rgba(255, 255, 255, 0.6);
}
.instructor-tag-avatar-placeholder {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #4f46e5;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.6rem;
  font-weight: 800;
  border: 1px solid rgba(255, 255, 255, 0.4);
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="lms-nav">
  <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
      </button>
      <a href="dashboard.php" class="brand text-decoration-none">
        <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
        <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
      </a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-sm-inline" style="font-size:.85rem;color:var(--muted)">
        <span id="greet"></span>, <strong><?= e($firstName) ?></strong> 👋
      </span>
      <?php if ($isAdminImpersonating): ?>
        <a href="admin_switch.php?return=1" class="btn-brand" style="background:var(--warning);color:#111827;font-size:.82rem;padding:.4rem .9rem">
          <i class="fa fa-user-shield me-1"></i><span class="d-none d-sm-inline">Return to Admin</span>
        </a>
      <?php endif; ?>
      <a href="profile.php" class="btn-ghost" style="font-size:.82rem;padding:.4rem .9rem"><i class="fa fa-user me-1"></i><span class="d-none d-sm-inline">Profile</span></a>
      <a href="logout.php" style="font-size:.82rem;color:var(--danger);font-weight:600"><i class="fa fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline">Logout</span></a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar" id="sidebar">
    <div class="nav-section">Student</div>
    <a href="dashboard.php" class="nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>

    <!-- My Courses accordion -->
    <button class="sidebar-acc-btn" id="myCoursesToggle" aria-expanded="false" aria-controls="myCoursesList">
      <i class="fa fa-book-open icon"></i> My Courses
      <i class="fa fa-chevron-down chev"></i>
    </button>
    <div class="sidebar-courses" id="myCoursesList" role="region">
      <?php foreach ($myCourses as $c): ?>
        <a href="<?= e(courseUrl($c)) ?>" class="sidebar-course-item">
          <i class="fa fa-circle" style="color:var(--brand)"></i><?= e($c['title']) ?>
        </a>
      <?php endforeach; ?>
      <?php if (empty($myCourses)): ?>
        <div class="sidebar-course-item" style="font-style:italic;color:var(--muted)">No enrollments yet</div>
      <?php endif; ?>
    </div>

    <!-- Available Courses accordion -->
    <button class="sidebar-acc-btn" id="availToggle" aria-expanded="false" aria-controls="availList">
      <i class="fa fa-graduation-cap icon"></i> Available Courses
      <i class="fa fa-chevron-down chev"></i>
    </button>
    <div class="sidebar-courses" id="availList" role="region">
      <?php foreach ($availableCourses as $c): ?>
        <a href="<?= e(courseUrl($c)) ?>" class="sidebar-course-item">
          <i class="fa fa-circle" style="color:var(--success)"></i><?= e($c['title']) ?>
        </a>
      <?php endforeach; ?>
      <?php if (empty($availableCourses)): ?>
        <div class="sidebar-course-item" style="font-style:italic;color:var(--muted)">All courses enrolled</div>
      <?php endif; ?>
    </div>

    <div class="nav-section">Learning</div>
    <a href="videos.php" class="nav-link"><i class="fa fa-play-circle"></i> Videos</a>
    <a href="assignments.php" class="nav-link"><i class="fa fa-tasks"></i> Assignments</a>
    <a href="exams.php" class="nav-link"><i class="fa fa-pen-alt"></i> Exams</a>
    <a href="live_session.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions</a>
    <a href="ai_tutor.php" class="nav-link" style="color:var(--brand)"><i class="fa fa-robot"></i> AI Tutor <span style="font-size:.65rem;background:var(--brand);color:#fff;padding:.1rem .4rem;border-radius:99px;margin-left:.25rem">24/7</span></a>

    <div class="nav-section">Account</div>
    <a href="payments.php" class="nav-link"><i class="fa fa-credit-card"></i> Payments</a>
    <a href="certificate.php" class="nav-link"><i class="fa fa-certificate"></i> Certificates</a>
    <a href="badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="profile.php" class="nav-link"><i class="fa fa-user-cog"></i> Profile</a>

    <div class="nav-section">Portal</div>
    <a href="logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">

    <?php if ($isAdminImpersonating): ?>
      <div class="lms-alert lms-alert-warning mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="fa fa-user-secret me-1"></i>You are viewing the portal as this student.</span>
        <a href="admin_switch.php?return=1" class="btn-ghost" style="font-size:.82rem;padding:.4rem .8rem">
          <i class="fa fa-arrow-left me-1"></i>Return to Admin
        </a>
      </div>
    <?php endif; ?>

    <?php if ($isAffiliate && $affiliateCourse): ?>
    <!-- ═══════════ AFFILIATE STUDENT DASHBOARD ═══════════ -->
    <div class="lms-card mb-4" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);color:#fff;border:2px solid #0d9488;border-radius:16px;">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <i class="fa fa-graduation-cap text-info fs-5"></i>
            <span class="fw-bold fs-5">Affiliate Student Dashboard</span>
            <?php $lvlLabel = str_replace(['JSS','SSS'], ['JSS ', 'SSS '], $affiliateClassLevel); ?>
            <span class="badge ms-2 px-3 py-1 rounded-pill fw-bold" style="background:rgba(13,148,136,0.25);color:#5eead4;border:1px solid #0d9488;font-size:.75rem;">
              <?= e($affiliateClassRange ?: 'N/A') ?> &mdash; <?= e($lvlLabel) ?>
            </span>
          </div>
          <div class="text-white-50 small">You are enrolled in the Unitary Academy affiliate curriculum.</div>
        </div>
        <div class="text-end">
          <?php if ($affiliatePartner): ?>
          <div class="small text-white-50 mb-1">Referred by</div>
          <div class="fw-semibold text-info"><?= e($affiliatePartner['partner_name']) ?></div>
          <div class="small text-white-50 mb-2"><?= e(ucfirst($affiliatePartner['partner_type'])) ?> Partner</div>
          <?php endif; ?>
          <button type="button" class="btn btn-sm btn-info text-dark fw-bold mt-1" data-bs-toggle="modal" data-bs-target="#idCardModal">
            <i class="fa fa-id-card me-1"></i> View Student ID Card
          </button>
        </div>
      </div>

      <!-- Course Info -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.06);">
            <div class="small text-white-50 mb-1">ENROLLED COURSE</div>
            <div class="fw-bold text-white"><?= e($affiliateCourse['title']) ?></div>
            <div class="small text-info"><?= e(ucfirst($affiliateCourse['level'])) ?> &bull; <?= e($affiliateCourse['category'] ?? '') ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.06);">
            <div class="small text-white-50 mb-1">CLASS LEVEL</div>
            <div class="fw-bold text-white fs-5"><?= e($lvlLabel) ?></div>
            <div class="small text-info"><?= $affiliateClassRange === 'JSS' ? 'Junior Secondary School' : ($affiliateClassRange === 'SSS' ? 'Senior Secondary School' : 'Higher Institution') ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.06);">
            <div class="small text-white-50 mb-1">CURRICULUM TERMS</div>
            <div class="fw-bold text-white">3 Terms / Year</div>
            <div class="small text-info"><?= count($affiliateScheme) ?> term(s) loaded</div>
          </div>
        </div>
      </div>

      <!-- Course Outline Tabs -->
      <?php if (!empty($affiliateScheme)): ?>
      <div class="mb-1 fw-semibold text-white"><i class="fa fa-book-open me-1 text-info"></i>Course Outline — <?= e($affiliateCourse['title']) ?> (<?= e($lvlLabel) ?>)</div>
      <div class="small text-white-50 mb-3">Your term-by-term syllabus topics.</div>

      <ul class="nav nav-tabs border-0 gap-1 mb-3" id="sowTabs" role="tablist">
        <?php $tIdx = 0; foreach ($affiliateScheme as $term => $weeks): ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link <?= $tIdx === 0 ? 'active' : '' ?> fw-semibold"
            id="sow-tab-<?= $tIdx ?>"
            data-bs-toggle="tab"
            data-bs-target="#sow-pane-<?= $tIdx ?>"
            type="button" role="tab"
            style="background:rgba(255,255,255,0.07);color:#94a3b8;border:1px solid rgba(255,255,255,0.1);border-radius:8px;">
            <?= e($term) ?> Term
          </button>
        </li>
        <?php $tIdx++; endforeach; ?>
      </ul>

      <div class="tab-content">
        <?php $tIdx = 0; foreach ($affiliateScheme as $term => $weeks): ?>
        <div class="tab-pane fade <?= $tIdx === 0 ? 'show active' : '' ?>" id="sow-pane-<?= $tIdx ?>" role="tabpanel">
          <div style="overflow-x:auto;max-height:320px;overflow-y:auto;">
            <table class="lms-table" style="font-size:.82rem;">
              <thead style="background:rgba(13,148,136,0.2);">
                <tr>
                  <th style="color:#5eead4;width:60px;">Week</th>
                  <th style="color:#5eead4;">Topic</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($weeks as $w): ?>
                <tr>
                  <td class="text-center fw-bold text-info"><?= (int)$w['week_number'] ?></td>
                  <td style="color:#e2e8f0;"><?= e($w['topic']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php $tIdx++; endforeach; ?>
      </div>
      <?php else: ?>
      <div class="text-center py-4 text-white-50"><i class="fa fa-book fs-3 mb-2 d-block"></i>Course outline will load once your class level is confirmed.</div>
      <?php endif; ?>
    </div>
    <!-- ═══════════ END AFFILIATE DASHBOARD ═══════════ -->
    <?php endif; ?>

    <!-- STATS -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa fa-book-open"></i></div>
          <div>
            <div class="stat-value"><?= count($myCourses) ?></div>
            <div class="stat-label">Enrolled Courses</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa fa-graduation-cap"></i></div>
          <div>
            <div class="stat-value"><?= count($availableCourses) ?></div>
            <div class="stat-label">Available Courses</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon amber"><i class="fa fa-wallet"></i></div>
          <div>
            <div class="stat-value" style="font-size:1.1rem"><?= formatMoney($totalPaid) ?></div>
            <div class="stat-label">Total Paid</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <a href="certificate.php" class="text-decoration-none text-dark">
          <div class="stat-card">
            <div class="stat-icon cyan"><i class="fa fa-certificate"></i></div>
            <div>
              <div class="stat-value"><?= $certCount ?></div>
              <div class="stat-label">Certificates</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <a href="badges.php" class="text-decoration-none text-dark">
          <div class="stat-card">
            <div class="stat-icon purple" style="background:rgba(227,193,98,0.12); color:#e3c162"><i class="fa fa-award"></i></div>
            <div>
              <div class="stat-value"><?= $badgeCount ?></div>
              <div class="stat-label">Badges</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa fa-bell"></i></div>
          <div>
            <div class="stat-value"><?= $unreadNotifications ?></div>
            <div class="stat-label">New Alerts</div>
          </div>
        </div>
      </div>
    </div>

    <div class="lms-card mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="section-title mb-0"><i class="fa fa-bell me-2" style="color:var(--brand)"></i>Notifications</div>
        <span style="font-size:.8rem;color:var(--muted)"><?= count($notifications) ?> recent</span>
      </div>
      <?php if (empty($notifications)): ?>
        <div class="lms-alert lms-alert-info mb-0">
          <i class="fa fa-info-circle me-1"></i>No notifications yet.
        </div>
      <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($notifications as $notice): ?>
            <div class="p-3 rounded" style="border:1px solid var(--border);background:#fff">
              <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div style="font-weight:700"><?= e($notice['title']) ?></div>
                <span class="badge-<?= $notice['type'] === 'assignment' ? 'warning' : 'info' ?>">
                  <?= $notice['type'] === 'assignment' ? 'Assignment' : 'Live Session' ?>
                </span>
              </div>
              <div style="font-size:.88rem;color:var(--dark);margin-top:.35rem"><?= e($notice['message']) ?></div>
              <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-2">
                <span style="font-size:.76rem;color:var(--muted)"><?= e(date('d M Y, g:ia', strtotime((string)$notice['created_at']))) ?></span>
                <?php if (!empty($notice['action_url'])): ?>
                  <a href="<?= e((string)$notice['action_url']) ?>" class="btn-ghost" style="font-size:.78rem;padding:.35rem .8rem">
                    <i class="fa fa-arrow-right me-1"></i>Open
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- MY ENROLLED COURSES -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="section-title mb-0">My Enrolled Courses</div>
    </div>

    <?php if (empty($myCourses)): ?>
      <div class="lms-alert lms-alert-info mb-4">
        <i class="fa fa-info-circle"></i>
        You haven't enrolled in any course yet. Click <strong>Available Courses</strong> in the sidebar to browse.
      </div>
    <?php else: ?>
      <div class="row g-3 mb-4">
      <?php foreach ($myCourses as $c):
        $c = workspaceCourseRow((array)$c);
        $cid     = (int)$c['id'];
        $access  = enrollmentAccessState($c);
        $paid    = (float)($c['paid_amount'] ?? 0);
        $price   = (float)($c['price'] ?? 0);
        if ($isAffiliate && ($affiliateClassRange === 'JSS' || $affiliateClassRange === 'SSS')) {
            $price = min($price, 5000.0);
        }
        $ptype   = (string)($c['payment_type'] ?? 'full');
        $status  = (string)($c['enroll_status'] ?? '');
        $expired = (bool)$access['is_expired'];
        $installmentDue = (bool)$access['installment_due'];
        $balance = max(0, $price - $paid);
        $pct     = $price > 0 ? min(100, round($paid / $price * 100)) : 100;

        if ($expired) {
          $badge  = '<span class="badge-danger">Expired</span>';
          $action = '<a class="btn-ghost w-100 text-center d-block" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'">Renew Access</a>';
        } elseif ($status === 'paid' || ($price > 0 && $paid >= $price)) {
          $badge  = '<span class="badge-success">Unlocked ✓</span>';
          $action = '<div class="d-grid gap-2">'
                  . '<a class="btn-brand w-100 justify-content-center d-flex" href="' . e(workspaceLaunchUrl($c)) . '"><i class="fa ' . e(workspaceTypeIcon($c)) . ' me-1"></i>Launch ' . e(workspaceTypeLabel($c)) . '</a>'
                  . '<a class="btn-ghost w-100 text-center d-block" href="' . e(courseUrl(['id' => $cid, 'slug' => $c['slug'] ?? ''])) . '">View Course →</a>'
                  . '</div>';
        } elseif ($ptype === 'installment' && $paid > 0 && !$installmentDue) {
          $badge  = '<span class="badge-success">Active on Installment</span>';
          $action = '<div class="d-grid gap-2">'
                  . '<a class="btn-brand w-100 justify-content-center d-flex" href="' . e(workspaceLaunchUrl($c)) . '"><i class="fa ' . e(workspaceTypeIcon($c)) . ' me-1"></i>Launch ' . e(workspaceTypeLabel($c)) . '</a>'
                  . '<a class="btn-ghost w-100 text-center d-block" href="' . e(courseUrl(['id' => $cid, 'slug' => $c['slug'] ?? ''])) . '">View Course →</a>'
                  . '</div>';
        } elseif ($ptype === 'installment' && $installmentDue) {
          $badge  = '<span class="badge-danger">2nd Payment Due</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--warning);color:#000" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Balance ('.formatMoney($balance).')</a>';
        } elseif ($status === 'installment' || ($paid > 0 && $paid < $price)) {
          $badge  = '<span class="badge-warning">Installment</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--warning);color:#000" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Balance ('.formatMoney($balance).')</a>';
        } else {
          $badge  = '<span class="badge-muted">Pending Payment</span>';
          $action = '<a class="btn-brand w-100 justify-content-center d-flex" style="background:var(--success)" href="pay.php?enrollment_id='.(int)$c['enrollment_id'].'"><i class="fa fa-credit-card me-1"></i>Pay Now</a>';
        }
      ?>
        <div class="col-md-6 col-xl-4">
          <div class="course-card h-100" style="position: relative;">
            <?php if (!empty($c['instructor_name'])): ?>
              <div class="instructor-tag" title="Assigned Instructor">
                <?php if (!empty($c['instructor_photo'])): ?>
                  <img src="uploads/<?= e($c['instructor_photo']) ?>" alt="" class="instructor-tag-avatar">
                <?php else: ?>
                  <div class="instructor-tag-avatar-placeholder">
                    <?= strtoupper(substr($c['instructor_name'], 0, 1)) ?>
                  </div>
                <?php endif; ?>
                <span><?= e($c['instructor_name']) ?></span>
              </div>
            <?php endif; ?>
            <div class="course-thumb">
              <i class="fa fa-book-open"></i>
            </div>
            <div class="course-body">
              <div class="course-title"><?= e($c['title']) ?></div>
              <div class="course-price d-flex align-items-center justify-content-between mt-1">
                <span><?= formatMoney($price) ?></span>
                <span class="badge-muted" style="font-size:.7rem"><?= e(ucfirst($c['level'] ?? '')) ?></span>
              </div>
              <div class="mt-2 mb-1"><?= $badge ?></div>
              <!-- Payment progress -->
              <div class="progress mt-2" style="height:5px">
                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
              </div>
              <div class="d-flex justify-content-between" style="font-size:.72rem;color:var(--muted);margin-top:.2rem">
                <span>Paid: <?= formatMoney($paid) ?></span>
                <span><?= $pct ?>%</span>
              </div>
              <?php if (!empty($c['next_due_date']) && $balance > 0): ?>
                <div style="font-size:.75rem;color:var(--danger);margin-top:.3rem">
                  <i class="fa fa-clock me-1"></i>Next due: <?= e(date('d M Y', strtotime($c['next_due_date']))) ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="course-footer"><?= $action ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>
</div>

<script>
/* Greeting */
const h = new Date().getHours();
const el = document.getElementById('greet');
if (el) el.textContent = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : h < 22 ? 'Good Evening' : 'Good Night';

/* Sidebar mobile toggle */
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});

/* Accordion helper — works with max-height CSS transition */
function initAccordion(btnId, panelId) {
  const btn   = document.getElementById(btnId);
  const panel = document.getElementById(panelId);
  if (!btn || !panel) return;
  btn.addEventListener('click', function () {
    const isOpen = panel.classList.toggle('open');
    btn.classList.toggle('open', isOpen);
    btn.setAttribute('aria-expanded', String(isOpen));
  });
}
initAccordion('myCoursesToggle', 'myCoursesList');
initAccordion('availToggle',     'availList');
</script>

<?php if ($isAffiliate): ?>
<!-- Student ID Card Modal -->
<div class="modal fade" id="idCardModal" tabindex="-1" aria-labelledby="idCardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background:#f8fafc; color:#334155;">
      <div class="modal-header bg-dark text-white border-0 py-3">
        <h5 class="modal-title fw-bold" id="idCardModalLabel"><i class="fa fa-id-card text-info me-2"></i>My Student ID Card</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <!-- Stacked ID Card (Front & Back) -->
        <div id="studentIdCard" class="mx-auto" style="width: 320px; display: flex; flex-direction: column; gap: 20px;">
          
          <!-- FRONT SIDE -->
          <div class="card-front p-4 position-relative shadow rounded-4 overflow-hidden text-start" style="width: 320px; min-height: 380px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; border: 2px solid #0d9488; font-family: 'Inter', sans-serif; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <!-- Card Header -->
              <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-3">
                <div>
                  <h6 class="fw-bold text-info mb-0" style="letter-spacing: 1px; font-size: 0.9rem;">UNITARY ACADEMY</h6>
                  <span class="text-muted" style="font-size: 0.65rem;">STUDENT REFERRAL ID</span>
                </div>
                <i class="fa fa-graduation-cap text-info fs-3"></i>
              </div>
              
              <!-- Card Body -->
              <div class="row g-2 align-items-center">
                <!-- Profile avatar / initials or Passport photo -->
                <div class="col-4 text-center">
                  <div class="d-flex align-items-center justify-content-center bg-secondary text-info fw-bold rounded-circle border border-info overflow-hidden" style="width: 75px; height: 75px; font-size: 1.5rem;" id="card_avatar_container">
                    <?php if (!empty($student['passport'])): ?>
                      <img id="card_passport" src="uploads/<?= e($student['passport']) ?>" alt="Passport" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                      <?php
                        $names = explode(' ', $name);
                        $initials = '';
                        if (count($names) > 0) {
                          $initials .= strtoupper(substr($names[0], 0, 1));
                          if (count($names) > 1) {
                            $initials .= strtoupper(substr($names[count($names)-1], 0, 1));
                          }
                        }
                      ?>
                      <span id="card_initials"><?= e($initials ?: 'ST') ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <!-- Bio Info -->
                <div class="col-8 ps-3" style="font-size: 0.75rem;">
                  <div class="mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">FULL NAME</span>
                    <strong class="text-white d-block text-truncate" id="card_name" style="font-size: 0.85rem;"><?= e($name) ?></strong>
                  </div>
                  <div class="mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">EMAIL ADDRESS</span>
                    <span class="text-info d-block text-truncate" id="card_email"><?= e($student['email'] ?? '') ?></span>
                  </div>
                  <div class="mb-0 d-flex justify-content-between align-items-end">
                    <div>
                      <span class="text-muted d-block" style="font-size: 0.6rem;">COURSE / TRACK</span>
                      <span class="text-white d-block text-truncate fw-semibold" id="card_course" style="max-width: 100px;"><?= e($affiliateCourse['title'] ?? 'Affiliate Course') ?></span>
                    </div>
                    <div class="text-end">
                      <span class="text-muted d-block text-end" style="font-size: 0.55rem; margin-bottom: 2px;">SIGNATURE</span>
                      <div class="border rounded bg-white p-1 d-inline-block" style="height: 30px; width: 75px;">
                        <?php if (!empty($student['signature'])): ?>
                          <img id="card_signature" src="uploads/<?= e($student['signature']) ?>" alt="Signature" style="height: 100%; width: 100%; object-fit: contain;">
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- QR Code section -->
            <div class="border-top border-secondary pt-3 mt-3 text-center">
              <div class="mb-2" style="font-size: 0.65rem; color: #94a3b8;">SCAN TO ENROLL & ACCESS COURSES</div>
              <!-- Unique QR Code from API -->
              <?php
                $refToken = $affiliatePartner['referral_token'] ?? '';
                $regLink = appAbsoluteUrl('register.php?ref_token=' . $refToken);
              ?>
              <img id="card_qr" src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&color=0d9488&data=<?= urlencode($regLink) ?>" alt="QR Link" class="img-fluid rounded border p-1" style="background:#fff; width: 110px; height: 110px;">
              <div class="mt-2 text-info fw-mono fw-bold" id="card_token" style="font-size: 0.7rem; letter-spacing: 1px;">REF-<?= strtoupper(substr($refToken, 0, 8)) ?></div>
            </div>
          </div>
          
          <!-- BACK SIDE -->
          <div class="card-back p-4 position-relative shadow rounded-4 overflow-hidden text-start" style="width: 320px; min-height: 380px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; border: 2px solid #0d9488; font-family: 'Inter', sans-serif; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-3">
                <span class="fw-bold text-info" style="font-size: 0.8rem; letter-spacing: 1px;">CARD ACCESS & TERMS</span>
                <span class="text-white-50" style="font-size: 0.6rem;">BACK</span>
              </div>
              
              <div class="text-white-50" style="font-size: 0.65rem; line-height: 1.4;">
                <p class="mb-2">1. This card is the property of <strong>Unitary Academy</strong> and must be presented on request.</p>
                <p class="mb-2">2. It certifies that the holder is a registered student in the affiliate curriculum track.</p>
                <p class="mb-2">3. In case of lost credentials or forgotten password, scan the security barcode below to instantly authenticate and access your student dashboard.</p>
                <p class="mb-0">4. Misuse of this card or the autologin barcode will lead to immediate suspension of portal access.</p>
              </div>
            </div>
            
            <div class="pt-3 border-top border-secondary mt-3">
              <!-- Company Signature -->
              <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                  <span class="text-muted d-block" style="font-size: 0.55rem;">ISSUING AUTHORITY</span>
                  <span class="text-info fw-semibold" style="font-size: 0.65rem;">Mirror Age Concepts</span>
                </div>
                <div class="text-end">
                  <div class="d-inline-block" style="height: 25px; margin-bottom: 2px;">
                    <img id="card_org_signature" src="assets/img/og-sign.png" alt="Signature" style="height: 100%; object-fit: contain; transform: rotate(-5deg); filter: invert(1) brightness(1.2);">
                  </div>
                  <span class="text-muted d-block" style="font-size: 0.55rem; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 2px;">Director of Studies</span>
                </div>
              </div>
              
              <!-- Security Barcode -->
              <div class="text-center bg-white p-2 rounded">
              <!-- Security Autologin Code -->
              <div class="text-center bg-white p-2 rounded">
                <div class="small text-dark mb-1 fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">SECURITY AUTOLOGIN CODE</div>
                <?php
                  $autologinToken = $student['autologin_token'] ?? '';
                  $autologinUrl = appAbsoluteUrl('autologin.php?token=' . $autologinToken);
                ?>
                <img id="card_barcode" src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&color=0f172a&data=<?= urlencode($autologinUrl) ?>" alt="Security QR Code" class="img-fluid rounded border p-1" style="background:#fff; width: 90px; height: 90px; object-fit: contain;">
                <div class="text-muted mt-1 fw-mono" id="card_barcode_text" style="font-size: 0.55rem;">*SYS-LOGIN-<?= strtoupper(substr($autologinToken, 0, 12)) ?>*</div>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Actions -->
        <div class="d-flex gap-2 justify-content-center mt-3">
          <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-brand w-50 py-2" onclick="printIdCard()"><i class="fa fa-print me-2"></i>Print Card</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function printIdCard() {
  const cardHtml = document.getElementById('studentIdCard').outerHTML;
  const printWindow = window.open('', '_blank', 'width=600,height=600');
  printWindow.document.write('<html><head><title>Student ID Card</title>');
  printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">');
  printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
  printWindow.document.write('<style>body { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; min-height: 100vh; background: #f8fafc; margin: 0; padding: 20px; } #studentIdCard { gap: 20px !important; } @media print { body { background: none !important; padding: 0 !important; } * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; } #studentIdCard { gap: 20px !important; page-break-inside: avoid !important; } .card-front { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; background-color: #0f172a !important; color: #ffffff !important; border: 2px solid #0d9488 !important; page-break-inside: avoid !important; } .card-back { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; background-color: #1e293b !important; color: #ffffff !important; border: 2px solid #0d9488 !important; page-break-inside: avoid !important; } .border.rounded.bg-white { background: transparent !important; background-color: transparent !important; border-color: rgba(255, 255, 255, 0.2) !important; } #card_signature { mix-blend-mode: multiply !important; } #card_org_signature { filter: invert(1) !important; } #card_qr { background: #ffffff !important; border: 1px solid #cbd5e1 !important; } .text-center.bg-white.p-2.rounded { background: #ffffff !important; background-color: #ffffff !important; } #card_barcode { background: #ffffff !important; } .text-info { color: #0d9488 !important; } .text-muted { color: #94a3b8 !important; } }</style>');
  printWindow.document.write('</head><body>');
  printWindow.document.write(cardHtml);
  printWindow.document.write('<script>window.onload = function() { window.print(); window.close(); }<\/script>');
  printWindow.document.write('</body></html>');
  printWindow.document.close();
}
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
