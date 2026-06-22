<?php
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$sql = file_get_contents(__DIR__.'/migrate_assessments.sql');
foreach(array_filter(array_map('trim',explode(';',$sql))) as $s){
    if(trim($s)===''||str_starts_with(trim($s),'--')) continue;
    try{$pdo->exec($s);echo 'OK: '.substr($s,0,60).PHP_EOL;}
    catch(PDOException $e){echo 'ERR: '.$e->getMessage().PHP_EOL;}
}
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "Tables: lms_lesson_assessments, lms_assessment_questions, lms_assessment_submissions\n";
