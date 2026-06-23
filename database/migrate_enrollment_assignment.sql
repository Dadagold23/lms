-- Migration: Add instructor auto-assignment fields to lms_enrollments
-- Run once: php run_patches.php OR execute directly in phpMyAdmin

ALTER TABLE lms_enrollments
  ADD COLUMN IF NOT EXISTS assigned_instructor_id INT NULL DEFAULT NULL
    COMMENT 'Instructor auto-assigned based on lms_instructor_courses mapping',
  ADD COLUMN IF NOT EXISTS needs_instructor_assignment TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = no instructor was assigned at enrollment time; admin must assign manually';

-- Index for admin enrollment assignment queries
CREATE INDEX IF NOT EXISTS idx_enrollments_needs_instructor
  ON lms_enrollments (needs_instructor_assignment);

CREATE INDEX IF NOT EXISTS idx_enrollments_assigned_instructor
  ON lms_enrollments (assigned_instructor_id);
