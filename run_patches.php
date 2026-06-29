<?php
declare(strict_types=1);
/**
 * One-time database patch runner.
 * Access: http://localhost/lms/run_patches.php
 * DELETE this file after running.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';

// Block access in production
if (($_ENV['APP_ENV'] ?? 'production') !== 'local') {
    http_response_code(404);
    exit('Not found.');
}

$patches = [
    'database/migrate_new_tables.sql'          => 'New tables (lesson completions, instructor bio, course assignments)',
    'database/migrate_enrollment_payment_type.sql' => 'Enrollment payment_type column + status enum fix',
    'database/lessons_patch.sql'               => 'Real lesson content for all 16 courses (106 lessons)',
    'database/videos_patch.sql'                => 'YouTube video links for all 16 courses (104 videos)',
    'database/exam_questions_patch.sql'        => 'Real exam questions for all 16 courses (160 questions)',
];

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run'])) {
    foreach ($patches as $file => $desc) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            $results[$file] = ['ok' => false, 'msg' => 'File not found'];
            continue;
        }
        $sql = file_get_contents(__DIR__ . '/' . $file);
        try {
            // Split on semicolons but preserve content inside strings
            $pdo->exec($sql);
            $results[$file] = ['ok' => true, 'msg' => 'Applied successfully'];
        } catch (PDOException $e) {
            $results[$file] = ['ok' => false, 'msg' => $e->getMessage()];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Run DB Patches | Mirror LMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
  body { background: #f8fafc; font-family: Inter, system-ui; }
  .card { border-radius: 14px; }
</style>
</head>
<body class="container py-5" style="max-width:760px">

  <div class="text-center mb-4">
    <h3><i class="fa fa-database me-2 text-primary"></i>Database Patch Runner</h3>
    <p class="text-muted">Applies all pending SQL patches to the database. Run once, then delete this file.</p>
  </div>

  <?php if (!empty($results)): ?>
    <div class="card p-4 mb-4">
      <h5 class="mb-3">Results</h5>
      <?php foreach ($results as $file => $r): ?>
        <div class="d-flex align-items-start gap-3 mb-3 p-3 rounded" style="background:<?= $r['ok'] ? '#d1fae5' : '#fee2e2' ?>">
          <i class="fa fa-<?= $r['ok'] ? 'check-circle text-success' : 'times-circle text-danger' ?> mt-1"></i>
          <div>
            <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars(basename($file)) ?></div>
            <div style="font-size:.82rem;color:#555"><?= htmlspecialchars($r['msg']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php $allOk = array_reduce($results, fn($c, $r) => $c && $r['ok'], true); ?>
      <?php if ($allOk): ?>
        <div class="alert alert-success mt-2 mb-0">
          <i class="fa fa-check-circle me-1"></i>
          All patches applied. <strong>Delete this file now:</strong> <code>run_patches.php</code>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="card p-4">
    <h5 class="mb-3">Patches to Apply</h5>
    <table class="table table-sm mb-4">
      <thead><tr><th>File</th><th>Description</th><th>Exists</th></tr></thead>
      <tbody>
        <?php foreach ($patches as $file => $desc): ?>
          <tr>
            <td><code style="font-size:.8rem"><?= htmlspecialchars(basename($file)) ?></code></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($desc) ?></td>
            <td>
              <?php if (file_exists(__DIR__ . '/' . $file)): ?>
                <span class="badge bg-success">✓</span>
              <?php else: ?>
                <span class="badge bg-danger">Missing</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <form method="post">
      <input type="hidden" name="run" value="1">
      <button class="btn btn-primary btn-lg w-100" onclick="return confirm('Apply all patches to the database? This will TRUNCATE lessons, videos, and exam questions tables and reload them.')">
        <i class="fa fa-play me-2"></i> Run All Patches Now
      </button>
    </form>
  </div>

  <div class="alert alert-warning mt-4">
    <i class="fa fa-exclamation-triangle me-1"></i>
    <strong>Security:</strong> Delete <code>run_patches.php</code> immediately after use.
  </div>

</body>
</html>
