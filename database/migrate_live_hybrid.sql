CREATE TABLE IF NOT EXISTS `lms_session_chat_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned DEFAULT NULL,
  `instructor_id` int(10) unsigned DEFAULT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_role` enum('student','instructor','admin','system') NOT NULL DEFAULT 'student',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session_created` (`session_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lms_session_participants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `participant_key` varchar(64) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `display_name` varchar(150) NOT NULL,
  `role` enum('student','instructor','admin') NOT NULL DEFAULT 'student',
  `last_seen_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_session_participant_key` (`session_id`,`participant_key`),
  KEY `idx_session_last_seen` (`session_id`,`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lms_session_signals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `from_key` varchar(64) NOT NULL,
  `to_key` varchar(64) NOT NULL,
  `signal_type` enum('offer','answer','ice') NOT NULL,
  `payload` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session_to_key` (`session_id`,`to_key`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
