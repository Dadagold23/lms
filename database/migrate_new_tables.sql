-- Migration: New tables for lesson completions, instructor bio, course assignments
-- Run once on existing databases

-- 1. Lesson completions tracking
CREATE TABLE IF NOT EXISTS `lms_lesson_completions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_student_lesson` (`student_id`,`lesson_id`),
  KEY `idx_student_course` (`student_id`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Expand lms_instructors with bio and specialization
ALTER TABLE `lms_instructors`
  ADD COLUMN IF NOT EXISTS `phone`          varchar(30)  DEFAULT NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `bio`            text         DEFAULT NULL AFTER `phone`,
  ADD COLUMN IF NOT EXISTS `specialization` varchar(255) DEFAULT NULL AFTER `bio`,
  ADD COLUMN IF NOT EXISTS `qualification`  varchar(255) DEFAULT NULL AFTER `specialization`,
  ADD COLUMN IF NOT EXISTS `experience_years` tinyint UNSIGNED DEFAULT 0 AFTER `qualification`,
  ADD COLUMN IF NOT EXISTS `linkedin_url`   varchar(255) DEFAULT NULL AFTER `experience_years`,
  ADD COLUMN IF NOT EXISTS `photo`          varchar(255) DEFAULT NULL AFTER `linkedin_url`;

-- 3. Instructor ↔ Course assignment table
CREATE TABLE IF NOT EXISTS `lms_instructor_courses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instructor_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_instructor_course` (`instructor_id`,`course_id`),
  KEY `idx_course` (`course_id`),
  CONSTRAINT `fk_ic_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `lms_instructors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ic_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Fix lms_exam_results: add percent and status columns if missing
ALTER TABLE `lms_exam_results`
  ADD COLUMN IF NOT EXISTS `percent` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `total`,
  ADD COLUMN IF NOT EXISTS `status` enum('pass','fail') NOT NULL DEFAULT 'fail' AFTER `percent`;

-- 5. Add lms_lesson_completions to schema
ALTER TABLE `lms_lesson_completions`
  ADD CONSTRAINT `fk_lc_student` FOREIGN KEY (`student_id`) REFERENCES `lms_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lc_lesson`  FOREIGN KEY (`lesson_id`)  REFERENCES `lms_lessons`  (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lc_course`  FOREIGN KEY (`course_id`)  REFERENCES `lms_courses`  (`id`) ON DELETE CASCADE;
