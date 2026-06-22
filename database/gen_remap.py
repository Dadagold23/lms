# remap

# Remap exam questions from old exam_ids (1-16) to actual exam_ids (46-60)
# course_id -> actual_exam_id mapping from DB query result
COURSE_TO_EXAM = {
    1: 46, 2: 47, 3: 48, 4: 49, 5: 50,
    6: 51, 7: 52, 8: 53, 9: 54, 10: 55,
    11: 56, 13: 57, 14: 58, 15: 59, 16: 60,
}
# old exam_id (1-16) maps to course_id (same number for 1-16)
# course 12 has no exam in the latest set — skip
OLD_TO_NEW = {}
for cid, new_eid in COURSE_TO_EXAM.items():
    old_eid = cid  # old exam_id == course_id for 1-16
    OLD_TO_NEW[old_eid] = new_eid

# Read the existing exam_questions_patch.sql and remap exam_ids
src = open('database/exam_questions_patch.sql', encoding='utf-8').read()

# Replace each old exam_id reference in the VALUES rows
import re

def remap_row(m):
    lid = m.group(1)
    old_eid = int(m.group(2))
    rest = m.group(3)
    new_eid = OLD_TO_NEW.get(old_eid, old_eid)
    return f"({lid},{new_eid},{rest}"

# Match rows like (1,1,'question',...) — id, exam_id, rest
remapped = re.sub(r'\((\d+),(\d+),(.*?)(?=\),|\);)', remap_row, src, flags=re.DOTALL)

out = open('database/exam_questions_remap.sql', 'w', encoding='utf-8')
out.write("-- Exam questions with correct exam_ids (46-60)\n\n")
out.write("SET FOREIGN_KEY_CHECKS=0;\n")
out.write("TRUNCATE TABLE lms_exam_questions;\n")
out.write("SET FOREIGN_KEY_CHECKS=1;\n\n")

# Re-extract just the INSERT block from remapped
insert_match = re.search(r'(INSERT INTO.*?;)', remapped, re.DOTALL)
if insert_match:
    out.write(insert_match.group(1))
    out.write('\n\n')

# Update total_questions on actual exams
out.write("-- Update total_questions on actual exams\n")
for cid, new_eid in sorted(COURSE_TO_EXAM.items()):
    out.write(f"UPDATE lms_exams SET total_questions=10, total_marks=10, pass_mark=50 WHERE id={new_eid};\n")

out.close()
print("Remap SQL written:", len(OLD_TO_NEW), "exams remapped")
