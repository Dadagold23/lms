-- =============================================================
-- Migration: Affiliate Course System
-- Creates lms_affiliate_courses, lms_affiliate_scheme_of_work
-- Alters lms_affiliate_referrals and lms_students
-- =============================================================

-- ---------------------------------------------------------------
-- 1. Affiliate Courses Table
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lms_affiliate_courses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 150000.00,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_affiliate_course_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------
-- 2. Affiliate Scheme of Work Table
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lms_affiliate_scheme_of_work` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` int(10) UNSIGNED NOT NULL,
  `class_level` enum('JSS1','JSS2','JSS3','SSS1','SSS2','SSS3') NOT NULL,
  `term` enum('1st','2nd','3rd') NOT NULL,
  `week_number` tinyint(3) UNSIGNED NOT NULL,
  `topic` varchar(500) NOT NULL,
  `objectives` text DEFAULT NULL,
  `activities` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sow_course` (`course_id`),
  KEY `idx_sow_level_term` (`class_level`, `term`),
  CONSTRAINT `fk_sow_course` FOREIGN KEY (`course_id`) REFERENCES `lms_affiliate_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------
-- 3. Alter lms_affiliate_referrals (add class routing columns)
-- ---------------------------------------------------------------
ALTER TABLE `lms_affiliate_referrals`
  ADD COLUMN IF NOT EXISTS `pupil_dob` date DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `class_range` enum('JSS','SSS','Higher') DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `class_level` varchar(10) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `affiliate_course_id` int(10) UNSIGNED DEFAULT NULL;

-- ---------------------------------------------------------------
-- 4. Alter lms_students (add affiliate tracking columns)
-- ---------------------------------------------------------------
ALTER TABLE `lms_students`
  ADD COLUMN IF NOT EXISTS `is_affiliate` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `affiliate_class_range` varchar(10) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `affiliate_class_level` varchar(10) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `affiliate_course_id` int(10) UNSIGNED DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `affiliate_partner_id` int(10) UNSIGNED DEFAULT NULL;
