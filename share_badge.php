<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

$token = trim((string)($_GET['token'] ?? ''));

$badge = null;
$studentName = '';
$issueDate = '';
$certCode = '';

if ($token !== '') {
    // Fetch student badge details
    $stmt = $pdo->prepare("
        SELECT 
            sb.share_token,
            sb.earned_at,
            b.badge_title,
            b.badge_description,
            b.badge_style,
            b.badge_color,
            b.icon_class,
            c.id AS course_id,
            c.title AS course_title,
            c.slug AS course_slug,
            s.first_name,
            s.last_name
        FROM lms_student_badges sb
        JOIN lms_badges b ON b.course_id = sb.course_id
        JOIN lms_courses c ON c.id = sb.course_id
        JOIN lms_students s ON s.id = sb.student_id
        WHERE sb.share_token = ?
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $badge = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($badge) {
        $studentName = trim(($badge['first_name'] ?? '') . ' ' . ($badge['last_name'] ?? ''));
        $issueDate = date('F d, Y', strtotime((string)$badge['earned_at']));

        // Get certificate code for verification linkage
        $certStmt = $pdo->prepare("
            SELECT certificate_code 
            FROM lms_certificates 
            WHERE student_id = (SELECT student_id FROM lms_student_badges WHERE share_token = ? LIMIT 1) 
              AND course_id = ? 
            LIMIT 1
        ");
        $certStmt->execute([$token, (int)$badge['course_id']]);
        $certCode = (string)$certStmt->fetchColumn();
    }
}

// Select matching OG image based on course slug
$ogImage = 'badge_general.png';
if ($badge) {
    $slug = (string)$badge['course_slug'];
    $potentialImage = 'badge_' . $slug . '.png';
    if (file_exists(__DIR__ . '/assets/img/badges/' . $potentialImage)) {
        $ogImage = $potentialImage;
    }
}

$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$currentUrl = $proto . '://' . $host . $_SERVER['REQUEST_URI'];
$ogImageUrl = $proto . '://' . $host . $uri . '/assets/img/badges/' . $ogImage;

/* Dynamic SVG Renderer */
function renderShareBadgeSvg(array $b, string $studentName, string $dateStr): string {
    $style = $b['badge_style'] ?? 'hexagon';
    $color = $b['badge_color'] ?? 'gold-purple';
    $iconClass = $b['icon_class'] ?? 'fa-award';
    $courseTitle = $b['course_title'] ?? 'Course';
    
    // Gradient definitions
    $gradStart = '#7c5cbf';
    $gradEnd = '#e3c162';
    $primaryColor = '#e3c162';
    
    if ($color === 'emerald-teal') {
        $gradStart = '#06b6d4';
        $gradEnd = '#10b981';
        $primaryColor = '#10b981';
    } elseif ($color === 'ruby-orange') {
        $gradStart = '#f97316';
        $gradEnd = '#ef4444';
        $primaryColor = '#ef4444';
    } elseif ($color === 'sapphire-blue') {
        $gradStart = '#3b82f6';
        $gradEnd = '#2563eb';
        $primaryColor = '#3b82f6';
    }

    $shapeHtml = '';
    if ($style === 'shield') {
        $shapeHtml = '<path d="M100,35 C132,35 156,43 156,59 C156,107 132,147 100,163 C68,147 44,107 44,59 C44,43 68,35 100,35 Z" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.45)" stroke-width="3" filter="url(#svgShadow)" />';
    } elseif ($style === 'star') {
        $shapeHtml = '<path d="M100,30 L114,60 L146,54 L128,80 L154,98 L128,116 L146,142 L114,136 L100,166 L86,136 L54,142 L72,116 L46,98 L72,80 L54,54 L86,60 Z" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.45)" stroke-width="3" filter="url(#svgShadow)" />';
    } elseif ($style === 'seal') {
        $shapeHtml = '<circle cx="100" cy="98" r="62" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.45)" stroke-width="3" filter="url(#svgShadow)" />';
        $shapeHtml .= '<circle cx="100" cy="98" r="56" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" stroke-dasharray="4,4" />';
    } else { // hexagon
        $shapeHtml = '<polygon points="100,30 156,62 156,126 100,158 44,126 44,62" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.45)" stroke-width="3" filter="url(#svgShadow)" />';
    }

    // FontAwesome icon unicode mapping
    $unicode = '&#xf091;'; 
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

    // Shorten text if needed
    $displayTitle = strlen($courseTitle) > 22 ? substr($courseTitle, 0, 19) . '...' : $courseTitle;
    $displayStudent = strlen($studentName) > 22 ? substr($studentName, 0, 19) . '...' : $studentName;

    return <<<SVG
    <svg viewBox="0 0 200 200" style="display:block; width:100%; height:100%;" class="badge-svg-main animate-badge">
      <style>
        .animate-badge {
          animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
          0% { transform: translateY(0px); }
          50% { transform: translateY(-8px); }
          100% { transform: translateY(0px); }
        }
      </style>
      <defs>
        <linearGradient id="svgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="{$gradStart}" />
          <stop offset="100%" stop-color="{$gradEnd}" />
        </linearGradient>
        <filter id="svgShadow" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="6" stdDeviation="6" flood-opacity="0.5"/>
        </filter>
        <!-- Circular wrap text paths -->
        <path id="badgeTopPath" d="M 22,100 A 78,78 0 0,1 178,100" fill="none" />
        <path id="badgeBottomPath" d="M 178,100 A 78,78 0 0,1 22,100" fill="none" />
      </defs>

      <!-- Outer ring -->
      <circle cx="100" cy="100" r="88" fill="rgba(15,23,42,0.98)" stroke="{$primaryColor}" stroke-width="3" />
      <circle cx="100" cy="100" r="94" fill="none" stroke="{$primaryColor}" stroke-width="0.8" opacity="0.4" />

      <!-- Curved text top -->
      <text font-family="'Inter', sans-serif" font-size="7" font-weight="800" fill="rgba(255,255,255,0.9)" letter-spacing="1.1">
        <textPath href="#badgeTopPath" startOffset="50%" text-anchor="middle">GRAFIX@MIRROR CERTIFIED</textPath>
      </text>

      <!-- Curved text bottom -->
      <text font-family="'Inter', sans-serif" font-size="7" font-weight="800" fill="{$primaryColor}" letter-spacing="1.3">
        <textPath href="#badgeBottomPath" startOffset="50%" text-anchor="middle">GRADUATE</textPath>
      </text>

      <!-- Main Shape -->
      {$shapeHtml}

      <!-- Center Icon -->
      <text x="100" y="105" font-family="'Font Awesome 6 Free', 'Font Awesome 5 Free'" font-weight="900" font-size="34" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">
        {$unicode}
      </text>

      <!-- Course inside shape -->
      <text x="100" y="128" font-family="'Inter', sans-serif" font-size="6.5" font-weight="800" fill="#ffffff" text-anchor="middle">
        {$displayTitle}
      </text>
      
      <!-- Student inside shape -->
      <text x="100" y="138" font-family="'Inter', sans-serif" font-size="5" font-weight="600" fill="rgba(255,255,255,0.7)" text-anchor="middle">
        {$displayStudent}
      </text>
    </svg>
SVG;
}
?>
<!doctype html>
<html lang="en">
<head>
<?php if ($badge): ?>
  <title>Verified Badge: <?= e($badge['badge_title']) ?> — <?= e($studentName) ?></title>
  <meta name="description" content="Official course completion credential issued by Mirror Age Concepts / Grafix@Mirror LMS.">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e($currentUrl) ?>">
  <meta property="og:title" content="Verified Achievement: <?= e($badge['badge_title']) ?> Badge by <?= e($studentName) ?> 🏆">
  <meta property="og:description" content="Certified credential earned on Grafix@Mirror LMS for completing the course '<?= e($badge['course_title']) ?>'.">
  <meta property="og:image" content="<?= e($ogImageUrl) ?>">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?= e($currentUrl) ?>">
  <meta name="twitter:title" content="Verified Achievement: <?= e($badge['badge_title']) ?> Badge by <?= e($studentName) ?> 🏆">
  <meta name="twitter:description" content="Certified credential earned on Grafix@Mirror LMS for completing the course '<?= e($badge['course_title']) ?>'.">
  <meta name="twitter:image" content="<?= e($ogImageUrl) ?>">
<?php else: ?>
  <title>Badge Credentials | Grafix@Mirror LMS</title>
<?php endif; ?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
  :root {
    --bg-dark: #090d16;
    --card-bg: rgba(30, 41, 59, 0.45);
    --border-color: rgba(255, 255, 255, 0.08);
    --brand: #e3c162;
  }
  body {
    background-color: var(--bg-dark);
    color: #f8fafc;
    font-family: 'Inter', system-ui, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* Ambient glow backgrounds */
    background-image: 
      radial-gradient(ellipse 50% 50% at 80% 10%, rgba(124, 92, 191, 0.15) 0%, transparent 80%),
      radial-gradient(ellipse 50% 50% at 20% 90%, rgba(16, 185, 129, 0.1) 0%, transparent 80%);
    background-repeat: no-repeat;
  }
  .glass-card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  }
  .badge-holder-box {
    max-width: 250px;
    margin: 0 auto 1.5rem;
    filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.6));
  }
  .verify-pill {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
    font-size: .8rem;
    font-weight: 700;
    padding: .35rem .9rem;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
  }
  .meta-label {
    font-size: .78rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .2rem;
  }
  .meta-value {
    font-size: .95rem;
    font-weight: 600;
    color: #f1f5f9;
  }
  .btn-action {
    border-radius: 10px;
    padding: .65rem 1.5rem;
    font-weight: 600;
    font-size: .9rem;
    transition: transform .2s, box-shadow .2s;
  }
  .btn-action:hover {
    transform: translateY(-2px);
  }
  .btn-brand-primary {
    background-color: var(--brand);
    color: #0f172a;
    border: none;
    box-shadow: 0 4px 14px rgba(227, 193, 98, 0.3);
  }
  .btn-brand-primary:hover {
    background-color: #d4b050;
    color: #0f172a;
    box-shadow: 0 6px 20px rgba(227, 193, 98, 0.45);
  }
  .btn-brand-outline {
    background: transparent;
    color: #f8fafc;
    border: 1px solid rgba(255,255,255,0.15);
  }
  .btn-brand-outline:hover {
    background: rgba(255,255,255,0.05);
    color: #fff;
    border-color: rgba(255,255,255,0.3);
  }
  .btn-facebook-primary {
    background-color: #1877f2;
    color: #ffffff !important;
    border: none;
    box-shadow: 0 4px 14px rgba(24, 119, 242, 0.35);
  }
  .btn-facebook-primary:hover {
    background-color: #166fe5;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(24, 119, 242, 0.5);
  }
  .btn-secondary-option {
    background: rgba(255, 255, 255, 0.05);
    color: #f8fafc !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .btn-secondary-option:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff !important;
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
  }
  .footer-brand {
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 1.5rem 0;
    text-align: center;
    font-size: .8rem;
    color: #64748b;
  }
  .social-icons-row a {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    text-decoration: none;
    transition: all 0.2s;
  }
  .social-icons-row a:hover {
    background: var(--brand);
    color: #0f172a;
    transform: translateY(-2px);
  }
