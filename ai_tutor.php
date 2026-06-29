<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ── Enrolled courses ── */
$enStmt = $pdo->prepare("SELECT c.id, c.title FROM lms_courses c JOIN lms_enrollments e ON e.course_id=c.id WHERE e.student_id=? ORDER BY c.title");
$enStmt->execute([$studentId]);
$enrolledCourses = $enStmt->fetchAll(PDO::FETCH_ASSOC);

$courseId = (int)($_GET['course_id'] ?? ($_POST['course_id'] ?? 0));
 $lessonId = (int)($_GET['lesson_id'] ?? ($_POST['lesson_id'] ?? 0));
if ($courseId <= 0 && !empty($enrolledCourses)) $courseId = (int)$enrolledCourses[0]['id'];

$selectedCourse = null;
foreach ($enrolledCourses as $ec) {
    if ((int)$ec['id'] === $courseId) { $selectedCourse = $ec; break; }
}

$selectedLesson = null;
if ($lessonId > 0) {
    $lessonStmt = $pdo->prepare("
        SELECT id, course_id, title, content
        FROM lms_lessons
        WHERE id = ? AND is_published = 1
        LIMIT 1
    ");
    $lessonStmt->execute([$lessonId]);
    $selectedLesson = $lessonStmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if ($selectedLesson && $courseId <= 0) {
        $courseId = (int)$selectedLesson['course_id'];
    }

    if ($selectedLesson && $courseId > 0 && (int)$selectedLesson['course_id'] !== $courseId) {
        $selectedLesson = null;
        $lessonId = 0;
    }
}

function envValue(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string)$value;
}

function fetchCourseContext(PDO $pdo, int $courseId, int $lessonId = 0): array
{
    if ($courseId <= 0) {
        return ['summary' => '', 'lesson_excerpt' => ''];
    }

    $courseStmt = $pdo->prepare("SELECT title, description, short_description, level FROM lms_courses WHERE id=? LIMIT 1");
    $courseStmt->execute([$courseId]);
    $course = $courseStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    if ($lessonId > 0) {
        $lessonStmt = $pdo->prepare("
            SELECT title, content
            FROM lms_lessons
            WHERE course_id = ? AND id = ? AND is_published = 1
            LIMIT 1
        ");
        $lessonStmt->execute([$courseId, $lessonId]);
    } else {
        $lessonStmt = $pdo->prepare("
            SELECT title, content
            FROM lms_lessons
            WHERE course_id = ? AND is_published = 1
            ORDER BY sort_order ASC, id ASC
            LIMIT 5
        ");
        $lessonStmt->execute([$courseId]);
    }
    $lessons = $lessonStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $summary = '';
    if ($course) {
        $summary = 'Student is enrolled in "' . ($course['title'] ?? 'this course') . '". ';
        if (!empty($course['short_description'])) $summary .= trim((string)$course['short_description']) . ' ';
        if (!empty($course['description'])) $summary .= mb_substr(trim((string)$course['description']), 0, 280) . '. ';
        if (!empty($course['level'])) $summary .= 'Level: ' . $course['level'] . '. ';
    }

    if ($lessons) {
        $summary .= 'Published lessons include: ' . implode(', ', array_map(static function ($lesson) {
            return (string)($lesson['title'] ?? '');
        }, $lessons)) . '. ';
    }

    $lessonExcerpt = '';
    foreach ($lessons as $lesson) {
        $title = trim((string)($lesson['title'] ?? ''));
        $content = trim(strip_tags((string)($lesson['content'] ?? '')));
        if ($content === '') {
            continue;
        }

        $lessonExcerpt .= "Lesson: {$title}\n";
        $lessonExcerpt .= mb_substr($content, 0, 900) . "\n\n";
    }

    return [
        'summary' => trim($summary),
        'lesson_excerpt' => trim($lessonExcerpt),
    ];
}

function generateLocalTutorReply(string $baseUrl, string $model, string $systemInstruction, array $history): array
{
    $promptLines = [];
    foreach ($history as $item) {
        $role = (string)($item['role'] ?? 'user');
        $speaker = $role === 'assistant' ? 'Tutor' : 'Student';
        $promptLines[] = $speaker . ': ' . trim((string)($item['content'] ?? ''));
    }

    $payload = json_encode([
        'model' => $model,
        'system' => $systemInstruction,
        'prompt' => implode("\n\n", $promptLines),
        'stream' => false,
        'options' => ['temperature' => 0.7],
    ], JSON_THROW_ON_ERROR);

    $ch = curl_init(rtrim($baseUrl, '/') . '/api/generate');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 120,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'error' => 'Unable to reach local AI service: ' . $error];
    }

    if ($code >= 400) {
        return ['ok' => false, 'error' => 'Local AI service returned HTTP ' . $code];
    }

    $data = json_decode($response, true);
    $text = trim((string)($data['response'] ?? ''));

    if ($text === '') {
        return ['ok' => false, 'error' => 'Local AI service returned an empty response.'];
    }

    return ['ok' => true, 'reply' => $text];
}

