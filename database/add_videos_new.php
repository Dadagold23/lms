<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = '2026-04-15 08:00:00';

$vStmt = $pdo->prepare("INSERT IGNORE INTO lms_videos (course_id,lesson_id,title,video_path,duration_seconds,is_published,created_at) VALUES (?,NULL,?,?,?,1,?)");

// Course 17: Data Science
$ds = [
    ['Data Science Full Course for Beginners 2024',       'https://www.youtube.com/watch?v=ua-CiDNNj30', 14400],
    ['Python for Data Science — Complete Tutorial',       'https://www.youtube.com/watch?v=LHBE6Q9XlzI', 7200],
    ['Statistics for Data Science — Full Course',         'https://www.youtube.com/watch?v=xxpc-HPKN28', 5400],
    ['Data Wrangling with pandas — Full Tutorial',        'https://www.youtube.com/watch?v=vmEHCJofslg', 6000],
    ['Machine Learning for Data Scientists',              'https://www.youtube.com/watch?v=7eh4d6sabA0', 7200],
    ['Data Visualisation with Python — Matplotlib & Seaborn', 'https://www.youtube.com/watch?v=a9UrKTVEeZA', 3600],
    ['Big Data & Apache Spark Tutorial',                  'https://www.youtube.com/watch?v=F8pyaR4uQ2g', 5400],
    ['End-to-End Data Science Project Tutorial',          'https://www.youtube.com/watch?v=fwY9Qv96DJY', 9000],
];

// Course 18: Artificial Intelligence
$ai = [
    ['Artificial Intelligence Full Course 2024',          'https://www.youtube.com/watch?v=JMUxmLyrhSk', 14400],
    ['Machine Learning Crash Course — Google',            'https://www.youtube.com/watch?v=KNAWp2S3w94', 7200],
    ['Neural Networks from Scratch — Python',             'https://www.youtube.com/watch?v=Wo5dMEP_BbI', 5400],
    ['Computer Vision with OpenCV & Python',              'https://www.youtube.com/watch?v=oXlwWbU8l2o', 6000],
    ['NLP with Python — Full Course',                     'https://www.youtube.com/watch?v=X2vAabgKiuM', 7200],
    ['ChatGPT & OpenAI API — Full Tutorial',              'https://www.youtube.com/watch?v=c-g6epk3fFE', 4800],
    ['AI Ethics & Responsible AI — Full Course',          'https://www.youtube.com/watch?v=aGwYtUzMQUk', 3600],
    ['Build an AI App — End-to-End Project',              'https://www.youtube.com/watch?v=ztBJqzBU5kc', 9000],
];

// Course 19: Machine Learning
$ml = [
    ['Machine Learning Full Course — Simplilearn',        'https://www.youtube.com/watch?v=GwIo3gDZCVQ', 14400],
    ['Supervised Learning — Regression & Classification', 'https://www.youtube.com/watch?v=7eh4d6sabA0', 7200],
    ['Unsupervised Learning — Clustering & PCA',          'https://www.youtube.com/watch?v=IUn8k5zSI6g', 5400],
    ['Model Evaluation & Cross-Validation',               'https://www.youtube.com/watch?v=fSytzGwwBVw', 3600],
    ['Feature Engineering for Machine Learning',          'https://www.youtube.com/watch?v=6WDFfaYtN6s', 4800],
    ['XGBoost & Gradient Boosting — Full Tutorial',       'https://www.youtube.com/watch?v=OtD8wVaFm6E', 5400],
    ['ML Model Deployment with Flask & Docker',           'https://www.youtube.com/watch?v=ipFUANeStYE', 4200],
    ['MLOps — Machine Learning in Production',            'https://www.youtube.com/watch?v=NgWujOrCZFo', 6000],
];

$count = 0;
foreach ([17 => $ds, 18 => $ai, 19 => $ml] as $cid => $videos) {
    foreach ($videos as [$title, $url, $dur]) {
        $vStmt->execute([$cid, $title, $url, $dur, $ts]);
        $count++;
    }
    // Set intro_video on course
    $pdo->prepare("UPDATE lms_courses SET intro_video=? WHERE id=?")
        ->execute([$videos[0][1], $cid]);
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "Videos inserted: {$count}\n";
echo "Videos in DB for courses 17-19: ".$pdo->query("SELECT COUNT(*) FROM lms_videos WHERE course_id IN (17,18,19)")->fetchColumn()."\n";
