<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/db.php';

try {
    echo "=== Running Affiliate KYC & Autologin Migration ===\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $sqlPath = __DIR__ . '/migrate_affiliate_kyc_autologin.sql';
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
        try {
            $pdo->exec($query);
            echo "OK: " . substr(preg_replace('/\s+/', ' ', $query), 0, 80) . "...\n";
        } catch (PDOException $e) {
            // Ignore error if it is column already exists or similar, but print it
            echo "NOTICE/ERROR: " . $e->getMessage() . "\n";
        }
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "\n=== Migration Completed Successfully ===\n";

    // Verify new columns
    $cols = $pdo->query("SHOW COLUMNS FROM lms_affiliate_referrals")->fetchAll(PDO::FETCH_COLUMN);
    $verifyCols = ['kyc_type', 'kyc_number', 'passport', 'signature', 'autologin_token'];
    foreach ($verifyCols as $col) {
        echo "Referrals column '$col': " . (in_array($col, $cols, true) ? "EXISTS" : "MISSING") . "\n";
    }

    $cols2 = $pdo->query("SHOW COLUMNS FROM lms_students")->fetchAll(PDO::FETCH_COLUMN);
    echo "Students column 'autologin_token': " . (in_array('autologin_token', $cols2, true) ? "EXISTS" : "MISSING") . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
