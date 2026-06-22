<?php
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$sql = file_get_contents(__DIR__.'/migrate_live_ai.sql');
foreach(array_filter(array_map('trim',explode(';',$sql))) as $s){
    if(trim($s)===''||str_starts_with(trim($s),'--')) continue;
    try{$pdo->exec($s);echo 'OK: '.substr($s,0,50).PHP_EOL;}
    catch(PDOException $e){echo 'ERR: '.$e->getMessage().PHP_EOL;}
}
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
echo 'live_sessions: '.$pdo->query("SHOW TABLES LIKE 'lms_live_sessions'")->fetchColumn().PHP_EOL;
echo 'ai_chats: '.$pdo->query("SHOW TABLES LIKE 'lms_ai_chats'")->fetchColumn().PHP_EOL;
