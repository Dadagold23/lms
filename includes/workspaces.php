<?php
declare(strict_types=1);

function workspaceColumnsExist(PDO $pdo): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM lms_courses LIKE 'workspace_type'");
        $cache = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}

function workspaceCourseSelectSql(PDO $pdo, string $alias = 'c'): string
{
    if (!workspaceColumnsExist($pdo)) {
        return '';
    }

    return ", {$alias}.workspace_type, {$alias}.workspace_url";
}

function workspaceCourseRow(array $course): array
{
    if (!array_key_exists('workspace_type', $course)) {
        $course['workspace_type'] = 'default';
    }

    if (!array_key_exists('workspace_url', $course)) {
        $course['workspace_url'] = null;
    }

    return $course;
}

function workspaceTypeOptions(): array
{
    return [
        'default' => 'Auto Detect',
        'ide' => 'IDE Workspace',
        'spreadsheet' => 'Spreadsheet Workspace',
        'design' => 'Design Workspace',
        'office' => 'Office Workspace',
    ];
}

function normalizeWorkspaceType(?string $type): string
{
    $type = strtolower(trim((string)$type));
    return array_key_exists($type, workspaceTypeOptions()) ? $type : 'default';
}

function inferWorkspaceType(array $course): string
{
    $explicit = normalizeWorkspaceType((string)($course['workspace_type'] ?? 'default'));
    if ($explicit !== 'default') {
        return $explicit;
    }

    $haystack = strtolower(trim(
        (string)($course['title'] ?? '') . ' ' .
        (string)($course['slug'] ?? '') . ' ' .
        (string)($course['short_description'] ?? '')
    ));

    if ($haystack === '') {
        return 'office';
    }

    // UI/UX and web design courses behave more like build/prototype workflows
    // than raster graphics editing, so prefer the IDE-style workspace.
    if (preg_match('/web design|web-development|web development|ui\/ux|ui ux|ux design|product design|frontend|front-end|desktop application|desktop app/', $haystack)) {
        return 'ide';
    }

    if (preg_match('/graphic|photo|branding|illustration|photoshop|photopea/', $haystack)) {
        return 'design';
    }

    if (preg_match('/analysis|analytics|spreadsheet|excel|account|finance|business|marketing/', $haystack)) {
        return 'spreadsheet';
    }

    if (preg_match('/web|php|mysql|development|mobile|app|cyber|code|program|software|data science|machine learning|ai/', $haystack)) {
        return 'ide';
    }

    return 'office';
}

function workspaceTypeLabel(array $course): string
{
    $type = inferWorkspaceType($course);
    return workspaceTypeOptions()[$type] ?? 'Workspace';
}

function workspaceTypeIcon(array $course): string
{
    return match (inferWorkspaceType($course)) {
        'ide' => 'fa-code',
        'spreadsheet' => 'fa-table',
        'design' => 'fa-pen-ruler',
        default => 'fa-file-lines',
    };
}

function workspaceTypeDescription(array $course): string
{
    return match (inferWorkspaceType($course)) {
        'ide' => 'Code, preview, and experiment inside the LMS with a browser-based practice IDE.',
        'spreadsheet' => 'Work with rows, columns, and quick calculations in a spreadsheet-style workspace.',
        'design' => 'Launch a visual design studio for mockups, edits, and creative practice without leaving the LMS.',
        default => 'Take structured notes, draft documents, and organize course work in an office workspace.',
    };
}

function workspaceLaunchUrl(array $course): string
{
    return 'workspace.php?course_id=' . (int)($course['id'] ?? 0);
}

