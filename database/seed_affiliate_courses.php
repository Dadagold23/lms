<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/db.php';

echo "=== Seeding Affiliate Courses & Scheme of Work ===\n";

// ─────────────────────────────────────────────────────────────
// 1. COURSE DEFINITIONS (20 courses)
// Fetches credentials from lms_courses where title matches,
// Robotics is added fresh.
// ─────────────────────────────────────────────────────────────
$courseDefs = [
    ['title' => 'Graphic Design',              'level' => 'beginner',     'price' => 150000, 'category' => 'Creative Design'],
    ['title' => 'Advanced Graphic Design',     'level' => 'advanced',     'price' => 200000, 'category' => 'Creative Design'],
    ['title' => 'Web Design',                  'level' => 'beginner',     'price' => 180000, 'category' => 'Web Technology'],
    ['title' => 'Web Development',             'level' => 'intermediate', 'price' => 250000, 'category' => 'Web Technology'],
    ['title' => 'PHP & MySQL Development',     'level' => 'intermediate', 'price' => 300000, 'category' => 'Web Technology'],
    ['title' => 'Mobile App Development',      'level' => 'intermediate', 'price' => 300000, 'category' => 'Software Development'],
    ['title' => 'UI/UX Design',                'level' => 'intermediate', 'price' => 180000, 'category' => 'Creative Design'],
    ['title' => 'Digital Marketing',           'level' => 'beginner',     'price' => 150000, 'category' => 'Marketing'],
    ['title' => 'Data Analysis',               'level' => 'intermediate', 'price' => 280000, 'category' => 'Data Science'],
    ['title' => 'Cybersecurity Fundamentals',  'level' => 'intermediate', 'price' => 220000, 'category' => 'Security'],
    ['title' => 'Computer Fundamentals',       'level' => 'beginner',     'price' => 100000, 'category' => 'ICT Basics'],
    ['title' => 'Desktop Application Dev',     'level' => 'intermediate', 'price' => 260000, 'category' => 'Software Development'],
    ['title' => 'POS & ICT Support',           'level' => 'beginner',     'price' => 120000, 'category' => 'ICT Basics'],
    ['title' => 'Networking Basics',           'level' => 'beginner',     'price' => 180000, 'category' => 'Networking'],
    ['title' => 'Cloud Computing',             'level' => 'intermediate', 'price' => 320000, 'category' => 'Cloud & DevOps'],
    ['title' => 'Software Engineering',        'level' => 'advanced',     'price' => 350000, 'category' => 'Software Development'],
    ['title' => 'Data Science',                'level' => 'advanced',     'price' => 380000, 'category' => 'Data Science'],
    ['title' => 'Artificial Intelligence (AI)','level' => 'advanced',     'price' => 420000, 'category' => 'AI & ML'],
    ['title' => 'Machine Learning (ML)',       'level' => 'advanced',     'price' => 400000, 'category' => 'AI & ML'],
    ['title' => 'Robotics',                    'level' => 'advanced',     'price' => 200000, 'category' => 'Engineering & Robotics'],
];

