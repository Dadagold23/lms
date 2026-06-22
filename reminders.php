<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

requireAdminLogin();

$days = (int)($_GET['days'] ?? 7);
if ($days < 1) $days = 7;

$stmt = $pdo->prepare("
  SELECT
    s.id AS student_id,
    s.first_name,
    s.last_name,
    s.phone,
    s.email,
    c.title AS course_title,
    c.price,
    e.id AS enrollment_id,
    e.paid_amount,
    e.next_due_date
  FROM lms_enrollments e
  JOIN lms_students s ON s.id = e.student_id
  JOIN lms_courses c ON c.id = e.course_id
  WHERE e.next_due_date IS NOT NULL
    AND e.next_due_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
    AND COALESCE(e.paid_amount,0) < c.price
  ORDER BY e.next_due_date ASC
");
$stmt->execute([$days]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Reminders | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f7fbff;font-family:Inter,system-ui}.card{border-radius:14px}</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold">Payment Reminders</span>
    <div class="ms-auto d-flex gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="card p-3 mb-3">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-md-3">
        <label class="form-label small">Due within (days)</label>
        <input class="form-control form-control-sm" name="days" type="number" value="<?= (int)$days ?>">
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary btn-sm">Filter</button>
      </div>
    </form>
  </div>

  <div class="card p-3">
    <?php if (!$rows): ?>
      <p class="text-muted mb-0">No upcoming installment dues.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Student</th>
              <th>Course</th>
              <th>Paid</th>
              <th>Balance</th>
              <th>Due Date</th>
              <th>WhatsApp</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r):
            $paid = (float)($r['paid_amount'] ?? 0);
            $price = (float)($r['price'] ?? 0);
            $bal = max(0, $price - $paid);
          ?>
            <tr>
              <td>
                <?= e($r['first_name'].' '.$r['last_name']) ?><br>
                <small class="text-muted"><?= e((string)$r['email']) ?></small>
              </td>
              <td><?= e((string)$r['course_title']) ?></td>
              <td><?= formatMoney($paid) ?></td>
              <td class="fw-bold"><?= formatMoney($bal) ?></td>
              <td><?= e((string)$r['next_due_date']) ?></td>
              <td>
                <a class="btn btn-success btn-sm"
                   href="whatsapp_render.php?enrollment_id=<?= (int)$r['enrollment_id'] ?>">
                  Message
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