function workspaceIdeStarterTemplate(array $course, array $lessonTitles = []): array
{
    $title = trim((string)($course['title'] ?? 'Course Project'));
    $slug = trim((string)($course['slug'] ?? 'project'));
    $description = trim((string)($course['short_description'] ?? $course['description'] ?? 'Build directly inside the LMS workspace.'));
    $haystack = strtolower($title . ' ' . $slug . ' ' . $description);

    $html = "<main class=\"app-shell\">\n  <header class=\"hero\">\n    <p class=\"eyebrow\">Grafix@Mirror LMS</p>\n    <h1>{$title}</h1>\n    <p class=\"lead\">{$description}</p>\n    <a class=\"cta\" href=\"about.html\">Explore project notes</a>\n  </header>\n</main>";
    $css = "body {\n  font-family: Arial, sans-serif;\n  margin: 0;\n  padding: 2rem;\n  background: #f8fafc;\n  color: #0f172a;\n}\n\n.app-shell {\n  max-width: 960px;\n  margin: 0 auto;\n}\n\n.hero {\n  background: #ffffff;\n  border: 1px solid #dbeafe;\n  border-radius: 20px;\n  padding: 2rem;\n  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);\n}\n\n.eyebrow {\n  text-transform: uppercase;\n  letter-spacing: 0.12em;\n  color: #2563eb;\n  font-size: 0.78rem;\n  font-weight: 700;\n}\n\nh1 {\n  margin: 0.2rem 0 0.75rem;\n}\n\n.lead {\n  max-width: 60ch;\n  line-height: 1.7;\n}\n\n.cta {\n  display: inline-block;\n  margin-top: 1rem;\n  padding: 0.8rem 1.1rem;\n  border-radius: 999px;\n  background: #0f766e;\n  color: #ffffff;\n  text-decoration: none;\n  font-weight: 700;\n}\n";
    $js = "console.log('Workspace ready for {$title}');";

    $isPhpCourse = preg_match('/php|laravel|mysql|backend|server-side/', $haystack) === 1;

    if (preg_match('/web|frontend|php|mysql|software|app|desktop|mobile|development|program/', $haystack)) {
        $html = "<main class=\"layout\">\n  <section class=\"hero-card\">\n    <span class=\"pill\">Build Project</span>\n    <h1>{$title}</h1>\n    <p>{$description}</p>\n    <div class=\"actions\">\n      <a class=\"primary-btn\" href=\"about.html\">View brief</a>\n      <button class=\"secondary-btn\" type=\"button\" id=\"demoBtn\">Run interaction</button>\n    </div>\n  </section>\n  <section class=\"grid\" id=\"featureGrid\"></section>\n</main>";
        $css = "body {\n  margin: 0;\n  font-family: Segoe UI, Arial, sans-serif;\n  background: linear-gradient(135deg, #eff6ff, #ecfeff);\n  color: #0f172a;\n}\n\n.layout {\n  max-width: 1120px;\n  margin: 0 auto;\n  padding: 2rem;\n}\n\n.hero-card {\n  background: rgba(255,255,255,0.92);\n  border: 1px solid rgba(37,99,235,0.12);\n  border-radius: 24px;\n  padding: 2rem;\n  box-shadow: 0 24px 60px rgba(37, 99, 235, 0.12);\n}\n\n.pill {\n  display: inline-flex;\n  padding: 0.35rem 0.7rem;\n  border-radius: 999px;\n  background: #dbeafe;\n  color: #1d4ed8;\n  font-size: 0.78rem;\n  font-weight: 700;\n  letter-spacing: 0.06em;\n  text-transform: uppercase;\n}\n\n.actions {\n  display: flex;\n  gap: 0.75rem;\n  flex-wrap: wrap;\n  margin-top: 1rem;\n}\n\n.primary-btn,\n.secondary-btn {\n  border: 0;\n  border-radius: 999px;\n  padding: 0.85rem 1.15rem;\n  font-weight: 700;\n  cursor: pointer;\n  text-decoration: none;\n}\n\n.primary-btn {\n  background: #2563eb;\n  color: #fff;\n}\n\n.secondary-btn {\n  background: #e2e8f0;\n  color: #0f172a;\n}\n\n.grid {\n  display: grid;\n  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));\n  gap: 1rem;\n  margin-top: 1.25rem;\n}\n\n.card {\n  background: #fff;\n  border-radius: 18px;\n  padding: 1rem;\n  border: 1px solid #e2e8f0;\n}\n";
        $js = "const lessonTitles = " . json_encode(array_values($lessonTitles), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ";\nconst grid = document.getElementById('featureGrid');\nconst items = (lessonTitles.length ? lessonTitles : ['Structure layout', 'Style interface', 'Add interactivity']).map((title, index) => ({ title, detail: `Task \${index + 1}: refine this area of the project.` }));\ngrid.innerHTML = items.map((item) => `<article class=\"card\"><h3>\${item.title}</h3><p>\${item.detail}</p></article>`).join('');\ndocument.getElementById('demoBtn')?.addEventListener('click', () => alert('Interactive demo running inside the LMS workspace.'));";
    } elseif (preg_match('/ui\/ux|ux|product design/', $haystack)) {
        $html = "<main class=\"ux-board\">\n  <section class=\"summary\">\n    <h1>{$title}</h1>\n    <p>{$description}</p>\n  </section>\n  <section class=\"columns\">\n    <article><h2>User Goal</h2><p>What is the user trying to achieve?</p></article>\n    <article><h2>Pain Point</h2><p>What currently makes the journey difficult?</p></article>\n    <article><h2>Solution</h2><p>Sketch the improved flow and interface idea.</p></article>\n  </section>\n</main>";
        $css = "body {\n  margin: 0;\n  font-family: Arial, sans-serif;\n  background: #fff7ed;\n  color: #431407;\n}\n\n.ux-board {\n  max-width: 1100px;\n  margin: 0 auto;\n  padding: 2rem;\n}\n\n.summary,\n.columns article {\n  background: #ffffff;\n  border: 1px solid #fed7aa;\n  border-radius: 20px;\n  padding: 1.4rem;\n}\n\n.columns {\n  display: grid;\n  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));\n  gap: 1rem;\n  margin-top: 1rem;\n}\n";
        $js = "console.log('UX starter template loaded');";
    }

    $aboutLines = array_map(static fn(string $lesson): string => '- ' . $lesson, $lessonTitles);
    $about = "<main>\n  <h1>Project Brief</h1>\n  <p>This page can hold research, requirements, and supporting screens for {$title}.</p>\n  <a href=\"index.html\">Return to main page</a>\n</main>";
    $readme = "# {$title} Workspace Notes\n\n## Goals\n- Capture the task you are building\n- Keep links to lesson concepts\n- Track bugs, edge cases, and improvements\n\n## Lesson Checklist\n" . (!empty($aboutLines) ? implode("\n", $aboutLines) : "- Add lesson milestones here");

    if ($isPhpCourse) {
        $indexPhp = "<?php\n\$pageTitle = '" . addslashes($title) . "';\n\$description = '" . addslashes($description) . "';\n?>\n<!doctype html>\n<html lang=\"en\">\n<head>\n  <meta charset=\"utf-8\">\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n  <title><?= htmlspecialchars(\$pageTitle, ENT_QUOTES, 'UTF-8') ?></title>\n  <link rel=\"stylesheet\" href=\"styles.css\">\n</head>\n<body>\n  <main class=\"layout\">\n    <section class=\"hero-card\">\n      <span class=\"pill\">PHP Project</span>\n      <h1><?= htmlspecialchars(\$pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>\n      <p><?= htmlspecialchars(\$description, ENT_QUOTES, 'UTF-8') ?></p>\n      <div class=\"actions\">\n        <a class=\"primary-btn\" href=\"about.php\">Open details</a>\n        <button class=\"secondary-btn\" type=\"button\" id=\"demoBtn\">Run interaction</button>\n      </div>\n    </section>\n    <section class=\"grid\" id=\"featureGrid\"></section>\n  </main>\n  <script src=\"app.js\"></script>\n</body>\n</html>";
        $aboutPhp = "<?php\n\$items = " . var_export($lessonTitles ?: ['Set up route', 'Render dynamic data', 'Add validation'], true) . ";\n?>\n<!doctype html>\n<html lang=\"en\">\n<head>\n  <meta charset=\"utf-8\">\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n  <title>Project Brief</title>\n  <link rel=\"stylesheet\" href=\"styles.css\">\n</head>\n<body>\n  <main class=\"layout\">\n    <section class=\"hero-card\">\n      <span class=\"pill\">Brief</span>\n      <h1>PHP Project Notes</h1>\n      <ul>\n        <?php foreach (\$items as \$item): ?>\n          <li><?= htmlspecialchars((string)\$item, ENT_QUOTES, 'UTF-8') ?></li>\n        <?php endforeach; ?>\n      </ul>\n      <a class=\"primary-btn\" href=\"index.php\">Back to home</a>\n    </section>\n  </main>\n</body>\n</html>";

        return [
            ['name' => 'index.php', 'type' => 'php', 'content' => $indexPhp],
            ['name' => 'about.php', 'type' => 'php', 'content' => $aboutPhp],
            ['name' => 'styles.css', 'type' => 'css', 'content' => $css],
            ['name' => 'app.js', 'type' => 'js', 'content' => $js],
            ['name' => 'README.md', 'type' => 'md', 'content' => $readme],
        ];
    }

    return [
        ['name' => 'index.html', 'type' => 'html', 'content' => $html],
        ['name' => 'about.html', 'type' => 'html', 'content' => $about],
        ['name' => 'styles.css', 'type' => 'css', 'content' => $css],
        ['name' => 'app.js', 'type' => 'js', 'content' => $js],
        ['name' => 'README.md', 'type' => 'md', 'content' => $readme],
    ];
}
