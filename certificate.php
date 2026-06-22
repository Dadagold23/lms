<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$studentId = (int)($_SESSION['user']['id'] ?? 0);

// Auto-award eligible credentials (certificates and badges)
autoAwardCredentials($studentId, $pdo);

/* ── Courses where student is fully paid AND has passed the exam ── */
$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.title,
        c.price,
        e.paid_amount,
        e.status AS enroll_status,
        COALESCE(MAX(r.percent), 0) AS best_score,
        COALESCE(MAX(CASE WHEN r.status='pass' THEN 1 ELSE 0 END), 0) AS exam_passed,
        cert.certificate_code,
        cert.issued_at
    FROM lms_courses c
    INNER JOIN lms_enrollments e ON e.course_id = c.id AND e.student_id = ?
    LEFT JOIN lms_exams ex ON ex.course_id = c.id AND ex.is_published = 1
    LEFT JOIN lms_exam_results r ON r.exam_id = ex.id AND r.student_id = ?
    LEFT JOIN lms_certificates cert ON cert.course_id = c.id AND cert.student_id = ?
    WHERE (e.paid_amount >= c.price OR e.status = 'paid')
    GROUP BY c.id, c.title, c.price, e.paid_amount, e.status, cert.certificate_code, cert.issued_at
    ORDER BY c.title
