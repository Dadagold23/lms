<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$examId    = (int)($_GET['id'] ?? $_POST['exam_id'] ?? 0);
if ($examId <= 0) { http_response_code(400); exit('Invalid exam.'); }

/* ── Fetch exam ── */
$stmt = $pdo->prepare("SELECT id,title,duration_minutes,pass_mark,course_id FROM lms_exams WHERE id=? LIMIT 1");
$stmt->execute([$examId]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$exam) { http_response_code(404); exit('Exam not found.'); }

$courseId = (int)$exam['course_id'];
$passMark = (int)($exam['pass_mark'] ?? 50);

/* ── Enrollment + payment check ── */
$en = $pdo->prepare("SELECT e.paid_amount,e.status,c.price FROM lms_enrollments e JOIN lms_courses c ON c.id=e.course_id WHERE e.student_id=? AND e.course_id=? LIMIT 1");
$en->execute([$studentId, $courseId]);
$enrollment = $en->fetch(PDO::FETCH_ASSOC);
if (!$enrollment) { http_response_code(403); exit('Not enrolled in this course.'); }
$paid   = (float)$enrollment['paid_amount'];
$price  = (float)$enrollment['price'];
$enStat = (string)$enrollment['status'];
$isUnlocked = $enStat === 'paid' || ($enStat === 'installment' && $paid > 0) || ($price > 0 && $paid >= $price);
if (!$isUnlocked) redirect('pay.php?enrollment_id=' . $courseId);

/* ── Lesson completion check ── */
$totalLessons = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE course_id={$courseId} AND is_published=1")->fetchColumn();
$doneLessons  = 0;
try { $doneLessons = (int)$pdo->query("SELECT COUNT(*) FROM lms_lesson_completions WHERE student_id={$studentId} AND course_id={$courseId}")->fetchColumn(); } catch (Throwable $e) {}
if ($totalLessons > 0 && $doneLessons < $totalLessons) {
    redirect('course_lessons.php?course_id=' . $courseId . '&msg=complete_lessons');
}

/* ── Attempt tracking (max 3) ── */
$MAX_ATTEMPTS = 3;
$attemptCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_results WHERE student_id={$studentId} AND exam_id={$examId}")->fetchColumn();
$attemptsLeft = $MAX_ATTEMPTS - $attemptCount;

$bestStmt = $pdo->prepare("SELECT score,total,percent,status,taken_at FROM lms_exam_results WHERE student_id=? AND exam_id=? ORDER BY percent DESC LIMIT 1");
$bestStmt->execute([$studentId, $examId]);
$bestResult = $bestStmt->fetch(PDO::FETCH_ASSOC);

