<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

/* ── Filters ── */
$filter = $_GET['filter'] ?? 'outstanding'; // outstanding | all | overdue
$search = trim($_GET['q'] ?? '');

/* ── Build query ── */
$where = "WHERE 1=1";
$params = [];

if ($filter === 'outstanding') {
    $where .= " AND e.paid_amount < c.price";
} elseif ($filter === 'overdue') {
    $where .= " AND e.paid_amount < c.price AND e.next_due_date < CURDATE() AND e.next_due_date IS NOT NULL";
}

if ($search !== '') {
    $where .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.phone LIKE ? OR c.title LIKE ?)";
    $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"]);
}

$enrollments = $pdo->prepare("
    SELECT
        e.id AS enrollment_id,
        e.paid_amount,
        e.payment_type,
        e.status AS enroll_status,
        e.next_due_date,
        c.price,
        c.title AS course_title,
        s.id AS student_id,
        s.first_name,
        s.last_name,
        s.phone,
        s.email,
        (c.price - e.paid_amount) AS balance
    FROM lms_enrollments e
    JOIN lms_students s ON s.id = e.student_id
    JOIN lms_courses c ON c.id = e.course_id
    {$where}
    ORDER BY e.next_due_date ASC, balance DESC
    LIMIT 200
");
$enrollments->execute($params);
$enrollments = $enrollments->fetchAll(PDO::FETCH_ASSOC);

/* ── Stats ── */
$totalOutstanding = (float)$pdo->query("SELECT COALESCE(SUM(c.price - e.paid_amount),0) FROM lms_enrollments e JOIN lms_courses c ON c.id=e.course_id WHERE e.paid_amount < c.price")->fetchColumn();
$countOutstanding = (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments e JOIN lms_courses c ON c.id=e.course_id WHERE e.paid_amount < c.price")->fetchColumn();
$countOverdue     = (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments e JOIN lms_courses c ON c.id=e.course_id WHERE e.paid_amount < c.price AND e.next_due_date < CURDATE() AND e.next_due_date IS NOT NULL")->fetchColumn();
$countNoPhone     = (int)$pdo->query("SELECT COUNT(*) FROM lms_enrollments e JOIN lms_students s ON s.id=e.student_id JOIN lms_courses c ON c.id=e.course_id WHERE e.paid_amount < c.price AND (s.phone IS NULL OR s.phone='')")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'WhatsApp Reminders';
$seoDesc    = 'Send WhatsApp payment reminders to students at Grafix@Mirror LMS admin panel.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
</head>
<body style="background:var(--surface)">

<nav class="lms-nav lms-nav-admin">
  <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
    <div class="brand">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">A</div>
      <span style="color:#fff">Admin <span style="color:#a5b4fc">Panel</span></span>
    </div>
    <a href="admin_dashboard.php" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.3)">
      <i class="fa fa-arrow-left me-1"></i>Dashboard
    </a>
  </div>
</nav>

<div class="container py-4" style="max-width:1100px">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h4 class="page-title mb-1">
        <i class="fab fa-whatsapp me-2" style="color:#25d366"></i>WhatsApp Payment Reminders
      </h4>
      <p class="text-muted mb-0" style="font-size:.88rem">
        Send payment reminders directly to students via WhatsApp
      </p>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-icon amber"><i class="fa fa-money-bill-wave"></i></div>
        <div>
          <div class="stat-value" style="font-size:1.1rem"><?= formatMoney($totalOutstanding) ?></div>
          <div class="stat-label">Total Outstanding</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fa fa-users"></i></div>
        <div>
          <div class="stat-value"><?= $countOutstanding ?></div>
          <div class="stat-label">Students with Balance</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-icon red"><i class="fa fa-calendar-times"></i></div>
        <div>
          <div class="stat-value"><?= $countOverdue ?></div>
          <div class="stat-label">Overdue Payments</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-icon cyan"><i class="fa fa-phone-slash"></i></div>
        <div>
          <div class="stat-value"><?= $countNoPhone ?></div>
          <div class="stat-label">No Phone Number</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters + Search -->
  <div class="lms-card mb-4">
    <form method="get" class="d-flex flex-wrap gap-3 align-items-end">
      <div>
        <label class="form-label">Filter</label>
        <div class="d-flex gap-2">
          <?php foreach (['outstanding' => 'Has Balance', 'overdue' => 'Overdue', 'all' => 'All Enrollments'] as $val => $label): ?>
            <a href="?filter=<?= $val ?>&q=<?= urlencode($search) ?>"
               class="<?= $filter === $val ? 'btn-brand' : 'btn-ghost' ?>"
               style="font-size:.85rem;padding:.4rem .9rem">
              <?= $label ?>
              <?php if ($val === 'overdue' && $countOverdue > 0): ?>
                <span style="background:var(--danger);color:#fff;border-radius:99px;padding:.05rem .4rem;font-size:.7rem;margin-left:.25rem"><?= $countOverdue ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="flex-grow-1">
        <label class="form-label">Search</label>
        <div class="d-flex gap-2">
          <input type="hidden" name="filter" value="<?= e($filter) ?>">
          <input type="text" name="q" value="<?= e($search) ?>" class="form-control" placeholder="Name, phone, or course...">
          <button class="btn-brand"><i class="fa fa-search"></i></button>
          <?php if ($search): ?>
            <a href="?filter=<?= e($filter) ?>" class="btn-ghost">Clear</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>

  <!-- Student list -->
  <?php if (empty($enrollments)): ?>
    <div class="lms-alert lms-alert-success">
      <i class="fa fa-check-circle me-1"></i>
      <?= $filter === 'outstanding' ? 'No outstanding balances — all students are fully paid!' : 'No records found.' ?>
    </div>
  <?php else: ?>
    <div class="lms-card p-0" style="overflow:hidden">
      <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--border)">
        <span style="font-size:.85rem;color:var(--muted)"><?= count($enrollments) ?> record<?= count($enrollments) !== 1 ? 's' : '' ?> found</span>
        <span style="font-size:.82rem;color:var(--muted)">
          <i class="fab fa-whatsapp me-1" style="color:#25d366"></i>
          Click "Send" to open WhatsApp with a pre-filled message
        </span>
      </div>
      <table class="lms-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Balance</th>
            <th>Payment Plan</th>
            <th>Next Due</th>
            <th>Phone</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($enrollments as $r):
            $balance    = (float)$r['balance'];
            $hasPhone   = !empty(trim($r['phone'] ?? ''));
            $isOverdue  = !empty($r['next_due_date']) && strtotime($r['next_due_date']) < time();
            $isFullyPaid = $balance <= 0;
          ?>
            <tr style="<?= $isOverdue ? 'background:#fff7ed' : '' ?>">
              <td>
                <div style="font-weight:600"><?= e(trim($r['first_name'].' '.$r['last_name'])) ?></div>
                <div style="font-size:.78rem;color:var(--muted)"><?= e($r['email'] ?? '') ?></div>
              </td>
              <td style="font-size:.88rem"><?= e($r['course_title']) ?></td>
              <td>
                <?php if ($isFullyPaid): ?>
                  <span class="badge-success">Fully Paid</span>
                <?php else: ?>
                  <span style="font-weight:700;color:<?= $isOverdue ? 'var(--danger)' : 'var(--dark)' ?>">
                    <?= formatMoney($balance) ?>
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge-<?= $r['payment_type'] === 'installment' ? 'warning' : 'info' ?>">
                  <?= ucfirst($r['payment_type'] ?? 'full') ?>
                </span>
              </td>
              <td style="font-size:.85rem">
                <?php if (!empty($r['next_due_date'])): ?>
                  <span style="color:<?= $isOverdue ? 'var(--danger)' : 'var(--dark)' ?>;font-weight:<?= $isOverdue ? '700' : '400' ?>">
                    <?= $isOverdue ? '<i class="fa fa-exclamation-circle me-1"></i>' : '' ?>
                    <?= e(date('d M Y', strtotime($r['next_due_date']))) ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem">
                <?php if ($hasPhone): ?>
                  <span style="color:var(--success)"><i class="fa fa-check me-1"></i><?= e($r['phone']) ?></span>
                <?php else: ?>
                  <span style="color:var(--danger)"><i class="fa fa-times me-1"></i>No phone</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($hasPhone && !$isFullyPaid): ?>
                  <a href="whatsapp_render.php?enrollment_id=<?= (int)$r['enrollment_id'] ?>"
                     class="btn btn-sm btn-success"
                     style="background:#25d366;border:none;font-size:.8rem"
                     title="Send WhatsApp reminder to <?= e($r['first_name']) ?>">
                    <i class="fab fa-whatsapp me-1"></i>Send
                  </a>
                <?php elseif (!$hasPhone): ?>
                  <a href="admin_switch.php?student_id=<?= (int)$r['student_id'] ?>"
                     class="btn btn-sm btn-outline-secondary" style="font-size:.78rem"
                     title="Switch to student to update phone">
                    <i class="fa fa-phone me-1"></i>Add Phone
                  </a>
                <?php else: ?>
                  <span class="badge-success" style="font-size:.75rem">Paid</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($countNoPhone > 0): ?>
      <div class="lms-alert lms-alert-warning mt-3">
        <i class="fa fa-info-circle me-1"></i>
        <strong><?= $countNoPhone ?> student<?= $countNoPhone > 1 ? 's have' : ' has' ?> no phone number</strong> on record.
        Ask them to update their profile, or use the "Add Phone" button to switch to their account and update it.
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>
</body>
</html>