");
$stmt->execute([$studentId, $studentId, $studentId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Courses where payment is done but exam not yet passed ── */
$pendingStmt = $pdo->prepare("
    SELECT c.id, c.title,
           COALESCE(MAX(r.percent), 0) AS best_score,
           COALESCE(MAX(CASE WHEN r.status='pass' THEN 1 ELSE 0 END), 0) AS exam_passed
    FROM lms_courses c
    INNER JOIN lms_enrollments e ON e.course_id = c.id AND e.student_id = ?
    LEFT JOIN lms_exams ex ON ex.course_id = c.id AND ex.is_published = 1
    LEFT JOIN lms_exam_results r ON r.exam_id = ex.id AND r.student_id = ?
    WHERE (e.paid_amount >= c.price OR e.status = 'paid')
    GROUP BY c.id, c.title
    HAVING exam_passed = 0
    ORDER BY c.title
");
$pendingStmt->execute([$studentId, $studentId]);
$pendingCourses = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'My Certificates';
$seoDesc    = 'Download your course completion certificates from Grafix@Mirror LMS — Mirror Age Concepts.';
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
  .cert-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .cert-thumb-wrap {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.10);
    background: #0f172a;
    aspect-ratio: 16/10;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
    transition: box-shadow 0.3s ease;
  }
  .cert-thumb-wrap:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,0.32);
  }
  .cert-thumb-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.88;
    transition: opacity 0.3s ease, transform 0.35s ease;
    display: block;
  }
  .cert-thumb-wrap:hover img {
    opacity: 0.5;
    transform: scale(1.04);
  }
  .cert-thumb-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    opacity: 0;
    transition: opacity 0.3s ease;
    background: rgba(10, 10, 30, 0.55);
    backdrop-filter: blur(2px);
  }
  .cert-thumb-wrap:hover .cert-thumb-overlay {
    opacity: 1;
  }
  .cert-thumb-overlay i {
    font-size: 1.8rem;
    color: #e3c162;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.6));
    animation: bounceDown 1.2s ease-in-out infinite;
  }
  .cert-thumb-overlay span {
    font-size: .72rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: .08em;
    text-transform: uppercase;
  }
  @keyframes bounceDown {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(5px); }
  }
  .cert-action-row {
    display: flex;
    gap: .5rem;
    margin-top: auto;
  }
  .cert-action-row .btn-brand {
    flex: 1;
    justify-content: center;
  }
  .cert-share-btn {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: #1877f2;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    text-decoration: none;
    flex-shrink: 0;
    transition: filter .2s;
  }
  .cert-share-btn:hover { filter: brightness(1.15); color:#fff; }
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

<div class="container py-4" style="max-width:900px">

  <h4 class="page-title mb-4"><i class="fa fa-certificate me-2"></i>My Certificates</h4>

  <!-- Badges Promotion Banner -->
  <div class="lms-card mb-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); border: 1px solid rgba(227,193,98,0.25);">
    <div class="d-flex align-items-center justify-content-between p-3 flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <div style="font-size:2.2rem;color:#e3c162"><i class="fa fa-award"></i></div>
        <div>
          <h6 class="text-white mb-1" style="font-weight:700">Course Completion Badges</h6>
          <p class="text-white-50 mb-0" style="font-size:.82rem">You earn shareable digital badges alongside certificates. Display your skills on LinkedIn, Twitter, and other networks!</p>
        </div>
      </div>
      <a href="badges.php" class="btn-brand" style="background:#e3c162; color:#111; font-weight:700; font-size:.82rem; padding:.55rem 1.3rem">
        <i class="fa fa-award me-1"></i> View My Badges
      </a>
    </div>
  </div>

  <!-- Eligible certificates -->
  <?php
  $eligible = array_filter($courses, fn($c) => (int)$c['exam_passed'] === 1);
  ?>
  <?php if (empty($eligible)): ?>
    <div class="lms-alert lms-alert-info mb-4">
      <i class="fa fa-info-circle"></i>
      No certificates available yet. Complete payment and pass the exam for a course to earn your certificate.
    </div>
  <?php else: ?>
    <div class="row g-3 mb-4">
      <?php foreach ($eligible as $c):
        $dlUrl = 'certificate_download.php?course_id=' . (int)$c['id'];
        $fbUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').rtrim(dirname($_SERVER['SCRIPT_NAME']??''),'/\\').'/'.$dlUrl);
      ?>
        <div class="col-md-4">
          <div class="lms-card h-100 d-flex flex-column p-3" style="background:#fff;">

            <!-- Clickable Certificate Thumbnail -->
            <a href="<?= $dlUrl ?>" class="cert-card-link mb-3" title="Download Certificate">
              <div class="cert-thumb-wrap">
                <img src="assets/img/og-certificate.png" alt="<?= e($c['title']) ?> Certificate">
                <div class="cert-thumb-overlay">
                  <i class="fa fa-download"></i>
                  <span>Download</span>
                </div>
              </div>
            </a>

            <!-- Course Title & Meta -->
            <div class="fw-bold mb-1" style="font-size:.93rem;"><?= e($c['title']) ?></div>
            <?php if (!empty($c['issued_at'])): ?>
              <div class="text-muted" style="font-size:.78rem">Issued: <?= e(date('d M Y', strtotime($c['issued_at']))) ?></div>
            <?php endif; ?>
            <div class="text-muted mb-3" style="font-size:.78rem">
              Score: <strong><?= number_format((float)$c['best_score'], 1) ?>%</strong>
            </div>

            <!-- Action Row: Download + Share on Facebook -->
            <div class="cert-action-row mt-auto">
              <a href="<?= $dlUrl ?>" class="btn-brand d-flex align-items-center gap-1">
                <i class="fa fa-download"></i> Download
              </a>
              <a href="<?= $fbUrl ?>" target="_blank" class="cert-share-btn" title="Share on Facebook">
                <i class="fab fa-facebook"></i>
              </a>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Courses paid but exam not yet passed -->
  <?php if (!empty($pendingCourses)): ?>
    <div class="section-title">Complete to Earn Certificate</div>
    <div class="row g-3">
      <?php foreach ($pendingCourses as $c): ?>
        <div class="col-md-4">
          <div class="lms-card h-100">
            <div style="font-weight:700;margin-bottom:.5rem"><?= e($c['title']) ?></div>
            <div class="lms-alert lms-alert-warning" style="font-size:.82rem">
              <i class="fa fa-pen-alt me-1"></i>
              <?php if ((float)$c['best_score'] > 0): ?>
                Best score: <?= number_format((float)$c['best_score'], 1) ?>% — pass 50% to earn certificate.
              <?php else: ?>
                Take and pass the exam to earn your certificate.
              <?php endif; ?>
            </div>
            <a href="exams.php?course_id=<?= (int)$c['id'] ?>" class="btn-outline-brand d-flex align-items-center gap-2">
              <i class="fa fa-pen-alt"></i> Take Exam
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
