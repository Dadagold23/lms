ALTER TABLE `lms_payments`
  ADD INDEX `idx_payments_status_channel_created` (`status`, `channel`, `created_at`),
  ADD INDEX `idx_payments_reference` (`reference`),
  ADD INDEX `idx_payments_student_enrollment_status` (`student_id`, `enrollment_id`, `status`);
