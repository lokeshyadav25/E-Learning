<?php
// student/enroll.php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id']) || !isset($_POST['course_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = intval($_POST['course_id']);

// Check if already enrolled
$check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $student_id, $course_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    // Enroll the student
    $insert_sql = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $student_id, $course_id);
    $insert_stmt->execute();
}

header("Location: dashboard.php");
exit();
