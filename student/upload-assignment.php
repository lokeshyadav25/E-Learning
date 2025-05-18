<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['course_id']) || !isset($_FILES['assignment_file'])) {
        die('Invalid request');
    }

    $course_id = (int)$_POST['course_id'];
    $file = $_FILES['assignment_file'];

    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('File upload error. Please try again.');
    }

    // Allowed MIME types
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!in_array($file['type'], $allowedTypes)) {
        die('Only PDF and Word documents are allowed.');
    }

    // Create directory if not exists
    $uploadDir = '../uploads/assignments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $filename = $student_id . '_' . $course_id . '_' . time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($file['name']));
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Save in DB (no original_name column)
        $stmt = $conn->prepare("INSERT INTO assignments (student_id, course_id, filename, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $student_id, $course_id, $filename);
        $stmt->execute();

        $_SESSION['upload_success'] = "Assignment uploaded successfully!";
    } else {
        $_SESSION['upload_error'] = "Failed to move uploaded file.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
