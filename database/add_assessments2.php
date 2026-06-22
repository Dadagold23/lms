<?php
declare(strict_types=1);
require_once dirname(__DIR__).'./config/db.php'  ;
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = '2026-04-15 08:00:00'  ;
$lessons = $pdo->query("SELECT id, course_id, title, sort_order FROM lms_lessons ORDER BY course_id, sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$byCourse = [];
foreach ($lessons as $l) { $byCourse[$l["course_id"]][] = $l; }
$aStmt = $pdo->prepare("INSERT IGNORE INTO lms_lesson_assessments (lesson_id,course_id,type,title,instructions,pass_score,is_required,created_at) VALUES (?,?,?,?,?,60,1,?)");
$qStmt = $pdo->prepare("INSERT IGNORE INTO lms_assessment_questions (assessment_id,question,option_a,option_b,option_c,option_d,correct_option,marks,sort_order) VALUES (?,?,?,?,?,?,?,1,?)");
$ta=0; $tq=0;
function A(PDO $pdo, PDOStatement $a, PDOStatement $q, int $lid, int $cid, string $type, string $title, string $inst, array $qs, string $ts, int &$ta, int &$tq): void {
    $a->execute([$lid,$cid,$type,$title,$inst,$ts]);
    $aid=(int)$pdo->lastInsertId();
    if($aid===0) return;
    foreach($qs as $i=>$x){ $q->execute([$aid,$x[0],$x[1],$x[2],$x[3]??null,$x[4]??null,$x[5],$i+1]); $tq++; }
    $ta++;
}
