<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireInstructorLogin();
$ins = $_SESSION['instructor'];

$courseId   = (int)($_GET['course_id'] ?? 20);
$courseType = $_GET['type'] ?? 'affiliate'; // 'affiliate' or 'normal'

if ($courseType !== 'normal' && $courseType !== 'affiliate') {
    $courseType = 'affiliate';
}

if ($courseType === 'normal') {
    $stmtCourse = $pdo->prepare("SELECT * FROM lms_courses WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmtCourse->execute([$courseId]);
    $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);
} else {
    $stmtCourse = $pdo->prepare("SELECT * FROM lms_affiliate_courses WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmtCourse->execute([$courseId]);
    $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);
}

if (!$course) {
    // Fallback: try first active affiliate course
    $course = $pdo->query("SELECT * FROM lms_affiliate_courses WHERE is_active = 1 ORDER BY title ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($course) {
        $courseId = (int)$course['id'];
        $courseType = 'affiliate';
    } else {
        // Try normal course
        $course = $pdo->query("SELECT * FROM lms_courses WHERE is_active = 1 ORDER BY title ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($course) {
            $courseId = (int)$course['id'];
            $courseType = 'normal';
        }
    }
}

// Determine course track type for dynamic pedagogical content
$courseTitle = $course ? ($course['title'] ?? '') : '';
$trackType = 'general';
if (stripos($courseTitle, 'robotics') !== false || stripos($courseTitle, 'hardware') !== false) {
    $trackType = 'robotics';
} elseif (
    stripos($courseTitle, 'design') !== false || 
    stripos($courseTitle, 'ui') !== false || 
    stripos($courseTitle, 'ux') !== false || 
    stripos($courseTitle, 'graphic') !== false
) {
    $trackType = 'design';
} elseif (
    stripos($courseTitle, 'development') !== false || 
    stripos($courseTitle, 'web') !== false || 
    stripos($courseTitle, 'php') !== false || 
    stripos($courseTitle, 'mysql') !== false || 
    stripos($courseTitle, 'programming') !== false || 
    stripos($courseTitle, 'software') !== false || 
    stripos($courseTitle, 'ai') !== false || 
    stripos($courseTitle, 'ml') !== false || 
    stripos($courseTitle, 'science') !== false || 
    stripos($courseTitle, 'intelligence') !== false || 
    stripos($courseTitle, 'learning') !== false
) {
    $trackType = 'software';
}

