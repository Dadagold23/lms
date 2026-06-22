-- Migration: Add payment_type to lms_enrollments and fix status enum
-- Run this once on any existing database

-- 1. Add payment_type column (if not exists)
ALTER TABLE `lms_enrollments`
  ADD COLUMN IF NOT EXISTS `payment_type` enum('full','installment') NOT NULL DEFAULT 'full'
  AFTER `access_expires_at`;

-- 2. Expand status enum to include 'paid' and 'installment'
ALTER TABLE `lms_enrollments`
  MODIFY COLUMN `status` enum('active','paid','installment','expired','cancelled') NOT NULL DEFAULT 'active';

-- 3. Fix existing rows with empty status
UPDATE `lms_enrollments` e
JOIN `lms_courses` c ON c.id = e.course_id
SET e.status = CASE
    WHEN e.paid_amount >= c.price AND c.price > 0 THEN 'paid'
    WHEN e.paid_amount > 0 THEN 'installment'
    ELSE 'active'
END
WHERE e.status = '' OR e.status IS NULL;

-- 4. Sync payment_type from lms_students for existing enrollments
UPDATE `lms_enrollments` e
JOIN `lms_students` s ON s.id = e.student_id
SET e.payment_type = COALESCE(s.payment_option, 'full')
WHERE e.payment_type = 'full';
