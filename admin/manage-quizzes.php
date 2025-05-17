<?php
session_start();
include("../includes/db.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

// Fetch all quizzes
$query = "SELECT * FROM quizzes";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Quizzes - E-Learning</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px 16px;
            border: 1px solid #ddd;
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
            color: white;
            background-color: #28a745;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 2px;
            display: inline-block;
        }
        .actions a.delete {
            background-color: #dc3545;
        }
        .actions a.questions {
            background-color: #17a2b8;
        }
        .add-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .add-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Quizzes</h1>

        <a href="add-quiz.php" class="add-btn">Add New Quiz</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Quiz Title</th>
                    <th>Course ID</th>
                    <th>Total Questions</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo $row['course_id']; ?></td>
                            <td><?php echo $row['total_questions']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td class="actions">
                                <a href="edit-quiz.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="delete-quiz.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
                                <a href="add-question.php?quiz_id=<?php echo $row['id']; ?>" class="questions">Add Questions</a>
        
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No quizzes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
