<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/workspaces.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);
$courseId = (int)($_GET['course_id'] ?? 0);

if ($studentId <= 0) {
    redirect('login.php');
}

if ($courseId <= 0) {
    http_response_code(400);
    exit('Invalid course.');
}

$workspaceSelect = workspaceCourseSelectSql($pdo, 'c');

$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.slug, c.short_description, c.description, c.price, c.level" . $workspaceSelect . ",
           e.id AS enrollment_id, e.paid_amount, e.payment_type, e.status, e.next_due_date, e.access_expires_at
    FROM lms_courses c
    JOIN lms_enrollments e ON e.course_id = c.id
    WHERE c.id = ? AND e.student_id = ?
    LIMIT 1
");
$stmt->execute([$courseId, $studentId]);
$course = workspaceCourseRow((array)($stmt->fetch(PDO::FETCH_ASSOC) ?: []));

if (!$course) {
    http_response_code(403);
    exit('You are not enrolled in this course.');
}

$price = (float)($course['price'] ?? 0);
$paid = (float)($course['paid_amount'] ?? 0);
$status = (string)($course['status'] ?? '');
$isExpired = !empty($course['access_expires_at']) && strtotime((string)$course['access_expires_at']) < time();
$isUnlocked = !$isExpired && ($status === 'paid' || ($status === 'installment' && $paid > 0) || ($price > 0 && $paid >= $price));

if (!$isUnlocked) {
    redirect('course.php?id=' . $courseId);
}

$workspaceType = inferWorkspaceType($course);
$workspaceLabel = workspaceTypeLabel($course);
$workspaceIcon = workspaceTypeIcon($course);
$workspaceDesc = workspaceTypeDescription($course);
$workspaceUrl = trim((string)($course['workspace_url'] ?? ''));
$storageKey = 'lms_workspace_' . $courseId . '_' . $workspaceType;
$lessonCount = 0;
$videoCount = 0;
$assignmentCount = 0;
$lessonTitles = [];