// ─────────────────────────────────────────────────────────────
// 2. SCHEME OF WORK TOPIC GENERATORS
// Returns array[6 levels][3 terms][10 weeks] of topic strings
// ─────────────────────────────────────────────────────────────
function buildTopics(string $course): array
{
    $c = strtolower($course);

    // Generic technology scaffolding by level
    $patterns = [
        'JSS1' => [
            '1st' => ['Introduction & History of %s', 'Basic Concepts & Terminology', 'Tools & Equipment Overview', 'Safety Rules & Best Practices', 'Navigating the Learning Environment', 'Simple Exercises: Getting Started', 'Understanding Inputs & Outputs', 'Guided Practice Session 1', 'Guided Practice Session 2', 'Term Assessment & Review'],
            '2nd' => ['Refresher: Core Concepts', 'Step-by-Step Beginner Project', 'Exploring Basic Features', 'Understanding File Management', 'Group Activity: Simple Creation', 'Introduction to Problem Solving', 'Creative Exploration Exercise', 'Peer Review & Feedback', 'Mini Project: Apply What You Learnt', 'Term Test & Corrections'],
            '3rd' => ['Recap of Terms 1 & 2', 'Practical Skill Consolidation', 'Beginner Showcase Project', 'Introduction to Digital Citizenship', 'Online Safety & Ethics', 'Portfolio Setup Basics', 'Simple Presentation Skills', 'Year-End Project Preparation', 'Project Presentation Day', 'JSS 1 Final Assessment'],
        ],
        'JSS2' => [
            '1st' => ['Review of JSS 1 Knowledge', 'Intermediate Concepts in %s', 'Hands-on Tool Mastery', 'Working with Real Examples', 'Problem-Solving Techniques', 'Understanding Workflows', 'Practical Exercise: Build Something', 'Collaboration & Teamwork in Tech', 'Guided Mini Project', 'Term 1 Test'],
            '2nd' => ['Expanding Core Skills', 'Exploring Advanced Features', 'Project-Based Learning: Phase 1', 'Project-Based Learning: Phase 2', 'Debugging & Troubleshooting Basics', 'Creative Thinking in Technology', 'Peer Collaboration Project', 'Introduction to Industry Standards', 'Mini Portfolio Development', 'Term 2 Assessment'],
            '3rd' => ['Cross-Topic Integration', 'Building Full Mini Solutions', 'Presentation & Communication Skills', 'Exploring Careers in %s', 'Community & Society Impact of Tech', 'Comparative Analysis of Tools', 'Practicals: Speed & Accuracy', 'Final Project Build', 'Project Showcase & Defence', 'JSS 2 Cumulative Test'],
        ],
        'JSS3' => [
            '1st' => ['Bridging JSS to SSS Knowledge', 'Advanced Practical Exercises', 'Real-World Applications of %s', 'Introduction to Professional Tools', 'Independent Mini Project', 'Data Literacy Basics', 'Research Methods for Technologists', 'Time Management in Projects', 'Critical Thinking Challenges', 'Term 1 Revision & Test'],
            '2nd' => ['Scholarship & JAMB Preparation Topics', 'WAEC-Level Practical Skills', 'Industry Vocabulary & Definitions', 'Understanding Specifications & Requirements', 'Applied Problem Solving', 'Group Challenge Activity', 'Portfolio Enhancement', 'Presentation of Work to Peers', 'Teacher Feedback & Corrections', 'Term 2 Test'],
            '3rd' => ['Exam Readiness: Theory Revision', 'Exam Readiness: Practical Revision', 'Past Questions Review & Analysis', 'Speed Test & Efficiency Skills', 'Entering Senior Secondary: Orientation', 'Entrepreneurship Basics in Tech', 'Innovation Challenge', 'Final Project Submission', 'BECE/Junior Cert Prep Review', 'JSS 3 Final Comprehensive Test'],
        ],
        'SSS1' => [
            '1st' => ['Welcome to SSS: Raising the Bar', 'Senior-Level Concepts in %s', 'Professional Environment Setup', 'Core Theoretical Foundations', 'Industry-Standard Tools Deep Dive', 'Case Study Analysis', 'Applied Practicals: Real Scenarios', 'Collaborative Team Project Start', 'Individual Assignment Submission', 'First Term Continuous Assessment'],
            '2nd' => ['Advanced Technical Skills', 'Project Lifecycle Introduction', 'Understanding Clients & Requirements', 'Prototyping & Wireframing Concepts', 'Code / Design / Build Review Cycles', 'Presentation of Progress', 'Intermediate Portfolio Update', 'Guest Expert Interaction (Virtual/Live)', 'Evaluation & Self-Assessment', 'Second Term Test'],
            '3rd' => ['Cross-Disciplinary Integration', 'Comprehensive Capstone Project Start', 'Peer Review & Iteration', 'Documenting Your Work', 'Introduction to Entrepreneurship via Tech', 'Building a Professional Portfolio', 'Team Presentations', 'Revision: Key SSS1 Topics', 'Project Defence', 'Year-End Examination'],
        ],
        'SSS2' => [
            '1st' => ['SSS2 Orientation & Goal Setting', 'Advanced Theoretical Concepts', 'Industry Best Practices', 'Architecture & System Design Basics', 'Hands-On Expert-Level Exercises', 'Research Paper / Report Writing', 'Group Project: Sprint 1', 'Group Project: Sprint 2', 'Individual Deep-Dive Assignment', 'Term 1 Evaluation'],
            '2nd' => ['Professional Tool Mastery', 'Security & Ethics in %s', 'Optimisation & Performance', 'Building Scalable Solutions', 'Regulatory Standards Overview', 'Internship Readiness Topics', 'Live Industry Challenge', 'Mentorship / Mock Sessions', 'Portfolio Review', 'Term 2 Comprehensive Test'],
            '3rd' => ['WAEC/NECO Practical Preparation', 'Theory Paper Practice', 'Past Questions & Marking Schemes', 'Time Management in Exams', 'SSS3 Preview: Advanced Capstone Topics', 'Entrepreneurship: Business Plan Basics', 'Mock External Examination', 'Self-Assessment Reflection', 'Graduation Project Proposal', 'End-of-Year Portfolio Submission'],
        ],
        'SSS3' => [
            '1st' => ['Final-Year Orientation & Planning', 'Comprehensive Review of All Key Topics', 'Advanced Capstone Project Kick-Off', 'Interview Preparation Basics', 'University / Polytechnic Admission Topics', 'Professional CV & Portfolio Building', 'Practice: WAEC/NECO Theory Questions', 'Practice: WAEC/NECO Practical Tasks', 'Expert Guest Session', 'First Term Final Test'],
            '2nd' => ['WAEC/NECO Intensive Revision', 'Practical Drills & Speed Testing', 'Group Mock Examination', 'Individual Mock Examination', 'Analysis of Weak Areas', 'Final Capstone Project Completion', 'Project Documentation & Submission', 'Entrepreneurship: Launching a Startup Idea', 'Certificate & Credential Readiness', 'Pre-WAEC/NECO Assessment'],
            '3rd' => ['Final Capstone Defence', 'Peer Evaluation & Feedback', 'Career Pathway Exploration', 'Higher Institution Readiness', 'Scholarship & Funding Opportunities', 'Networking & Professional Growth', 'Industry Certification Pathways', 'Graduation Portfolio Showcase', 'Farewell & Future Planning Session', 'SSS3 Grand Final Examination'],
        ],
    ];

    // Substitute %s with short course name
    $shortName = $course;
    // Map short names for common courses
    $nameMap = [
        'graphic design' => 'Graphic Design',
        'advanced graphic design' => 'Graphic Design',
        'web design' => 'Web Design',
        'web development' => 'Web Dev',
        'php & mysql development' => 'PHP & MySQL',
        'mobile app development' => 'Mobile Dev',
        'ui/ux design' => 'UI/UX',
        'digital marketing' => 'Digital Marketing',
        'data analysis' => 'Data Analysis',
        'cybersecurity fundamentals' => 'Cybersecurity',
        'computer fundamentals' => 'Computing',
        'desktop application dev' => 'Desktop Dev',
        'pos & ict support' => 'POS & ICT',
        'networking basics' => 'Networking',
        'cloud computing' => 'Cloud Computing',
        'software engineering' => 'Software Engineering',
        'data science' => 'Data Science',
        'artificial intelligence (ai)' => 'AI',
        'machine learning (ml)' => 'ML',
        'robotics' => 'Robotics',
    ];
    $shortName = $nameMap[$c] ?? $course;

    // Build result substituting %s
    $result = [];
    foreach ($patterns as $level => $terms) {
        $result[$level] = [];
        foreach ($terms as $term => $weeks) {
            $result[$level][$term] = array_map(fn($t) => sprintf($t, $shortName), $weeks);
        }
    }
    return $result;
}

