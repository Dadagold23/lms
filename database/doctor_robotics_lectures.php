<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db.php';

echo "=== Doctoring Robotics Lecture Materials (180 Weeks) ===\n";

$courseId = 20; // Robotics

// Verify course exists
$stmtCourse = $pdo->prepare("SELECT id FROM lms_affiliate_courses WHERE id = ?");
$stmtCourse->execute([$courseId]);
if (!$stmtCourse->fetch()) {
    echo "Error: Robotics course not found in lms_affiliate_courses table.\n";
    exit(1);
}

// Fetch all SOW rows for Robotics
$stmtSow = $pdo->prepare("
    SELECT id, class_level, term, week_number, topic, objectives, activities 
    FROM lms_affiliate_scheme_of_work 
    WHERE course_id = ?
    ORDER BY class_level, term, week_number
");
$stmtSow->execute([$courseId]);
$sows = $stmtSow->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($sows) . " scheme of work entries to doctor.\n";

$updateStmt = $pdo->prepare("
    UPDATE lms_affiliate_scheme_of_work
    SET lecture_content = ?, quiz_json = ?
    WHERE id = ?
");

$successCount = 0;

foreach ($sows as $sow) {
    $id = (int)$sow['id'];
    $level = $sow['class_level'];
    $term = $sow['term'];
    $week = (int)$sow['week_number'];
    $topic = $sow['topic'];
    $objectives = $sow['objectives'];
    $activities = $sow['activities'];
    
    $topicLower = strtolower($topic);
    $isSenior = strpos($level, 'SSS') !== false;
    
    $lecture = '';
    $quiz = [];
    
    // Generator Logic based on Topic content
    if (strpos($topicLower, 'introduction') !== false || strpos($topicLower, 'welcome') !== false || strpos($topicLower, 'orientation') !== false) {
        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "Welcome to this specialized lecture on **{$topic}** for {$level} students.\n\n"
                 . "#### Lesson Notes & Core Concepts\n"
                 . "- **Definition of Robotics**: Robotics is the branch of technology that deals with the design, construction, operation, and application of robots.\n"
                 . "- **The Sense-Think-Act Loop**: The fundamental cycle of a robotic system. Sensors interact with the environment (Sense), the microcontroller processes the inputs and makes decisions (Think), and motors or actuators perform the physical motion (Act).\n"
                 . "- **Types of Robots**: Fixed robotic arms (industrial manipulators), mobile wheeled robots, aerial drones, and humanoid systems.\n\n"
                 . "#### Technical Details\n"
                 . "Robots bridge the gap between virtual software and the physical world. In this curriculum track, we focus on both mechanical structures and embedded microcontroller programming to control sensors and actuators.\n\n"
                 . "#### Classroom Practical Activity\n"
                 . "Identify 3 automated machines in your school or home (e.g., microwave, washing machine) and discuss if they qualify as robots under the autonomous Sense-Think-Act classification.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Choose the correct option.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'What is the correct order of the fundamental robotic control cycle?',
                    'option_a' => 'Act, Think, Sense',
                    'option_b' => 'Sense, Think, Act',
                    'option_c' => 'Think, Sense, Act',
                    'option_d' => 'Power, Connect, Move',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'Which component acts as the "brain" of a robot?',
                    'option_a' => 'An actuator',
                    'option_b' => 'A battery pack',
                    'option_c' => 'A microcontroller',
                    'option_d' => 'A bumper switch',
                    'correct_option' => 'C'
                ],
                [
                    'question' => 'Where does the word "robot" come from?',
                    'option_a' => 'Greek word for metal machine',
                    'option_b' => 'Czech word for forced labor',
                    'option_c' => 'Latin word for rotation',
                    'option_d' => 'French word for calculation',
                    'correct_option' => 'B'
                ]
            ]
        ];
    } elseif (strpos($topicLower, 'concept') !== false || strpos($topicLower, 'theory') !== false || strpos($topicLower, 'principle') !== false) {
        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "In this lesson, we explore the core mathematical and scientific concepts behind **{$topic}**.\n\n"
                 . "#### Key Principles\n"
                 . "- **Ohm\'s Law**: Dictates circuit behavior. voltage is equal to current multiplied by resistance (\$V = I \times R). This governs how we power LED status indicators and motor systems.\n"
                 . "- **Kinematics**: The study of motion without considering forces. In robotics, we study forward kinematics (calculating end effector position from joint angles) and inverse kinematics (calculating joint angles to reach a specific point).\n"
                 . "- **Degrees of Freedom (DoF)**: The number of independent ways a mechanical structure can move.\n\n"
                 . "#### Educational Insights\n"
                 . "For mobile robots, understanding the friction between wheels and surfaces is crucial. Seniors will study PWM (Pulse-Width Modulation) to control DC motor velocities precisely.\n\n"
                 . "#### Practical Lab Work\n"
                 . "Measure voltage drop across a resistor using a digital multimeter. Verify the theoretical calculation from Ohm\'s Law.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Answer these conceptual questions.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'According to Ohm\'s Law, if Voltage is 5V and Resistance is 250 ohms, what is the Current?',
                    'option_a' => '0.02 Amperes (20mA)',
                    'option_b' => '50 Amperes',
                    'option_c' => '1.25 Amperes',
                    'option_d' => '0.5 Amperes',
                    'correct_option' => 'A'
                ],
                [
                    'question' => 'What is the term for calculating joint angles needed to position a robot arm?',
                    'option_a' => 'Inverse Kinematics',
                    'option_b' => 'Forward Kinematics',
                    'option_c' => 'Differential Friction',
                    'option_d' => 'Joint Actuation',
                    'correct_option' => 'A'
                ],
                [
                    'question' => 'How many Degrees of Freedom (DoF) does a rigid body have in 3D space?',
                    'option_a' => '3',
                    'option_b' => '4',
                    'option_c' => '6',
                    'option_d' => '8',
                    'correct_option' => 'C'
                ]
            ]
        ];
    } elseif (strpos($topicLower, 'hardware') !== false || strpos($topicLower, 'sensor') !== false || strpos($topicLower, 'circuit') !== false || strpos($topicLower, 'tool') !== false) {
        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "This lesson explores the physical hardware, circuits, and sensors utilized in modern robotic assemblies.\n\n"
                 . "#### Core Hardware Elements\n"
                 . "- **Breadboard Prototyping**: Allows solderless wiring of components. Power rails run vertically along the edges; tie points run horizontally.\n"
                 . "- **Ultrasonic Sensors (HC-SR04)**: Emit high-frequency sound waves and measure the echo return delay to calculate distance.\n"
                 . "- **Actuators (DC Motors & Servos)**: DC motors provide continuous rotation, while servos provide precise angular positioning (typically 0-180 degrees).\n"
                 . "- **H-Bridge Driver**: Integrated circuits like the L298N that allow microcontrollers to drive motors in both directions safely.\n\n"
                 . "#### Safety Guidelines\n"
                 . "Always check polarities on capacitors and power sources. Short-circuiting battery packs (especially LiPo cells) can result in fire or component damage.\n\n"
                 . "#### Lab Exercise\n"
                 . "Wire an LED, a protective 220-ohm resistor, and a push-button switch in series on a breadboard. Power the circuit with a 5V supply and verify its operation.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Verify your understanding of hardware connections.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'Why is a motor driver (like L298N) needed between a microcontroller and a DC motor?',
                    'option_a' => 'To translate digital values into text commands',
                    'option_b' => 'Because motors require more current than microcontroller GPIO pins can safely supply',
                    'option_c' => 'To convert DC power to AC power',
                    'option_d' => 'To measure motor speed',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'Which sensor calculates distance by measuring sound reflection time?',
                    'option_a' => 'LDR Photoresistor',
                    'option_b' => 'Ultrasonic Sensor',
                    'option_c' => 'Infrared sensor',
                    'option_d' => 'Inertial Measurement Unit (IMU)',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'How are the tie points in a standard breadboard terminal row electrically connected?',
                    'option_a' => 'Vertically',
                    'option_b' => 'Horizontally (groups of 5)',
                    'option_c' => 'Diagonally',
                    'option_d' => 'They are not connected',
                    'correct_option' => 'B'
                ]
            ]
        ];
    } elseif (strpos($topicLower, 'programming') !== false || strpos($topicLower, 'code') !== false || strpos($topicLower, 'software') !== false || strpos($topicLower, 'logic') !== false) {
        $codeExample = $isSenior 
            ? "```cpp\nvoid setup() {\n  pinMode(9, OUTPUT);\n}\nvoid loop() {\n  // Speed control via PWM (duty cycle 128/255 = ~50%)\n  analogWrite(9, 128);\n  delay(2000);\n  analogWrite(9, 0);\n  delay(1000);\n}\n```"
            : "```cpp\nvoid setup() {\n  pinMode(13, OUTPUT);\n}\nvoid loop() {\n  digitalWrite(13, HIGH);\n  delay(1000);\n  digitalWrite(13, LOW);\n  delay(1000);\n}\n```";

        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "Programming bridges logical statements and physical movements. Today we study microcontroller logic and code structure.\n\n"
                 . "#### Programming Concepts\n"
                 . "- **Structure of a Sketch**: Arduino programs require `setup()` (runs once at startup) and `loop()` (runs continuously).\n"
                 . "- **Digital vs Analog I/O**: `digitalWrite()` toggles pins between 0V and 5V. `analogWrite()` uses Pulse-Width Modulation (PWM) to output variable simulated voltages.\n"
                 . "- **Conditional Statements**: Using `if` conditions to decide action based on sensor values (e.g., if distance is less than 20cm, stop motors).\n\n"
                 . "#### Sample Code\n"
                 . "Here is a standard C++ snippet for controlling a hardware peripheral:\n\n"
                 . "{$codeExample}\n\n"
                 . "#### Practical Task\n"
                 . "Write an algorithm flowchart representing a line-following robot. Define steps for when the left sensor, center sensor, or right sensor detects the line.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Test your logic and embedded coding skills.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'Which function in an Arduino sketch runs once on power-up to initialize pins?',
                    'option_a' => 'loop()',
                    'option_b' => 'setup()',
                    'option_c' => 'main()',
                    'option_d' => 'config()',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'How does a microcontroller output variable motor speeds if its pins can only output 0V or 5V?',
                    'option_a' => 'Using a digital switch',
                    'option_b' => 'Using Pulse-Width Modulation (PWM) to toggle pins rapidly',
                    'option_c' => 'Using a step-up transformer',
                    'option_d' => 'By changing the battery voltage',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'What is the typical range of integer values passed into analogWrite() to represent duty cycle?',
                    'option_a' => '0 to 1023',
                    'option_b' => '0 to 100',
                    'option_c' => '0 to 255',
                    'option_d' => '-127 to 127',
                    'correct_option' => 'C'
                ]
            ]
        ];
    } elseif (strpos($topicLower, 'project') !== false || strpos($topicLower, 'capstone') !== false || strpos($topicLower, 'build') !== false || strpos($topicLower, 'portfolio') !== false || strpos($topicLower, 'showcase') !== false || strpos($topicLower, 'proposal') !== false) {
        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "This module covers project implementation, hardware integration, structural assembly, and portfolio documentation.\n\n"
                 . "#### Engineering Method\n"
                 . "Building complex systems requires a step-by-step approach:\n"
                 . "1. **Design & Schematic**: Sketching structural frames and power/signal wiring routes.\n"
                 . "2. **BOM (Bill of Materials)**: Listing every required motor, screw, chip, and wire.\n"
                 . "3. **Unit Testing**: Verifying components individually before complete final assembly.\n"
                 . "4. **System Tuning**: Tweaking software delay thresholds and sensor alignments to optimize accuracy.\n\n"
                 . "#### Presentation Best Practices\n"
                 . "When showing a robot, document its operation with clear videos, system diagrams, and structural annotations. Explain how failure points during assembly were identified and debugged.\n\n"
                 . "#### Activity\n"
                 . "Form teams, assign roles (Project Manager, Hardware Lead, Software Lead), and write a project charter for a robotic vehicle.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Questions on project construction methodologies.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'What is a Bill of Materials (BOM)?',
                    'option_a' => 'An electrical diagram',
                    'option_b' => 'A comprehensive list of parts, tools, and components required for a project',
                    'option_c' => 'A software source code printout',
                    'option_d' => 'An invoice from a supplier',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'What is "Unit Testing" in mechanical assembly?',
                    'option_a' => 'Testing everything only at the very end',
                    'option_b' => 'Testing individual components or modules separately before final system integration',
                    'option_c' => 'Testing under extreme weather conditions',
                    'option_d' => 'Writing documentation sheets',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'Why is documentation critical in robotic projects?',
                    'option_a' => 'It allows reproducibility and helps trace hardware bugs',
                    'option_b' => 'It speeds up motor speeds',
                    'option_c' => 'It supplies auxiliary power',
                    'option_d' => 'It eliminates the need for programming',
                    'correct_option' => 'A'
                ]
            ]
        ];
    } else { // Revision, tests, review, exams, and default topics
        $lecture = "### 📚 Topic: {$topic}\n\n"
                 . "This session is designed to consolidate knowledge, prepare for certifications, and review cumulative theoretical concepts.\n\n"
                 . "#### Core Syllabus Summary\n"
                 . "- **Robotic Systems**: Review components (actuators, microcontrollers, sensors, power supplies).\n"
                 . "- **Logic Controls**: Review conditional states, loops, sensors thresholds, and algorithm control systems like PID.\n"
                 . "- **Circuit Analysis**: Review voltage dividers, Multimeter measurements, and resistor color codes.\n\n"
                 . "#### Exam Preparation Tips\n"
                 . "Pay close attention to schematic symbols (LED, Ground, Resistor, Battery). Practice tracing current loops to identify short circuits quickly during exams.\n\n"
                 . "#### Classroom Practice\n"
                 . "Solve sample multiple-choice questions in teams. Present solutions for circuit calculations on the whiteboard.";
                 
        $quiz = [
            'title' => "{$topic} Quiz",
            'instructions' => 'Demonstrate cumulative topic understanding.',
            'pass_score' => 50,
            'questions' => [
                [
                    'question' => 'Which device measures voltage, current, and resistance in a circuit?',
                    'option_a' => 'Breadboard',
                    'option_b' => 'H-Bridge',
                    'option_c' => 'Digital Multimeter (DMM)',
                    'option_d' => 'Servo driver',
                    'correct_option' => 'C'
                ],
                [
                    'question' => 'What is the function of a resistor in series with an LED?',
                    'option_a' => 'To increase LED brightness',
                    'option_b' => 'To limit current flow and protect the LED from burning out',
                    'option_c' => 'To store electricity',
                    'option_d' => 'To reverse current direction',
                    'correct_option' => 'B'
                ],
                [
                    'question' => 'Which component acts as the main sensory input for an obstacle-avoiding mobile robot?',
                    'option_a' => 'LiPo Battery Pack',
                    'option_b' => 'L298N Motor Driver',
                    'option_c' => 'HC-SR04 Ultrasonic Distance Sensor',
                    'option_d' => 'DC Geared Motor',
                    'correct_option' => 'C'
                ]
            ]
        ];
    }
    
    // Save to DB
    $quizJson = json_encode($quiz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $updateStmt->execute([$lecture, $quizJson, $id]);
    $successCount++;
}

echo "Successfully doctored {$successCount}/180 scheme of work entries with real lecture materials and quizzes!\n";
echo "=== Doctoring Complete ===\n";
