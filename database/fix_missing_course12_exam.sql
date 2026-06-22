INSERT INTO `lms_exams` (
  `id`,
  `course_id`,
  `title`,
  `duration_minutes`,
  `total_marks`,
  `pass_mark`,
  `total_questions`,
  `is_published`,
  `created_at`
)
SELECT
  12,
  12,
  'Desktop Application Dev - Final Exam',
  40,
  10,
  50,
  10,
  1,
  NOW()
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `lms_exams` WHERE `course_id` = 12
);
