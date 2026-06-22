ALTER TABLE `lms_courses`
  ADD COLUMN `workspace_type` varchar(30) NOT NULL DEFAULT 'default' AFTER `intro_video`,
  ADD COLUMN `workspace_url` varchar(255) DEFAULT NULL AFTER `workspace_type`;
