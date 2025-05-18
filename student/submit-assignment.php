<?php
// student/submit-assignment.php
session_start();
include '../includes/db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

// Validate course ID
if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    $_SESSION['error'] = "Invalid course selected.";
    header("Location: dashboard.php");
    exit();
}

$course_id = (int)$_POST['course_id'];

// Check if student is enrolled in this course
$enrollCheck = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$enrollCheck->bind_param("ii", $student_id, $course_id);
$enrollCheck->execute();
$enrolled = $enrollCheck->get_result()->fetch_assoc();

if (!$enrolled) {
    $_SESSION['error'] = "You are not enrolled in this course.";
    header("Location: dashboard.php");
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['assignment']) || $_FILES['assignment']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Error uploading file. Please try again.";
    header("Location: course-content.php?course_id=" . $course_id);
    exit();
}

// Create uploads directory if it doesn't exist
$uploadDir = "../uploads/assignments/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate a unique filename
$fileExtension = pathinfo($_FILES['assignment']['name'], PATHINFO_EXTENSION);
$newFilename = uniqid('assignment_') . '_' . time() . '.' . $fileExtension;
$targetFile = $uploadDir . $newFilename;

// Check file size (limit to 10MB)
if ($_FILES['assignment']['size'] > 10000000) {
    $_SESSION['error'] = "File is too large. Maximum size is 10MB.";
    header("Location: course-content.php?course_id=" . $course_id);
    exit();
}

// Allow certain file formats
$allowedExtensions = array('pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png');
if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
    $_SESSION['error'] = "Sorry, only PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR, JPG, JPEG, PNG files are allowed.";
    header("Location: course-content.php?course_id=" . $course_id);
    exit();
}

// Try to upload file
if (!move_uploaded_file($_FILES['assignment']['tmp_name'], $targetFile)) {
    $_SESSION['error'] = "Error uploading file. Please try again.";
    header("Location: course-content.php?course_id=" . $course_id);
    exit();
}

// Save assignment information to database
$originalFilename = $_FILES['assignment']['name'];
$stmt = $conn->prepare("INSERT INTO assignments (student_id, course_id, filename, original_name, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiss", $student_id, $course_id, $newFilename, $originalFilename);

if ($stmt->execute()) {
    $_SESSION['success'] = "Assignment uploaded successfully!";
} else {
    $_SESSION['error'] = "Error saving assignment information. Please try again.";
    // Delete the uploaded file if database insert fails
    unlink($targetFile);
}

// Redirect back to course content page
header("Location: course-content.php?course_id=" . $course_id);
exit();
?>
