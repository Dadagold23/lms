-- Migration: Affiliate KYC & Autologin Columns
-- Adds KYC type, KYC number, passport, signature, and autologin_token columns.

ALTER TABLE `lms_affiliate_referrals`
  ADD COLUMN `kyc_type` varchar(100) DEFAULT NULL,
  ADD COLUMN `kyc_number` varchar(100) DEFAULT NULL,
  ADD COLUMN `passport` varchar(255) DEFAULT NULL,
  ADD COLUMN `signature` varchar(255) DEFAULT NULL,
  ADD COLUMN `autologin_token` varchar(64) DEFAULT NULL;

ALTER TABLE `lms_students`
  ADD COLUMN `autologin_token` varchar(64) DEFAULT NULL,
  ADD UNIQUE KEY `uq_student_autologin_token` (`autologin_token`);
