<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db.php';

$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = '2026-06-30 08:00:00';

echo "=== Adding Robotics course to main LMS ===\n";

// 1. INSERT COURSE
$stmt = $pdo->prepare("
    INSERT INTO lms_courses (id, title, slug, description, short_description, price, level, workspace_type, is_active, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'default', 1, ?)
    ON DUPLICATE KEY UPDATE title=VALUES(title), price=VALUES(price), level=VALUES(level)
");
$stmt->execute([
    20, 
    'Robotics', 
    'robotics', 
    'Learn robotics foundations, microcontroller programming, circuit design, sensor integration, and building intelligent physical systems.', 
    'Foundations of robotics, sensors, and hardware programming.', 
    200000.00, 
    'advanced', 
    $ts
]);
echo "Robotics course added to lms_courses.\n";

// 2. INSERT EXAM
$eStmt = $pdo->prepare("
    INSERT INTO lms_exams (id, course_id, title, duration_minutes, total_questions, pass_mark, total_marks, is_published, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
    ON DUPLICATE KEY UPDATE title=VALUES(title), duration_minutes=VALUES(duration_minutes)
");
$eStmt->execute([64, 20, 'Robotics — Final Exam', 40, 10, 50, 10, $ts]);
echo "Robotics exam added to lms_exams.\n";

// 3. INSERT LESSONS
$pdo->prepare("DELETE FROM lms_lessons WHERE course_id = 20")->execute();
$lid = 201; // Start ID for Robotics lessons

function add_lesson(PDO $pdo, int &$lid, int $cid, string $title, string $content, int $sort, string $ts): void {
    $pdo->prepare("INSERT INTO lms_lessons (id,course_id,title,content,sort_order,is_published,created_at) VALUES (?,?,?,?,?,1,?)")
        ->execute([$lid++, $cid, $title, $content, $sort, $ts]);
}

add_lesson($pdo, $lid, 20, "Introduction to Robotics",
'## What is Robotics?

Robotics is an interdisciplinary branch of computer science and engineering focused on creating machines that can perform tasks autonomously or semi-autonomously.

## Core Components of a Robot

1. **Actuators & Effectors**: Muscles that move the robot (motors, solenoids, pneumatic cylinders).
2. **Sensors**: Sensory organs that perceive the environment (cameras, sonar, light sensors).
3. **Controller**: The brain that processes sensor inputs and decides actions (microcontrollers, microprocessors).
4. **Power Source**: Batteries, solar cells, or tethered power supplying energy.

## Robotic Classifications

- **Stationary Robots**: Robotic arms used in automotive manufacturing.
- **Mobile Robots**: Autonomous Guided Vehicles (AGVs) in warehouses.
- **Humanoid Robots**: Designed to mimic human appearance and behavior.
- **Drones / UAVs**: Aerial robots for mapping or delivery.

## Robot Degrees of Freedom (DoF)

Degrees of Freedom (DoF) refer to the number of independent axes or motions a robot can execute. A rigid body in 3D space has 6 DoF: translation along three axes and rotation about three axes (roll, pitch, yaw).

## Practical Task

Research the definition of a robot according to the Robot Institute of America (RIA). Sketch a block diagram showing how inputs (sensors), processing (controller), and outputs (actuators) interact in a modern robotic vacuum cleaner.

## Self-Check
1. What are the four core components of any robot?
2. What is the difference between a sensor and an actuator?
3. Explain the term \'Degrees of Freedom\' (DoF) in robotics.', 1, $ts);

add_lesson($pdo, $lid, 20, "Microcontrollers & Embedded Systems",
'## What is an Embedded System?

An embedded system is a dedicated computer system designed to perform specific control functions within a larger mechanical or electrical system. Unlike general-purpose computers (PCs), embedded systems run custom code with minimal resources.

## Microcontrollers vs Microprocessors

- **Microprocessor (MPU)**: Contains only the CPU (e.g., Intel Core i7). Requires external RAM, ROM, and I/O peripherals. Used in high-performance tasks.
- **Microcontroller (MCU)**: A complete computer on a single chip. Contains CPU, RAM, ROM (flash memory), and I/O pins (e.g., ATmega328P on Arduino Uno). Ideal for direct hardware control.

## Core Concepts: GPIO Pins

General Purpose Input/Output (GPIO) pins allow the microcontroller to interact with hardware:
- **Digital Inputs**: Detect discrete high (5V/3.3V) or low (0V) states (e.g., button presses).
- **Digital Outputs**: Provide high or low states to toggle components (e.g., turning on an LED).
- **Analog Inputs**: Read continuous voltage values using an Analog-to-Digital Converter (ADC) (e.g., reading light levels).

## Embedded Protocols

To communicate with other chips and sensors, MCUs use serial protocols:
- **UART (Universal Asynchronous Receiver-Transmitter)**: Simple point-to-point communication using RX and TX lines.
- **I2C (Inter-Integrated Circuit)**: Uses two lines (SDA, SCL) to connect up to 128 devices on the same bus.
- **SPI (Serial Peripheral Interface)**: Uses four lines for high-speed, full-duplex communication with sensors and SD cards.

## Practical Task

Identify the microcontroller chip on an Arduino Uno board (usually the ATmega328P). Download the datasheet for it and locate the hardware pins for UART (TX/RX), SPI (MOSI/MISO), and I2C (SDA/SCL).

## Self-Check
1. Explain the primary difference between a microprocessor and a microcontroller.
2. What is the function of an Analog-to-Digital Converter (ADC)?
3. Name the two signal lines used in I2C communication.', 2, $ts);

add_lesson($pdo, $lid, 20, "Electronic Circuits & Actuators",
'## Foundations of Electronics

To build robots, you must master electronic circuits. The primary governing rule is **Ohm\'s Law**:

$$V = I \times R$$

Where $V$ is Voltage (Volts), $I$ is Current (Amperes), and $R$ is Resistance (Ohms).

## Actuators: Making Robots Move

Actuators convert electrical energy into mechanical movement:

1. **DC Motors**: Simple rotational motors. Speed depends on voltage; direction changes with polarity. Spin fast with low torque.
2. **Gearmotors**: DC motors combined with gearboxes. They reduce speed but greatly increase torque, allowing the robot to carry weight.
3. **Servo Motors**: Rotary actuators that allow precise control of angular position (typically 0-180 degrees). They contain a DC motor, gears, potentiometer, and a control circuit. Controlled using Pulse-Width Modulation (PWM).
4. **Stepper Motors**: Rotate in precise discrete steps. High precision and holding torque, ideal for 3D printers and CNC machines.
5. **Solenoids**: Linear actuators that push or pull a metal plunger when energized.

## Breadboarding & Circuit Diagrams

A breadboard allows prototyping circuits without soldering. Holes in the side strips (power rails) are connected vertically, while holes in the terminal rows are connected horizontally.

## Practical Task

Calculate the required resistor for an LED connected to a 5V supply, assuming the LED has a forward voltage drop of 2V and requires 20mA ($0.02A$) of current. Draw the circuit schematic using standard electrical symbols.

## Self-Check
1. What is Ohm\'s Law and what does each variable represent?
2. Why are gearboxes added to standard DC motors for mobile robots?
3. How is a servo motor controlled to maintain a specific angle?', 3, $ts);

add_lesson($pdo, $lid, 20, "Sensors & Input Devices",
'## Understanding Sensors

Sensors allow robots to gather information about their environment. They translate physical quantities (like distance, light, temperature) into electrical signals.

## Key Sensors in Mobile Robotics

1. **Ultrasonic Sensor (e.g., HC-SR04)**: Measures distance by sending high-frequency sound waves and timing how long it takes for the echo to return. Distance = (Time * Speed of Sound) / 2.
2. **Infrared (IR) Obstacle Sensor**: Uses an IR emitter LED and receiver photodiode. When an obstacle is close, the IR light reflects back to the receiver.
3. **Light Dependent Resistor (LDR)**: Resistors whose resistance decreases when exposed to light. Used to build light-seeking or shadow-evading robots.
4. **Inertial Measurement Unit (IMU)**: Combines accelerometers and gyroscopes to measure pitch, roll, yaw, and acceleration.
5. **Rotary Encoders**: Sensors attached to motor shafts that count rotation steps to measure distance traveled and speed.

## Analog vs Digital Sensors

- **Digital Sensors**: Output high or low (e.g., limit switch switch is either closed or open).
- **Analog Sensors**: Output variable voltage levels (e.g., LDR voltage divider outputting between 0V and 5V depending on light intensity).

## Practical Task

Write down the formula to calculate distance using an ultrasonic sensor in centimeters, given the speed of sound is $343\text{ m/s}$ ($0.0343\text{ cm/microsecond}$) and the echo pulse duration is in microseconds.

## Self-Check
1. How does an ultrasonic sensor measure distance?
2. What is the difference between an analog sensor and a digital sensor?
3. What is the purpose of an IMU (Inertial Measurement Unit) in robotics?', 4, $ts);

add_lesson($pdo, $lid, 20, "Programming Robots (C++ for Arduino)",
'## Embedded Programming Basics

Arduino programming is based on C++. An Arduino sketch has two main functions:
- `setup()`: Runs once on boot. Used to configure pin modes and initialize libraries.
- `loop()`: Runs continuously. Contains the core logic of the robot.

## Writing Your First Sketch

```cpp
// Blink Built-in LED
void setup() {
  pinMode(13, OUTPUT); // Configure Pin 13 as output
}

void loop() {
  digitalWrite(13, HIGH); // Turn LED on (5V)
  delay(1000);            // Wait 1 second
  digitalWrite(13, LOW);  // Turn LED off (0V)
  delay(1000);            // Wait 1 second
}
```

## Reading Sensor Inputs

```cpp
const int sensorPin = A0; // Analog input pin for LDR
int sensorValue = 0;

void setup() {
  Serial.begin(9600); // Initialize serial monitor communication
}

void loop() {
  sensorValue = analogRead(sensorPin); // Read A0 (returns 0-1023)
  Serial.print("Sensor Reading: ");
  Serial.println(sensorValue);
  delay(200);
}
```

## Controlling Servos

```cpp
#include <Servo.h>

Servo myServo;

void setup() {
  myServo.attach(9); // Connect servo control pin to Pin 9
}

void loop() {
  myServo.write(90);  // Rotate to 90 degrees (middle position)
  delay(1000);
  myServo.write(180); // Rotate to 180 degrees
  delay(1000);
}
```

## Practical Task

Write a complete Arduino C++ sketch that reads an analog input from an LDR on Pin A0. If the light reading drops below 500 (dark), turn on a digital LED output on Pin 8. Otherwise, turn the LED off.

## Self-Check
1. What is the purpose of `Serial.begin(9600)` in Arduino?
2. What is the range of values returned by `analogRead()` on a standard 10-bit Arduino ADC?
3. Explain the difference between `digitalWrite()` and `analogWrite()`.', 5, $ts);

add_lesson($pdo, $lid, 20, "Control Systems & Algorithms",
'## Control Systems in Robotics

A control system manages the behavior of actuators based on sensor inputs to achieve a desired system response. There are two main types:

1. **Open-Loop Control**: Actuation is independent of sensor feedback (e.g., turning a motor on for 5 seconds without checking if the robot actually moved).
2. **Closed-Loop Control**: Actuation is continuously adjusted based on sensor feedback (e.g., checking distance to a wall and slowing down as the robot gets closer).

## Feedback Loops

In a closed-loop system, the **Error** is the difference between the desired target (Setpoint) and the actual measured value (Process Variable).

$$\text{Error} = \text{Setpoint} - \text{Measured Value}$$

## PID Control Algorithm

PID (Proportional-Integral-Derivative) is the most common closed-loop control algorithm:
- **Proportional (P)**: Output is proportional to the current error. ($P_\text{out} = K_p \times e(t)$).
- **Integral (I)**: Output depends on the accumulation of past errors, correcting steady-state offsets.
- **Derivative (D)**: Output predicts future error based on its rate of change, dampening oscillations.

## Line Following Algorithm

A line-following robot uses a sensor array (usually 2 to 5 IR sensors) to detect a black line on a white background:
- Left sensor on line: Turn left.
- Right sensor on line: Turn right.
- Center sensor on line: Drive straight.

## Practical Task

Sketch a flowchart for a line-following robot that uses two IR sensors (Left and Right). Clearly show the decision-making logic for driving forward, turning left, turning right, and stopping.

## Self-Check
1. Explain the difference between open-loop and closed-loop control.
2. What are the three components of a PID controller?
3. How does a line-following sensor array detect a black line on white surface?', 6, $ts);

add_lesson($pdo, $lid, 20, "Power Systems & Motor Drivers",
'## Powering Robots

Power systems are critical in robotics. Actuators draw high currents that microcontrollers cannot handle directly. Connecting motors directly to microcontroller pins will destroy the chip.

## Battery Technologies

- **LiPo (Lithium Polymer)**: High energy density and discharge rates, lightweight. Prone to fire if punctured or overdischarged. Used in drones.
- **Li-Ion (Lithium-Ion)**: Reliable, high capacity, rechargeable (e.g., 18650 cells). Excellent choice for wheeled robots.
- **NiMH (Nickel Metal Hydride)**: Safe and durable, lower energy density.

## H-Bridge & Motor Drivers

To control DC motor direction, you must reverse the voltage polarity. An **H-Bridge** circuit uses four transistors to switch polarity. A motor driver IC (like the **L298N** or **L9110S**) contains H-bridges and current protection.

```
        H-Bridge Concept
            Vcc (Power)
             |     |
          [SW1]   [SW2]
             |--Motor--|
          [SW3]   [SW4]
             |     |
            Gnd (Ground)
```
*SW1 + SW4 Closed: Motor turns forward.*
*SW2 + SW3 Closed: Motor turns backward.*

## Controlling Speed via PWM

Pulse-Width Modulation (PWM) rapidly switches power on and off to simulate variable voltage. The **Duty Cycle** is the percentage of time the signal is ON. A 50% duty cycle runs the motor at roughly half speed.

## Practical Task

Draw a wiring diagram connecting an Arduino, an L298N motor driver module, a 7.4V battery pack, and two DC motors. Indicate where the common ground (GND) connections are placed.

## Self-Check
1. Why can you not connect a DC motor directly to an Arduino pin?
2. How does an H-bridge allow a motor to rotate in both directions?
3. What is a PWM duty cycle and how does it affect motor speed?', 7, $ts);

add_lesson($pdo, $lid, 20, "Capstone: Building a Mobile Robot",
'## Project Brief: Obstacle-Avoiding Robot

You will build a mobile robot that navigates autonomously, using an ultrasonic sensor to scan for walls and steer away from obstacles.

## Bill of Materials (BOM)

- 1x Arduino Uno (Microcontroller)
- 1x 2WD Robot Chassis (Motors, Wheels, Battery Holder)
- 1x L298N Motor Driver Module
- 1x HC-SR04 Ultrasonic Distance Sensor
- 1x SG90 Servo Motor (to rotate sensor)
- 1x 7.4V Li-ion battery pack
- Connecting jumper wires

## Software Design: Obstacle Avoidance Loop

```cpp
#include <Servo.h>

const int trigPin = 12;
const int echoPin = 11;
Servo radarServo;

void setup() {
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  radarServo.attach(10);
  // Initialize motor control pins...
}

void loop() {
  int distance = readDistance();
  if (distance < 20) {
    stopMotors();
    lookAroundAndChoosePath();
  } else {
    driveForward();
  }
  delay(50);
}

int readDistance() {
  digitalWrite(trigPin, LOW); delayMicroseconds(2);
  digitalWrite(trigPin, HIGH); delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  long duration = pulseIn(echoPin, HIGH);
  return duration * 0.0343 / 2;
}
```

## Assembly & Testing Steps

1. **Chassis Assembly**: Mount motors, wheels, battery pack, and caster wheel onto the chassis.
2. **Wiring**: Connect power supply, motor driver, microcontroller, and sensor components securely.
3. **Unit Testing**: Upload separate sketches to test motors (forward/backward) and sensor readings individually.
4. **Integration**: Upload the full obstacle-avoidance control logic, tune threshold distances, and refine steering turn durations.

## Capstone Deliverables

- Working physical or simulated robot
- Complete wiring schematic
- Arduino sketch source code
- 2-minute video showing the robot successfully avoiding 3 obstacles in a maze

## Self-Check
1. Describe the logical steps your robot takes when it detects an obstacle.
2. What safety precautions should you take when charging Li-ion batteries?
3. How would you calibrate the ultrasonic sensor to ignore fake reflections?', 8, $ts);

echo "Robotics lessons added.\n";

// 4. INSERT EXAM QUESTIONS (10 questions for Exam 64)
$pdo->prepare("DELETE FROM lms_exam_questions WHERE exam_id = 64")->execute();
$qid = 2001; // Start ID for Robotics exam questions
$q = $pdo->prepare("INSERT INTO lms_exam_questions (id,exam_id,question,option_a,option_b,option_c,option_d,correct_option,marks,created_at) VALUES (?,?,?,?,?,?,?,?,1,?)");

function Q(PDOStatement $q,int &$qid,int $eid,string $question,string $a,string $b,string $c,string $d,string $correct,string $ts):void{
    $q->execute([$qid++,$eid,$question,$a,$b,$c,$d,$correct,$ts]);
}

Q($q,$qid,64,"Which of the following is an actuator commonly used in robotics?","Ultrasonic sensor","DC motor","Microcontroller","LDR photoresistor","B",$ts);
Q($q,$qid,64,"What does 'Degrees of Freedom' (DoF) refer to in robot kinematics?","The cost of the robot's mechanical joints","The number of independent movements or coordinates a robot can execute","The program memory capacity of the controller","The speed of the robot's main processor","B",$ts);
Q($q,$qid,64,"What is the main advantage of a microcontroller (MCU) over a microprocessor (MPU) for robotic control?","It has higher clock speeds","It is much larger in physical size","It integrates CPU, RAM, ROM, and GPIO pins on a single chip","It does not require programming","C",$ts);
Q($q,$qid,64,"Which serial communication protocol uses exactly two lines (SDA and SCL) to connect multiple peripherals?","UART","SPI","I2C","USB","C",$ts);
Q($q,$qid,64,"According to Ohm's Law, what is the relation between Voltage (V), Current (I), and Resistance (R)?","V = I / R","V = I * R","I = V * R","R = I / V","B",$ts);
Q($q,$qid,64,"What type of motor allows precise control of specific angular positions (e.g., 0 to 180 degrees) using PWM?","DC Motor","Brushless Motor","Servo Motor","Induction Motor","C",$ts);
Q($q,$qid,64,"How does an ultrasonic sensor calculate distance to a wall?","By measuring ambient light variations","By emitting high-frequency sound waves and timing the reflection echo","By detecting temperature changes","By computing electromagnetic field induction","B",$ts);
Q($q,$qid,64,"What are the two mandatory functions in every Arduino sketch?","init() and execute()","start() and run()","setup() and loop()","begin() and repeat()","C",$ts);
Q($q,$qid,64,"What does an H-bridge circuit allow a robot to do?","Convert AC power to DC power","Read analog sensor inputs","Control the direction of a DC motor by reversing its polarity","Scale down battery voltages to 5V","C",$ts);
Q($q,$qid,64,"Which control algorithm uses proportional, integral, and derivative terms to minimize system errors?","BFS","PID","A* search","SMOTE","B",$ts);

// Update exam totals
$cnt = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_questions WHERE exam_id=64")->fetchColumn();
$pdo->exec("UPDATE lms_exams SET total_questions={$cnt}, total_marks={$cnt} WHERE id=64");

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "Robotics exam questions added.\n";
echo "Main LMS Robotics course seeding successfully completed!\n";