try {
    $lessonStmt = $pdo->prepare("
        SELECT title
        FROM lms_lessons
        WHERE course_id = ?
        ORDER BY sort_order ASC, id ASC
        LIMIT 5
    ");
    $lessonStmt->execute([$courseId]);
    $lessonTitles = array_values(array_filter(array_map('strval', $lessonStmt->fetchAll(PDO::FETCH_COLUMN))));

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_lessons WHERE course_id = ?");
    $countStmt->execute([$courseId]);
    $lessonCount = (int)$countStmt->fetchColumn();

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_videos WHERE course_id = ?");
    $countStmt->execute([$courseId]);
    $videoCount = (int)$countStmt->fetchColumn();

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM lms_assignments WHERE course_id = ?");
    $countStmt->execute([$courseId]);
    $assignmentCount = (int)$countStmt->fetchColumn();
} catch (Throwable $e) {
}
$ideStarterFiles = workspaceIdeStarterTemplate($course, $lessonTitles);
$idePrimaryHtml = "<main>\n  <h1>" . e((string)$course['title']) . "</h1>\n  <p>Use this IDE workspace to practice course exercises inside the LMS.</p>\n</main>";
$idePrimaryCss = "body {\n  font-family: Arial, sans-serif;\n  padding: 2rem;\n  background: #f8fafc;\n  color: #0f172a;\n}\n\nh1 {\n  color: #0f766e;\n}";
$idePrimaryJs = "console.log('Workspace ready for " . addslashes((string)$course['title']) . "');";
$idePrimaryPhp = '';
$ideNotes = "# " . (string)$course['title'] . " Workspace Notes\n\n## Goals\n- Capture the task you are building\n- Keep links to lesson concepts\n- Track bugs, edge cases, and improvements\n\n## Lesson Checklist\n" . implode("\n", array_map(static fn(string $title): string => '- ' . $title, $lessonTitles));

foreach ($ideStarterFiles as $starterFile) {
    $starterName = strtolower((string)($starterFile['name'] ?? ''));
    $starterType = strtolower((string)($starterFile['type'] ?? ''));
    $starterContent = (string)($starterFile['content'] ?? '');

    if ($starterName === 'index.html' || ($starterType === 'html' && $idePrimaryHtml === '')) {
        $idePrimaryHtml = $starterContent;
    }
    if ($starterName === 'styles.css' || ($starterType === 'css' && $idePrimaryCss === '')) {
        $idePrimaryCss = $starterContent;
    }
    if ($starterName === 'app.js' || ($starterType === 'js' && $idePrimaryJs === '')) {
        $idePrimaryJs = $starterContent;
    }
    if ($starterName === 'index.php' || ($starterType === 'php' && $idePrimaryPhp === '')) {
        $idePrimaryPhp = $starterContent;
    }
    if ($starterName === 'readme.md' || $starterType === 'md') {
        $ideNotes = $starterContent;
    }
}

$showPhpEditor = $idePrimaryPhp !== '';
?>
<!doctype html>
<html lang="en">

<head>
    <?php
$seoTitle   = 'Workspace';
$seoDesc    = 'Practice directly inside your course workspace at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
    <style>
    .workspace-shell {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 1.25rem;
        min-height: calc(100vh - 150px);
        align-items: start;
        transition: all .25s ease;
    }

    .workspace-shell.expanded {
        grid-template-columns: 1fr;
    }

    .workspace-shell.expanded .workspace-sidebar {
        display: none;
    }

    .workspace-sidebar,
    .workspace-main {
        background: rgba(255, 255, 255, .92);
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 22px;
        box-shadow: 0 18px 48px rgba(15, 23, 42, .07);
        backdrop-filter: blur(12px);
    }

    .workspace-sidebar {
        position: sticky;
        top: 88px;
        padding: 1.35rem;
    }

    .workspace-main {
        padding: 1.25rem;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(79, 70, 229, .08), transparent 22rem),
            linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .96));
    }

    .workspace-stage {
        height: 100%;
        min-height: clamp(720px, 82vh, 1400px);
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9);
        transition: all .25s ease;
    }

    body.workspace-focus-mode {
        overflow: hidden;
    }

    .workspace-stage.fullscreen {
        position: fixed;
        inset: 1rem;
        z-index: 1050;
        min-height: auto;
        height: auto;
        box-shadow: 0 24px 80px rgba(15, 23, 42, .25);
    }

    .workspace-stage.fullscreen .panel-body,
    .workspace-stage.fullscreen .sheet-wrap {
        height: 100%;
    }

    .workspace-stage.fullscreen .workspace-grid,
    .workspace-stage.fullscreen .ide-layout,
    .workspace-stage.fullscreen .workspace-stack {
        height: 100%;
    }

    .workspace-toolbar {
        display: flex;
        gap: .75rem;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 1.1rem;
        padding: .15rem 0 .25rem;
    }

    .workspace-intro {
        max-width: 720px;
    }

    .workspace-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr);
        gap: 1.1rem;
        height: 100%;
    }

    .workspace-stack {
        display: grid;
        gap: 1.1rem;
        height: 100%;
        grid-template-rows: minmax(0, 1fr) auto;
    }

    .workspace-grid.ide-grid {
        grid-template-columns: minmax(0, 1.6fr) minmax(300px, .7fr);
        align-items: stretch;
    }

    .ide-layout {
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr) minmax(320px, .9fr);
        gap: 1rem;
        height: 100%;
        align-items: stretch;
    }

    .ide-sidebar {
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #f8fbff;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .file-list {
        display: flex;
        flex-direction: column;
        gap: .5rem;
        max-height: 420px;
        overflow: auto;
    }

    .file-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        padding: .55rem .7rem;
        text-align: left;
    }

    .file-item.active {
        border-color: var(--brand);
        box-shadow: 0 0 0 1px rgba(37, 99, 235, .15);
        background: #eef4ff;
    }

    .file-name {
        font-size: .85rem;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-meta {
        font-size: .72rem;
        color: var(--muted);
    }

    .file-remove {
        border: 0;
        background: transparent;
        color: #b91c1c;
        padding: 0 .2rem;
    }

    .file-create-form {
        display: flex;
        gap: .5rem;
    }

    .file-create-form input,
    .file-create-form select {
        font-size: .82rem;
    }

    .editor-surface {
        min-height: 640px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
    }

    .editor-surface-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .editor-status {
        font-size: .78rem;
        color: var(--muted);
    }

    .panel-card {
        border: 1px solid rgba(226, 232, 240, .92);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .98));
        overflow: hidden;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
    }

    .panel-card h6 {
        margin: 0;
        padding: 1rem 1.1rem;
        border-bottom: 1px solid rgba(226, 232, 240, .92);
        font-weight: 700;
        background: rgba(248, 250, 252, .86);
    }

    .panel-body {
        padding: 1.1rem;
    }

    .editor-tabs {
        display: flex;
        gap: .5rem;
        margin-bottom: .75rem;
        flex-wrap: wrap;
    }

    .editor-tab {
        border: 1px solid rgba(226, 232, 240, .95);
        background: rgba(255, 255, 255, .9);
        color: var(--dark);
        border-radius: 999px;
        padding: .45rem .95rem;
        font-size: .82rem;
        font-weight: 600;
        transition: all .18s ease;
    }

    .editor-tab.active {
        background: var(--brand);
        color: #fff;
        border-color: var(--brand);
        box-shadow: 0 10px 24px rgba(79, 70, 229, .22);
    }

    .editor-pane {
        display: none;
    }

    .editor-pane.active {
        display: block;
    }

    .code-input {
        width: 100%;
        min-height: 500px;
        resize: none;
        border-radius: 14px;
        border: 0;
        outline: none;
        font-family: "Cascadia Code", Consolas, monospace;
        font-size: .9rem;
        line-height: 1.6;
        background: linear-gradient(180deg, #0f172a 0%, #111c33 100%);
        color: #e2e8f0;
        padding: 1rem 1.05rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
    }

    .preview-frame {
        width: 100%;
        min-height: 500px;
        height: 100%;
        border: 0;
        background: #fff;
        border-radius: 0 0 18px 18px;
    }

    .sheet-wrap {
        overflow: auto;
        height: 100%;
        padding: 1rem;
        background: #fff;
    }

    .sheet-table {
        border-collapse: collapse;
        min-width: 1200px;
        width: 100%;
    }

    .sheet-table th,
    .sheet-table td {
        border: 1px solid #d8e1eb;
        min-width: 110px;
        height: 42px;
        padding: .35rem .5rem;
    }

    .sheet-table td {
        background: #fffdf8;
    }

    .sheet-table td[contenteditable="true"] {
        outline: none;
    }

    .sheet-table th {
        background: #eff6ff;
        text-align: center;
        font-size: .8rem;
    }

    .note-editor {
        min-height: 520px;
        border: 0;
        outline: none;
        padding: 1rem;
        font-size: .95rem;
        line-height: 1.65;
    }

    .design-frame {
        width: 100%;
        min-height: 620px;
        border: 0;
        background: #fff;
    }

    .mini-stat {
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 16px;
        padding: .9rem 1rem;
        background: linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .context-list {
        display: flex;
        flex-direction: column;
        gap: .65rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .context-list li {
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 14px;
        padding: .8rem .9rem;
        background: linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .workspace-note {
        width: 100%;
        min-height: 190px;
        resize: vertical;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 14px;
        padding: .85rem 1rem;
        font-size: .92rem;
        background: #fff;
    }

    .workspace-actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .workspace-actions button,
    .workspace-actions select {
        border: 1px solid rgba(203, 213, 225, .95);
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        padding: .5rem .9rem;
        font-size: .82rem;
        font-weight: 600;
        transition: all .18s ease;
    }

    .workspace-actions button:hover,
    .workspace-actions select:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .workspace-actions button.is-active {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .workspace-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .workspace-metric {
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 16px;
        padding: .9rem 1rem;
        background: linear-gradient(180deg, #ffffff, #f5f8ff);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
    }

    .workspace-metric .value {
        font-size: 1.15rem;
        font-weight: 800;
    }

    .focus-only-close {
        display: none;
    }

    body.workspace-focus-mode .focus-only-close {
        display: inline-flex;
    }

    @media (max-width: 991px) {
        .workspace-shell {
            grid-template-columns: 1fr;
        }

        .workspace-sidebar {
            position: static;
        }

        .workspace-grid {
            grid-template-columns: 1fr;
        }

        .workspace-metrics {
            grid-template-columns: 1fr;
        }

        .ide-layout {
            grid-template-columns: 1fr;
        }

        .workspace-main,
        .workspace-sidebar {
            padding: 1rem;
            border-radius: 18px;
        }

        .workspace-stage {
            min-height: auto;
        }

        .code-input,
        .preview-frame,
        .design-frame,
        .note-editor {
            min-height: 380px;
        }

        .workspace-actions {
            width: 100%;
        }

        .workspace-actions button,
        .workspace-actions select {
            flex: 1 1 auto;
            justify-content: center;
        }
    }

    @media (max-width: 575px) {
        .workspace-toolbar {
            align-items: stretch;
        }

        .workspace-intro {
            max-width: none;
        }

        .workspace-metric .value {
            font-size: 1rem;
        }
    }
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
        <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
        <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
      </div>
    </div>
  </nav>

    <div class="container py-4">
        <div class="workspace-shell" id="workspaceShell">
            <aside class="workspace-sidebar">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <div class="text-muted small text-uppercase" style="letter-spacing:.08em">Course Workspace</div>
                        <h3 class="mb-1" style="font-weight:800"><?= e((string)$course['title']) ?></h3>
                        <div class="small text-muted"><i
                                class="fa <?= e($workspaceIcon) ?> me-1"></i><?= e($workspaceLabel) ?></div>
                    </div>
                    <span class="badge-success">Live</span>
                </div>

                <p class="text-muted" style="font-size:.92rem"><?= e($workspaceDesc) ?></p>

                <div class="mini-stat mb-2">
                    <div class="small text-muted">Workspace Type</div>
                    <div class="fw-semibold"><?= e($workspaceLabel) ?></div>
                </div>
                <div class="mini-stat mb-2">
                    <div class="small text-muted">Level</div>
                    <div class="fw-semibold"><?= e(ucfirst((string)($course['level'] ?? 'beginner'))) ?></div>
                </div>
                <div class="mini-stat mb-3">
                    <div class="small text-muted">Payment Status</div>
                    <div class="fw-semibold">
                        <?= e(ucfirst($status === 'installment' ? 'unlocked by installment' : $status)) ?></div>
                </div>

                <div class="lms-alert lms-alert-info mb-3" style="font-size:.85rem">
                    <i class="fa fa-circle-info me-1"></i>
                    Your work is stored locally in this browser for quick practice sessions inside the LMS.
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn-brand" id="saveWorkspaceBtn">
                        <i class="fa fa-floppy-disk me-1"></i> Save Workspace
                    </button>
                    <button type="button" class="btn-ghost" id="resetWorkspaceBtn">
                        <i class="fa fa-rotate-left me-1"></i> Reset Workspace
                    </button>
                </div>
            </aside>

            <section class="workspace-main">
                <div class="workspace-toolbar">
                    <div class="workspace-intro">
                        <div class="form-section-title mb-1"><i
                                class="fa <?= e($workspaceIcon) ?> me-1"></i><?= e($workspaceLabel) ?></div>
                        <div class="text-muted" style="font-size:.82rem">
                            <?= e((string)($course['short_description'] ?: $course['description'] ?: 'Practice directly inside the LMS environment.')) ?>
                        </div>
                    </div>
                    <div class="workspace-actions">
                        <button type="button" id="toggleWorkspaceExpandBtn" onclick="toggleWorkspaceExpand()"><i
                                class="fa fa-up-right-and-down-left-from-center me-1"></i><span>Expand</span></button>
                        <button type="button" id="toggleWorkspaceFullscreenBtn"
                            onclick="toggleWorkspaceFullscreen(false)"><i class="fa fa-maximize me-1"></i><span>Focus
                                Mode</span></button>
                        <button type="button" id="exitWorkspaceFullscreenBtn" class="focus-only-close"
                            onclick="toggleWorkspaceFullscreen(true)"><i class="fa fa-xmark me-1"></i><span>Exit
                                Focus</span></button>
                    </div>
                </div>

                <div class="workspace-metrics">
                    <div class="workspace-metric">
                        <div class="text-muted small">Lessons</div>
                        <div class="value"><?= $lessonCount ?></div>
                    </div>
                    <div class="workspace-metric">
                        <div class="text-muted small">Videos</div>
                        <div class="value"><?= $videoCount ?></div>
                    </div>
                    <div class="workspace-metric">
                        <div class="text-muted small">Assignments</div>
                        <div class="value"><?= $assignmentCount ?></div>
                    </div>
                </div>

                <div class="workspace-stage" id="workspaceStage">
                    <?php if ($workspaceType === 'ide'): ?>
                    <div class="panel-body h-100">
                        <div class="workspace-grid">
                            <div class="panel-card">
                                <h6><i class="fa fa-code me-2"></i>Code Editor</h6>
                                <div class="panel-body">
                                    <div class="editor-tabs">
                                        <button type="button" class="editor-tab active"
                                            data-pane="htmlPane">HTML</button>
                                        <?php if ($showPhpEditor): ?>
                                        <button type="button" class="editor-tab" data-pane="phpPane">PHP</button>
                                        <?php endif; ?>
                                        <button type="button" class="editor-tab" data-pane="cssPane">CSS</button>
                                        <button type="button" class="editor-tab" data-pane="jsPane">JavaScript</button>
                                        <button type="button" class="editor-tab" data-pane="notesPane">Notes</button>
                                    </div>
                                    <div class="editor-pane active" id="htmlPane">
                                        <textarea class="code-input" id="htmlEditor"><?= e($idePrimaryHtml) ?></textarea>
                                    </div>
                                    <?php if ($showPhpEditor): ?>
                                    <div class="editor-pane" id="phpPane">
                                        <textarea class="code-input" id="phpEditor"><?= e($idePrimaryPhp) ?></textarea>
                                    </div>
                                    <?php endif; ?>
                                    <div class="editor-pane" id="cssPane">
                                        <textarea class="code-input" id="cssEditor"><?= e($idePrimaryCss) ?></textarea>
                                    </div>
                                    <div class="editor-pane" id="jsPane">
                                        <textarea class="code-input" id="jsEditor"><?= e($idePrimaryJs) ?></textarea>
                                    </div>
                                    <div class="editor-pane" id="notesPane">
                                        <textarea class="code-input" id="notesEditor"><?= e($ideNotes) ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="workspace-stack">
                                <div class="panel-card">
                                    <h6><i class="fa fa-display me-2"></i>Live Preview</h6>
                                    <iframe title="Workspace Preview" id="previewFrame" class="preview-frame"></iframe>
                                </div>
                                <div class="panel-card">
                                    <h6><i class="fa fa-layer-group me-2"></i>Course Context</h6>
                                    <div class="panel-body">
                                        <ul class="context-list">
                                            <?php if ($lessonTitles): ?>
                                            <?php foreach ($lessonTitles as $title): ?>
                                            <li><?= e($title) ?></li>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <li>No lesson outline available yet. Add your implementation plan here as
                                                the course grows.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php elseif ($workspaceType === 'spreadsheet'): ?>
                    <div class="sheet-wrap">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="text-muted small">Edit cells directly. Use the first row for headers and build
                                your worksheet inside the LMS.</div>
                            <div class="workspace-actions">
                                <button type="button" id="addSheetRowBtn"><i class="fa fa-plus me-1"></i>Add
                                    Row</button>
                                <button type="button" id="addSheetColumnBtn"><i class="fa fa-table-columns me-1"></i>Add
                                    Column</button>
                                <button type="button" class="btn-ghost" id="downloadCsvBtn"><i
                                        class="fa fa-download me-1"></i>Download CSV</button>
                            </div>
                        </div>
                        <table class="sheet-table" id="sheetTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                    <th>D</th>
                                    <th>E</th>
                                    <th>F</th>
                                    <th>G</th>
                                    <th>H</th>
                                    <th>I</th>
                                    <th>J</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($r = 1; $r <= 20; $r++): ?>
                                <tr>
                                    <th><?= $r ?></th>
                                    <?php for ($c = 1; $c <= 10; $c++): ?>
                                    <td contenteditable="true" data-row="<?= $r ?>" data-col="<?= $c ?>"></td>
                                    <?php endfor; ?>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php elseif ($workspaceType === 'design'): ?>
                    <div class="panel-body h-100">
                        <div class="workspace-grid">
                            <div class="panel-card">
                                <h6><i class="fa fa-pen-ruler me-2"></i>Design Studio</h6>
                                <iframe title="Design Workspace" class="design-frame"
                                    src="<?= e($workspaceUrl !== '' ? $workspaceUrl : 'https://www.photopea.com') ?>"
                                    allow="clipboard-read; clipboard-write" loading="lazy"></iframe>
                            </div>
                            <div class="workspace-stack">
                                <div class="panel-card">
                                    <h6><i class="fa fa-clipboard-list me-2"></i>Creative Brief</h6>
                                    <div class="panel-body">
                                        <textarea class="workspace-note" id="designBriefEditor">Project brief for <?= e((string)$course['title']) ?>:

- Objective:
- Target audience:
- Style direction:
- Deliverables:
- Review checklist:</textarea>
                                    </div>
                                </div>
                                <div class="panel-card">
                                    <h6><i class="fa fa-list-check me-2"></i>Course Milestones</h6>
                                    <div class="panel-body">
                                        <ul class="context-list">
                                            <?php if ($lessonTitles): ?>
                                            <?php foreach ($lessonTitles as $title): ?>
                                            <li><?= e($title) ?></li>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <li>Add lesson-based design milestones as your course grows.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="panel-body h-100">
                        <div class="workspace-grid">
                            <div class="panel-card h-100">
                                <h6><i class="fa fa-file-lines me-2"></i>Office Workspace</h6>
                                <div class="panel-body">
                                    <div class="text-muted small mb-2">Draft documents, summarize lessons, and keep
                                        structured notes for this course.</div>
                                    <div id="officeEditor" class="note-editor" contenteditable="true">
                                        <h2><?= e((string)$course['title']) ?> Workspace Notes</h2>
                                        <p>Use this space to capture ideas, write reports, and organize your course
                                            work.</p>
                                        <ul>
                                            <li>Lesson objectives</li>
                                            <li>Practice tasks</li>
                                            <li>Submission checklist</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="workspace-stack">
                                <div class="panel-card">
                                    <h6><i class="fa fa-book-open me-2"></i>Course Outline</h6>
                                    <div class="panel-body">
                                        <ul class="context-list">
                                            <?php if ($lessonTitles): ?>
                                            <?php foreach ($lessonTitles as $title): ?>
                                            <li><?= e($title) ?></li>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <li>No lessons have been added yet.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-card">
                                    <h6><i class="fa fa-rectangle-list me-2"></i>Research Pad</h6>
                                    <div class="panel-body">
                                        <textarea class="workspace-note"
                                            id="officeScratchpad">Use this pad for quick bullets, references, and draft ideas as the course content grows.</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script>
    const storageKey = <?= json_encode($storageKey, JSON_UNESCAPED_SLASHES) ?>;
    const workspaceType = <?= json_encode($workspaceType, JSON_UNESCAPED_SLASHES) ?>;

    function saveWorkspaceState() {
        let payload = {};

        if (workspaceType === 'ide') {
            payload = {
                html: document.getElementById('htmlEditor')?.value || '',
                php: document.getElementById('phpEditor')?.value || '',
                css: document.getElementById('cssEditor')?.value || '',
                js: document.getElementById('jsEditor')?.value || '',
                notes: document.getElementById('notesEditor')?.value || ''
            };
        } else if (workspaceType === 'spreadsheet') {
            payload = {
                cells: {},
                meta: {
                    rows: document.querySelectorAll('#sheetTable tbody tr').length,
                    cols: Math.max(0, document.querySelectorAll('#sheetTable thead th').length - 1)
                }
            };

            document.querySelectorAll('#sheetTable td[contenteditable="true"]').forEach((cell) => {
                payload.cells[cell.dataset.row + '_' + cell.dataset.col] = cell.innerHTML;
            });
        } else if (workspaceType === 'design') {
            payload = {
                brief: document.getElementById('designBriefEditor')?.value || ''
            };
        } else {
            payload = {
                html: document.getElementById('officeEditor')?.innerHTML || '',
                scratchpad: document.getElementById('officeScratchpad')?.value || ''
            };
        }

        localStorage.setItem(storageKey, JSON.stringify(payload));
    }

    function loadWorkspaceState() {
        const raw = localStorage.getItem(storageKey);
        if (!raw) {
            return;
        }

        try {
            const payload = JSON.parse(raw);

            if (workspaceType === 'ide') {
                if (typeof payload.html === 'string') {
                    document.getElementById('htmlEditor').value = payload.html;
                }
                if (typeof payload.php === 'string' && document.getElementById('phpEditor')) {
                    document.getElementById('phpEditor').value = payload.php;
                }
                if (typeof payload.css === 'string') {
                    document.getElementById('cssEditor').value = payload.css;
                }
                if (typeof payload.js === 'string') {
                    document.getElementById('jsEditor').value = payload.js;
                }
                if (typeof payload.notes === 'string') {
                    document.getElementById('notesEditor').value = payload.notes;
                }
            } else if (workspaceType === 'spreadsheet') {
                expandSheet(
                    Number(payload.meta?.rows || 0),
                    Number(payload.meta?.cols || 0)
                );

                document.querySelectorAll('#sheetTable td[contenteditable="true"]').forEach((cell) => {
                    const key = cell.dataset.row + '_' + cell.dataset.col;
                    if (Object.prototype.hasOwnProperty.call(payload.cells || {}, key)) {
                        cell.innerHTML = payload.cells[key];
                    }
                });
            } else if (workspaceType === 'design') {
                if (typeof payload.brief === 'string') {
                    document.getElementById('designBriefEditor').value = payload.brief;
                }
            } else {
                if (typeof payload.html === 'string') {
                    document.getElementById('officeEditor').innerHTML = payload.html;
                }
                if (typeof payload.scratchpad === 'string') {
                    document.getElementById('officeScratchpad').value = payload.scratchpad;
                }
            }
        } catch (error) {
            console.warn('Failed to restore workspace state', error);
        }
    }

    function resetWorkspaceState() {
        localStorage.removeItem(storageKey);
        window.location.reload();
    }

    function renderIdePreview() {
        if (workspaceType !== 'ide') {
            return;
        }

        const frame = document.getElementById('previewFrame');
        if (!frame) {
            return;
        }

        const html = document.getElementById('htmlEditor')?.value || '';
        const php = document.getElementById('phpEditor')?.value || '';
        const css = document.getElementById('cssEditor')?.value || '';
        const js = document.getElementById('jsEditor')?.value || '';
        const doc = frame.contentDocument || frame.contentWindow.document;
        const source = php.trim() !== '' ? php : html;
        const previewNotice = php.trim() !== ''
            ? '<aside style="margin:0 0 1rem;padding:.85rem 1rem;border:1px solid #fed7aa;border-radius:14px;background:#fff7ed;color:#9a3412;font:14px Arial,sans-serif">PHP preview is shown in safe design mode. PHP code is editable here, but not executed inside the LMS preview.</aside>'
            : '';
        const renderedMarkup = source
            .replace(/<\?php[\s\S]*?\?>/gi, '')
            .replace(/<\?=[\s\S]*?\?>/gi, '')
            .replace(/<\?(?!xml)[\s\S]*?\?>/gi, '')
            .trim();

        doc.open();
        doc.write(`<!doctype html><html><head><style>${css}</style></head><body>${previewNotice}${renderedMarkup}<script>${js}<\/script></body></html>`);
        doc.close();
    }

    function downloadSheetCsv() {
        const rows = [];

        document.querySelectorAll('#sheetTable tbody tr').forEach((row) => {
            const cells = Array.from(row.querySelectorAll('td')).map((cell) => {
                return '"' + (cell.innerText || '').replace(/"/g, '""') + '"';
            });
            rows.push(cells.join(','));
        });

        const blob = new Blob([rows.join('\n')], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'course-workspace.csv';
        link.click();
        URL.revokeObjectURL(url);
    }

    function columnLabel(index) {
        let label = '';
        let current = index;

        while (current > 0) {
            const mod = (current - 1) % 26;
            label = String.fromCharCode(65 + mod) + label;
            current = Math.floor((current - mod) / 26);
        }

        return label;
    }

    function attachSheetCellListeners() {
        document.querySelectorAll('#sheetTable td[contenteditable="true"]').forEach((cell) => {
            if (cell.dataset.bound === '1') {
                return;
            }

            cell.dataset.bound = '1';
            cell.addEventListener('input', saveWorkspaceState);
        });
    }

    function expandSheet(targetRows, targetCols) {
        if (workspaceType !== 'spreadsheet') {
            return;
        }

        const table = document.getElementById('sheetTable');
        const headRow = table?.querySelector('thead tr');
        const body = table?.querySelector('tbody');
        if (!headRow || !body) {
            return;
        }

        const requestedRows = Math.max(targetRows, body.querySelectorAll('tr').length);
        const requestedCols = Math.max(targetCols, headRow.querySelectorAll('th').length - 1);
        let currentCols = Math.max(0, headRow.querySelectorAll('th').length - 1);
        let currentRows = body.querySelectorAll('tr').length;

        while (currentCols < requestedCols) {
            currentCols += 1;
            const th = document.createElement('th');
            th.textContent = columnLabel(currentCols);
            headRow.appendChild(th);

            body.querySelectorAll('tr').forEach((row, rowIndex) => {
                const td = document.createElement('td');
                td.setAttribute('contenteditable', 'true');
                td.dataset.row = String(rowIndex + 1);
                td.dataset.col = String(currentCols);
                row.appendChild(td);
            });
        }

        while (currentRows < requestedRows) {
            currentRows += 1;
            const tr = document.createElement('tr');
            const th = document.createElement('th');
            th.textContent = String(currentRows);
            tr.appendChild(th);

            for (let col = 1; col <= currentCols; col += 1) {
                const td = document.createElement('td');
                td.setAttribute('contenteditable', 'true');
                td.dataset.row = String(currentRows);
                td.dataset.col = String(col);
                tr.appendChild(td);
            }

            body.appendChild(tr);
        }

        attachSheetCellListeners();
    }

    function syncWorkspaceControls() {
        const shell = document.getElementById('workspaceShell');
        const stage = document.getElementById('workspaceStage');
        const expandBtn = document.getElementById('toggleWorkspaceExpandBtn');
        const focusBtn = document.getElementById('toggleWorkspaceFullscreenBtn');
        const exitFocusBtn = document.getElementById('exitWorkspaceFullscreenBtn');
        const isExpanded = shell?.classList.contains('expanded') || false;
        const isFocused = stage?.classList.contains('fullscreen') || false;

        if (expandBtn) {
            const label = expandBtn.querySelector('span');
            const icon = expandBtn.querySelector('i');
            if (label) {
                label.textContent = isExpanded ? 'Collapse' : 'Expand';
            }
            if (icon) {
                icon.className = isExpanded ? 'fa fa-compress me-1' : 'fa fa-up-right-and-down-left-from-center me-1';
            }
            expandBtn.classList.toggle('is-active', isExpanded);
            expandBtn.setAttribute('aria-pressed', isExpanded ? 'true' : 'false');
        }

        if (focusBtn) {
            const label = focusBtn.querySelector('span');
            const icon = focusBtn.querySelector('i');
            if (label) {
                label.textContent = isFocused ? 'Exit Focus' : 'Focus Mode';
            }
            if (icon) {
                icon.className = isFocused ? 'fa fa-minimize me-1' : 'fa fa-maximize me-1';
            }
            focusBtn.classList.toggle('is-active', isFocused);
            focusBtn.setAttribute('aria-pressed', isFocused ? 'true' : 'false');
        }

        if (exitFocusBtn) {
            exitFocusBtn.style.display = isFocused ? 'inline-flex' : 'none';
        }
    }

    window.toggleWorkspaceExpand = function() {
        const shell = document.getElementById('workspaceShell');
        if (!shell) {
            return;
        }

        shell.classList.toggle('expanded');
        syncWorkspaceControls();
    };

    window.toggleWorkspaceFullscreen = function(forceOff = false) {
        const shell = document.getElementById('workspaceShell');
        const stage = document.getElementById('workspaceStage');
        if (!stage) {
            return;
        }

        if (forceOff) {
            stage.classList.remove('fullscreen');
            document.body.classList.remove('workspace-focus-mode');
            if (shell?.dataset.focusAutoExpanded === '1') {
                shell.classList.remove('expanded');
                delete shell.dataset.focusAutoExpanded;
            }
            syncWorkspaceControls();
            return;
        }

        const enteringFocus = !stage.classList.contains('fullscreen');
        if (enteringFocus && shell && !shell.classList.contains('expanded')) {
            shell.classList.add('expanded');
            shell.dataset.focusAutoExpanded = '1';
        }

        stage.classList.toggle('fullscreen', enteringFocus);
        document.body.classList.toggle('workspace-focus-mode', enteringFocus);

        if (!enteringFocus && shell?.dataset.focusAutoExpanded === '1') {
            shell.classList.remove('expanded');
            delete shell.dataset.focusAutoExpanded;
        }

        syncWorkspaceControls();
    };

    document.getElementById('saveWorkspaceBtn')?.addEventListener('click', saveWorkspaceState);
    document.getElementById('resetWorkspaceBtn')?.addEventListener('click', resetWorkspaceState);
    document.getElementById('downloadCsvBtn')?.addEventListener('click', downloadSheetCsv);
    document.getElementById('addSheetRowBtn')?.addEventListener('click', () => {
        expandSheet(
            document.querySelectorAll('#sheetTable tbody tr').length + 1,
            Math.max(0, document.querySelectorAll('#sheetTable thead th').length - 1)
        );
        saveWorkspaceState();
    });
    document.getElementById('addSheetColumnBtn')?.addEventListener('click', () => {
        expandSheet(
            document.querySelectorAll('#sheetTable tbody tr').length,
            document.querySelectorAll('#sheetTable thead th').length
        );
        saveWorkspaceState();
    });

    if (workspaceType === 'ide') {
        loadWorkspaceState();

        document.querySelectorAll('.editor-tab').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.editor-tab').forEach((tab) => tab.classList.remove('active'));
                document.querySelectorAll('.editor-pane').forEach((pane) => pane.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(button.dataset.pane)?.classList.add('active');
            });
        });

        ['htmlEditor', 'phpEditor', 'cssEditor', 'jsEditor', 'notesEditor'].forEach((id) => {
            document.getElementById(id)?.addEventListener('input', () => {
                renderIdePreview();
                saveWorkspaceState();
            });
        });

        renderIdePreview();
    } else if (workspaceType === 'spreadsheet') {
        loadWorkspaceState();
        attachSheetCellListeners();
    } else if (workspaceType === 'design') {
        loadWorkspaceState();
        document.getElementById('designBriefEditor')?.addEventListener('input', saveWorkspaceState);
    } else {
        loadWorkspaceState();
        document.getElementById('officeEditor')?.addEventListener('input', saveWorkspaceState);
        document.getElementById('officeScratchpad')?.addEventListener('input', saveWorkspaceState);
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            window.toggleWorkspaceFullscreen(true);
        }
    });

    syncWorkspaceControls();
    </script>
</body>

</html>
