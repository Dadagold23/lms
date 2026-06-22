<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

function envValue(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string)$value;
}

function fetchLocalAiStatus(string $baseUrl): array
{
    $url = rtrim($baseUrl, '/') . '/api/tags';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return [
            'ok' => false,
            'http_code' => $code,
            'error' => $error !== '' ? $error : 'Unable to connect',
            'models' => [],
        ];
    }

    $data = json_decode($response, true);
    $models = [];
    foreach (($data['models'] ?? []) as $model) {
        if (!empty($model['name'])) {
            $models[] = (string)$model['name'];
        }
    }

    return [
        'ok' => $code >= 200 && $code < 300,
        'http_code' => $code,
        'error' => '',
        'models' => $models,
    ];
}

$ollamaBaseUrl = envValue('OLLAMA_BASE_URL', 'http://127.0.0.1:11434');
$ollamaModel = envValue('OLLAMA_MODEL', 'llama3.1:8b');
$localAiStatus = fetchLocalAiStatus($ollamaBaseUrl);

$stats = [
    'courses' => (int)$pdo->query("SELECT COUNT(*) FROM lms_courses")->fetchColumn(),
    'lessons' => (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE is_published=1")->fetchColumn(),
    'students' => (int)$pdo->query("SELECT COUNT(*) FROM lms_students")->fetchColumn(),
    'ai_chats' => (int)$pdo->query("SELECT COUNT(*) FROM lms_ai_chats")->fetchColumn(),
];

$recentChats = $pdo->query("
    SELECT c.id, c.student_id, c.course_id, c.role, LEFT(c.message, 180) AS message_preview, c.created_at,
           CONCAT_WS(' ', s.first_name, s.last_name) AS student_name,
           cr.title AS course_title
    FROM lms_ai_chats c
    LEFT JOIN lms_students s ON s.id = c.student_id
    LEFT JOIN lms_courses cr ON cr.id = c.course_id
    ORDER BY c.id DESC
    LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'AI Tutor Diagnostics';
$seoDesc    = 'Admin diagnostics for the LMS AI Tutor.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
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
    <a href="admin_dashboard.php" class="brand text-decoration-none">
      <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>
    <div class="d-flex gap-2">
      <a href="admin_dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="admin_live_sessions.php" class="btn-ghost"><i class="fa fa-video me-1"></i>Live Sessions</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:1100px">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h3 class="page-title mb-1"><i class="fa fa-robot me-2" style="color:var(--brand)"></i>AI Tutor Diagnostics</h3>
      <p class="text-muted mb-0">Check local AI connectivity, LMS tutor activity, and current tutor configuration.</p>
    </div>
    <a href="ai_tutor.php" class="btn-brand"><i class="fa fa-external-link-alt me-1"></i>Open Tutor</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="lms-card h-100">
        <div class="text-muted small mb-1">Courses</div>
        <div style="font-size:1.8rem;font-weight:800"><?= number_format($stats['courses']) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="lms-card h-100">
        <div class="text-muted small mb-1">Published Lessons</div>
        <div style="font-size:1.8rem;font-weight:800"><?= number_format($stats['lessons']) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="lms-card h-100">
        <div class="text-muted small mb-1">Students</div>
        <div style="font-size:1.8rem;font-weight:800"><?= number_format($stats['students']) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="lms-card h-100">
        <div class="text-muted small mb-1">AI Chat Messages</div>
        <div style="font-size:1.8rem;font-weight:800"><?= number_format($stats['ai_chats']) ?></div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="lms-card h-100">
        <h5 style="font-weight:800;margin-bottom:1rem">Local AI Status</h5>
        <div class="mb-3">
          <div class="text-muted small">Configured Base URL</div>
          <code><?= e($ollamaBaseUrl) ?></code>
        </div>
        <div class="mb-3">
          <div class="text-muted small">Configured Model</div>
          <code><?= e($ollamaModel) ?></code>
        </div>
        <div class="mb-3">
          <div class="text-muted small">Connection Status</div>
          <?php if ($localAiStatus['ok']): ?>
            <div style="color:#15803d;font-weight:700"><i class="fa fa-circle-check me-1"></i>Online</div>
          <?php else: ?>
            <div style="color:#b91c1c;font-weight:700"><i class="fa fa-circle-xmark me-1"></i>Offline</div>
            <div class="text-muted small mt-1"><?= e($localAiStatus['error']) ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <div class="text-muted small">Available Models</div>
          <?php if (!empty($localAiStatus['models'])): ?>
            <div><?= e(implode(', ', $localAiStatus['models'])) ?></div>
          <?php else: ?>
            <div class="text-muted">No models detected from the local AI endpoint.</div>
          <?php endif; ?>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:1rem">
          <div style="font-weight:700;margin-bottom:.5rem">If the local AI is offline</div>
          <div class="text-muted" style="font-size:.92rem">
            The tutor will still answer using lesson and course content stored in MySQL. To enable full generative local AI, start your local model server at the configured base URL and make sure the configured model is available.
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="lms-card h-100">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <h5 style="font-weight:800;margin-bottom:0">Recent Tutor Chats</h5>
          <span class="text-muted" style="font-size:.9rem">Latest 12 messages from <code>lms_ai_chats</code></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Course</th>
                <th>Role</th>
                <th>Preview</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentChats as $chat): ?>
                <tr>
                  <td><?= (int)$chat['id'] ?></td>
                  <td>
                    <div><?= e($chat['student_name'] ?: ('Student #' . $chat['student_id'])) ?></div>
                    <div class="text-muted small">ID <?= (int)$chat['student_id'] ?></div>
                  </td>
                  <td><?= e($chat['course_title'] ?: 'General') ?></td>
                  <td><span class="badge text-bg-light"><?= e($chat['role']) ?></span></td>
                  <td style="min-width:260px"><?= e($chat['message_preview']) ?></td>
                  <td class="text-muted small"><?= e($chat['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
