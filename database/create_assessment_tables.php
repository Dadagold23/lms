<?php
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");

$pdo->exec("CREATE TABLE IF NOT EXISTS `lms_lesson_assessments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `type` enum('test','practical','assignment') NOT NULL DEFAULT 'test',
  `title` varchar(200) NOT NULL,
  `instructions` text DEFAULT NULL,
  `pass_score` tinyint UNSIGNED NOT NULL DEFAULT 60,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lesson` (`lesson_id`),
  KEY `idx_course` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS `lms_assessment_questions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(300) NOT NULL,
  `option_b` varchar(300) NOT NULL,
  `option_c` varchar(300) DEFAULT NULL,
  `option_d` varchar(300) DEFAULT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `marks` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `sort_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_assessment` (`assessment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS `lms_assessment_submissions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `total` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `attempt` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_assessment` (`student_id`,`assessment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");

$tables = $pdo->query("SHOW TABLES LIKE 'lms_%assessment%'")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables created:\n";
foreach ($tables as $t) echo "  $t\n";
