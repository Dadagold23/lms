<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$admin = $_SESSION['admin'];

$flash = $_SESSION['admin_badge_flash'] ?? null;
unset($_SESSION['admin_badge_flash']);

/* ======================
   HANDLE POST ACTIONS
   ====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $badgeTitle = trim((string)($_POST['badge_title'] ?? ''));
    $desc = trim((string)($_POST['badge_description'] ?? ''));
    $style = trim((string)($_POST['badge_style'] ?? 'hexagon'));
    $color = trim((string)($_POST['badge_color'] ?? 'gold-purple'));
    $icon = trim((string)($_POST['icon_class'] ?? 'fa-award'));

    if ($action === 'update') {
        if ($id <= 0 || $badgeTitle === '') {
            $_SESSION['admin_badge_flash'] = 'Badge Title is required.';
            redirect('admin_badges.php');
        }

        $stmt = $pdo->prepare("
            UPDATE lms_badges
            SET badge_title = ?, badge_description = ?, badge_style = ?, badge_color = ?, icon_class = ?
            WHERE id = ?
        ");
        $stmt->execute([$badgeTitle, $desc, $style, $color, $icon, $id]);

        $_SESSION['admin_badge_flash'] = 'Badge updated successfully.';
        redirect('admin_badges.php?edit=' . $id);
    }
}

/* ======================
   LOAD EDIT BADGE
   ====================== */
