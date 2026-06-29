<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

// AJAX endpoint: GET ?course_id=X&class_level=JSS1&term=1st
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['instructor'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
    exit;
}

$courseId   = (int)($_GET['course_id'] ?? 0);
$classLevel = trim($_GET['class_level'] ?? '');
$term       = trim($_GET['term'] ?? '');

$allowedLevels = ['JSS1','JSS2','JSS3','SSS1','SSS2','SSS3'];
$allowedTerms  = ['1st','2nd','3rd'];

if ($courseId <= 0 || !in_array($classLevel, $allowedLevels, true) || !in_array($term, $allowedTerms, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

try {
    // Verify course exists
    $stmtCourse = $pdo->prepare("SELECT title FROM lms_affiliate_courses WHERE id = ? AND is_active = 1");
    $stmtCourse->execute([$courseId]);
    $courseTitle = $stmtCourse->fetchColumn();
    if (!$courseTitle) {
        echo json_encode(['success' => false, 'message' => 'Course not found.']);
        exit;
    }

    // Fetch scheme of work
    $stmt = $pdo->prepare("
        SELECT week_number, topic, objectives, activities
        FROM lms_affiliate_scheme_of_work
        WHERE course_id = ? AND class_level = ? AND term = ?
        ORDER BY week_number ASC
    ");
    $stmt->execute([$courseId, $classLevel, $term]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'      => true,
        'course_title' => $courseTitle,
        'class_level'  => $classLevel,
        'term'         => $term,
        'rows'         => $rows,
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