/* ── Fetch questions ── */
$qstmt = $pdo->prepare("SELECT id,question,option_a,option_b,option_c,option_d,correct_option,COALESCE(marks,1) AS marks FROM lms_exam_questions WHERE exam_id=? ORDER BY id ASC");
$qstmt->execute([$examId]);
$questions = $qstmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Handle submission ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf((string)($_POST['_csrf'] ?? ''));

    $currentAttempts = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_results WHERE student_id={$studentId} AND exam_id={$examId}")->fetchColumn();
    if ($currentAttempts >= $MAX_ATTEMPTS) {
        $_SESSION['flash_error'] = "You have used all {$MAX_ATTEMPTS} attempts for this exam.";
        redirect('exams.php');
    }

    $answers = $_POST['ans'] ?? [];
    $score = 0.0; $total = 0.0;
    foreach ($questions as $q) {
        $mark    = (float)$q['marks'];
        $total  += $mark;
        $chosen  = strtoupper(trim((string)($answers[(int)$q['id']] ?? '')));
        $correct = strtoupper(trim((string)$q['correct_option']));
        if ($chosen !== '' && $chosen === $correct) $score += $mark;
    }

    $percent = $total > 0 ? round($score / $total * 100, 2) : 0;
    $result  = $percent >= $passMark ? 'pass' : 'fail';

    $pdo->prepare("INSERT INTO lms_exam_results (student_id,exam_id,score,total,percent,status,taken_at) VALUES (?,?,?,?,?,?,NOW())")
        ->execute([$studentId, $examId, (int)$score, (int)$total, $percent, $result]);

    $left = $MAX_ATTEMPTS - ($currentAttempts + 1);
    $msg  = "Score: {$score}/{$total} ({$percent}%) — " . ucfirst($result);
    if ($left > 0 && $result === 'fail') $msg .= " · {$left} attempt" . ($left > 1 ? 's' : '') . " remaining";
    $_SESSION['flash_success'] = $msg;
    redirect('exams.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Take Exam';
$seoDesc    = 'Take your course exam at Grafix@Mirror LMS — Mirror Age Concepts professional technology training.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<title><?= e($exam['title']) ?> | Grafix@Mirror LMS</title>
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
  <div class="lms-card mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h4 class="mb-1" style="font-weight:800"><?= e($exam['title']) ?></h4>
        <div class="d-flex gap-3 flex-wrap" style="font-size:.85rem;color:var(--muted)">
          <span><i class="fa fa-question-circle me-1"></i><?= count($questions) ?> questions</span>
          <span><i class="fa fa-clock me-1"></i><?= (int)$exam['duration_minutes'] ?> mins</span>
          <span><i class="fa fa-star me-1"></i>Pass: <?= $passMark ?>%</span>
        </div>
      </div>
      <div class="text-end">
        <div style="font-size:.8rem;color:var(--muted)">Attempts</div>
        <div class="d-flex gap-1 mt-1">
          <?php for ($a=1;$a<=$MAX_ATTEMPTS;$a++): ?>
            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;
                 background:<?= $a<=$attemptCount?'var(--danger)':($a===$attemptCount+1?'var(--brand)':'var(--border)') ?>;
                 color:<?= ($a<=$attemptCount||$a===$attemptCount+1)?'#fff':'var(--muted)' ?>">
              <?= $a ?>
            </div>
          <?php endfor; ?>
        </div>
        <div style="font-size:.75rem;margin-top:.25rem;color:<?= $attemptsLeft>0?'var(--muted)':'var(--danger)' ?>">
          <?= $attemptsLeft>0 ? "{$attemptsLeft} attempt".($attemptsLeft>1?'s':'')." remaining" : 'No attempts remaining' ?>
        </div>
      </div>
    </div>
    <?php if ($bestResult): ?>
      <div class="lms-alert lms-alert-<?= $bestResult['status']==='pass'?'success':'warning' ?> mt-3 mb-0">
        <i class="fa fa-history me-1"></i>
        Best: <strong><?= number_format((float)$bestResult['percent'],1) ?>%</strong>
        (<?= (int)$bestResult['score'] ?>/<?= (int)$bestResult['total'] ?>) 
        <?= $bestResult['status']==='pass'?'Passed':'Failed' ?>
        on <?= e(date('d M Y',strtotime($bestResult['taken_at']))) ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($attemptCount>=$MAX_ATTEMPTS): ?>
    <div class="lms-card text-center py-5">
      <i class="fa fa-ban fa-3x mb-3" style="color:var(--danger)"></i>
      <h5>No Attempts Remaining</h5>
      <p class="text-muted">You have used all <?= $MAX_ATTEMPTS ?> attempts for this exam.</p>
      <?php if ($bestResult && $bestResult['status']==='pass'): ?>
        <a href="certificate_download.php?course_id=<?= $courseId ?>" class="btn-brand">
          <i class="fa fa-certificate me-1"></i> Download Certificate
        </a>
      <?php else: ?>
        <p class="text-muted small">Contact your instructor to request additional attempts.</p>
        <a href="exams.php" class="btn-ghost">Back to Exams</a>
      <?php endif; ?>
    </div>
  <?php elseif (!$questions): ?>
    <div class="lms-alert lms-alert-warning">
      <i class="fa fa-exclamation-triangle me-1"></i>
      No questions have been added for this exam yet. Check back soon.
    </div>
  <?php else: ?>
    <form method="post" id="examForm">
      <?php echo csrfField(); ?>
      <input type="hidden" name="exam_id" value="<?= (int)$examId ?>">
      <?php foreach ($questions as $idx => $q): ?>
        <?php $qid=(int)$q['id']; ?>
        <div class="lms-card mb-3">
          <div class="d-flex gap-3">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0">
              <?= $idx+1 ?>
            </div>
            <div class="flex-grow-1">
              <div style="font-weight:600;margin-bottom:.75rem"><?= e((string)$q['question']) ?></div>
              <?php foreach (['A'=>$q['option_a'],'B'=>$q['option_b'],'C'=>$q['option_c'],'D'=>$q['option_d']] as $k=>$txt): ?>
                <?php if ($txt===null||$txt==='') continue; ?>
                <label class="d-flex align-items-center gap-2 p-2 mb-1 rounded" style="cursor:pointer;transition:background .15s"
                       onmouseover="this.style.background='var(--brand-light)'" onmouseout="this.style.background=''">
                  <input type="radio" name="ans[<?= $qid ?>]" value="<?= e($k) ?>" style="accent-color:var(--brand)">
                  <span><strong><?= e($k) ?>.</strong> <?= e((string)$txt) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
        <div style="font-size:.85rem;color:var(--muted)">
          <i class="fa fa-info-circle me-1"></i>
          Attempt <?= $attemptCount+1 ?> of <?= $MAX_ATTEMPTS ?>. Answer all questions before submitting.
        </div>
        <button type="submit" class="btn-brand" style="padding:.75rem 2rem;font-size:1rem"
                onclick="return confirm('Submit exam? This uses attempt <?= $attemptCount+1 ?> of <?= $MAX_ATTEMPTS ?>.')">
          <i class="fa fa-paper-plane me-1"></i> Submit Exam
        </button>
      </div>
    </form>
  <?php endif; ?>
</div>
<script>
<?php if ($attemptsLeft>0 && $questions): ?>
const SECS = <?= (int)$exam['duration_minutes'] ?> * 60;
let rem = SECS;
const el = document.getElementById('timer');
function fmt(s){return String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0');}
if(el) el.textContent = fmt(rem);
const iv = setInterval(()=>{
  rem--;
  if(el){el.textContent=fmt(rem);el.style.color=rem<60?'var(--danger)':'var(--brand)';}
  if(rem<=0){clearInterval(iv);alert('Time is up!');document.getElementById('examForm')?.submit();}
},1000);
<?php endif; ?>
</script>
</body>
</html>