$editId = (int)($_GET['edit'] ?? 0);
$editBadge = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("
        SELECT b.*, c.title AS course_title
        FROM lms_badges b
        JOIN lms_courses c ON c.id = b.course_id
        WHERE b.id = ?
    ");
    $stmt->execute([$editId]);
    $editBadge = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ======================
   LIST ALL BADGES
   ===================== */
$badges = $pdo->query("
    SELECT b.*, c.title AS course_title, c.level
    FROM lms_badges b
    JOIN lms_courses c ON c.id = b.course_id
    ORDER BY c.title ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* Helper to render SVG Badge in Admin Panel */
function renderBadgeSvg(string $style, string $color, string $iconClass): string {
    $gradStart = '#7c5cbf';
    $gradEnd = '#e3c162';
    
    if ($color === 'emerald-teal') {
        $gradStart = '#06b6d4';
        $gradEnd = '#10b981';
    } elseif ($color === 'ruby-orange') {
        $gradStart = '#f97316';
        $gradEnd = '#ef4444';
    } elseif ($color === 'sapphire-blue') {
        $gradStart = '#3b82f6';
        $gradEnd = '#2563eb';
    }

    $shapeHtml = '';
    if ($style === 'shield') {
        $shapeHtml = '<path d="M100,20 C140,20 170,30 170,50 C170,110 140,160 100,180 C60,160 30,110 30,50 C30,30 60,20 100,20 Z" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />';
    } elseif ($style === 'star') {
        // Multi-point star
        $shapeHtml = '<path d="M100,15 L118,52 L158,45 L135,78 L168,100 L135,122 L158,155 L118,148 L100,185 L82,148 L42,155 L65,122 L32,100 L65,78 L42,45 L82,52 Z" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />';
    } elseif ($style === 'seal') {
        // Wavy Seal/Round
        $shapeHtml = '<circle cx="100" cy="100" r="75" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />';
        $shapeHtml .= '<circle cx="100" cy="100" r="68" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" stroke-dasharray="4,4" />';
    } else { // hexagon
        $shapeHtml = '<polygon points="100,20 170,60 170,140 100,180 30,140 30,60" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />';
    }

    // FontAwesome Icon Unicode Map Helper (simplifies showing inside SVG)
    $unicode = '&#xf091;'; // default award trophy
    if (str_contains($iconClass, 'graduation-cap')) $unicode = '&#xf19d;';
    elseif (str_contains($iconClass, 'crown')) $unicode = '&#xf521;';
    elseif (str_contains($iconClass, 'trophy')) $unicode = '&#xf091;';
    elseif (str_contains($iconClass, 'award')) $unicode = '&#xf559;';
    elseif (str_contains($iconClass, 'medal')) $unicode = '&#xf5a2;';
    elseif (str_contains($iconClass, 'code')) $unicode = '&#xf1c9;';
    elseif (str_contains($iconClass, 'shield')) $unicode = '&#xf3ed;';
    elseif (str_contains($iconClass, 'laptop')) $unicode = '&#xf5fc;';
    elseif (str_contains($iconClass, 'star')) $unicode = '&#xf005;';
    elseif (str_contains($iconClass, 'database')) $unicode = '&#xf1c0;';
    elseif (str_contains($iconClass, 'server')) $unicode = '&#xf233;';
    elseif (str_contains($iconClass, 'chart')) $unicode = '&#xf200;';

    return <<<SVG
    <svg viewBox="0 0 200 200" width="120" height="120" style="display:block;margin:0 auto;">
      <defs>
        <linearGradient id="adminGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="{$gradStart}" />
          <stop offset="100%" stop-color="{$gradEnd}" />
        </linearGradient>
        <filter id="adminShadow" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="4" stdDeviation="4" flood-opacity="0.3"/>
        </filter>
      </defs>
      {$shapeHtml}
      <!-- Icon -->
      <text x="100" y="112" font-family="'Font Awesome 6 Free', 'Font Awesome 5 Free'" font-weight="900" font-size="42" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">
        {$unicode}
      </text>
      <!-- Circular text overlay background glow -->
      <circle cx="100" cy="100" r="50" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="6" />
    </svg>
SVG;
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Manage Badges';
$seoDesc    = 'Manage badges for courses at Grafix@Mirror LMS — Mirror Age Concepts admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
  body{background:#f7fbff;font-family:Inter,system-ui}
  .card{border-radius:14px;box-shadow:0 4px 12px rgba(0,0,0,0.03)}
  .badge-preview-box {
    background: #111827;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 180px;
    border: 1px solid #1f2937;
  }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white text-decoration-none" href="admin_dashboard.php">Admin Panel</a>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_courses.php" class="btn btn-outline-light btn-sm">Courses</a>
      <a href="admin_dashboard.php" class="btn btn-warning btn-sm">Dashboard</a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <?php if ($flash): ?>
    <div class="alert alert-info"><?= e($flash) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- EDIT BADGE FORM -->
    <div class="col-lg-5">
      <div class="card p-4">
        <h5 class="mb-3"><?= $editBadge ? 'Customize Badge' : 'Select a badge to edit' ?></h5>
        
        <?php if ($editBadge): ?>
          <div class="mb-3">
            <label class="form-label d-block text-muted" style="font-size:.85rem">Badge Live Preview</label>
            <div class="badge-preview-box">
              <div id="svgPreviewContainer">
                <?= renderBadgeSvg($editBadge['badge_style'], $editBadge['badge_color'], $editBadge['icon_class']) ?>
              </div>
            </div>
          </div>

          <form method="post" id="badgeForm">
            <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$editBadge['id'] ?>">

            <div class="mb-2">
              <label class="form-label">Course</label>
              <input class="form-control" readonly disabled value="<?= e($editBadge['course_title']) ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Badge Title</label>
              <input class="form-control" name="badge_title" required value="<?= e($editBadge['badge_title']) ?>" placeholder="e.g. Web Design Master">
            </div>

            <div class="mb-2">
              <label class="form-label">Badge Description</label>
              <textarea class="form-control" rows="3" name="badge_description"><?= e($editBadge['badge_description'] ?? '') ?></textarea>
            </div>

            <div class="row g-2">
              <div class="col-md-6 mb-2">
                <label class="form-label">Badge Style</label>
                <select class="form-select" name="badge_style" id="badgeStyle">
                  <?php foreach (['hexagon'=>'Hexagon', 'shield'=>'Shield', 'star'=>'Star (Multi)', 'seal'=>'Seal (Round)'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $editBadge['badge_style'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label class="form-label">Color Theme</label>
                <select class="form-select" name="badge_color" id="badgeColor">
                  <?php foreach ([
                      'gold-purple'=>'Gold-Purple', 
                      'emerald-teal'=>'Emerald-Teal', 
                      'ruby-orange'=>'Ruby-Orange', 
                      'sapphire-blue'=>'Sapphire-Blue'
                    ] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $editBadge['badge_color'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Icon Class (FontAwesome 6)</label>
              <select class="form-select" name="icon_class" id="iconClass">
                <?php foreach ([
                    'fa-award'=>'Award Emblem (fa-award)',
                    'fa-graduation-cap'=>'Graduation Cap (fa-graduation-cap)',
                    'fa-trophy'=>'Trophy (fa-trophy)',
                    'fa-crown'=>'Crown (fa-crown)',
                    'fa-medal'=>'Medal (fa-medal)',
                    'fa-code'=>'Coding Brackets (fa-code)',
                    'fa-shield-halved'=>'Shield Protection (fa-shield-halved)',
                    'fa-laptop-code'=>'Laptop + Code (fa-laptop-code)',
                    'fa-star'=>'Star (fa-star)',
                    'fa-database'=>'Database (fa-database)',
                    'fa-server'=>'Server Rack (fa-server)',
                    'fa-chart-pie'=>'Chart (fa-chart-pie)'
                  ] as $val => $label): ?>
                  <option value="<?= e($val) ?>" <?= $editBadge['icon_class'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Selects the central emblem of the badge.</div>
            </div>

            <div class="d-grid gap-2">
              <button class="btn btn-primary"><i class="fa fa-save me-1"></i> Save Changes</button>
              <a href="admin_badges.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>

        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fa fa-award fa-3x mb-3 text-secondary"></i>
            <p>Select a course badge from the table to customize its style, title, and emblem.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- BADGE LIST TABLE -->
    <div class="col-lg-7">
      <div class="card p-4">
        <h5 class="mb-3">Course Badges</h5>
        
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width: 70px;">Badge</th>
                <th>Course Name</th>
                <th>Badge Title</th>
                <th>Style</th>
                <th>Color</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($badges as $b): ?>
                <tr class="<?= $editId === (int)$b['id'] ? 'table-primary' : '' ?>">
                  <td>
                    <div style="transform: scale(0.4); transform-origin: center; width:48px; height:48px; margin:-12px;">
                      <?= renderBadgeSvg($b['badge_style'], $b['badge_color'], $b['icon_class']) ?>
                    </div>
                  </td>
                  <td>
                    <div style="font-weight:600"><?= e($b['course_title']) ?></div>
                    <span class="badge bg-secondary" style="font-size:.7rem"><?= e(ucfirst($b['level'])) ?></span>
                  </td>
                  <td><?= e($b['badge_title']) ?></td>
                  <td><code style="font-size:.75rem"><?= e($b['badge_style']) ?></code></td>
                  <td><span class="badge text-dark" style="background:#e0e7ff; font-size:.75rem"><?= e($b['badge_color']) ?></span></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="admin_badges.php?edit=<?= (int)$b['id'] ?>">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Unicode map for SVG preview update
const iconUnicodeMap = {
  'fa-award': '&#xf559;',
  'fa-graduation-cap': '&#xf19d;',
  'fa-trophy': '&#xf091;',
  'fa-crown': '&#xf521;',
  'fa-medal': '&#xf5a2;',
  'fa-code': '&#xf1c9;',
  'fa-shield-halved': '&#xf3ed;',
  'fa-laptop-code': '&#xf5fc;',
  'fa-star': '&#xf005;',
  'fa-database': '&#xf1c0;',
  'fa-server': '&#xf233;',
  'fa-chart-pie': '&#xf200;'
};

const styleShapes = {
  'shield': '<path d="M100,20 C140,20 170,30 170,50 C170,110 140,160 100,180 C60,160 30,110 30,50 C30,30 60,20 100,20 Z" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />',
  'star': '<path d="M100,15 L118,52 L158,45 L135,78 L168,100 L135,122 L158,155 L118,148 L100,185 L82,148 L42,155 L65,122 L32,100 L65,78 L42,45 L82,52 Z" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />',
  'seal': '<circle cx="100" cy="100" r="75" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" /><circle cx="100" cy="100" r="68" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" stroke-dasharray="4,4" />',
  'hexagon': '<polygon points="100,20 170,60 170,140 100,180 30,140 30,60" fill="url(#adminGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="3" filter="url(#adminShadow)" />'
};

const colorGradients = {
  'gold-purple': {start: '#7c5cbf', end: '#e3c162'},
  'emerald-teal': {start: '#06b6d4', end: '#10b981'},
  'ruby-orange': {start: '#f97316', end: '#ef4444'},
  'sapphire-blue': {start: '#3b82f6', end: '#2563eb'}
};

function updatePreview() {
  const style = document.getElementById('badgeStyle')?.value;
  const color = document.getElementById('badgeColor')?.value;
  const icon = document.getElementById('iconClass')?.value;
  
  if (!style || !color || !icon) return;
  
  const gradColors = colorGradients[color];
  const shape = styleShapes[style];
  const unicode = iconUnicodeMap[icon] || '&#xf091;';
  
  const svgHtml = `
    <svg viewBox="0 0 200 200" width="120" height="120" style="display:block;margin:0 auto;">
      <defs>
        <linearGradient id="adminGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="${gradColors.start}" />
          <stop offset="100%" stop-color="${gradColors.end}" />
        </linearGradient>
        <filter id="adminShadow" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="4" stdDeviation="4" flood-opacity="0.3"/>
        </filter>
      </defs>
      ${shape}
      <text x="100" y="112" font-family="'Font Awesome 6 Free', 'Font Awesome 5 Free'" font-weight="900" font-size="42" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">
        ${unicode}
      </text>
      <circle cx="100" cy="100" r="50" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="6" />
    </svg>
  `;
  
  const container = document.getElementById('svgPreviewContainer');
  if (container) container.innerHTML = svgHtml;
}

document.getElementById('badgeStyle')?.addEventListener('change', updatePreview);
document.getElementById('badgeColor')?.addEventListener('change', updatePreview);
document.getElementById('iconClass')?.addEventListener('change', updatePreview);
</script>
</body>
</html>
