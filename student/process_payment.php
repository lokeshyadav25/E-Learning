<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id']) || !isset($_POST['course_id'])) {
    http_response_code(403);
    echo "unauthorized";
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = (int)$_POST['course_id'];

$insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $student_id, $course_id);

if ($insert->execute()) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}

$insert->close();
$conn->close();
?>
