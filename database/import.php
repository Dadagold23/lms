<?php
// One-time import script — run via: php database/import.php
require_once __DIR__ . '/../config/db.php';

function importFile(PDO $pdo, string $file): void {
    if (!file_exists($file)) {
        echo "SKIP (not found): $file\n";
        return;
    }

    $sql = file_get_contents($file);

    // Disable FK checks for the whole import
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("SET SESSION sql_mode=''");

    // Use a proper SQL splitter that respects quoted strings
    $statements = splitSQL($sql);

    $ok = 0; $err = 0;
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || preg_match('/^--/', $stmt) || preg_match('/^\/\*/', $stmt)) continue;
        // Skip the FK check statements — we handle them ourselves
        if (stripos($stmt, 'FOREIGN_KEY_CHECKS') !== false) continue;
        try {
            $pdo->exec($stmt);
            $ok++;
        } catch (PDOException $e) {
            $code = $e->getCode();
            $msg  = $e->getMessage();
            if (in_array($code, ['23000','42S01','42S21'], true)) continue;
            if (str_contains($msg, 'Duplicate key name') || str_contains($msg, 'already exists')) continue;
            echo "ERR [$code]: " . substr($msg, 0, 120) . "\n";
            $err++;
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "OK: " . basename($file) . " — $ok statements, $err errors\n";
}

/**
 * Split SQL respecting single-quoted strings (so semicolons inside strings are ignored)
 */
function splitSQL(string $sql): array {
    $statements = [];
    $current    = '';
    $inString   = false;
    $strChar    = '';
    $len        = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];

        if ($inString) {
            $current .= $c;
            if ($c === '\\') {
                // escaped char — consume next
                if ($i + 1 < $len) { $current .= $sql[++$i]; }
            } elseif ($c === $strChar) {
                // check for doubled quote (escape)
                if ($i + 1 < $len && $sql[$i + 1] === $strChar) {
                    $current .= $sql[++$i];
                } else {
                    $inString = false;
                }
            }
        } else {
            if ($c === "'" || $c === '"') {
                $inString = true;
                $strChar  = $c;
                $current .= $c;
            } elseif ($c === ';') {
                $statements[] = trim($current);
                $current = '';
            } else {
                $current .= $c;
            }
        }
    }
    if (trim($current) !== '') $statements[] = trim($current);
    return $statements;
}

$files = [
    __DIR__ . '/migrate_new_tables.sql',
    __DIR__ . '/lessons_patch.sql',
    __DIR__ . '/videos_patch.sql',
    __DIR__ . '/exam_questions_remap.sql',
];

foreach ($files as $file) {
    importFile($pdo, $file);
}
echo "\nAll done. Verify counts:\n";
echo "Lessons: " . $pdo->query("SELECT COUNT(*) FROM lms_lessons")->fetchColumn() . "\n";
echo "Videos:  " . $pdo->query("SELECT COUNT(*) FROM lms_videos")->fetchColumn() . "\n";
echo "Questions: " . $pdo->query("SELECT COUNT(*) FROM lms_exam_questions")->fetchColumn() . "\n";
echo "Course 2 lessons: " . $pdo->query("SELECT COUNT(*) FROM lms_lessons WHERE course_id=2")->fetchColumn() . "\n";