// ─────────────────────────────────────────────────────────────
// 3. SEED AFFILIATE COURSES
// ─────────────────────────────────────────────────────────────
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

// Clear and re-seed
$pdo->exec('DELETE FROM lms_affiliate_scheme_of_work');
$pdo->exec('ALTER TABLE lms_affiliate_scheme_of_work AUTO_INCREMENT = 1');
$pdo->exec('DELETE FROM lms_affiliate_courses');
$pdo->exec('ALTER TABLE lms_affiliate_courses AUTO_INCREMENT = 1');

$courseInsert = $pdo->prepare("
    INSERT INTO lms_affiliate_courses (title, slug, description, short_description, price, level, category, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE title=VALUES(title), price=VALUES(price), level=VALUES(level), category=VALUES(category)
");

$affiliateCourseIds = [];

foreach ($courseDefs as $def) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $def['title']));
    $slug = trim($slug, '-');
    $desc = "An affiliate course covering " . $def['title'] . " tailored for secondary school students (JSS/SSS) through the Unitary Academy affiliate programme.";
    $short = "Learn " . $def['title'] . " in a structured, class-appropriate curriculum designed for JSS and SSS levels.";

    $courseInsert->execute([$def['title'], $slug, $desc, $short, $def['price'], $def['level'], $def['category']]);
    $id = (int)$pdo->lastInsertId();
    $affiliateCourseIds[$def['title']] = $id;
    echo "  Course: [{$id}] {$def['title']}\n";
}