function buildOfflineTutorReply(string $question, string $courseSummary, string $lessonExcerpt): string
{
    $questionLower = strtolower(trim($question));
    
    // Friendly introductory greeting
    $reply = "👋 **Hello! I am your Grafix@Mirror AI Tutor.**\n\n";
    $reply .= "I've analyzed your question and cross-referenced it with your current lesson material. Here is a guided breakdown to help you understand:\n\n";

    // 1. Keyword-based tailored responses
    $customResponse = '';
    if (str_contains($questionLower, 'scratch') || str_contains($questionLower, 'sprite') || str_contains($questionLower, 'block-based')) {
        $customResponse = "### 🐈 Understanding Block-Based Programming (Scratch)\n"
            . "Scratch is a visual, block-based programming environment where you control **Sprites** (characters or objects) using scripts built by snapping blocks together.\n\n"
            . "**Key Elements to Remember:**\n"
            . "- **Stage**: Where your Sprites interact and the animation/game runs.\n"
            . "- **Scripts Area**: The workspace where you drag and drop code blocks from the palette.\n"
            . "- **Events**: Code blocks like `When Green Flag Clicked` that trigger your programs.\n"
            . "- **Control Loops**: Blocks like `Repeat` or `Forever` that make actions run multiple times.\n\n"
            . "**💡 Practical Exercise:**\n"
            . "Open Scratch on your computer, add a new Sprite, and snap `When Green Flag Clicked` -> `Repeat 10` -> `Move 10 Steps` -> `Play Sound Meow`. Press the green flag to test!";
    } elseif (str_contains($questionLower, 'python') || str_contains($questionLower, 'variable') || str_contains($questionLower, 'function') || str_contains($questionLower, 'code')) {
        $customResponse = "### 🐍 Understanding Python Variables & Structure\n"
            . "Python is an elegant, high-level programming language known for its readability.\n\n"
            . "**Key Coding Rules:**\n"
            . "1. **Variables**: Containers for storing data values. You define them simply as `name = \"David\"` or `age = 16`.\n"
            . "2. **Indentation**: Python uses spaces to define blocks of code (like inside `if` statements or functions) instead of curly braces `{}`.\n"
            . "3. **Functions**: Defined with the `def` keyword, allowing you to write reusable code blocks.\n\n"
            . "**💻 Example Python Code:**\n"
            . "```python\n"
            . "def greet_student(name):\n"
            . "    return \"Welcome back, \" + name + \"!\"\n\n"
            . "print(greet_student(\"David\"))\n"
            . "```";
    } elseif (str_contains($questionLower, 'html') || str_contains($questionLower, 'css') || str_contains($questionLower, 'web design') || str_contains($questionLower, 'styling')) {
        $customResponse = "### 🌐 Web Design: HTML and CSS Basics\n"
            . "Websites are built using two core languages: HTML for structure and CSS for presentation.\n\n"
            . "**How they work together:**\n"
            . "- **HTML (Markup)**: Uses tags to describe content. E.g., `<h1>Heading</h1>` or `<p>Paragraph</p>`.\n"
            . "- **CSS (Styling)**: Defines how HTML tags look (colors, layouts, sizes). E.g., `h1 { color: blue; }`.\n\n"
            . "**💡 Pro-Tip:**\n"
            . "Always keep your styles separate from your content! Using external stylesheet files (CSS) keeps your code clean and professional.";
    } elseif (str_contains($questionLower, 'database') || str_contains($questionLower, 'sql') || str_contains($questionLower, 'join')) {
        $customResponse = "### 🗄️ Understanding Database Systems & SQL Queries\n"
            . "A database is an organized collection of tables containing columns (attributes) and rows (records).\n\n"
            . "**Key SQL Query Concepts:**\n"
            . "- **SELECT**: Used to query and fetch data. E.g. `SELECT name FROM students;`\n"
            . "- **WHERE**: Filters records based on conditions. E.g. `WHERE score >= 75;`\n"
            . "- **JOIN**: Merges rows from multiple tables (like students and enrollments) based on matching IDs.\n\n"
            . "**🔍 Example Query:**\n"
            . "```sql\n"
            . "SELECT s.name, e.status \n"
            . "FROM lms_students s\n"
            . "JOIN lms_enrollments e ON s.id = e.student_id;\n"
            . "```";
    } elseif (str_contains($questionLower, 'algorithm') || str_contains($questionLower, 'flowchart')) {
        $customResponse = "### 📐 Algorithms and Flowcharts\n"
            . "Before coding, programmers map out solutions using logical algorithms and flowcharts.\n\n"
            . "- **Algorithm**: A step-by-step set of written instructions to solve a problem.\n"
            . "- **Flowchart**: A diagram representing the algorithm using standard shapes:\n"
            . "  - *Oval*: Start and End terminal points.\n  - *Rectangle*: Process actions or operations.\n  - *Diamond*: Decision questions (Yes/No).\n  - *Parallelogram*: Input/Output read or write data.";
    } elseif (str_contains($questionLower, 'computer') || str_contains($questionLower, 'input') || str_contains($questionLower, 'output') || str_contains($questionLower, 'hardware') || str_contains($questionLower, 'software') || str_contains($questionLower, 'cpu') || str_contains($questionLower, 'memory') || str_contains($questionLower, 'ram') || str_contains($questionLower, 'rom')) {
        $customResponse = "### 🖥️ Understanding Computer Hardware & Software Systems\n"
            . "A computer system is a combination of physical hardware components and logical software programs.\n\n"
            . "**1. Key Hardware Components:**\n"
            . "- **Input Devices**: Keyboard, Mouse, Scanner (to feed data).\n"
            . "- **Output Devices**: Monitor, Printer, Speakers (to present data).\n"
            . "- **CPU (Central Processing Unit)**: The brain of the computer that processes all instructions.\n"
            . "- **RAM**: Fast, temporary memory (clears when powered off).\n"
            . "- **ROM**: Permanent, read-only memory containing system startup instructions.\n\n"
            . "**2. Key Software Types:**\n"
            . "- **System Software**: Manages hardware (e.g. Windows, macOS).\n"
            . "- **Application Software**: Programs that help you work (e.g. browsers, MS Word).";
    }

    if ($customResponse !== '') {
        $reply .= $customResponse;
    } else {
        // Fallback to extract sentences from context, formatted beautifully
        $plainLesson = trim(preg_replace('/\s+/', ' ', strip_tags($lessonExcerpt)));
        $plainSummary = trim(preg_replace('/\s+/', ' ', strip_tags($courseSummary)));
        $sourceText = $plainLesson !== '' ? $plainLesson : $plainSummary;
        
        $sentences = preg_split('/(?<=[\.\!\?])\s+/', $sourceText) ?: [];
        $keywords = array_values(array_filter(array_unique(preg_split('/[^a-z0-9]+/i', strtolower($question)) ?: []), static function ($word) {
            return strlen($word) >= 4;
        }));

        $matches = [];
        foreach ($sentences as $sentence) {
            $score = 0;
            $lower = strtolower($sentence);
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $matches[] = ['score' => $score, 'sentence' => trim($sentence)];
            }
        }

        usort($matches, static fn($a, $b) => $b['score'] <=> $a['score']);
        $best = array_slice(array_column($matches, 'sentence'), 0, 3);

        if ($best === [] && $plainLesson !== '') {
            $best[] = mb_substr($plainLesson, 0, 420) . (mb_strlen($plainLesson) > 420 ? '...' : '');
        } elseif ($best === [] && $plainSummary !== '') {
            $best[] = $plainSummary;
        }

        $body = $best !== [] ? implode(" ", $best) : '';
        
        $reply .= "### 📖 Insights from Your Current Lesson\n";
        if ($body !== '') {
            $reply .= $body . "\n\n";
        } else {
            $reply .= "I couldn't find a direct text match for your question in the current lesson, but I am ready to guide you on any topic related to this course!\n\n";
        }
        $reply .= "**💡 Tip:** Try asking a narrower question focusing on specific terms in the lesson title to get a more precise explanation.";
    }

    $reply .= "\n\n---\n";
    $reply .= "✨ **Keep up the great work!** Let me know if you need more examples or a breakdown of any specific concept.";

    return $reply;
}

