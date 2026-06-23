-- Migration: Add availability_status column to lms_instructors table if not exists
ALTER TABLE `lms_instructors`
  ADD COLUMN IF NOT EXISTS `availability_status` varchar(50) NOT NULL DEFAULT 'available' AFTER `status`;