echo "\nInserted " . count($affiliateCourseIds) . " affiliate courses.\n\n";

// ─────────────────────────────────────────────────────────────
// 4. SEED SCHEME OF WORK WITH REALISTIC WEEKLY OBJECTIVES & ACTIVITIES
// ─────────────────────────────────────────────────────────────
function getProperSowDetails(string $level, string $term, int $weekNum, string $topic, string $courseName): array
{
    $topicLower = strtolower($topic);
    $objectives = [];
    $activities = [];

    // Match topic patterns to generate realistic objectives and activities
    if (strpos($topicLower, 'introduction') !== false || strpos($topicLower, 'history') !== false) {
        $objectives = [
            "Explain the basic definition and historical evolution of " . $courseName . ".",
            "Identify key historical milestones and structural foundations.",
            "Discuss the relevance and applications of " . $courseName . " in modern society."
        ];
        $activities = [
            "Teacher-led presentation showing historical timelines and evolution pathways.",
            "Group session where students discuss and compare historical methods with modern ones.",
            "Formative quiz matching key innovators and milestones with their contributions."
        ];
    } elseif (strpos($topicLower, 'concept') !== false || strpos($topicLower, 'terminology') !== false || strpos($topicLower, 'terms') !== false) {
        $objectives = [
            "Define the core terms and fundamental vocabulary used in " . $courseName . ".",
            "Differentiate between foundational principles and minor components.",
            "Apply terminology accurately to identify key structures or functions."
        ];
        $activities = [
            "Interactive class brainstorming session creating a glossary of key terms.",
            "Guided diagram analysis where students label and define components.",
            "Individual match-up worksheet defining critical principles."
        ];
    } elseif (strpos($topicLower, 'tool') !== false || strpos($topicLower, 'equipment') !== false || strpos($topicLower, 'setup') !== false) {
        $objectives = [
            "Identify primary software/hardware tools and workspace environments.",
            "State the correct use case and function for each interface panel or tool.",
            "Configure, customize, and save a clean workspace environment."
        ];
        $activities = [
            "Live demonstration showing toolbars, panels, configuration settings, and menus.",
            "Hands-on student exercise setting up and customizing personal work environments.",
            "Guided practice troubleshooting common panel visibility or preference errors."
        ];
    } elseif (strpos($topicLower, 'safety') !== false || strpos($topicLower, 'ethics') !== false || strpos($topicLower, 'citizenship') !== false) {
        $objectives = [
            "Outline standard safety rules, ergonomics, and cybersecurity practices.",
            "Explain the importance of online privacy, intellectual property rights, and copyrights.",
            "Demonstrate responsible digital interaction and system maintenance protocols."
        ];
        $activities = [
            "Case study discussion analyzing real examples of security breaches or plagiarism.",
            "Student group project designing a safety and ethics poster for the lab.",
            "Self-assessment quiz on online footprints and safe communication habits."
        ];
    } elseif (strpos($topicLower, 'project') !== false || strpos($topicLower, 'portfolio') !== false || strpos($topicLower, 'capstone') !== false || strpos($topicLower, 'showcase') !== false || strpos($topicLower, 'proposal') !== false || strpos($topicLower, 'submission') !== false) {
        $objectives = [
            "Conceptualize and plan a complete project matching design specifications.",
            "Develop, test, and document the project deliverables.",
            "Present and showcase the project highlights, explaining design/code decisions."
        ];
        $activities = [
            "Project planning session detailing user requirements and milestones.",
            "Independent project build phase with regular instructor checkpoints.",
            "Class presentation and peer feedback evaluation session."
        ];
    } elseif (strpos($topicLower, 'assessment') !== false || strpos($topicLower, 'test') !== false || strpos($topicLower, 'review') !== false || strpos($topicLower, 'revision') !== false || strpos($topicLower, 'readiness') !== false || strpos($topicLower, 'exam') !== false || strpos($topicLower, 'prepar') !== false || strpos($topicLower, 'questions') !== false) {
        $objectives = [
            "Consolidate understanding of the syllabus topics covered during the term.",
            "Analyze previous mistakes, clarify doubts, and resolve theory queries.",
            "Complete a simulated examination under timed test conditions."
        ];
        $activities = [
            "Interactive term review session solving past questions together.",
            "Group quiz competition covering terms, concepts, and calculations.",
            "Timed mock paper assessment followed by model answer corrections."
        ];
    } elseif (strpos($topicLower, 'advanced') !== false || strpos($topicLower, 'expert') !== false || strpos($topicLower, 'intermediate') !== false) {
        $objectives = [
            "Apply advanced techniques and optimizations within " . $courseName . ".",
            "Integrate external libraries, styles, or APIs to enhance output quality.",
            "Analyze and resolve complex logic and styling bottlenecks."
        ];
        $activities = [
            "Interactive walk-through of expert workflows and design patterns.",
            "Lab exercise building a high-fidelity mock or writing complex logic.",
            "Code/design clinic reviewing and refactoring student submissions."
        ];
    } else {
        $objectives = [
            "Explain the principles and theoretical foundation of: " . $topic . ".",
            "Demonstrate practical proficiency by completing hands-on exercises.",
            "Apply best practices to solve real-world problems related to: " . $topic . "."
        ];
        $activities = [
            "Teacher-led explanation breaking down the core concepts of: " . $topic . ".",
            "Guided step-by-step practical session constructing a basic output.",
            "Formative review activity checking student understanding of the topic."
        ];
    }

    $objHtml = "<ul>\n";
    foreach ($objectives as $obj) {
        $objHtml .= "  <li>" . htmlspecialchars($obj) . "</li>\n";
    }
    $objHtml .= "</ul>";

    $actHtml = "<ul>\n";
    foreach ($activities as $act) {
        $actHtml .= "  <li>" . htmlspecialchars($act) . "</li>\n";
    }
    $actHtml .= "</ul>";

    return [
        'objectives' => $objHtml,
        'activities' => $actHtml
    ];
}

