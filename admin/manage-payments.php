<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

// Fetch all payments
$sql = "SELECT * FROM payments ORDER BY payment_date DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching payments: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 25px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        .message {
            padding: 12px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .actions a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            color: white;
            margin: 0 3px;
            display: inline-block;
        }
        .actions a.view {
            background-color: #17a2b8;
        }
        .actions a.delete {
            background-color: #dc3545;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }
        .status.completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status.refunded {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Payments</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Course ID</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['course_id']) ?></td>
                    <td>$<?= number_format($row['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_date']) ?></td>
                    <td>
                        <span class="status <?= strtolower($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="view-payment.php?id=<?= $row['id'] ?>" class="view">View</a>
                        <a href="delete-payment.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this payment?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No payments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
