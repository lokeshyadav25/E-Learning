<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch courses joined with faculty to get faculty full name, including price
$query = "
    SELECT c.id, c.title, c.description, c.created_at, c.price, f.full_name 
    FROM courses c
    LEFT JOIN faculty f ON c.faculty_id = f.id
";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Courses - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 90%; margin: 20px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a { color: #007BFF; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { padding: 8px 16px; background: #007BFF; color: white; border-radius: 5px; text-decoration: none; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Courses</h1>
        <a href="add-course.php" class="btn">Add New Course</a>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Faculty</th>
                        <th>Price (₹)</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['id']); ?></td>
                            <td><?php echo htmlspecialchars($course['title'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($course['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($course['full_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo number_format((int)$course['price']); ?></td>
                            <td><?php echo htmlspecialchars($course['created_at'] ?? ''); ?></td>
                            <td>
                                <a href="edit-course.php?id=<?php echo $course['id']; ?>">Edit</a> |
                                <a href="delete-course.php?id=<?php echo $course['id']; ?>" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No courses found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
