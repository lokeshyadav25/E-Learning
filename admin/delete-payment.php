<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php'; // Adjust path if needed

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-payments.php?message=Payment ID is missing");
    exit();
}

$payment_id = intval($_GET['id']); // sanitize input

// Delete payment record
$sql = "DELETE FROM payments WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $payment_id);
if (mysqli_stmt_execute($stmt)) {
    header("Location: manage-payments.php?message=Payment deleted successfully");
    exit();
} else {
    header("Location: manage-payments.php?message=Failed to delete payment");
    exit();
}
