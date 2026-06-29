<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

try {
    echo "=== Running Affiliate Tables Migration ===\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    
    $sqlPath = __DIR__ . '/migrate_affiliate_partnerships.sql';
    if (!file_exists($sqlPath)) {
        throw new Exception("Migration SQL file not found: $sqlPath");
    }
    
    $sql = file_get_contents($sqlPath);
    
    // Clean up SQL comments (lines starting with -- or #)
    $lines = explode("\n", $sql);
    $cleanLines = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
            continue;
        }
        $cleanLines[] = $line;
    }
    $cleanSql = implode("\n", $cleanLines);
    
    // Split on semicolon
    $queries = array_filter(array_map('trim', explode(';', $cleanSql)));
    
    foreach ($queries as $query) {
        if ($query === '') {
            continue;
        }
        
        $pdo->exec($query);
        echo "OK: " . substr(preg_replace('/\s+/', ' ', $query), 0, 60) . "...\n";
    }
    
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "=== Migration Completed Successfully ===\n";
    
    // Verify tables
    $tables = ['lms_affiliate_partners', 'lms_affiliate_campaigns', 'lms_affiliate_referrals'];
    foreach ($tables as $table) {
        $exists = $pdo->query("SHOW TABLES LIKE '$table'")->fetchColumn();
        echo "Table '$table': " . ($exists ? "CREATED" : "MISSING") . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
