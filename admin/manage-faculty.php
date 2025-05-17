<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch all faculty members from the database
$query = "SELECT * FROM faculty";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty - Admin Panel</title>
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
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        td a {
            text-decoration: none;
            color: #007BFF;
        }
        td a:hover {
            text-decoration: underline;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Faculty</h1>
        <a href="add-faculty.php" class="btn">Add New Faculty</a>
        
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while ($faculty = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $faculty['id']; ?></td>
                            <td><?php echo htmlspecialchars($faculty['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                            <td>
                                <a href="edit-faculty.php?id=<?php echo $faculty['id']; ?>">Edit</a> |
                                <a href="delete-faculty.php?id=<?php echo $faculty['id']; ?>" onclick="return confirm('Are you sure you want to delete this faculty member?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No faculty members found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
