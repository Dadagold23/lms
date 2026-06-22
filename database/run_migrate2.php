<?php
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");

$pdo->exec("CREATE TABLE IF NOT EXISTS `lms_live_sessions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` int(10) UNSIGNED NOT NULL,
  `instructor_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `anydesk_id` varchar(100) DEFAULT NULL,
  `meeting_link` varchar(500) DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `recording_url` varchar(500) DEFAULT NULL,
  `status` enum('scheduled','live','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `max_students` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_course` (`course_id`),
  KEY `idx_scheduled` (`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "live_sessions: ".$pdo->query("SHOW TABLES LIKE 'lms_live_sessions'")->fetchColumn().PHP_EOL;
echo "attendance: ".$pdo->query("SHOW TABLES LIKE 'lms_session_attendance'")->fetchColumn().PHP_EOL;
echo "ai_chats: ".$pdo->query("SHOW TABLES LIKE 'lms_ai_chats'")->fetchColumn().PHP_EOL;