$sowInsert = $pdo->prepare("
    INSERT INTO lms_affiliate_scheme_of_work (course_id, class_level, term, week_number, topic, objectives, activities)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$levels = ['JSS1', 'JSS2', 'JSS3', 'SSS1', 'SSS2', 'SSS3'];
$terms  = ['1st', '2nd', '3rd'];

$totalRows = 0;

foreach ($courseDefs as $def) {
    $courseId = $affiliateCourseIds[$def['title']] ?? null;
    if (!$courseId) {
        echo "  SKIP (no ID): {$def['title']}\n";
        continue;
    }

    $topics = buildTopics($def['title']);

    foreach ($levels as $level) {
        foreach ($terms as $term) {
            $weekTopics = $topics[$level][$term] ?? [];
            foreach ($weekTopics as $weekIdx => $topic) {
                $weekNum = $weekIdx + 1;
                $details = getProperSowDetails($level, $term, $weekNum, $topic, $def['title']);
                $objectives = $details['objectives'];
                $activities = $details['activities'];

                $sowInsert->execute([$courseId, $level, $term, $weekNum, $topic, $objectives, $activities]);
                $totalRows++;
            }
        }
    }
    echo "  SOW seeded for: {$def['title']}\n";
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

echo "\n=== Seeding Complete ===\n";
echo "Total Scheme of Work rows inserted: {$totalRows}\n";
echo "Expected: " . (count($courseDefs) * 6 * 3 * 10) . "\n";