// Fetch all courses for selection dropdown
$allAffiliateCourses = $pdo->query("SELECT id, title FROM lms_affiliate_courses WHERE is_active = 1 ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
$allNormalCourses    = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch scheme of work or lessons grouped
$sowGrouped = [];
$lessons = [];
$exams = [];

if ($course) {
    if ($courseType === 'affiliate') {
        $stmtSow = $pdo->prepare("
            SELECT id, class_level, term, week_number, topic, objectives, activities, lecture_content, quiz_json
            FROM lms_affiliate_scheme_of_work
            WHERE course_id = ?
            ORDER BY 
                CASE class_level
                    WHEN 'JSS1' THEN 1
                    WHEN 'JSS2' THEN 2
                    WHEN 'JSS3' THEN 3
                    WHEN 'SSS1' THEN 4
                    WHEN 'SSS2' THEN 5
                    WHEN 'SSS3' THEN 6
                    ELSE 7
                END,
                CASE term
                    WHEN '1st' THEN 1
                    WHEN '2nd' THEN 2
                    WHEN '3rd' THEN 3
                    ELSE 4
                END,
                week_number ASC
        ");
        $stmtSow->execute([$courseId]);
        $sowRows = $stmtSow->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sowRows as $row) {
            $sowGrouped[$row['class_level']][$row['term']][] = $row;
        }
    } else {
        // Fetch lessons
        $stmtLessons = $pdo->prepare("
            SELECT id, title, content, sort_order 
            FROM lms_lessons 
            WHERE course_id = ? AND is_published = 1 
            ORDER BY sort_order ASC
        ");
        $stmtLessons->execute([$courseId]);
        $lessons = $stmtLessons->fetchAll(PDO::FETCH_ASSOC);

        // Fetch exams
        $stmtExams = $pdo->prepare("
            SELECT id, title, duration_minutes, pass_mark 
            FROM lms_exams 
            WHERE course_id = ? AND is_published = 1
        ");
        $stmtExams->execute([$courseId]);
        $exams = $stmtExams->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = $course ? $course['title'] . ' - Tutor Guide' : 'Affiliate Course Tutor Guide';
$seoDesc    = 'Instructor companion tutor guide and curriculum schemes of work at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body {
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
  }
  .guide-header {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    color: #fff;
    padding: 3rem 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
  }
  .badge-level {
    background-color: #4f46e5;
    font-size: .8rem;
    padding: .35rem .75rem;
    border-radius: 9999px;
  }
  .card-guide {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    overflow: hidden;
  }
  .card-guide-header {
    background-color: #f1f5f9;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 1.1rem;
    color: #1e293b;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .card-guide-body {
    padding: 1.5rem;
  }
  .level-tab-btn {
    border-radius: 8px;
    font-weight: 600;
    padding: .5rem 1.25rem;
    border: 1px solid transparent;
    transition: all .2s;
  }
  .level-tab-btn.active {
    background-color: #4f46e5;
    color: #fff;
  }
  .term-header {
    background: #e0e7ff;
    color: #3730a3;
    padding: .75rem 1.25rem;
    font-weight: 700;
    border-radius: 8px;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-size: 1rem;
  }
  .lecture-notes-block {
    background-color: #f8fafc;
    border-left: 4px solid #4f46e5;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    font-size: .9rem;
    margin-top: .5rem;
  }
  .quiz-block {
    background-color: #fffbeb;
    border-left: 4px solid #d97706;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    font-size: .9rem;
    margin-top: .5rem;
  }
  @media print {
    .no-print {
      display: none !important;
    }
    body {
      background-color: #fff;
    }
    .guide-header {
      background: none !important;
      color: #000 !important;
      padding: 0 !important;
      box-shadow: none !important;
      margin-bottom: 2rem;
    }
    .guide-header h1 {
      color: #000 !important;
      font-size: 2rem !important;
    }
    .card-guide {
      box-shadow: none !important;
      border: none !important;
      margin-bottom: 3rem !important;
    }
    .card-guide-header {
      background: none !important;
      border-bottom: 2px solid #000 !important;
      padding: 0 0 .5rem 0 !important;
      color: #000 !important;
    }
    .tab-content > .tab-pane {
      display: block !important;
      opacity: 1 !important;
    }
    .lecture-notes-block, .quiz-block {
      page-break-inside: avoid;
    }
  }
</style>
</head>
<body>

<nav class="lms-nav lms-nav-instructor no-print">
  <div class="container px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">T</div>
      <span style="color:#fff">Tutor Guide <span style="color:#c7d2fe">Companion</span></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="instructor_dashboard.php" style="font-size:.85rem;color:rgba(255,255,255,.9)" class="fw-semibold"><i class="fa fa-arrow-left me-1"></i>Back to Dashboard</a>
    </div>
  </div>
</nav>

<div class="container my-5">
  
  <?php if (!$course): ?>
    <div class="alert alert-warning text-center">
      <h4>No Active Affiliate Courses Found</h4>
      <p class="mb-0">Please register or activate courses in the administrator panel.</p>
    </div>
  <?php else: ?>

    <!-- HEADER / COURSE SELECTOR -->
    <div class="guide-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
      <div>
        <span class="badge badge-level mb-2 text-uppercase"><?= e($course['level']) ?> Course Track</span>
        <h1 class="fw-bold mb-1"><?= e($course['title']) ?> Tutor Guide</h1>
        <p class="mb-0 text-indigo-200" style="opacity: 0.85; max-width: 600px;"><?= e($course['description'] ?: $course['short_description']) ?></p>
      </div>
      <div class="no-print d-flex gap-2">
        <select class="form-select w-auto bg-white border-0 text-dark" style="font-weight:600;" onchange="const [id, type] = this.value.split('-'); location.href='instructor_tutor_guide.php?course_id=' + id + '&type=' + type">
          <optgroup label="Affiliate Courses">
            <?php foreach ($allAffiliateCourses as $c): ?>
              <option value="<?= (int)$c['id'] ?>-affiliate" <?= ($courseType === 'affiliate' && $c['id'] == $courseId) ? 'selected' : '' ?>><?= e($c['title']) ?> (Affiliate)</option>
            <?php endforeach; ?>
          </optgroup>
          <optgroup label="Normal LMS Courses">
            <?php foreach ($allNormalCourses as $c): ?>
              <option value="<?= (int)$c['id'] ?>-normal" <?= ($courseType === 'normal' && $c['id'] == $courseId) ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </optgroup>
        </select>
        <button class="btn btn-light fw-bold" onclick="window.print()"><i class="fa fa-print me-1"></i> Print Guide</button>
      </div>
    </div>

    <!-- PEDAGOGICAL METHODOLOGY -->
    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card-guide h-100">
          <div class="card-guide-header">
            <span><i class="fa fa-graduation-cap me-2 text-primary"></i>Pedagogical Methodology</span>
          </div>
          <div class="card-guide-body">
            <ul class="d-flex flex-column gap-3 mb-0" style="padding-left: 1.2rem;">
              <?php if ($trackType === 'robotics'): ?>
                <li>
                  <strong>Active Learning Cycle:</strong> Move away from pure lecturing. Always integrate hands-on build phases (wiring, breadboarding, code editing) within 15 minutes of introducing any hardware concept.
                </li>
                <li>
                  <strong>Fail-Fast & Debug:</strong> Encourage students to troubleshoot their own wiring errors. Teach them to use a digital multimeter (DMM) and monitor serial console output early on.
                </li>
                <li>
                  <strong>Safety First:</strong> For electronics and robotics modules, enforce strict handling rules for Li-ion battery terminals, prevent polarity reversals, and ensure proper workspace ventilation.
                </li>
              <?php elseif ($trackType === 'design'): ?>
                <li>
                  <strong>Visual Discovery & Critique:</strong> Avoid lecturing. Begin with visual case studies. Introduce design theory (typography, grids, color harmony) by analyzing real-world posters and interfaces.
                </li>
                <li>
                  <strong>Iterative Sketching:</strong> Require wireframes, layout drafts, or moodboards on paper before opening digital design tools (Photoshop, Figma, Illustrator).
                </li>
                <li>
                  <strong>Critique Culture:</strong> Dedicate the last 15 minutes of class to peer critiques, helping students learn to give and receive constructive design feedback.
                </li>
              <?php elseif ($trackType === 'software'): ?>
                <li>
                  <strong>Code-Along Sessions:</strong> Avoid passive listening. Conduct interactive code-along sessions where students write code concurrently with the tutor.
                </li>
                <li>
                  <strong>Console-First Debugging:</strong> Teach students how to read error traces, use browser developer tools (inspect console, network tab), and write unit tests early.
                </li>
                <li>
                  <strong>Architectural Literacy:</strong> Encourage modular, reusable code architectures (OOP, MVC, APIs) over copy-pasting code snippets.
                </li>
              <?php else: ?>
                <li>
                  <strong>Scenario-Based Learning:</strong> Start classes with real-world problems (e.g., client lost their password, network connection failed) to teach troubleshooting.
                </li>
                <li>
                  <strong>Hands-On Configuration:</strong> Prioritize live terminal/command execution, router setups, cloud provisioning, or hardware configurations.
                </li>
                <li>
                  <strong>Security & Compliance:</strong> Emphasize security standards (passwords, MFA, protocol encryption) in every lesson.
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card-guide h-100">
          <div class="card-guide-header">
            <span><i class="fa fa-clipboard-check me-2 text-warning"></i>Project Grading Rubric</span>
          </div>
          <div class="card-guide-body">
            <ul class="d-flex flex-column gap-2 mb-0" style="padding-left: 1.2rem;">
              <?php if ($trackType === 'robotics'): ?>
                <li><strong>Excellent (80-100%):</strong> Robot or system fulfills all logic requirements cleanly. Code is well-commented, circuits are organized, and student explains the flow autonomously.</li>
                <li><strong>Good (60-79%):</strong> System functions correctly, but has cluttered cabling, minor mechanical stability issues, or requires brief manual overrides.</li>
                <li><strong>Satisfactory (50-59%):</strong> Inputs and outputs are connected correctly, but the system logic contains significant bugs or crashes during operation.</li>
                <li><strong>Unsatisfactory (<50%):</strong> Non-functional project, code failure to compile, or total lack of theoretical understanding.</li>
              <?php elseif ($trackType === 'design'): ?>
                <li><strong>Excellent (80-100%):</strong> Design displays strong composition, typography hierarchy, and a clear brand identity. Asset alignment is pixel-perfect, and execution is clean.</li>
                <li><strong>Good (60-79%):</strong> Layout is visually appealing but has minor alignment, contrast, or typographic inconsistencies.</li>
                <li><strong>Satisfactory (50-59%):</strong> Basic design requirements met, but lacks creative depth, uses poor font pairings, or has low-contrast elements.</li>
                <li><strong>Unsatisfactory (<50%):</strong> Unfinished design files, complete disregard for design principles, or plagiarized work.</li>
              <?php elseif ($trackType === 'software'): ?>
                <li><strong>Excellent (80-100%):</strong> Application is fully functional, secure, and complies with clean coding guidelines. Handles edge cases and explains logic/architecture cleanly.</li>
                <li><strong>Good (60-79%):</strong> Core features work correctly, but has minor code repetition, suboptimal DB queries, or missing error handling.</li>
                <li><strong>Satisfactory (50-59%):</strong> App runs but crashes on edge cases, has security vulnerabilities (e.g. no SQL validation), or lacks basic comments.</li>
                <li><strong>Unsatisfactory (<50%):</strong> Code fails to compile/run, or has total reliance on copy-pasted blocks with no comprehension.</li>
              <?php else: ?>
                <li><strong>Excellent (80-100%):</strong> System/network configured perfectly. Meets all security requirements, shows optimal throughput, and student explains the infrastructure layout.</li>
                <li><strong>Good (60-79%):</strong> Configuration works, but lacks optimized performance settings or complete documentation.</li>
                <li><strong>Satisfactory (50-59%):</strong> System functions but has security gaps, incorrect IP subnetting, or unorganized settings.</li>
                <li><strong>Unsatisfactory (<50%):</strong> Non-functional setup, inability to ping/connect, or total lack of theoretical network/cloud understanding.</li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- SCHEMES OF WORK / CURRICULUM CONTENT -->
    <?php if ($courseType === 'normal'): ?>
      <h3 class="fw-bold mb-4 d-flex align-items-center gap-2">
        <i class="fa fa-book-open text-indigo"></i> 
        Course Lessons & Exams
      </h3>

      <div class="card-guide">
        <div class="card-guide-header">
          <span>Normal LMS Course Lessons</span>
          <span class="badge bg-secondary text-white"><?= count($lessons) ?> Lessons Total</span>
        </div>
        <div class="card-guide-body">
          <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle" style="font-size: .88rem;">
              <thead class="table-light text-dark fw-bold">
                <tr>
                  <th style="width: 70px;" class="text-center">Order</th>
                  <th style="width: 250px;">Lesson Title</th>
                  <th>Preview Content</th>
                  <th style="width: 200px;" class="text-center no-print">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lessons as $row): ?>
                  <tr class="table-row-week">
                    <td class="text-center fw-bold text-indigo" style="font-size: 1.05rem;"><?= (int)$row['sort_order'] ?></td>
                    <td><div class="fw-bold mb-1 text-dark" style="font-size: .92rem;"><?= e($row['title']) ?></div></td>
                    <td class="text-muted"><?= e(mb_strimwidth(strip_tags((string)$row['content']), 0, 140, '...')) ?></td>
                    <td class="text-center no-print">
                      <button class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:.75rem; border-radius: 6px;" type="button" data-bs-toggle="collapse" data-bs-target="#lecture-<?= (int)$row['id'] ?>" aria-expanded="false">
                        <i class="fa fa-eye me-1"></i> View Lecture Notes
                      </button>
                    </td>
                  </tr>
                  <tr class="collapse no-print bg-light" id="lecture-<?= (int)$row['id'] ?>">
                    <td colspan="4" class="p-3">
                      <div class="lecture-notes-block">
                        <h6 class="fw-bold text-indigo mb-2"><i class="fa fa-book-reader me-2"></i>Lecture Notes</h6>
                        <div style="white-space: pre-wrap; font-size:.82rem; font-family: monospace; background: #fff; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; max-height: 400px; overflow-y: auto;"><?= e($row['content'] ?: 'No content configured.') ?></div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($lessons)): ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">No lessons found for this course.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- EXAMS & QUIZZES -->
      <div class="card-guide mt-4">
        <div class="card-guide-header">
          <span>Course Exams & Quizzes</span>
          <span class="badge bg-secondary text-white"><?= count($exams) ?> Exam(s) Total</span>
        </div>
        <div class="card-guide-body">
          <?php foreach ($exams as $ex): ?>
            <div class="term-header">
              <i class="fa fa-file-signature me-2"></i><?= e($ex['title']) ?> (Duration: <?= (int)$ex['duration_minutes'] ?> mins, Pass Mark: <?= (int)$ex['pass_mark'] ?>)
            </div>
            
            <div class="table-responsive mb-4">
              <table class="table table-bordered align-middle" style="font-size: .88rem;">
                <thead class="table-light text-dark fw-bold">
                  <tr>
                    <th style="width: 250px;">Exam Question</th>
                    <th>Answer Options</th>
                    <th style="width: 150px;" class="text-center">Correct Answer</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $stmtQuestions = $pdo->prepare("
                      SELECT question, option_a, option_b, option_c, option_d, correct_option 
                      FROM lms_exam_questions 
                      WHERE exam_id = ?
                      ORDER BY id ASC
                  ");
                  $stmtQuestions->execute([$ex['id']]);
                  $questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($questions as $qIdx => $q): 
                  ?>
                    <tr class="table-row-week">
                      <td><strong>Q<?= ($qIdx+1) ?>:</strong> <?= e($q['question']) ?></td>
                      <td>
                        A) <?= e($q['option_a']) ?><br>
                        B) <?= e($q['option_b']) ?><br>
                        C) <?= e($q['option_c']) ?><br>
                        D) <?= e($q['option_d']) ?>
                      </td>
                      <td class="text-center"><span class="badge bg-success text-white fw-bold"><?= e($q['correct_option']) ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($questions)): ?>
                    <tr>
                      <td colspan="3" class="text-center text-muted py-3">No questions configured for this exam.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          <?php endforeach; ?>
          <?php if (empty($exams)): ?>
            <div class="text-center text-muted py-3">No exams configured for this course.</div>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <!-- SCHEMES OF WORK BY LEVEL (Existing Affiliate Code) -->
      <h3 class="fw-bold mb-4 d-flex align-items-center gap-2">
        <i class="fa fa-book-open text-indigo"></i> 
        Class Level Curriculum Schemes
      </h3>

      <ul class="nav nav-pills gap-2 mb-4 no-print" id="pills-tab" role="tablist">
        <?php 
        $levels = ['JSS1', 'JSS2', 'JSS3', 'SSS1', 'SSS2', 'SSS3'];
        $isFirst = true;
        foreach ($levels as $lvl): 
          if (!isset($sowGrouped[$lvl])) continue;
        ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link level-tab-btn <?= $isFirst ? 'active' : '' ?>" id="pills-<?= $lvl ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $lvl ?>" type="button" role="tab" aria-controls="pills-<?= $lvl ?>" aria-selected="<?= $isFirst ? 'true' : 'false' ?>">
              <?= e($lvl) ?> Track
            </button>
          </li>
        <?php 
          $isFirst = false;
        endforeach; 
        ?>
      </ul>

      <div class="tab-content" id="pills-tabContent">
        <?php 
        $isFirst = true;
        foreach ($levels as $lvl): 
          if (!isset($sowGrouped[$lvl])) continue;
        ?>
          <div class="tab-pane fade <?= $isFirst ? 'show active' : '' ?>" id="pills-<?= $lvl ?>" role="tabpanel" aria-labelledby="pills-<?= $lvl ?>-tab">
            
            <div class="card-guide">
              <div class="card-guide-header">
                <span><?= e($lvl) ?> Full Academic Scheme of Work</span>
                <span class="badge bg-secondary text-white"><?= count($sowGrouped[$lvl], COUNT_RECURSIVE) - count($sowGrouped[$lvl]) ?> Weeks Total</span>
              </div>
              <div class="card-guide-body">
                
                <?php foreach (['1st', '2nd', '3rd'] as $termName): ?>
                  <?php if (!isset($sowGrouped[$lvl][$termName])) continue; ?>
                  
                  <div class="term-header">
                    <i class="fa fa-calendar-alt me-2"></i><?= e($termName) ?> Term
                  </div>

                  <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle" style="font-size: .88rem;">
                      <thead class="table-light text-dark fw-bold">
                        <tr>
                          <th style="width: 70px;" class="text-center">Week</th>
                          <th style="width: 250px;">Topic & Theme</th>
                          <th>Objectives</th>
                          <th class="d-none d-md-table-cell">Activities</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($sowGrouped[$lvl][$termName] as $row): ?>
                          <tr class="table-row-week">
                            <td class="text-center fw-bold text-indigo" style="font-size: 1.05rem;"><?= (int)$row['week_number'] ?></td>
                            <td>
                              <div class="fw-bold mb-1 text-dark" style="font-size: .92rem;"><?= e($row['topic']) ?></div>
                              <div class="no-print mt-2">
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2 font-monospace" style="font-size:.7rem; border-radius: 4px;" type="button" data-bs-toggle="collapse" data-bs-target="#lecture-<?= (int)$row['id'] ?>" aria-expanded="false">
                                  <i class="fa fa-eye me-1"></i> View Lecture Notes & Quiz
                                </button>
                              </div>
                            </td>
                            <td><?= $row['objectives'] ?></td>
                            <td class="d-none d-md-table-cell text-muted"><?= $row['activities'] ?></td>
                          </tr>
                          <!-- Collapsible Lecture notes and Quiz (only for UI convenience, always shown in print view via css/html structure when needed) -->
                          <tr class="collapse no-print bg-light" id="lecture-<?= (int)$row['id'] ?>">
                            <td colspan="4" class="p-3">
                              <div class="row g-3">
                                <div class="col-lg-7">
                                  <div class="lecture-notes-block">
                                    <h6 class="fw-bold text-indigo mb-2"><i class="fa fa-book-reader me-2"></i>Doctored Lecture Notes</h6>
                                    <div style="white-space: pre-wrap; font-size:.82rem; font-family: monospace; background: #fff; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; max-height: 250px; overflow-y: auto;"><?= e($row['lecture_content'] ?: 'No notes generated yet.') ?></div>
                                  </div>
                                </div>
                                <div class="col-lg-5">
                                  <div class="quiz-block">
                                    <h6 class="fw-bold text-amber-800 mb-2"><i class="fa fa-tasks me-2"></i>Assessment Quiz Questions</h6>
                                    <div style="font-size:.82rem; background: #fff; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; max-height: 250px; overflow-y: auto;">
                                      <?php 
                                      $quiz = json_decode((string)$row['quiz_json'], true);
                                      if ($quiz && isset($quiz['questions'])):
                                        echo '<strong class="text-dark d-block mb-2">' . e($quiz['title'] ?? 'Quiz') . '</strong>';
                                        foreach ($quiz['questions'] as $qIdx => $q):
                                          echo '<div class="mb-2">';
                                          echo '<strong>Q' . ($qIdx+1) . ':</strong> ' . e($q['question']) . '<br>';
                                          echo 'A) ' . e($q['option_a']) . ' | ';
                                          echo 'B) ' . e($q['option_b']) . ' | ';
                                          echo 'C) ' . e($q['option_c']) . ' | ';
                                          echo 'D) ' . e($q['option_d']) . '<br>';
                                          echo '<span class="text-success fw-bold">Correct: ' . e($q['correct_option']) . '</span>';
                                          echo '</div>';
                                        endforeach;
                                      else:
                                        echo '<span class="text-muted">No quiz configured.</span>';
                                      endif;
                                      ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>

                <?php endforeach; ?>

              </div>
            </div>

          </div>
        <?php 
          $isFirst = false;
        endforeach; 
        ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
