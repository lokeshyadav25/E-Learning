<?php
// admin/delete-course.php

// Start session if you want to check admin authentication (optional)
// session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

// Include your database connection file
require_once __DIR__ . '/../includes/db.php';


if (!isset($_GET['id'])) {
    // No id provided, redirect or show error
    header('Location: courses.php?error=missing_id');
    exit;
}

$id = $_GET['id'];

// Validate id as integer
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    header('Location: courses.php?error=invalid_id');
    exit;
}

$conn = $conn ?? null; // ensure $conn is available from db.php

if (!$conn) {
    die("Database connection failed.");
}

// Prepare SQL to prevent SQL injection
$stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Success - redirect to courses page with success message
    header('Location: courses.php?message=course_deleted');
} else {
    // Failure - redirect with error
    header('Location: courses.php?error=delete_failed');
}

$stmt->close();
$conn->close();
exit;
