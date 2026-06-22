<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';

$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("SET SESSION sql_mode=''");

echo "=== STEP 1: Fix exam questions (remap to correct exam IDs) ===\n";

// Map course_id => actual latest exam_id
$map = [1=>46,2=>47,3=>48,4=>49,5=>50,6=>51,7=>52,8=>53,9=>54,10=>55,11=>56,13=>57,14=>58,15=>59,16=>60];

$existing = $pdo->query("SELECT exam_id, COUNT(*) as cnt FROM lms_exam_questions GROUP BY exam_id ORDER BY exam_id")->fetchAll(PDO::FETCH_ASSOC);
echo "Current questions by exam_id:\n";
foreach ($existing as $r) echo "  exam_id={$r['exam_id']}: {$r['cnt']}\n";

foreach ($map as $cid => $newEid) {
    $oldEid = $cid;
    $n = $pdo->exec("UPDATE lms_exam_questions SET exam_id={$newEid} WHERE exam_id={$oldEid}");
    if ($n > 0) echo "  Remapped {$n} questions: exam_id={$oldEid} -> {$newEid} (course {$cid})\n";
}

foreach ($map as $cid => $eid) {
    $cnt = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_questions WHERE exam_id={$eid}")->fetchColumn();
    $pdo->exec("UPDATE lms_exams SET total_questions={$cnt}, total_marks={$cnt}, pass_mark=50 WHERE id={$eid}");
}

$total = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_questions")->fetchColumn();
echo "Total questions after remap: {$total}\n\n";

echo "=== STEP 2: Verify lessons per course ===\n";
$rows = $pdo->query("SELECT course_id, COUNT(*) as cnt FROM lms_lessons GROUP BY course_id ORDER BY course_id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) echo "  Course {$r['course_id']}: {$r['cnt']} lessons\n";
$total = (int)$pdo->query("SELECT COUNT(*) FROM lms_lessons")->fetchColumn();
echo "Total lessons: {$total}\n\n";

echo "=== STEP 3: Verify videos ===\n";
$vrows = $pdo->query("SELECT course_id, COUNT(*) as cnt FROM lms_videos GROUP BY course_id ORDER BY course_id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vrows as $r) echo "  Course {$r['course_id']}: {$r['cnt']} videos\n";
$vtotal = (int)$pdo->query("SELECT COUNT(*) FROM lms_videos")->fetchColumn();
echo "Total videos: {$vtotal}\n\n";

echo "=== STEP 4: Create lms_lesson_completions if missing ===\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS lms_lesson_completions (
        id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        student_id int(10) UNSIGNED NOT NULL,
        lesson_id int(10) UNSIGNED NOT NULL,
        course_id int(10) UNSIGNED NOT NULL,
        completed_at timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        UNIQUE KEY uq_student_lesson (student_id, lesson_id),
        KEY idx_student_course (student_id, course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  lms_lesson_completions table ready\n\n";

echo "=== STEP 5: Add missing instructor columns ===\n";
$cols = $pdo->query("SHOW COLUMNS FROM lms_instructors")->fetchAll(PDO::FETCH_COLUMN);
$add = [
    'phone'            => "ALTER TABLE lms_instructors ADD COLUMN phone varchar(30) DEFAULT NULL AFTER email",
    'bio'              => "ALTER TABLE lms_instructors ADD COLUMN bio text DEFAULT NULL AFTER phone",
    'specialization'   => "ALTER TABLE lms_instructors ADD COLUMN specialization varchar(255) DEFAULT NULL AFTER bio",
    'qualification'    => "ALTER TABLE lms_instructors ADD COLUMN qualification varchar(255) DEFAULT NULL AFTER specialization",
    'experience_years' => "ALTER TABLE lms_instructors ADD COLUMN experience_years tinyint UNSIGNED DEFAULT 0 AFTER qualification",
    'linkedin_url'     => "ALTER TABLE lms_instructors ADD COLUMN linkedin_url varchar(255) DEFAULT NULL AFTER experience_years",
    'photo'            => "ALTER TABLE lms_instructors ADD COLUMN photo varchar(255) DEFAULT NULL AFTER linkedin_url",
];
foreach ($add as $col => $sql) {
    if (!in_array($col, $cols, true)) {
        try { $pdo->exec($sql); echo "  Added column: {$col}\n"; }
        catch (PDOException $e) { echo "  Skip {$col}: " . $e->getMessage() . "\n"; }
    } else {
        echo "  Column exists: {$col}\n";
    }
}

echo "\n=== STEP 6: Create lms_instructor_courses if missing ===\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS lms_instructor_courses (
        id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        instructor_id int(10) UNSIGNED NOT NULL,
        course_id int(10) UNSIGNED NOT NULL,
        assigned_at timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        UNIQUE KEY uq_instructor_course (instructor_id, course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "  lms_instructor_courses table ready\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "\n=== ALL DONE ===\n";
