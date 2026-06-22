-- Migration: Newsletter subscribers
-- Stores public newsletter signups from the website footer.

CREATE TABLE IF NOT EXISTS `lms_newsletter_subscribers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `status` enum('active','unsubscribed') NOT NULL DEFAULT 'active',
  `source` varchar(60) NOT NULL DEFAULT 'footer',
  `subscribed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_newsletter_email` (`email`),
  KEY `idx_newsletter_status` (`status`),
  KEY `idx_newsletter_subscribed_at` (`subscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
