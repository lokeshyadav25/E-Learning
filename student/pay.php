<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$course = null;
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "<p>Invalid course selected.</p>";
    exit();
}

// Simulate payment success (for now)
$insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $student_id, $course_id);

if ($insert->execute()) {
    echo "<p>Payment successful! You are now enrolled in <strong>" . htmlspecialchars($course['title']) . "</strong>.</p>";
    echo '<p><a href="dashboard.php">Go to Dashboard</a></p>';
} else {
    echo "<p>Error enrolling in course.</p>";
}
?>
