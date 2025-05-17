<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage-faculty.php");
    exit();
}

$query = $conn->prepare("DELETE FROM faculty WHERE id = ?");
$query->bind_param("i", $id);
if ($query->execute()) {
    header("Location: manage-faculty.php?success=Faculty deleted successfully.");
} else {
    header("Location: manage-faculty.php?error=Failed to delete faculty.");
}
?>