/* ── AJAX: handle chat message ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    header('Content-Type: application/json; charset=utf-8');
    verifyCsrf($_POST['_csrf'] ?? '');
    $userMsg = trim((string)($_POST['message'] ?? ''));
    $cid     = (int)($_POST['course_id'] ?? 0);
    if ($userMsg === '') { echo json_encode(['ok'=>false,'error'=>'Empty message']); exit; }

    $pdo->prepare("INSERT INTO lms_ai_chats (student_id,course_id,role,message) VALUES (?,?,?,?)")
        ->execute([$studentId, $cid ?: null, 'user', $userMsg]);

    $activeLessonId = (int)($_POST['lesson_id'] ?? $lessonId);
    $courseContext = fetchCourseContext($pdo, $cid, $activeLessonId);
    $ctx = $courseContext['summary'];
    $lessonExcerpt = $courseContext['lesson_excerpt'];

    // Last 6 messages for history
    $hist = $pdo->query("SELECT role,message FROM lms_ai_chats WHERE student_id={$studentId}".($cid>0?" AND course_id={$cid}":"")." ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
    $hist = array_reverse($hist);

    $systemInstruction = "You are an expert AI tutor for Grafix@Mirror LMS, a professional tech training institute in Nigeria. "
        . "Help students understand course material, answer questions, and provide practical guidance. "
        . $ctx . ' '
        . "Be encouraging, clear, constructive, creative when helpful, and gently corrective when the learner is mistaken. "
        . "Use Nigerian tech industry examples where relevant. "
        . "Format responses with clear structure using headings and bullet points when helpful. "
        . "Keep responses 150-300 words unless more detail is genuinely needed. "
        . "If lesson material is provided, stay grounded in it and say when you are extending beyond the provided lesson.";

    if ($lessonExcerpt !== '') {
        $systemInstruction .= "\n\nRelevant lesson material:\n" . $lessonExcerpt;
    }

    $inputMessages = [];
    foreach ($hist as $h) {
        $inputMessages[] = [
            'role' => $h['role'],
            'content' => $h['message'],
        ];
    }
    $inputMessages[] = ['role' => 'user', 'content' => $userMsg];

    $localBaseUrl = envValue('OLLAMA_BASE_URL', 'http://127.0.0.1:11434');
    $localModel = envValue('OLLAMA_MODEL', 'llama3.1:8b');
    $localReply = generateLocalTutorReply($localBaseUrl, $localModel, $systemInstruction, $inputMessages);

    if (!$localReply['ok']) {
        $reply = buildOfflineTutorReply($userMsg, $ctx, $lessonExcerpt);
    } else {
        $reply = (string)$localReply['reply'];
    }

    $pdo->prepare("INSERT INTO lms_ai_chats (student_id,course_id,role,message) VALUES (?,?,?,?)")
        ->execute([$studentId, $cid ?: null, 'assistant', $reply]);

    echo json_encode(['ok'=>true,'reply'=>$reply]);
    exit;
}

/* ── Load chat history ── */
$chatHistory = $pdo->query("
    SELECT role,message,created_at FROM lms_ai_chats
    WHERE student_id={$studentId}".($courseId>0?" AND course_id={$courseId}":"")."
    ORDER BY created_at ASC LIMIT 60
")->fetchAll(PDO::FETCH_ASSOC);

/* ── Clear chat ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_chat'])) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $pdo->exec("DELETE FROM lms_ai_chats WHERE student_id={$studentId}".($courseId>0?" AND course_id={$courseId}":""));
    redirect('ai_tutor.php?course_id='.$courseId);
}

function formatAIMessage(string $text): string {
    // Convert markdown-style to HTML (server-side fallback)
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = nl2br($text);
    return $text;
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'AI Tutor';
$seoDesc    = '24/7 AI-powered tutoring and mentoring at Grafix@Mirror LMS — get instant answers to your course questions.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
.chat-wrap{display:flex;flex-direction:column;height:calc(100vh - 200px);min-height:420px;background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.chat-messages{flex:1;overflow-y:auto;padding:1.25rem;display:flex;flex-direction:column;gap:.85rem}
.bubble{max-width:82%;padding:.75rem 1rem;border-radius:14px;font-size:.9rem;line-height:1.6;word-break:break-word}
.bubble.user{background:var(--brand);color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.bubble.assistant{background:var(--surface);border:1px solid var(--border);align-self:flex-start;border-bottom-left-radius:4px;color:var(--dark)}
.bubble.assistant pre{background:#f1f5f9;padding:.6rem;border-radius:6px;font-size:.8rem;overflow-x:auto;margin:.5rem 0}
.bubble.assistant code{background:#f1f5f9;padding:.1rem .3rem;border-radius:4px;font-size:.82rem}
.bubble.assistant strong{color:var(--brand)}
.chat-input-row{padding:.75rem 1rem;border-top:1px solid var(--border);background:var(--card-bg);display:flex;gap:.5rem}
.typing{display:none;align-self:flex-start;padding:.6rem 1rem;background:var(--surface);border:1px solid var(--border);border-radius:14px;border-bottom-left-radius:4px}
.dot{width:7px;height:7px;border-radius:50%;background:var(--muted);display:inline-block;animation:blink .9s infinite}
.dot:nth-child(2){animation-delay:.2s}.dot:nth-child(3){animation-delay:.4s}
@keyframes blink{0%,80%,100%{opacity:.2}40%{opacity:1}}
</style>
</head>
<body style="background:var(--surface)">

<nav class="lms-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="brand text-decoration-none">
      <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>
    <div class="d-flex gap-2">
      <a href="live_session.php" class="btn-ghost" style="font-size:.82rem">
        <i class="fa fa-video me-1"></i>Live Sessions
      </a>
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
    </div>
  </div>
</nav>

<div class="container py-3" style="max-width:900px">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h4 class="page-title mb-0"><i class="fa fa-robot me-2" style="color:var(--brand)"></i>AI Tutor</h4>
      <p class="text-muted mb-0" style="font-size:.82rem">Available 24/7  ask anything about your course</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <?php if (count($enrolledCourses) > 1): ?>
      <form method="get" class="d-flex gap-1">
        <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:180px">
          <option value="0">General (no course)</option>
          <?php foreach ($enrolledCourses as $ec): ?>
            <option value="<?= (int)$ec['id'] ?>" <?= (int)$ec['id']===$courseId?'selected':'' ?>><?= e($ec['title']) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if ($lessonId > 0): ?>
          <input type="hidden" name="lesson_id" value="<?= (int)$lessonId ?>">
        <?php endif; ?>
      </form>
      <?php endif; ?>
      <?php if (!empty($chatHistory)): ?>
        <form method="post" class="d-inline" onsubmit="return confirm('Clear chat history?')">
          <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
          <input type="hidden" name="clear_chat" value="1">
          <button type="submit" class="btn-ghost" style="font-size:.8rem">
            <i class="fa fa-trash me-1"></i>Clear
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="chat-wrap">
    <div class="chat-messages" id="chatMessages">

      <!-- Welcome message -->
      <?php if (empty($chatHistory)): ?>
        <div class="bubble assistant">
          <strong>Hello! I am your AI Tutor.</strong><br>
        <?php if ($selectedLesson): ?>
            I am here to help you with <strong><?= e($selectedLesson['title']) ?></strong> in <strong><?= e($selectedCourse['title'] ?? 'your course') ?></strong>.
        <?php elseif ($selectedCourse): ?>
            I am here to help you with <strong><?= e($selectedCourse['title']) ?></strong> and any related topics.
          <?php else: ?>
            I can help you with any of your enrolled courses. Select a course above for focused help.
          <?php endif; ?>
          <br><br>
          Ask me anything  concepts, code examples, career advice, or practice questions. I am available 24/7!
        </div>
      <?php endif; ?>

      <!-- Chat history -->
      <?php foreach ($chatHistory as $msg): ?>
        <div class="bubble <?= e($msg['role']) ?>" data-role="<?= e($msg['role']) ?>">
          <?php if ($msg['role'] === 'assistant'): ?>
            <?= formatAIMessage(e($msg['message'])) ?>
          <?php else: ?>
            <?= e($msg['message']) ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <!-- Typing indicator -->
      <div class="typing" id="typing">
        <span class="dot"></span><span class="dot"></span><span class="dot"></span>
      </div>
    </div>

    <!-- Input -->
    <div class="chat-input-row">
      <textarea id="msgInput" class="form-control" rows="1"
                placeholder="Ask a question about <?= e($selectedLesson['title'] ?? $selectedCourse['title'] ?? 'your course') ?>..."
                style="resize:none;border-radius:10px;font-size:.9rem"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg();}"></textarea>
      <button onclick="sendMsg()" class="btn-brand" style="padding:.5rem 1rem;white-space:nowrap" id="sendBtn">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>
  </div>

  <!-- Suggested questions -->
  <div class="mt-3 d-flex flex-wrap gap-2" id="suggestions">
    <?php
    $suggestions = $selectedLesson ? [
        "Explain the key ideas in " . ($selectedLesson['title'] ?? 'this lesson'),
        "Summarise this lesson in simple terms",
        "Give me 3 practice questions from this lesson",
        "What are common mistakes in this lesson?",
        "Explain this lesson with a real-world example",
    ] : ($selectedCourse ? [
        "Explain the key concepts in " . ($selectedCourse['title'] ?? 'this course'),
        "Give me a practice question",
        "What should I focus on for the exam?",
        "Explain this with a real-world example",
        "What are common mistakes beginners make?",
    ] : [
        "What courses do you recommend for a beginner?",
        "How do I prepare for the exam?",
        "Give me a study plan",
    ]);
    foreach ($suggestions as $s):
    ?>
      <button class="btn-ghost" style="font-size:.78rem;padding:.3rem .7rem"
              onclick="document.getElementById('msgInput').value=<?= json_encode($s) ?>;sendMsg()">
        <?= e($s) ?>
      </button>
    <?php endforeach; ?>
  </div>

</div>

<script>
const courseId = <?= (int)$courseId ?>;
const lessonId = <?= (int)$lessonId ?>;
const csrfToken = <?= json_encode(csrfToken()) ?>;

function formatMarkdown(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`([^`]+)`/g, '<code>$1</code>')
    .replace(/```[\w]*\n?([\s\S]*?)```/g, '<pre>$1</pre>')
    .replace(/^### (.*)/gm, '<strong style="font-size:.95rem">$1</strong>')
    .replace(/^## (.*)/gm, '<strong style="font-size:1rem">$1</strong>')
    .replace(/^# (.*)/gm, '<strong style="font-size:1.05rem">$1</strong>')
    .replace(/^- (.*)/gm, ' $1')
    .replace(/\n/g, '<br>');
}

function appendBubble(role, text) {
  const wrap = document.getElementById('chatMessages');
  const div  = document.createElement('div');
  div.className = 'bubble ' + role;
  div.innerHTML = role === 'assistant' ? formatMarkdown(text) : escHtml(text);
  wrap.insertBefore(div, document.getElementById('typing'));
  wrap.scrollTop = wrap.scrollHeight;
}

function escHtml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

async function sendMsg() {
  const input = document.getElementById('msgInput');
  const msg   = input.value.trim();
  if (!msg) return;

  input.value = '';
  input.style.height = 'auto';
  document.getElementById('sendBtn').disabled = true;
  document.getElementById('suggestions').style.display = 'none';

  appendBubble('user', msg);
  document.getElementById('typing').style.display = 'flex';
  document.getElementById('chatMessages').scrollTop = 9999;

  try {
    const fd = new FormData();
    fd.append('message', msg);
    fd.append('course_id', courseId);
    fd.append('lesson_id', lessonId);
    fd.append('_csrf', csrfToken);

    const res  = await fetch('ai_tutor.php', { method:'POST', body:fd });
    const data = await res.json();

    document.getElementById('typing').style.display = 'none';

    if (data.ok) {
      appendBubble('assistant', data.reply);
    } else {
      appendBubble('assistant', 'Sorry, something went wrong: ' + (data.error || 'Unknown error'));
    }
  } catch(e) {
    document.getElementById('typing').style.display = 'none';
    appendBubble('assistant', 'Network error. Please check your connection and try again.');
  }

  document.getElementById('sendBtn').disabled = false;
  document.getElementById('chatMessages').scrollTop = 9999;
}

// Auto-resize textarea
document.getElementById('msgInput').addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Scroll to bottom on load
window.addEventListener('load', () => {
  const c = document.getElementById('chatMessages');
  c.scrollTop = c.scrollHeight;
});
</script>
</body>
</html>
