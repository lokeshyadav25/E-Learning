<?php
session_start();
include("../includes/db.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Optional: session timeout after 30 min inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin-login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// Handle Delete Student (with prepared statement)
if (isset($_GET['delete'])) {
    $student_id = (int)$_GET['delete']; // cast to int for safety
    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();

    // Redirect with success message
    header("Location: manage-students.php?deleted=1");
    exit();
}

// Fetch all students securely with prepared statement
$query = "SELECT id, full_name, email FROM students ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Students - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0; padding: 20px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #007BFF;
        }
        .btn, .add-btn, .delete-btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px 5px 15px 0;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .add-btn, .btn {
            background-color: #007BFF;
            color: #fff;
        }
        .add-btn:hover, .btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }
        .delete-btn:hover {
            background-color: #a71d2a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead th {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Students</h1>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="message">Student deleted successfully.</div>
        <?php endif; ?>

        <a href="add-student.php" class="add-btn btn">Add New Student</a>

        <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td>
                            <a href="edit-student.php?id=<?php echo urlencode($student['id']); ?>" class="btn add-btn">Edit</a>
                            <a href="?delete=<?php echo urlencode($student['id']); ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No students found.</p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