</style>
</head>
<body>

<!-- TOP BRAND BAR -->
<header class="container py-3">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div style="width:30px;height:30px;background:var(--brand);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#0f172a;font-weight:800;font-size:.85rem">G</div>
      <span class="fw-bold" style="font-size:1rem;color:#f8fafc;letter-spacing:.05em">Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </div>
    <span class="text-muted small">Official Credential Validation</span>
  </div>
</header>

<main class="container py-5 my-auto" style="max-width: 680px;">
  <?php if ($badge): ?>
    
    <div class="glass-card p-4 p-md-5 text-center">
      
      <!-- Verified Badge Tag -->
      <div class="mb-4">
        <span class="verify-pill">
          <i class="fa fa-circle-check"></i> Verified Credential
        </span>
      </div>

      <!-- Badge SVG graphic container -->
      <div class="badge-holder-box">
        <?= renderShareBadgeSvg($badge, $studentName, $issueDate) ?>
      </div>

      <!-- Credentials Text -->
      <h3 class="fw-bold mb-1" style="color:var(--brand);"><?= e($badge['badge_title']) ?></h3>
      <p class="text-white-50 mb-4" style="font-size:.9rem; max-width: 480px; margin:0 auto;">
        <?= e($badge['badge_description'] ?: 'Awarded for successful completion of all curriculum benchmarks, payment requirements, and examination targets.') ?>
      </p>

      <hr style="border-color: rgba(255,255,255,0.08);" class="my-4">

      <!-- Student & Verification Grid -->
      <div class="row g-3 text-start mb-4">
        <div class="col-6 col-sm-6">
          <div class="meta-label">Recipient</div>
          <div class="meta-value"><?= e($studentName) ?></div>
        </div>
        <div class="col-6 col-sm-6">
          <div class="meta-label">Course</div>
          <div class="meta-value"><?= e($badge['course_title']) ?></div>
        </div>
        <div class="col-6 col-sm-6">
          <div class="meta-label">Issued On</div>
          <div class="meta-value"><?= e($issueDate) ?></div>
        </div>
        <div class="col-6 col-sm-6">
          <div class="meta-label">Credential ID</div>
          <div class="meta-value" style="font-family: monospace; font-size:.85rem;"><?= e(substr($token, 0, 16)) ?>...</div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <?php if ($certCode !== ''): ?>
          <a href="certificate_verify.php?code=<?= urlencode($certCode) ?>" class="btn-action btn-brand-primary text-decoration-none text-center">
            <i class="fa fa-certificate me-1"></i> Verify Official Certificate
          </a>
        <?php endif; ?>
        <a href="<?= e(courseUrl(['id' => (int)$badge['course_id'], 'slug' => (string)($badge['course_slug'] ?? '')])) ?>" class="btn-action btn-brand-outline text-decoration-none text-center">
          <i class="fa fa-graduation-cap me-1"></i> Explore This Course
        </a>
      </div>

      <!-- Share block with Facebook as Primary -->
      <div class="mt-4 pt-3 text-start p-3 rounded-3 w-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
        <h6 class="text-white fw-bold mb-3" style="font-size:.85rem; letter-spacing:0.02em;"><i class="fa fa-share-nodes text-warning me-1"></i> SHARE THIS BADGE:</h6>
        
        <!-- Primary Share Target (Facebook) -->
        <div class="d-grid mb-3">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-action btn-facebook-primary text-center">
            <i class="fab fa-facebook"></i> Share on Facebook
          </a>
        </div>

        <!-- Secondary Share Options -->
        <div class="row g-2" id="shareOptionsRow">
          <div class="col-6 col-sm-4 share-opt-col">
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-action btn-secondary-option w-100 py-2 text-center" style="font-size:.78rem">
              <i class="fab fa-linkedin-in"></i> LinkedIn
            </a>
          </div>
          <div class="col-6 col-sm-4 share-opt-col">
            <?php 
              $shareText = rawurlencode("I am proud to share that I have earned my '{$badge['badge_title']}' achievement badge on Grafix@Mirror LMS! Verify here: ");
            ?>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= $shareText ?>" target="_blank" class="btn-action btn-secondary-option w-100 py-2 text-center" style="font-size:.78rem">
              <i class="fab fa-x-twitter"></i> Twitter/X
            </a>
          </div>
          <div class="col-12 col-sm-4 share-opt-col" id="copyLinkCol">
            <button onclick="navigator.clipboard.writeText('<?= $currentUrl ?>').then(() => alert('Verification link copied!'))" class="btn-action btn-secondary-option w-100 py-2 text-center" style="font-size:.78rem; cursor:pointer;">
              <i class="fa fa-copy"></i> Copy Link
            </button>
          </div>
          <div class="col-6 col-sm-4 d-none share-opt-col" id="webShareCol">
            <button id="webShareBtn" class="btn-action btn-secondary-option w-100 py-2 text-center" style="font-size:.78rem; cursor:pointer; background:rgba(168, 85, 247, 0.1); border-color: rgba(168, 85, 247, 0.3); color: #c084fc !important;">
              <i class="fa fa-share-nodes"></i> Share via App
            </button>
          </div>
        </div>
      </div>

    </div>

  <?php else: ?>
    
    <div class="glass-card p-5 text-center">
      <i class="fa fa-triangle-exclamation fa-4x text-danger mb-4"></i>
      <h4 class="fw-bold mb-2">Credential Not Found</h4>
      <p class="text-muted mb-4">The badge verification token provided is either invalid, expired, or has been revoked. Please check the URL and try again.</p>
      <a href="index.php" class="btn-action btn-brand-primary text-decoration-none">
        <i class="fa fa-home me-1"></i> Go to Homepage
      </a>
    </div>

  <?php endif; ?>
</main>

<footer class="footer-brand">
  <div class="container">
    <div>Mirror Age Concepts &copy; <?= date('Y') ?> &nbsp;|&nbsp; Grafix@Mirror Learning Portal</div>
    <div class="small mt-1 text-muted">All credentials on this portal are digitally verified and validated against real LMS enrollment records.</div>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (navigator.share) {
    const webShareCol = document.getElementById('webShareCol');
    const cols = document.querySelectorAll('.share-opt-col');
    if (webShareCol) {
      webShareCol.classList.remove('d-none');
      cols.forEach(c => {
        c.classList.remove('col-sm-4', 'col-12');
        c.classList.add('col-6', 'col-sm-3');
      });
      document.getElementById('webShareBtn').addEventListener('click', function() {
        navigator.share({
          title: document.title,
          text: `Check out my verified certification badge on Grafix@Mirror LMS!`,
          url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
      });
    }
  }
});
</script>
</body>
</html>
