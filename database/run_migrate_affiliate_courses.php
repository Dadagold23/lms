<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/db.php';

try {
    echo "=== Running Affiliate Courses Migration ===\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $sqlPath = __DIR__ . '/migrate_affiliate_courses.sql';
    if (!file_exists($sqlPath)) {
        throw new Exception("Migration SQL file not found: $sqlPath");
    }

    $sql = file_get_contents($sqlPath);

    // Strip comment lines
    $lines = explode("\n", $sql);
    $cleanLines = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) continue;
        $cleanLines[] = $line;
    }
    $cleanSql = implode("\n", $cleanLines);

    // Split on semicolon and execute
    $queries = array_filter(array_map('trim', explode(';', $cleanSql)));
    foreach ($queries as $query) {
        if ($query === '') continue;
        $pdo->exec($query);
        echo "OK: " . substr(preg_replace('/\s+/', ' ', $query), 0, 80) . "...\n";
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "\n=== Migration Completed Successfully ===\n";

    // Verify tables
    $tables = ['lms_affiliate_courses', 'lms_affiliate_scheme_of_work'];
    foreach ($tables as $table) {
        $exists = $pdo->query("SHOW TABLES LIKE '$table'")->fetchColumn();
        echo "Table '$table': " . ($exists ? "EXISTS" : "MISSING") . "\n";
    }

    // Verify new columns on referrals
    $cols = $pdo->query("SHOW COLUMNS FROM lms_affiliate_referrals LIKE 'class_range'")->fetchColumn();
    echo "Column lms_affiliate_referrals.class_range: " . ($cols ? "EXISTS" : "MISSING") . "\n";

    $cols2 = $pdo->query("SHOW COLUMNS FROM lms_students LIKE 'is_affiliate'")->fetchColumn();
    echo "Column lms_students.is_affiliate: " . ($cols2 ? "EXISTS" : "MISSING") . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
