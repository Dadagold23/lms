<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/enrollment_access.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$lessonId  = (int)($_GET['id'] ?? 0);
$courseId  = (int)($_GET['course_id'] ?? 0); // optional hint

if ($lessonId <= 0) { http_response_code(400); exit('Invalid lesson.'); }

/* ── Fetch lesson ── */
$stmt = $pdo->prepare("SELECT id, course_id, title, content, sort_order FROM lms_lessons WHERE id=? AND is_published=1 LIMIT 1");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lesson) { http_response_code(404); exit('Lesson not found.'); }

$courseId = (int)$lesson['course_id'];

/* ── Enrollment + access check ── */
$en = $pdo->prepare("
    SELECT e.paid_amount, e.status, e.access_expires_at, e.payment_type, e.next_due_date, e.created_at, c.price, c.title AS course_title, c.slug" . workspaceCourseSelectSql($pdo, 'c') . "
    FROM lms_enrollments e
    JOIN lms_courses c ON c.id = e.course_id
    WHERE e.student_id=? AND e.course_id=?
    LIMIT 1
");
$en->execute([$studentId, $courseId]);
$enrollment = workspaceCourseRow((array)($en->fetch(PDO::FETCH_ASSOC) ?: []));

if (!$enrollment) { http_response_code(403); exit('Access denied: not enrolled in this course.'); }

$access = enrollmentAccessState($enrollment);
$isUnlocked = (bool)$access['is_unlocked'];

if (!$isUnlocked) redirect(courseUrl($enrollment));

/* ── All lessons ordered for this course ── */
$allLessons = $pdo->prepare("SELECT id, title FROM lms_lessons WHERE course_id=? AND is_published=1 ORDER BY sort_order ASC, id ASC");
$allLessons->execute([$courseId]);
$allLessons = $allLessons->fetchAll(PDO::FETCH_ASSOC);

/* ── Find position of current lesson ── */
$lessonIndex = null;
$prevLesson  = null;
$nextLesson  = null;
foreach ($allLessons as $i => $l) {
    if ((int)$l['id'] === $lessonId) {
        $lessonIndex = $i;
        $prevLesson  = $allLessons[$i - 1] ?? null;
        $nextLesson  = $allLessons[$i + 1] ?? null;
        break;
    }
}

/* ── Completed lesson IDs ── */
$completedIds = [];
try {
    $cStmt = $pdo->prepare("SELECT lesson_id FROM lms_lesson_completions WHERE student_id=? AND course_id=?");
    $cStmt->execute([$studentId, $courseId]);
    $completedIds = array_column($cStmt->fetchAll(PDO::FETCH_ASSOC), 'lesson_id', 'lesson_id');
} catch (Throwable $e) {}

/* ── Enrollment date for weekly schedule ── */
$enrolledAt = null;
try {
    $enDate = $pdo->prepare("SELECT created_at FROM lms_enrollments WHERE student_id=? AND course_id=? LIMIT 1");
    $enDate->execute([$studentId, $courseId]);
    $enrolledAt = $enDate->fetchColumn();
} catch (Throwable $e) {}

/* ── Sequential lock: lesson N requires lesson N-1 completed AND weekly slot open ──
   Week 1 = lessons 1 (index 0), Week 2 = lesson 2 (index 1), etc.
   Each lesson unlocks 7 days after enrollment start + (index * 7) days.
   The entire course spans 26 weeks (6 months).
*/
$now = time();
$enrollTs = $enrolledAt ? strtotime((string)$enrolledAt) : $now;

function lessonUnlockTime(int $index, int $enrollTs): int {
    return $enrollTs + ($index * 7 * 24 * 3600);
}

if ($lessonIndex !== null && $lessonIndex > 0) {
    // Must have completed the previous lesson
    $prevId = (int)$allLessons[$lessonIndex - 1]['id'];
    $prevCompleted = isset($completedIds[$prevId]);

    // Weekly slot: lesson unlocks 7 days after enrollment per lesson index
    $unlockTime = lessonUnlockTime($lessonIndex, $enrollTs);
    $weeklyOpen = $now >= $unlockTime;

    if (!$prevCompleted || !$weeklyOpen) {
        $unlockDate = date('d M Y', $unlockTime);
        $lockReason = !$prevCompleted
            ? 'You must complete the previous lesson first.'
            : "This lesson unlocks on {$unlockDate}.";
        // Redirect back to lesson list with a message
        redirect('course_lessons.php?course_id=' . $courseId . '&locked=1&msg=' . urlencode($lockReason));
    }
}

$courseTitle = (string)$enrollment['course_title'];
$title       = (string)$lesson['title'];
$content     = (string)$lesson['content'];

/* ── Fetch lesson assessment ── */
$assessment     = null;
$assessQuestions = [];
$assessResult   = null;
$assessPassed   = false;
$assessAttempts = 0;

try {
    $aStmt = $pdo->prepare("SELECT * FROM lms_lesson_assessments WHERE lesson_id=? AND is_required=1 LIMIT 1");
    $aStmt->execute([$lessonId]);
    $assessment = $aStmt->fetch(PDO::FETCH_ASSOC);

    if ($assessment) {
        $aid = (int)$assessment['id'];
        $qStmt = $pdo->prepare("SELECT * FROM lms_assessment_questions WHERE assessment_id=? ORDER BY sort_order ASC");
        $qStmt->execute([$aid]);
        $assessQuestions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

        // Best result
        $rStmt = $pdo->prepare("SELECT * FROM lms_assessment_submissions WHERE assessment_id=? AND student_id=? ORDER BY percent DESC LIMIT 1");
        $rStmt->execute([$aid, $studentId]);
        $assessResult = $rStmt->fetch(PDO::FETCH_ASSOC);
        $assessPassed = $assessResult && (int)$assessResult['passed'] === 1;

        // Attempt count
        $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_assessment_submissions WHERE assessment_id=? AND student_id=?");
        $cntStmt->execute([$aid, $studentId]);
        $assessAttempts = (int)$cntStmt->fetchColumn();
    }
} catch (Throwable $e) {}

/* ── Handle assessment submission ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assessment']) && $assessment) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $aid      = (int)$assessment['id'];
    $answers  = $_POST['ans'] ?? [];
    $score    = 0;
    $total    = count($assessQuestions);
    $passPct  = (int)($assessment['pass_score'] ?? 60);

    foreach ($assessQuestions as $q) {
        $chosen  = strtoupper(trim((string)($answers[(int)$q['id']] ?? '')));
        $correct = strtoupper(trim((string)$q['correct_option']));
        if ($chosen !== '' && $chosen === $correct) $score++;
    }

    $percent = $total > 0 ? round($score / $total * 100, 2) : 0;
    $passed  = $percent >= $passPct ? 1 : 0;
    $attempt = $assessAttempts + 1;

    $pdo->prepare("INSERT INTO lms_assessment_submissions (assessment_id,student_id,score,total,percent,passed,attempt) VALUES (?,?,?,?,?,?,?)")
        ->execute([$aid, $studentId, $score, $total, $percent, $passed, $attempt]);

    // If passed, mark lesson complete
    if ($passed) {
        try {
            $pdo->prepare("INSERT IGNORE INTO lms_lesson_completions (student_id,lesson_id,course_id) VALUES (?,?,?)")
                ->execute([$studentId, $lessonId, $courseId]);
        } catch (Throwable $e) {}
    }

    redirect('lesson.php?id=' . $lessonId . '&course_id=' . $courseId . '&assessed=1');
}

/* ── Mark lesson as completed (only if no required assessment, or assessment already passed) ── */
$hasRequiredAssessment = !empty($assessment);
$shouldAutoComplete    = !$hasRequiredAssessment || $assessPassed;

if ($shouldAutoComplete) {
    try {
        $pdo->prepare("
            INSERT IGNORE INTO lms_lesson_completions (student_id, lesson_id, course_id)
            VALUES (?,?,?)
        ")->execute([$studentId, $lessonId, $courseId]);

        // Update lms_progress
        $totalLessons = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE course_id={$courseId} AND is_published=1")->fetchColumn();
        $completedLessons = (int)$pdo->query("SELECT COUNT(*) FROM lms_lesson_completions WHERE student_id={$studentId} AND course_id={$courseId}")->fetchColumn();
        $pct = $totalLessons > 0 ? round($completedLessons / $totalLessons * 100, 2) : 0;
        $pdo->prepare("
            INSERT INTO lms_progress (student_id, course_id, completed_lessons, total_lessons, percent)
            VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE completed_lessons=VALUES(completed_lessons), total_lessons=VALUES(total_lessons), percent=VALUES(percent)
        ")->execute([$studentId, $courseId, $completedLessons, $totalLessons, $pct]);
    } catch (Throwable $e) {
        // lms_lesson_completions table may not exist yet — silently skip
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Lesson';
$seoDesc    = 'Study your course lesson at Grafix@Mirror LMS — Mirror Age Concepts professional technology training.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<title><?= e($title) ?> | <?= e($courseTitle) ?> | Grafix@Mirror LMS</title>
<meta name="description" content="<?= e(mb_substr(strip_tags($content), 0, 160)) ?>">
<meta name="robots" content="noindex,nofollow">
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
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:820px">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.82rem">
      <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= e(courseUrl($enrollment)) ?>"><?= e($courseTitle) ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= e($title) ?></li>
    </ol>
  </nav>

  <div class="lms-card">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3" style="margin-bottom:1.5rem">
      <h3 style="font-weight:800;margin-bottom:0"><?= e($title) ?></h3>
      <a href="ai_tutor.php?course_id=<?= $courseId ?>&lesson_id=<?= $lessonId ?>" class="btn-brand" style="font-size:.9rem">
        <i class="fa fa-robot me-1"></i>Ask AI About This Lesson
      </a>
    </div>

    <?php if ($content !== ''): ?>
      <div class="lh-lg" style="font-size:.95rem;color:var(--dark)">
        <?= nl2br(e($content)) ?>
      </div>
    <?php else: ?>
      <p class="text-muted">No content for this lesson yet.</p>
    <?php endif; ?>
  </div>

  <div class="lms-card mt-4" id="lesson-ai-panel">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
      <div>
        <h4 style="font-weight:800;margin-bottom:.35rem"><i class="fa fa-robot me-2" style="color:var(--brand)"></i>Lesson AI Tutor</h4>
        <p class="text-muted mb-0" style="font-size:.9rem">Ask a question without leaving this lesson. The tutor uses this lesson and course as context.</p>
      </div>
      <a href="ai_tutor.php?course_id=<?= $courseId ?>&lesson_id=<?= $lessonId ?>" class="btn-ghost" style="font-size:.85rem">
        Open Full Tutor
      </a>
    </div>

    <div id="lessonTutorMessages" style="display:flex;flex-direction:column;gap:.85rem;max-height:320px;overflow-y:auto;padding:.25rem 0 1rem">
      <div style="max-width:88%;padding:.85rem 1rem;border-radius:14px;background:var(--surface);border:1px solid var(--border);line-height:1.6">
        Ask about this lesson, request an example, or get practice questions.
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap mb-3">
      <button type="button" class="btn-ghost" style="font-size:.8rem;padding:.35rem .7rem" onclick="fillLessonTutor('Summarise this lesson in simple terms')">Summarise</button>
      <button type="button" class="btn-ghost" style="font-size:.8rem;padding:.35rem .7rem" onclick="fillLessonTutor('Give me 3 practice questions from this lesson')">Practice Questions</button>
      <button type="button" class="btn-ghost" style="font-size:.8rem;padding:.35rem .7rem" onclick="fillLessonTutor('Explain this lesson with a real-world example')">Real-World Example</button>
    </div>

    <div class="d-flex gap-2">
      <textarea id="lessonTutorInput" class="form-control" rows="2" placeholder="Ask a question about this lesson..." style="resize:vertical"></textarea>
      <button type="button" class="btn-brand" id="lessonTutorSend" onclick="sendLessonTutorMessage()" style="white-space:nowrap">
        <i class="fa fa-paper-plane me-1"></i>Ask
      </button>
    </div>
  </div>

  <!-- ═══ LESSON ASSESSMENT ═══ -->
  <?php if ($assessment && !empty($assessQuestions)): ?>
  <div class="lms-card mt-4" id="assessment-section">

    <?php
    $typeLabel = match($assessment['type']) {
        'practical'  => 'Practical Exercise',
        'assignment' => 'Assignment',
        default      => 'Lesson Test',
    };
    $typeIcon = match($assessment['type']) {
        'practical'  => 'fa-laptop-code',
        'assignment' => 'fa-tasks',
        default      => 'fa-clipboard-check',
    };
    $passPct = (int)($assessment['pass_score'] ?? 60);
    ?>

    <div class="form-section-title">
      <i class="fa <?= $typeIcon ?> me-2"></i><?= e($typeLabel) ?>: <?= e($assessment['title']) ?>
    </div>

    <?php if (!empty($assessment['instructions'])): ?>
      <div class="lms-alert lms-alert-info mb-3" style="font-size:.88rem">
        <i class="fa fa-info-circle me-1"></i><?= nl2br(e($assessment['instructions'])) ?>
      </div>
    <?php endif; ?>

    <!-- Previous result -->
    <?php if ($assessResult): ?>
      <div class="lms-alert lms-alert-<?= $assessPassed ? 'success' : 'warning' ?> mb-3">
        <i class="fa fa-<?= $assessPassed ? 'check-circle' : 'exclamation-circle' ?> me-1"></i>
        <?php if ($assessPassed): ?>
          <strong>Passed!</strong> Your best score: <?= number_format((float)$assessResult['percent'], 1) ?>%
          (<?= (int)$assessResult['score'] ?>/<?= (int)$assessResult['total'] ?> correct)
          — You may proceed to the next lesson.
        <?php else: ?>
          <strong>Not yet passed.</strong> Best score: <?= number_format((float)$assessResult['percent'], 1) ?>%
          (<?= (int)$assessResult['score'] ?>/<?= (int)$assessResult['total'] ?> correct).
          You need <?= $passPct ?>% to pass. Attempt <?= $assessAttempts ?> used.
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['assessed'])): ?>
      <div class="lms-alert lms-alert-<?= $assessPassed ? 'success' : 'danger' ?> mb-3">
        <i class="fa fa-<?= $assessPassed ? 'trophy' : 'times-circle' ?> me-1"></i>
        <?= $assessPassed
            ? 'Assessment passed! You can now proceed to the next lesson.'
            : 'Not passed this time. Review the lesson content and try again.' ?>
      </div>
    <?php endif; ?>

    <!-- Assessment form (show if not passed or retake) -->
    <?php if (!$assessPassed || isset($_GET['retake'])): ?>
      <form method="post" id="assessForm">
        <?= csrfField() ?>
        <input type="hidden" name="submit_assessment" value="1">

        <?php foreach ($assessQuestions as $idx => $q): ?>
          <?php $qid = (int)$q['id']; ?>
          <div class="mb-4 p-3 rounded" style="background:var(--surface);border:1px solid var(--border)">
            <div class="d-flex gap-3">
              <div style="width:30px;height:30px;border-radius:50%;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;flex-shrink:0">
                <?= $idx + 1 ?>
              </div>
              <div class="flex-grow-1">
                <div style="font-weight:600;margin-bottom:.6rem;font-size:.92rem"><?= e((string)$q['question']) ?></div>
                <?php foreach (['A'=>$q['option_a'],'B'=>$q['option_b'],'C'=>$q['option_c'],'D'=>$q['option_d']] as $k=>$opt): ?>
                  <?php if (empty($opt)) continue; ?>
                  <label class="d-flex align-items-center gap-2 p-2 mb-1 rounded" style="cursor:pointer;transition:background .15s;font-size:.88rem"
                         onmouseover="this.style.background='var(--brand-light)'" onmouseout="this.style.background=''">
                    <input type="radio" name="ans[<?= $qid ?>]" value="<?= e($k) ?>" style="accent-color:var(--brand)">
                    <span><strong><?= e($k) ?>.</strong> <?= e((string)$opt) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
          <div style="font-size:.82rem;color:var(--muted)">
            <i class="fa fa-info-circle me-1"></i>
            Pass mark: <?= $passPct ?>% &nbsp;·&nbsp; Attempt <?= $assessAttempts + 1 ?>
            <?php if ($assessAttempts > 0): ?>&nbsp;·&nbsp; <?= $assessAttempts ?> previous attempt<?= $assessAttempts > 1 ? 's' : '' ?><?php endif; ?>
          </div>
          <button type="submit" class="btn-brand" onclick="return confirm('Submit assessment? Make sure you have answered all questions.')">
            <i class="fa fa-paper-plane me-1"></i> Submit Assessment
          </button>
        </div>
      </form>
    <?php elseif ($assessPassed): ?>
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div style="color:var(--success);font-weight:600"><i class="fa fa-check-circle me-1"></i>Assessment completed successfully</div>
        <a href="?id=<?= $lessonId ?>&course_id=<?= $courseId ?>&retake=1" class="btn-ghost" style="font-size:.82rem">
          <i class="fa fa-redo me-1"></i> Retake
        </a>
      </div>
    <?php endif; ?>

  </div>
  <?php endif; ?>

  <!-- Prev / Next navigation -->
  <div class="d-flex justify-content-between mt-4 gap-3">
    <?php if ($prevLesson): ?>
      <a href="lesson.php?id=<?= (int)$prevLesson['id'] ?>&course_id=<?= $courseId ?>" class="btn-ghost d-flex align-items-center gap-2">
        <i class="fa fa-arrow-left"></i>
        <span class="d-none d-sm-inline"><?= e($prevLesson['title']) ?></span>
        <span class="d-sm-none">Previous</span>
      </a>
    <?php else: ?>
      <div></div>
    <?php endif; ?>

    <?php
    // Next lesson is only accessible if assessment is passed (or no assessment)
    $canProceed = !$assessment || $assessPassed;
    ?>

    <?php if ($nextLesson): ?>
      <?php if ($canProceed): ?>
        <a href="lesson.php?id=<?= (int)$nextLesson['id'] ?>&course_id=<?= $courseId ?>" class="btn-brand d-flex align-items-center gap-2">
          <span class="d-none d-sm-inline"><?= e($nextLesson['title']) ?></span>
          <span class="d-sm-none">Next</span>
          <i class="fa fa-arrow-right"></i>
        </a>
      <?php else: ?>
        <div class="d-flex align-items-center gap-2">
          <span style="font-size:.82rem;color:var(--muted)"><i class="fa fa-lock me-1"></i>Pass the assessment to unlock next lesson</span>
          <a href="#assessment-section" class="btn-brand" style="font-size:.85rem">
            <i class="fa fa-clipboard-check me-1"></i> Take Assessment
          </a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php if ($canProceed): ?>
        <a href="exams.php?course_id=<?= $courseId ?>" class="btn-brand d-flex align-items-center gap-2">
          <i class="fa fa-pen-alt me-1"></i> Take Final Exam
        </a>
      <?php else: ?>
        <div class="d-flex align-items-center gap-2">
          <span style="font-size:.82rem;color:var(--muted)"><i class="fa fa-lock me-1"></i>Pass the assessment first</span>
          <a href="#assessment-section" class="btn-brand" style="font-size:.85rem">
            <i class="fa fa-clipboard-check me-1"></i> Take Assessment
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

</div>
<script>
const lessonTutorCourseId = <?= (int)$courseId ?>;
const lessonTutorLessonId = <?= (int)$lessonId ?>;
const lessonTutorCsrf = <?= json_encode(csrfToken()) ?>;

function fillLessonTutor(text) {
  const input = document.getElementById('lessonTutorInput');
  if (!input) return;
  input.value = text;
  input.focus();
}

function escapeLessonTutorHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\n/g, '<br>');
}

function renderLessonTutorMarkdown(text) {
  return escapeLessonTutorHtml(text)
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/`([^`]+)`/g, '<code>$1</code>');
}

function appendLessonTutorMessage(role, text) {
  const wrap = document.getElementById('lessonTutorMessages');
  if (!wrap) return;

  const bubble = document.createElement('div');
  bubble.style.maxWidth = '88%';
  bubble.style.padding = '.85rem 1rem';
  bubble.style.borderRadius = '14px';
  bubble.style.lineHeight = '1.6';

  if (role === 'user') {
    bubble.style.alignSelf = 'flex-end';
    bubble.style.background = 'var(--brand)';
    bubble.style.color = '#fff';
    bubble.innerHTML = escapeLessonTutorHtml(text);
  } else {
    bubble.style.alignSelf = 'flex-start';
    bubble.style.background = 'var(--surface)';
    bubble.style.border = '1px solid var(--border)';
    bubble.innerHTML = renderLessonTutorMarkdown(text);
  }

  wrap.appendChild(bubble);
  wrap.scrollTop = wrap.scrollHeight;
}

async function sendLessonTutorMessage() {
  const input = document.getElementById('lessonTutorInput');
  const button = document.getElementById('lessonTutorSend');
  if (!input || !button) return;

  const message = input.value.trim();
  if (!message) return;

  appendLessonTutorMessage('user', message);
  input.value = '';
  button.disabled = true;

  try {
    const fd = new FormData();
    fd.append('message', message);
    fd.append('course_id', lessonTutorCourseId);
    fd.append('lesson_id', lessonTutorLessonId);
    fd.append('_csrf', lessonTutorCsrf);

    const res = await fetch('ai_tutor.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
      appendLessonTutorMessage('assistant', data.reply);
    } else {
      appendLessonTutorMessage('assistant', 'Sorry, something went wrong: ' + (data.error || 'Unknown error'));
    }
  } catch (error) {
    appendLessonTutorMessage('assistant', 'Sorry, something went wrong while sending your message.');
  } finally {
    button.disabled = false;
    input.focus();
  }
}

document.getElementById('lessonTutorInput')?.addEventListener('keydown', function(event) {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendLessonTutorMessage();
  }
});
</script>
</body>
</html>
