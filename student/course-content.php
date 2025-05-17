<?php
// student/course-content.php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Check if student is enrolled in this course
$enrollCheck = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$enrollCheck->bind_param("ii", $student_id, $course_id);
$enrollCheck->execute();
$enrolled = $enrollCheck->get_result()->fetch_assoc();

if (!$enrolled) {
    echo "<p>You are not enrolled in this course.</p>";
    echo '<p><a href="dashboard.php">Go Back</a></p>';
    exit();
}

// Fetch course details
$courseQuery = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$courseQuery->bind_param("i", $course_id);
$courseQuery->execute();
$course = $courseQuery->get_result()->fetch_assoc();

if (!$course) {
    echo "<p>Course not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($course['title']) ?> - Course Content</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($course['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

    <!-- Course content area (video/notes/etc) -->
    <h3>Course Materials</h3>
    <p>This is a placeholder for course content. Add video links, PDFs, etc. here.</p>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
