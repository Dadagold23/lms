<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();
$admin = $_SESSION['admin'];

$flash = $_SESSION['admin_partner_flash'] ?? null;
unset($_SESSION['admin_partner_flash']);

/* ======================
   HANDLE POST ACTIONS
 ====================== */
if (isPost()) {
    verifyCsrf($_POST['_csrf'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $partnerId = (int)($_POST['partner_id'] ?? 0);
        $newStatus = trim((string)($_POST['status'] ?? 'pending'));
        if ($partnerId > 0 && in_array($newStatus, ['approved', 'pending', 'suspended'], true)) {
            $stmt = $pdo->prepare("UPDATE lms_affiliate_partners SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $partnerId]);
            $_SESSION['admin_partner_flash'] = "Partner status updated to '{$newStatus}' successfully.";
        }
        redirect('admin_partners.php');
    }

    if ($action === 'create_campaign') {
        $partnerId       = (int)($_POST['partner_id'] ?? 0);
        $schoolName      = trim((string)($_POST['school_name'] ?? ''));
        $programTitle    = trim((string)($_POST['program_title'] ?? ''));
        $courseId        = (int)($_POST['course_id'] ?? 0);
        $candidatesCount = (int)($_POST['candidates_count'] ?? 0);
        $discountRate    = (int)($_POST['discount_rate'] ?? 15);

        if ($partnerId <= 0 || $schoolName === '' || $candidatesCount <= 0) {
            $_SESSION['admin_partner_flash'] = "Partner, school/campaign name, and target candidate count are required.";
            redirect('admin_partners.php');
        }

        $stmt = $pdo->prepare("
            INSERT INTO lms_affiliate_campaigns (partner_id, school_name, program_title, course_id, candidates_count, discount_rate, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $partnerId,
            $schoolName,
            $programTitle ?: null,
            $courseId ?: null,
            $candidatesCount,
            $discountRate
        ]);

        $_SESSION['admin_partner_flash'] = "New affiliate campaign created successfully.";
        redirect('admin_partners.php');
    }
}

/* ======================
   LOAD DATA
 ====================== */
// Load all partners with their referral counts
$partners = $pdo->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM lms_affiliate_referrals WHERE partner_id = p.id) AS referral_count,
           (SELECT COUNT(*) FROM lms_affiliate_campaigns WHERE partner_id = p.id) AS campaign_count
    FROM lms_affiliate_partners p 
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Load all referrals with their course & partner details
$referrals = $pdo->query("
    SELECT r.*, p.name AS partner_name, p.partner_type, c.title AS course_title,
           s.id AS student_id, e.status AS enrollment_status, e.paid_amount AS enrollment_paid
    FROM lms_affiliate_referrals r
    JOIN lms_affiliate_partners p ON p.id = r.partner_id
    LEFT JOIN lms_courses c ON c.id = r.course_id
    LEFT JOIN lms_students s ON s.email = r.pupil_email
    LEFT JOIN lms_enrollments e ON e.student_id = s.id AND e.course_id = COALESCE(r.affiliate_course_id, r.course_id)
    ORDER BY r.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Load active courses for dropdown
$courses = $pdo->query("SELECT id, title FROM lms_courses WHERE is_active = 1 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Manage Affiliate Partnerships';
$seoDesc    = 'Manage campaigns, partners, and affiliate referrals at Grafix@Mirror LMS.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  body { background: #f8fafc; font-family: 'Inter', sans-serif; }
  .table-responsive { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; }
  .table { margin-bottom: 0; }
  .table th { background: #f1f5f9; color: #475569; font-weight: 600; font-size: .85rem; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
  .table td { padding: 14px 16px; vertical-align: middle; font-size: .88rem; color: #334155; }
  .badge-status { font-weight: 600; font-size: .75rem; padding: .25rem .6rem; border-radius: 99px; }
  .badge-approved { background: #dcfce7; color: #15803d; }
  .badge-pending { background: #fef3c7; color: #d97706; }
  .badge-suspended { background: #fee2e2; color: #b91c1c; }
  .tab-btn { font-weight: 600; border: none; background: none; padding: .75rem 1.25rem; border-bottom: 2px solid transparent; color: #64748b; transition: all 0.2s; }
  .tab-btn.active { border-bottom-color: #4f46e5; color: #4f46e5; }
</style>
</head>
<body>

<nav class="lms-nav lms-nav-admin">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
      <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:.82rem;color:#94a3b8">
        <i class="fa fa-user-shield me-1"></i><?= e($admin['full_name'] ?? 'Admin') ?>
      </span>
      <a href="admin_logout.php" style="font-size:.82rem;color:#f87171;font-weight:600"><i class="fa fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="lms-layout">

  <!-- SIDEBAR -->
  <aside class="lms-sidebar">
    <div class="nav-section">Overview</div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="analytics.php" class="nav-link"><i class="fa fa-chart-bar"></i> Analytics</a>
    <div class="nav-section">Management</div>
    <a href="admin_courses.php" class="nav-link"><i class="fa fa-book"></i> Courses</a>
    <a href="admin_instructors.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Instructors</a>
    <a href="admin_partners.php" class="nav-link active"><i class="fa fa-handshake"></i> Affiliate/Partners</a>
    <a href="admin_enrollment_assignments.php" class="nav-link"><i class="fa fa-user-tag"></i> Assignments</a>
    <a href="admin_student_performance.php" class="nav-link"><i class="fa fa-graduation-cap"></i> Student Performance</a>
    <a href="cert_settings.php" class="nav-link"><i class="fa fa-certificate"></i> Certificate</a>
    <a href="admin_badges.php" class="nav-link"><i class="fa fa-award"></i> Badges</a>
    <a href="admin_payment_approval.php" class="nav-link"><i class="fa fa-credit-card"></i> Payments</a>
    <a href="finance_report.php" class="nav-link"><i class="fa fa-file-invoice-dollar"></i> Finance Report</a>
    <a href="bulk_import.php" class="nav-link"><i class="fa fa-upload"></i> Bulk Import</a>
    <div class="nav-section">Tools</div>
    <a href="admin_live_sessions.php" class="nav-link"><i class="fa fa-video"></i> Live Sessions</a>
    <a href="admin_switch.php" class="nav-link"><i class="fa fa-exchange-alt"></i> Switch User</a>
    <a href="reminders.php" class="nav-link"><i class="fa fa-bell"></i> Reminders</a>
    <a href="whatsapp_messages.php" class="nav-link"><i class="fab fa-whatsapp"></i> Messages</a>
    <a href="create_admin.php" class="nav-link"><i class="fa fa-user-plus"></i> Create Admin</a>
    <a href="admin_change_password.php" class="nav-link"><i class="fa fa-key"></i> Change Password</a>
    <div class="nav-section">Portal</div>
    <a href="admin_logout.php" class="nav-link" style="color:var(--danger)"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="lms-main">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="page-title mb-1" style="font-size: 1.6rem;">Affiliate & Partnerships</h1>
        <p class="text-muted small mb-0">Manage registered marketing partners, custom discount campaigns, and referred candidate enrollments.</p>
      </div>
      <button class="btn btn-primary fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#createCampaignModal" style="border-radius: 8px;">
        <i class="fa fa-plus me-1"></i> Create Campaign
      </button>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-info border-0 shadow-sm p-3 mb-4" style="border-radius: 10px;">
        <i class="fa fa-info-circle me-1"></i> <?= e($flash) ?>
      </div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="d-flex border-bottom mb-4">
      <button class="tab-btn active" onclick="switchTab('partnersTab', this)">Partners (<?= count($partners) ?>)</button>
      <button class="tab-btn" onclick="switchTab('referralsTab', this)">Referred Students (<?= count($referrals) ?>)</button>
    </div>

    <!-- PARTNERS TAB -->
    <div id="partnersTab" class="tab-content">
      <div class="table-responsive shadow-sm">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Partner Details</th>
              <th>Track Type</th>
              <th>Stats</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($partners)): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No affiliate partners registered yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($partners as $p): ?>
                <tr>
                  <td>
                    <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                    <div class="text-muted small"><?= e($p['email']) ?></div>
                    <div class="text-muted small"><?= e($p['phone']) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-secondary text-capitalize"><?= e($p['partner_type']) ?></span>
                  </td>
                  <td>
                    <div class="small">Campaigns: <strong><?= (int)$p['campaign_count'] ?></strong></div>
                    <div class="small">Referrals: <strong><?= (int)$p['referral_count'] ?></strong></div>
                  </td>
                  <td>
                    <span class="badge-status badge-<?= e($p['status']) ?> text-capitalize">
                      <?= e($p['status']) ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <form method="post" class="d-inline">
                      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="partner_id" value="<?= (int)$p['id'] ?>">
                      
                      <?php if ($p['status'] === 'approved'): ?>
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="btn btn-outline-warning btn-sm fw-semibold" style="border-radius:6px;">Deactivate</button>
                      <?php else: ?>
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success btn-sm fw-semibold" style="border-radius:6px;">Approve</button>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- REFERRALS TAB -->
    <div id="referralsTab" class="tab-content d-none">
      <div class="table-responsive shadow-sm">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Pupil Info</th>
              <th>Course</th>
              <th>Referred By</th>
              <th>Status</th>
              <th>Paid Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($referrals)): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No referred students found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($referrals as $ref): ?>
                <tr>
                  <td>
                    <div class="fw-bold text-dark"><?= e($ref['pupil_name']) ?></div>
                    <div class="text-muted small"><?= e($ref['pupil_email']) ?></div>
                    <?php if ($ref['pupil_dob']): ?>
                      <div class="text-muted small"><i class="fa fa-calendar-alt me-1"></i>DOB: <?= e($ref['pupil_dob']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark"><?= e($ref['course_title'] ?: 'Custom Affiliate Course') ?></div>
                  </td>
                  <td>
                    <div class="fw-bold text-dark"><?= e($ref['partner_name']) ?></div>
                    <span class="badge bg-light text-dark text-capitalize text-muted small"><?= e($ref['partner_type']) ?></span>
                  </td>
                  <td>
                    <span class="badge bg-<?= $ref['enrollment_status'] === 'paid' ? 'success' : ($ref['enrollment_status'] === 'active' ? 'warning' : 'secondary') ?>">
                      <?= e($ref['enrollment_status'] ?: 'Pending Registration') ?>
                    </span>
                  </td>
                  <td class="fw-bold text-indigo">
                    ₦<?= number_format((float)($ref['enrollment_paid'] ?? 0.0), 2) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- CREATE CAMPAIGN MODAL -->
<div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
      <div class="modal-header bg-dark text-white py-3 border-0">
        <h5 class="modal-title fw-bold">Create Affiliate Campaign</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="create_campaign">
        
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Target Partner <span class="text-danger">*</span></label>
            <select name="partner_id" class="form-select" required>
              <option value="">Select approved partner...</option>
              <?php foreach ($partners as $p): ?>
                <?php if ($p['status'] === 'approved' && $p['partner_type'] !== 'individual'): ?>
                  <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['partner_type']) ?>)</option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <div class="form-text small text-muted">Only approved non-individual partnership tracks can have customized campaigns.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">School/Campaign Name <span class="text-danger">*</span></label>
            <input type="text" name="school_name" class="form-control" placeholder="e.g. Kwasu Secondary School" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Program / Campaign Title</label>
            <input type="text" name="program_title" class="form-control" placeholder="e.g. Summer Digital Literacy 2026">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Target Candidates <span class="text-danger">*</span></label>
              <input type="number" name="candidates_count" class="form-control" min="1" placeholder="e.g. 50" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Commission Rate (%)</label>
              <input type="number" name="discount_rate" class="form-control" min="0" max="100" value="15" required>
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label fw-semibold">Target Course</label>
            <select name="course_id" class="form-select">
              <option value="">Select specific course (optional)...</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px;">Create Campaign</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function switchTab(tabId, btn) {
  // Hide all contents
  document.querySelectorAll('.tab-content').forEach(el => el.classList.add('d-none'));
  // Show target
  document.getElementById(tabId).classList.remove('d-none');
  
  // Deactivate all tab buttons
  document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
  // Activate clicked
  btn.classList.add('active');
}
</script>
</body>
</html>
