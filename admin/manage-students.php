<?php
session_start();
include("../includes/db.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch all students from the database
$query = "SELECT * FROM students";
$result = $conn->query($query);

// Handle Delete Student
if (isset($_GET['delete'])) {
    $student_id = $_GET['delete'];
    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    header("Location: manage-students.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }
        th, td {
            padding: 10px;
        }
        .add-btn, .delete-btn {
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border-radius: 5px;
        }
        .delete-btn {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Students</h1>
        <a href="add-student.php" class="add-btn">Add New Student</a>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($student = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $student['id']; ?></td>
                    <td><?php echo $student['full_name']; ?></td>
                    <td><?php echo $student['email']; ?></td>
                    <td>
                        <a href="edit-student.php?id=<?php echo $student['id']; ?>" class="add-btn">Edit</a>
                        <a href="?delete=<?php echo $student['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
