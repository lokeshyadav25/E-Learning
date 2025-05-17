<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

// Fetch courses from the database
$sql = "SELECT * FROM courses ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .actions a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            color: white;
            margin: 0 3px;
            display: inline-block;
        }

        .actions a.edit {
            background-color: #28a745;
        }

        .actions a.delete {
            background-color: #dc3545;
        }

        .message {
            max-width: 1000px;
            margin: 20px auto;
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Courses</h1>

        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['description'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['created_at'] ?? 'N/A') ?></td>
                            <td class="actions">
                                <a href="edit-course.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                                <a href="delete-course.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No courses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
