-- Migration: Affiliate & Partnership Tables
-- Defines schemas for affiliate partners, targeted school campaigns, and individual student referrals.

DROP TABLE IF EXISTS `lms_affiliate_referrals`;
DROP TABLE IF EXISTS `lms_affiliate_campaigns`;
DROP TABLE IF EXISTS `lms_affiliate_partners`;

CREATE TABLE IF NOT EXISTS `lms_affiliate_partners` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `partner_type` enum('individual', 'organization', 'institution', 'private', 'government') NOT NULL,
  `promo_plan` text NOT NULL,
  `access_password` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_affiliate_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lms_affiliate_campaigns` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` int(10) UNSIGNED NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_type` enum('private', 'public') DEFAULT NULL,
  `grades` varchar(255) DEFAULT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `program_title` varchar(255) DEFAULT NULL,
  `candidates_count` int(10) UNSIGNED NOT NULL,
  `discount_rate` int(5) NOT NULL DEFAULT 15,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_campaign_partner` (`partner_id`),
  KEY `idx_campaign_course` (`course_id`),
  CONSTRAINT `fk_campaign_partner` FOREIGN KEY (`partner_id`) REFERENCES `lms_affiliate_partners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_campaign_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lms_affiliate_referrals` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` int(10) UNSIGNED NOT NULL,
  `campaign_id` int(10) UNSIGNED DEFAULT NULL,
  `pupil_name` varchar(255) NOT NULL,
  `pupil_email` varchar(255) NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `referral_token` varchar(64) UNIQUE DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending_enrollment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_referral_partner` (`partner_id`),
  KEY `idx_referral_course` (`course_id`),
  KEY `idx_referral_campaign` (`campaign_id`),
  CONSTRAINT `fk_referral_partner` FOREIGN KEY (`partner_id`) REFERENCES `lms_affiliate_partners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_referral_course` FOREIGN KEY (`course_id`) REFERENCES `lms_courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_referral_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `lms_affiliate_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
