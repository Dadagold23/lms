-- Migration: Deduplicate lms_exams (keep only latest per course) and add unique constraint
-- Run once on existing databases

-- Step 1: Delete duplicate exams, keeping only the latest per course
DELETE e1 FROM lms_exams e1
INNER JOIN lms_exams e2
  ON e1.course_id = e2.course_id
  AND e1.title = e2.title
  AND e1.id < e2.id;

-- Step 2: Add unique constraint so each course has one exam per title
ALTER TABLE `lms_exams`
  ADD UNIQUE KEY `uq_course_exam_title` (`course_id`, `title`);

-- Step 3: Ensure lms_lessons has unique sort_order per course
-- (no structural change needed — sort_order is just ordering, not unique)
-- But add an index for performance
ALTER TABLE `lms_lessons`
  ADD INDEX IF NOT EXISTS `idx_course_sort` (`course_id`, `sort_order`);

-- Step 4: Ensure lms_assignments has index on course_id
ALTER TABLE `lms_assignments`
  ADD INDEX IF NOT EXISTS `idx_assign_course` (`course_id`);
