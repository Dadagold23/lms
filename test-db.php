<?php
declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
loadEnv(__DIR__ . '/.env');

// Block access in production
if (($_ENV['APP_ENV'] ?? 'production') !== 'local') {
    http_response_code(404);
    exit('Not found.');
}

require_once __DIR__ . '/config/db.php';

echo "<h2>Mirror Age LMS — Database Test</h2>";

try {
    echo "<p>✅ Database connection successful.</p>";

    $dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "<p>Connected Database: <strong>{$dbName}</strong></p>";

    $requiredTables = [
        'lms_students', 'lms_courses', 'lms_enrollments', 'lms_lessons',
        'lms_assignments', 'lms_assignment_submissions', 'lms_exams',
        'lms_exam_results', 'lms_payments', 'lms_certificates', 'lms_activity_logs'
    ];

    echo "<h4>Table Check</h4><ul>";
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$table]);
        $exists = (int)$stmt->fetchColumn();
        echo $exists
            ? "<li>✅ {$table}</li>"
            : "<li style='color:red'>❌ {$table} (missing)</li>";
    }
    echo "</ul>";

    $count = $pdo->query("SELECT COUNT(*) FROM lms_students")->fetchColumn();
    echo "<p>Total registered students: <strong>{$count}</strong></p>";

    $pdo->beginTransaction();
    $pdo->exec("INSERT INTO lms_activity_logs (student_id, action, ip_address) VALUES (NULL, 'DB_TEST', '127.0.0.1')");
    $pdo->rollBack();
    echo "<p>✅ Transaction rollback successful.</p>";

    echo "<hr><h3 style='color:green'>ALL DATABASE TESTS PASSED ✔</h3>";

} catch (Throwable $e) {
    http_response_code(500);
    echo "<h3 style='color:red'>DATABASE TEST FAILED ❌</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
