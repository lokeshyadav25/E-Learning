<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Payment ID");
}

$payment_id = intval($_GET['id']);
$sql = "SELECT * FROM payments WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $payment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Payment not found.");
}

$payment = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Payment</title>
    <style>
        body { font-family: Arial; background: #f5f7fa; margin: 0; padding: 40px; }
        .container {
            background: white; padding: 20px 30px; max-width: 600px; margin: auto;
            border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { color: #007BFF; margin-bottom: 20px; }
        table { width: 100%; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .back-btn {
            display: inline-block; margin-top: 20px; background: #007BFF;
            color: white; padding: 10px 15px; border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Details</h2>
        <table>
            <tr><td><strong>ID:</strong></td><td><?= $payment['id'] ?></td></tr>
            <tr><td><strong>Student ID:</strong></td><td><?= $payment['student_id'] ?></td></tr>
            <tr><td><strong>Course ID:</strong></td><td><?= $payment['course_id'] ?></td></tr>
            <tr><td><strong>Amount:</strong></td><td>$<?= number_format($payment['amount'], 2) ?></td></tr>
            <tr><td><strong>Status:</strong></td><td><?= htmlspecialchars($payment['status']) ?></td></tr>
            <tr><td><strong>Date:</strong></td><td><?= $payment['payment_date'] ?></td></tr>
            <tr><td><strong>Transaction ID:</strong></td><td><?= $payment['transaction_id'] ?? 'N/A' ?></td></tr>
        </table>
        <a href="manage-payments.php" class="back-btn">← Back to Payments</a>
    </div>
</body>
</html>
