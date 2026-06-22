CREATE TABLE IF NOT EXISTS `lms_badges` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT UNSIGNED NOT NULL UNIQUE,
  `badge_title` VARCHAR(150) NOT NULL,
  `badge_description` TEXT DEFAULT NULL,
  `badge_style` VARCHAR(50) DEFAULT 'hexagon', -- style templates: 'hexagon', 'shield', 'star', 'seal'
  `badge_color` VARCHAR(50) DEFAULT 'gold-purple', -- gradients: 'gold-purple', 'emerald-teal', 'ruby-orange', 'sapphire-blue'
  `icon_class` VARCHAR(50) DEFAULT 'fa-award', -- FontAwesome icon inside the badge
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lms_student_badges` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `course_id` INT UNSIGNED NOT NULL,
  `share_token` VARCHAR(64) NOT NULL UNIQUE,
  `earned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `student_course_badge` (`student_id`, `course_id`),
  FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
