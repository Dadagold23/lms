<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
startSecureSession();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();
$ins = $_SESSION['instructor'];
$insId = (int)($ins['id'] ?? 0);

$coursesCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_instructor_courses WHERE instructor_id={$insId}")->fetchColumn();
$lessonsCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons l JOIN lms_instructor_courses ic ON ic.course_id = l.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$videosCount  = (int)$pdo->query("SELECT COUNT(*) FROM lms_videos v JOIN lms_instructor_courses ic ON ic.course_id = v.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$assignCount  = (int)$pdo->query("SELECT COUNT(*) FROM lms_assignments a JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();
$subsCount    = (int)$pdo->query("SELECT COUNT(*) FROM lms_assignment_submissions sub JOIN lms_assignments a ON a.id = sub.assignment_id JOIN lms_instructor_courses ic ON ic.course_id = a.course_id WHERE ic.instructor_id={$insId}")->fetchColumn();

$stats = [
  'courses' => $coursesCount,
  'lessons' => $lessonsCount,
  'videos'  => $videosCount,
  'assign'  => $assignCount,
  'subs'    => $subsCount,
];

// Live sessions for this instructor (or all if admin-level)
$liveSessions = $pdo->query("
    SELECT s.id, s.title, s.scheduled_at, s.status, s.anydesk_id, s.meeting_link,
           c.title AS course_title,
           (SELECT COUNT(*) FROM lms_session_attendance WHERE session_id=s.id) AS attendees
    FROM lms_live_sessions s
    JOIN lms_courses c ON c.id=s.course_id
    WHERE s.instructor_id={$insId}
      AND s.status IN ('scheduled','live')
    ORDER BY s.scheduled_at ASC LIMIT 5
")->fetchAll();

$recentSubs = $pdo->query("
    SELECT sub.id, sub.submitted_at, sub.file_path,
           a.title AS assignment_title,
           s.first_name, s.last_name
    FROM lms_assignment_submissions sub
    JOIN lms_assignments a ON a.id = sub.assignment_id
    JOIN lms_instructor_courses ic ON ic.course_id = a.course_id
    JOIN lms_students s ON s.id = sub.student_id
    WHERE ic.instructor_id={$insId}
    ORDER BY sub.submitted_at DESC LIMIT 8
")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Instructor Dashboard';
$seoDesc    = 'Instructor dashboard — manage courses, lessons, videos, assignments and live sessions at Grafix@Mirror LMS.';
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

<nav class="lms-nav lms-nav-instructor">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn-ghost d-md-none" style="padding:.4rem .7rem; color:#fff;" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
      </button>
      <div class="brand">
        <div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">I</div>
        <span style="color:#fff">Instructor <span style="color:#c7d2fe">Portal</span></span>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:rgba(255,255,255,.7)">
        <i class="fa fa-chalkboard-teacher me-1"></i><?= e($ins['full_name'] ?? 'Instructor') ?>
      </span>
      <a href="instructor_logout.php" style="font-size:.82rem;color:#fca5a5;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar" id="sidebar">
    <div class="nav-section">Overview</div>
    <a href="instructor_dashboard.php" class="nav-link active"><i class="fa fa-th-large"></i> Dashboard</a>
    <div class="nav-section">Content</div>
    <a href="instructor_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="instructor_lessons.php" class="nav-link"><i class="fa fa-file-alt"></i> Lessons</a>
    <a href="instructor_videos.php" class="nav-link"><i class="fa fa-video"></i> Videos</a>
    <a href="instructor_assignments.php" class="nav-link"><i class="fa fa-tasks"></i> Assignments</a>
    <a href="instructor_tutor_guide.php?type=normal" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Lecture Guide</a>
    <div class="nav-section">Upload</div>
    <a href="instructor_upload_course.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Course</a>
    <a href="instructor_upload_lesson.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Lesson</a>
    <a href="instructor_upload_video.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Video</a>
    <a href="instructor_upload_assignment.php" class="nav-link"><i class="fa fa-plus-circle"></i> New Assignment</a>
    <div class="nav-section">Live Teaching</div>
    <a href="instructor_live_sessions.php" class="nav-link" style="color:var(--danger)">
      <i class="fa fa-video"></i> Live Sessions
      <?php if (!empty($liveSessions)): ?>
        <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.1rem .45rem;font-size:.7rem;margin-left:auto"><?= count($liveSessions) ?></span>
      <?php endif; ?>
    </a>
    <div class="nav-section">Grading</div>
    <a href="instructor_grade_assignment.php" class="nav-link"><i class="fa fa-star"></i> Grade Submissions</a>
    <div class="nav-section">Affiliate</div>
    <a href="#sowSection" class="nav-link" onclick="document.getElementById('sowSection').scrollIntoView({behavior:'smooth'});return false;"><i class="fa fa-book-open"></i> Scheme of Work</a>
    <a href="instructor_tutor_guide.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Lesson Guide</a>
    <div class="nav-section">Settings</div>
    <a href="instructor_profile.php" class="nav-link"><i class="fa fa-user-cog"></i> Profile Settings</a>
    <div class="nav-section">Portal</div>
    <a href="instructor_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="lms-main">

    <div class="page-title mb-4">Instructor Dashboard</div>

    <!-- STATS -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa fa-book"></i></div>
          <div><div class="stat-value"><?= $stats['courses'] ?></div><div class="stat-label">Courses</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
          <div><div class="stat-value"><?= $stats['lessons'] ?></div><div class="stat-label">Lessons</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon cyan"><i class="fa fa-video"></i></div>
          <div><div class="stat-value"><?= $stats['videos'] ?></div><div class="stat-label">Videos</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon amber"><i class="fa fa-tasks"></i></div>
          <div><div class="stat-value"><?= $stats['assign'] ?></div><div class="stat-label">Assignments</div></div>
        </div>
      </div>
      <div class="col-sm-6 col-lg">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa fa-inbox"></i></div>
          <div><div class="stat-value"><?= $stats['subs'] ?></div><div class="stat-label">Submissions</div></div>
        </div>
      </div>
    </div>

    <!-- QUICK UPLOAD -->
    <div class="lms-card mb-4">
      <div class="section-title">Quick Actions</div>
      <div class="d-flex flex-wrap gap-2">
        <a href="instructor_live_sessions.php" class="btn-brand" style="background:var(--danger)">
          <i class="fa fa-broadcast-tower"></i> Schedule Live Session
        </a>
        <a href="instructor_upload_course.php" class="btn-brand"><i class="fa fa-plus"></i> New Course</a>
        <a href="instructor_upload_lesson.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Lesson</a>
        <a href="instructor_upload_video.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Video</a>
        <a href="instructor_upload_assignment.php" class="btn-outline-brand"><i class="fa fa-plus"></i> New Assignment</a>
        <a href="instructor_tutor_guide.php" class="btn-outline-brand"><i class="fa fa-chalkboard-teacher"></i> Lesson Guide</a>
        <a href="instructor_tutor_guide.php?type=normal" class="btn-outline-brand"><i class="fa fa-chalkboard-teacher"></i> Lecture Guide</a>
        <a href="instructor_grade_assignment.php" class="btn-ghost"><i class="fa fa-star"></i> Grade Submissions</a>
      </div>
    </div>

    <!-- UPCOMING LIVE SESSIONS -->
    <div class="lms-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0"><i class="fa fa-video me-2" style="color:var(--danger)"></i>My Live Sessions</div>
        <a href="instructor_live_sessions.php" style="font-size:.8rem;color:var(--brand)">Manage all →</a>
      </div>
      <?php if (empty($liveSessions)): ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-2">
          <p class="text-muted mb-0" style="font-size:.88rem">No upcoming sessions. Schedule one to start live tutoring.</p>
          <a href="instructor_live_sessions.php" class="btn-brand" style="background:var(--danger);font-size:.85rem">
            <i class="fa fa-plus me-1"></i> Schedule Session
          </a>
        </div>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="lms-table">
            <thead><tr><th>Session</th><th>Course</th><th>Scheduled</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($liveSessions as $s): ?>
              <tr>
                <td>
                  <div style="font-weight:600"><?= e($s['title']) ?></div>
                  <?php if (!empty($s['anydesk_id'])): ?>
                    <div style="font-size:.75rem;color:var(--muted)"><i class="fa fa-desktop me-1"></i>AnyDesk: <strong><?= e($s['anydesk_id']) ?></strong></div>
                  <?php endif; ?>
                </td>
                <td style="font-size:.85rem"><?= e($s['course_title']) ?></td>
                <td style="font-size:.82rem"><?= e(date('d M Y H:i', strtotime($s['scheduled_at']))) ?></td>
                <td>
                  <?php if ($s['status'] === 'live'): ?>
                    <span style="color:var(--danger);font-weight:700;font-size:.8rem;animation:pulse 1.5s infinite">● LIVE NOW</span>
                  <?php else: ?>
                    <span class="badge-info">Scheduled</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="instructor_live_sessions.php?id=<?= (int)$s['id'] ?>" class="btn-outline-brand" style="font-size:.78rem;padding:.25rem .7rem">
                    <i class="fa fa-edit"></i> Manage
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- RECENT SUBMISSIONS -->
    <div class="lms-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">Recent Submissions</div>
        <a href="instructor_grade_assignment.php" style="font-size:.8rem;color:var(--brand)">Grade all →</a>
      </div>
      <?php if (empty($recentSubs)): ?>
        <p class="text-muted" style="font-size:.88rem">No submissions yet.</p>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="lms-table">
            <thead><tr><th>Student</th><th>Assignment</th><th>Submitted</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($recentSubs as $sub): ?>
              <tr>
                <td style="font-weight:600"><?= e(($sub['first_name']??'').' '.($sub['last_name']??'')) ?></td>
                <td><?= e($sub['assignment_title']??'') ?></td>
                <td style="color:var(--muted)"><?= e(date('d M Y', strtotime((string)$sub['submitted_at']))) ?></td>
                <td>
                  <a href="instructor_grade_assignment.php?submission_id=<?= (int)$sub['id'] ?>" class="btn-outline-brand" style="font-size:.78rem;padding:.25rem .7rem">
                    Grade
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- ═══════ AFFILIATE SCHEME OF WORK ═══════ -->
    <div class="lms-card mb-4" id="sowSection">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0"><i class="fa fa-book-open me-2" style="color:var(--brand)"></i>Affiliate Scheme of Work</div>
        <div class="d-flex gap-2">
          <a id="sowGuideBtn" class="btn-ghost text-decoration-none" style="font-size:.82rem;display:none;align-items:center;" target="_blank" href="#">
            <i class="fa fa-chalkboard-teacher me-1"></i> Tutor Guide
          </a>
          <button id="sowPrintBtn" class="btn-ghost" style="font-size:.82rem;display:none;" onclick="printSOW()">
            <i class="fa fa-print me-1"></i> Print
          </button>
        </div>
      </div>
      <p style="font-size:.85rem;color:var(--muted)" class="mb-3">Load and view the scheme of work for any affiliate course, class level, and term.</p>

      <!-- Filters -->
      <div class="row g-2 mb-4" id="sowFilters">
        <div class="col-md-4">
          <label style="font-size:.8rem;font-weight:600;color:var(--muted);display:block;margin-bottom:4px">Course</label>
          <select id="sowCourseId" class="form-select" style="font-size:.85rem;">
            <option value="">-- Select Course --</option>
            <?php
              try {
                $ac = $pdo->query("SELECT id, title FROM lms_affiliate_courses WHERE is_active=1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($ac as $acRow) {
                    echo '<option value="' . (int)$acRow['id'] . '">' . htmlspecialchars($acRow['title']) . '</option>';
                }
              } catch (Throwable $e) {}
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label style="font-size:.8rem;font-weight:600;color:var(--muted);display:block;margin-bottom:4px">Class Level</label>
          <select id="sowClassLevel" class="form-select" style="font-size:.85rem;">
            <option value="">-- Select Level --</option>
            <option value="JSS1">JSS 1</option>
            <option value="JSS2">JSS 2</option>
            <option value="JSS3">JSS 3</option>
            <option value="SSS1">SSS 1</option>
            <option value="SSS2">SSS 2</option>
            <option value="SSS3">SSS 3</option>
          </select>
        </div>
        <div class="col-md-3">
          <label style="font-size:.8rem;font-weight:600;color:var(--muted);display:block;margin-bottom:4px">Term</label>
          <select id="sowTerm" class="form-select" style="font-size:.85rem;">
            <option value="">-- Select Term --</option>
            <option value="1st">1st Term</option>
            <option value="2nd">2nd Term</option>
            <option value="3rd">3rd Term</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button onclick="loadSOW()" class="btn-brand w-100" style="font-size:.85rem;padding:.5rem .8rem">
            <i class="fa fa-search me-1"></i> Load
          </button>
        </div>
      </div>

      <!-- Result area -->
      <div id="sowResult">
        <div class="text-center py-5" style="color:var(--muted);font-size:.88rem">
          <i class="fa fa-book fa-2x mb-3 d-block" style="opacity:.3"></i>
          Select a course, class level, and term above to view the scheme of work.
        </div>
      </div>
    </div>
    <!-- ═══════ END SOW SECTION ═══════ -->

  </main>
</div>

<script>
function loadSOW() {
  const courseId   = document.getElementById('sowCourseId').value;
  const classLevel = document.getElementById('sowClassLevel').value;
  const term       = document.getElementById('sowTerm').value;
  const result     = document.getElementById('sowResult');
  const printBtn   = document.getElementById('sowPrintBtn');
  const guideBtn   = document.getElementById('sowGuideBtn');

  if (!courseId || !classLevel || !term) {
    result.innerHTML = '<div class="lms-alert lms-alert-warning"><i class="fa fa-exclamation-triangle me-2"></i>Please select all three filters.</div>';
    return;
  }

  result.innerHTML = '<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x" style="color:var(--brand)"></i><br><span style="font-size:.85rem;color:var(--muted)">Loading scheme...</span></div>';
  printBtn.style.display = 'none';
  guideBtn.style.display = 'none';

  fetch(`ajax_affiliate_scheme.php?course_id=${encodeURIComponent(courseId)}&class_level=${encodeURIComponent(classLevel)}&term=${encodeURIComponent(term)}`)
    .then(r => r.json())
    .then(data => {
      if (!data.success) {
        result.innerHTML = `<div class="lms-alert lms-alert-danger"><i class="fa fa-exclamation-circle me-2"></i>${escSOW(data.message)}</div>`;
        return;
      }
      if (!data.rows || data.rows.length === 0) {
        result.innerHTML = '<div class="text-muted text-center py-4">No scheme of work found for this selection.</div>';
        return;
      }

      // Store globally
      window.currentSowRows = data.rows;

      let html = `
        <div class="mb-3" style="font-size:.9rem;font-weight:600;color:var(--text)">
          ${escSOW(data.course_title)} &mdash; ${escSOW(data.class_level.replace('JSS','JSS ').replace('SSS','SSS '))} &mdash; ${escSOW(data.term)} Term
          <span style="font-size:.75rem;color:var(--muted);font-weight:400;margin-left:8px">${data.rows.length} weeks</span>
        </div>
        <div style="overflow-x:auto;" id="sowPrintArea">
          <table class="lms-table">
            <thead><tr><th style="width:60px">Week</th><th>Topic & Objectives</th><th class="d-none d-lg-table-cell">Activities</th><th style="width:180px" class="text-end">Lecture Status / Action</th></tr></thead>
            <tbody>`;
      data.rows.forEach(row => {
        const hasLecture = row.lecture_content && row.lecture_content.trim() !== '';
        const statusBadge = hasLecture 
          ? `<span class="badge bg-success text-white small" style="cursor:pointer;" onclick="viewLecture(${row.id})"><i class="fa fa-check me-1"></i>Doctored</span>`
          : `<span class="badge bg-warning text-dark small"><i class="fa fa-clock me-1"></i>Pending</span>`;
        
        const actionBtn = `<button onclick="triggerDoctorLecture(${row.id}, this)" class="btn btn-sm btn-outline-primary py-1 px-2 fw-semibold" style="font-size:.78rem; border-radius:6px; background: #fff;">
          <i class="fa fa-magic me-1"></i> AI Doctor
        </button>`;

        html += `<tr>
          <td class="text-center fw-bold" style="color:var(--brand)">${row.week_number}</td>
          <td>
            <div style="font-weight:600; color: var(--text);">${escSOW(row.topic)}</div>
            <div class="small text-muted d-none d-md-block">${row.objectives}</div>
          </td>
          <td class="d-none d-lg-table-cell" style="font-size:.8rem;color:var(--muted)">${row.activities}</td>
          <td class="text-end">
            <div class="d-flex align-items-center justify-content-end gap-2">
              ${statusBadge}
              ${actionBtn}
            </div>
          </td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      result.innerHTML = html;
      printBtn.style.display = 'inline-flex';
      guideBtn.href = `instructor_tutor_guide.php?course_id=${encodeURIComponent(courseId)}`;
      guideBtn.style.display = 'inline-flex';
    })
    .catch(() => {
      result.innerHTML = '<div class="lms-alert lms-alert-danger">Request failed. Check your connection.</div>';
    });
}

function printSOW() {
  const content = document.getElementById('sowPrintArea');
  if (!content) return;
  const win = window.open('', '_blank', 'width=900,height=700');
  win.document.write('<html><head><title>Scheme of Work</title>');
  win.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
  win.document.write('<style>body{font-family:Inter,sans-serif;padding:2rem}table{width:100%;border-collapse:collapse}th,td{border:1px solid #e2e8f0;padding:.5rem .75rem;font-size:.85rem}thead{background:#f8fafc}</style>');
  win.document.write('</head><body>');
  win.document.write(content.outerHTML);
  win.document.write('<script>window.onload=function(){window.print();window.close();}<\/script>');
  win.document.write('</body></html>');
  win.document.close();
}

function triggerDoctorLecture(sowId, btn) {
  Swal.fire({
    title: 'AI Doctor Lecture Notes?',
    text: "This will compile structured lecture notes and generate 3 corresponding quiz questions automatically for this SOW topic.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, generate',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#4f46e5'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Doctoring Lecture...',
        text: 'Generating structured syllabus notes and quiz items via the AI SOW engine. Please wait.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const formData = new FormData();
      formData.append('_csrf', '<?= csrfToken() ?>');
      formData.append('sow_id', sowId);

      fetch('ajax_doctor_lecture.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Doctored!',
            text: data.message || 'Lecture compiled successfully.',
            icon: 'success'
          }).then(() => {
            loadSOW(); // reload grid
          });
        } else {
          Swal.fire('Error', data.message || 'Generation failed.', 'error');
        }
      })
      .catch(err => {
        Swal.fire('Error', 'Network error or server failed.', 'error');
      });
    }
  });
}

function viewLecture(sowId) {
  if (!window.currentSowRows) return;
  const row = window.currentSowRows.find(r => r.id == sowId);
  if (!row) return;

  let quizHtml = '';
  if (row.quiz_json) {
    try {
      const q = JSON.parse(row.quiz_json);
      quizHtml = `<hr><h6 class="fw-bold mt-3 text-indigo"><i class="fa fa-question-circle me-2"></i>Doctored Assessment (${escSOW(q.title || 'Quiz')})</h6>`;
      if (q.questions && q.questions.length > 0) {
        q.questions.forEach((question, idx) => {
          quizHtml += `<div class="mb-2 p-2 border rounded bg-light" style="font-size: .82rem;">
            <strong>Q${idx+1}: ${escSOW(question.question)}</strong><br>
            A: ${escSOW(question.option_a)} | B: ${escSOW(question.option_b)} | C: ${escSOW(question.option_c)} | D: ${escSOW(question.option_d)}<br>
            <span class="text-success fw-bold">Correct Option: ${escSOW(question.correct_option)}</span>
          </div>`;
        });
      }
    } catch(e) {
      quizHtml = `<hr><p class="text-danger small">Invalid Quiz JSON.</p>`;
    }
  }

  Swal.fire({
    title: `<span style="font-size: 1.1rem; font-weight:700;">${escSOW(row.topic)}</span>`,
    html: `
      <div class="text-start" style="max-height: 420px; overflow-y: auto; font-size: .88rem; line-height: 1.6;">
        <h6 class="fw-bold text-dark"><i class="fa fa-book-reader text-primary me-2"></i>Doctored Lecture Notes</h6>
        <div class="p-3 border rounded bg-white mb-3" style="white-space: pre-wrap; font-family: Inter, sans-serif; background: #fafafa;">${escSOW(row.lecture_content || 'No notes generated yet.')}</div>
        ${quizHtml}
      </div>
    `,
    confirmButtonText: 'Close',
    width: '650px'
  });
}

function escSOW(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});
</script>
