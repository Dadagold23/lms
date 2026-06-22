<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

/* ========================================================
   1. AUTO-AWARD SYSTEM: Grant eligible credentials
   ======================================================== */
autoAwardCredentials($studentId, $pdo);

/* ========================================================
   2. FETCH ALL COURSE BADGES AND EARNED STATUS
   ======================================================== */
$badgesStmt = $pdo->prepare("
    SELECT 
        b.id AS badge_id,
        b.badge_title,
        b.badge_description,
        b.badge_style,
        b.badge_color,
        b.icon_class,
        c.id AS course_id,
        c.title AS course_title,
        c.level AS course_level,
        sb.share_token,
        sb.earned_at
    FROM lms_badges b
    JOIN lms_courses c ON c.id = b.course_id
    LEFT JOIN lms_student_badges sb ON sb.course_id = c.id AND sb.student_id = ?
    ORDER BY (sb.earned_at IS NULL) ASC, c.title ASC
");
$badgesStmt->execute([$studentId]);
$badges = $badgesStmt->fetchAll(PDO::FETCH_ASSOC);

// Count earned badges
$earnedCount = array_reduce($badges, fn($carry, $b) => $carry + ($b['share_token'] !== null ? 1 : 0), 0);

/* ========================================================
   3. SECURE STUDENT INFO FOR SVG RENDER
   ======================================================== */
$stInfo = $pdo->prepare("SELECT first_name, last_name FROM lms_students WHERE id = ? LIMIT 1");
$stInfo->execute([$studentId]);
$studentRow = $stInfo->fetch(PDO::FETCH_ASSOC) ?: [];
$studentName = trim(($studentRow['first_name'] ?? '') . ' ' . ($studentRow['last_name'] ?? ''));

/* SVG Badge Generation Helper */
function generateDynamicBadgeSvg(array $b, string $studentName): string {
    $style = $b['badge_style'] ?? 'hexagon';
    $color = $b['badge_color'] ?? 'gold-purple';
    $iconClass = $b['icon_class'] ?? 'fa-award';
    $courseTitle = $b['course_title'] ?? 'Course';
    
    // Gradient config
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
        $shapeHtml = '<path d="M100,35 C132,35 156,43 156,59 C156,107 132,147 100,163 C68,147 44,107 44,59 C44,43 68,35 100,35 Z" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="2.5" filter="url(#svgShadow)" />';
    } elseif ($style === 'star') {
        $shapeHtml = '<path d="M100,30 L114,60 L146,54 L128,80 L154,98 L128,116 L146,142 L114,136 L100,166 L86,136 L54,142 L72,116 L46,98 L72,80 L54,54 L86,60 Z" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="2.5" filter="url(#svgShadow)" />';
    } elseif ($style === 'seal') {
        $shapeHtml = '<circle cx="100" cy="98" r="62" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="2.5" filter="url(#svgShadow)" />';
        $shapeHtml .= '<circle cx="100" cy="98" r="56" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.2" stroke-dasharray="3,3" />';
    } else { // hexagon
        $shapeHtml = '<polygon points="100,30 156,62 156,126 100,158 44,126 44,62" fill="url(#svgGrad)" stroke="rgba(255,255,255,0.4)" stroke-width="2.5" filter="url(#svgShadow)" />';
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

    // Shorten course title if too long
    $displayTitle = strlen($courseTitle) > 22 ? substr($courseTitle, 0, 19) . '...' : $courseTitle;

    return <<<SVG
    <svg viewBox="0 0 200 200" style="display:block; width:100%; height:100%;" class="badge-svg-raw">
      <defs>
        <linearGradient id="svgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="{$gradStart}" />
          <stop offset="100%" stop-color="{$gradEnd}" />
        </linearGradient>
        <filter id="svgShadow" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="5" stdDeviation="5" flood-opacity="0.4"/>
        </filter>
        <!-- Text paths for circular wrap -->
        <path id="badgeTopPath" d="M 24,100 A 76,76 0 0,1 176,100" fill="none" />
        <path id="badgeBottomPath" d="M 176,100 A 76,76 0 0,1 24,100" fill="none" />
      </defs>

      <!-- Outer ring -->
      <circle cx="100" cy="100" r="86" fill="rgba(17,24,39,0.95)" stroke="{$primaryColor}" stroke-width="2.5" />
      <circle cx="100" cy="100" r="92" fill="none" stroke="{$primaryColor}" stroke-width="0.8" opacity="0.5" />

      <!-- Curved text top -->
      <text font-family="'Inter', system-ui, sans-serif" font-size="7" font-weight="700" fill="rgba(255,255,255,0.85)" letter-spacing="1">
        <textPath href="#badgeTopPath" startOffset="50%" text-anchor="middle">GRAFIX@MIRROR CERTIFIED</textPath>
      </text>

      <!-- Curved text bottom -->
      <text font-family="'Inter', system-ui, sans-serif" font-size="7" font-weight="700" fill="{$primaryColor}" letter-spacing="1.2">
        <textPath href="#badgeBottomPath" startOffset="50%" text-anchor="middle">GRADUATE</textPath>
      </text>

      <!-- Main Badge Shape -->
      {$shapeHtml}

      <!-- Center Icon -->
      <text x="100" y="108" font-family="'Font Awesome 6 Free', 'Font Awesome 5 Free'" font-weight="900" font-size="34" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">
        {$unicode}
      </text>

      <!-- Course & Student Small Metadata centered below icon inside shape -->
      <text x="100" y="132" font-family="'Inter', system-ui, sans-serif" font-size="6" font-weight="800" fill="#ffffff" opacity="0.9" text-anchor="middle">
        {$displayTitle}
      </text>
    </svg>
SVG;
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'My Badges';
$seoDesc    = 'View and share your course completion badges from Grafix@Mirror LMS.';
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
  body {
    background: var(--surface);
  }
  .badge-grid-item {
    transition: transform .25s ease, box-shadow .25s ease;
  }
  .badge-grid-item:hover {
    transform: translateY(-5px);
  }
  .badge-container-svg {
    width: 140px;
    height: 140px;
    margin: 0 auto 1.25rem;
    position: relative;
    cursor: pointer;
    transition: filter 0.3s ease;
  }
  .badge-container-svg:hover .badge-svg-raw {
    filter: drop-shadow(0 0 15px rgba(227,193,98,0.4));
    animation: badgePulse 2s infinite ease-in-out;
  }
  @keyframes badgePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.03); }
    100% { transform: scale(1); }
  }
  .badge-locked {
    filter: grayscale(100%) opacity(35%);
  }
  /* Share Drawer Drawer-like modal */
  .share-modal {
    position: fixed;
    bottom: -100%;
    left: 0;
    width: 100%;
    background: #0f172a;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px 24px 0 0;
    z-index: 1050;
    transition: bottom 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 -10px 40px rgba(0,0,0,0.6);
  }
  .share-modal.open {
    bottom: 0;
  }
  .share-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1040;
    display: none;
  }
  .share-backdrop.open {
    display: block;
  }
  .social-share-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
    text-decoration: none;
    transition: transform 0.2s, filter 0.2s;
  }
  .social-share-btn:hover {
    transform: scale(1.1);
    color: #fff;
    filter: brightness(1.1);
  }
  .btn-linkedin { background: #0077b5; }
  .btn-twitter { background: #1da1f2; }
  .btn-facebook { background: #1877f2; }
  .btn-whatsapp { background: #25d366; }
  .btn-copylink { background: #475569; }
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
      <a href="certificate.php" class="btn-ghost"><i class="fa fa-certificate me-1"></i>Certificates</a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width:1000px">
  
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h4 class="page-title mb-1"><i class="fa fa-award me-2 text-warning"></i>My Badges</h4>
      <p class="text-muted mb-0">Complete your courses and pass final exams to unlock badges to display on social media.</p>
    </div>
    <div class="bg-dark text-white px-3 py-2 rounded-3 d-flex align-items-center gap-2 border border-secondary" style="font-size:.88rem">
      <i class="fa fa-award text-warning"></i>
      <span>Earned: <strong><?= $earnedCount ?></strong> / <?= count($badges) ?></span>
    </div>
  </div>

  <?php if ($earnedCount === 0): ?>
    <div class="lms-alert lms-alert-info mb-4">
      <i class="fa fa-info-circle me-1"></i>
      You haven't earned any badges yet. Once you complete course payment and pass the final exam (50%+), your badge will unlock here!
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <?php foreach ($badges as $b):
      $isEarned = ($b['share_token'] !== null);
      $themeClass = 'border-secondary';
      if ($isEarned) {
          if ($b['badge_color'] === 'gold-purple') $themeClass = 'border-warning';
          elseif ($b['badge_color'] === 'emerald-teal') $themeClass = 'border-success';
          elseif ($b['badge_color'] === 'ruby-orange') $themeClass = 'border-danger';
          elseif ($b['badge_color'] === 'sapphire-blue') $themeClass = 'border-primary';
      }
    ?>
      <div class="col-sm-6 col-md-4">
        <div class="lms-card h-100 text-center badge-grid-item d-flex flex-column justify-content-between p-4 <?= $isEarned ? 'border-2 ' . $themeClass : 'opacity-75' ?>" style="background:#fff;">
          <div>
            <!-- Visual Badge Representation -->
            <div class="badge-container-svg <?= !$isEarned ? 'badge-locked' : '' ?>" title="<?= $isEarned ? 'Earned!' : 'Locked' ?>">
              <?= generateDynamicBadgeSvg($b, $studentName) ?>
              <?php if (!$isEarned): ?>
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:36px; height:36px; background:rgba(17,24,39,0.85); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.9rem; border:1.5px solid rgba(255,255,255,0.2)">
                  <i class="fa fa-lock"></i>
                </div>
              <?php endif; ?>
            </div>

            <!-- Badge Information -->
            <h5 class="fw-bold mb-1" style="font-size:1.1rem;"><?= e($b['badge_title']) ?></h5>
            <span class="badge bg-secondary mb-3" style="font-size:.7rem;"><?= e(ucfirst($b['course_level'])) ?></span>
            
            <p class="text-muted text-start mb-4" style="font-size:.82rem; min-height:50px;">
              <?= e($b['badge_description'] ?: 'Complete this course and pass the exam to earn the badge.') ?>
            </p>
          </div>

          <div>
            <?php if ($isEarned): ?>
              <?php 
                $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $base = $proto.'://'.($_SERVER['HTTP_HOST'] ?? 'localhost').rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
                $shareUrl = $base . '/share_badge.php?token=' . urlencode((string)$b['share_token']);
              ?>
              <div class="text-success small mb-3 fw-semibold">
                <i class="fa fa-check-circle me-1"></i>Earned <?= date('M d, Y', strtotime((string)$b['earned_at'])) ?>
              </div>
              <button class="btn-brand w-100 justify-content-center d-flex align-items-center gap-2" 
                      onclick="openShareDrawer('<?= e($b['badge_title']) ?>', '<?= e($shareUrl) ?>')">
                <i class="fa fa-share-nodes"></i> Share / Verify Badge
              </button>
            <?php else: ?>
              <div class="text-muted small mb-3">
                <i class="fa fa-lock me-1"></i>Locked
              </div>
              <a href="course.php?id=<?= (int)$b['course_id'] ?>" class="btn-outline-brand w-100 justify-content-center d-flex align-items-center gap-2">
                Go to Course <i class="fa fa-arrow-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- SOCIAL SHARING DRAWER MODAL -->
<div class="share-backdrop" id="shareBackdrop" onclick="closeShareDrawer()"></div>
<div class="share-modal" id="shareDrawer">
  <div class="container py-4 text-center" style="max-width:550px">
    <div style="width:40px; height:4px; background:rgba(255,255,255,0.2); border-radius:99px; margin:0 auto 1.25rem;"></div>
    
    <h5 class="text-white fw-bold mb-1" id="shareTitle">Share Badge</h5>
    <p class="text-muted small mb-4">Validate your credentials and share your achievement with your network.</p>
    
    <!-- Link Input -->
    <div class="input-group mb-4">
      <input type="text" class="form-control bg-dark border-secondary text-white text-truncate" id="shareUrlInput" readonly>
      <button class="btn btn-primary" onclick="copyShareLink()">
        <i class="fa fa-copy me-1"></i>Copy
      </button>
    </div>

    <!-- Primary share target: Facebook -->
    <div class="d-grid mb-3">
      <a href="#" class="btn py-2.5 d-flex align-items-center justify-content-center gap-2 fw-bold text-white" id="shareFacebook" target="_blank" style="background:#1877f2; border:none; box-shadow:0 4px 12px rgba(24,119,242,0.3); border-radius:8px; text-decoration:none;">
        <i class="fab fa-facebook"></i> Share on Facebook
      </a>
    </div>

    <div style="font-size:.78rem; color:rgba(255,255,255,0.4); margin-bottom:.75rem">Other Sharing Options:</div>

    <!-- Secondary Share Channels -->
    <div class="d-flex justify-content-center gap-3 mb-3">
      <a href="#" class="social-share-btn btn-linkedin" id="shareLinkedIn" target="_blank" title="Share on LinkedIn">
        <i class="fab fa-linkedin-in"></i>
      </a>
      <a href="#" class="social-share-btn btn-twitter" id="shareTwitter" target="_blank" title="Share on Twitter/X">
        <i class="fab fa-x-twitter"></i>
      </a>
      <a href="#" class="social-share-btn btn-whatsapp" id="shareWhatsApp" target="_blank" title="Share via WhatsApp">
        <i class="fab fa-whatsapp"></i>
      </a>
      <button class="social-share-btn" id="shareWebShare" title="Share via Device" style="background: #a855f7; border:none; display:none; cursor:pointer;">
        <i class="fa fa-share-nodes"></i>
      </button>
    </div>
    
    <div class="mt-4">
      <button class="btn btn-outline-secondary w-100 text-white border-secondary" onclick="closeShareDrawer()">Close</button>
    </div>
  </div>
</div>

<script>
function openShareDrawer(badgeTitle, shareUrl) {
  document.getElementById('shareTitle').textContent = `Share achievement: ${badgeTitle}`;
  document.getElementById('shareUrlInput').value = shareUrl;

  // Configure URLs
  const encodedUrl = encodeURIComponent(shareUrl);
  const textMsg = encodeURIComponent(`I am excited to share that I have earned my '${badgeTitle}' certification badge from Grafix@Mirror LMS! Check it out here: `);

  document.getElementById('shareLinkedIn').href = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
  document.getElementById('shareTwitter').href = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${textMsg}`;
  document.getElementById('shareFacebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
  document.getElementById('shareWhatsApp').href = `https://api.whatsapp.com/send?text=${textMsg}%20${encodedUrl}`;

  // Configure Web Share if supported
  if (navigator.share) {
    const wsBtn = document.getElementById('shareWebShare');
    if (wsBtn) {
      wsBtn.style.display = 'flex';
      wsBtn.onclick = function() {
        navigator.share({
          title: `Share achievement: ${badgeTitle}`,
          text: `I am excited to share that I have earned my '${badgeTitle}' certification badge from Grafix@Mirror LMS!`,
          url: shareUrl
        }).catch(err => console.log("Web Share cancelled/failed", err));
      };
    }
  } else {
    const wsBtn = document.getElementById('shareWebShare');
    if (wsBtn) wsBtn.style.display = 'none';
  }

  // Open drawer
  document.getElementById('shareBackdrop').classList.add('open');
  document.getElementById('shareDrawer').classList.add('open');
}

function closeShareDrawer() {
  document.getElementById('shareBackdrop').classList.remove('open');
  document.getElementById('shareDrawer').classList.remove('open');
}

function copyShareLink() {
  const urlInput = document.getElementById('shareUrlInput');
  urlInput.select();
  urlInput.setSelectionRange(0, 99999); // for mobile devices
  
  navigator.clipboard.writeText(urlInput.value).then(() => {
    alert("Badge verification link copied to clipboard!");
  }).catch(err => {
    console.error("Copy failed:", err);
  });
}
</script>
</body>
</html>
