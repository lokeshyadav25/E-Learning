<?php
// view-results.php
session_start();
include '../includes/db.php';

// Check login
if (!isset($_SESSION['student_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Admin or Student
$is_admin = isset($_SESSION['admin_id']);
$student_id = $_SESSION['student_id'] ?? null;

if ($is_admin) {
    // Admin: fetch all results
    $query = "SELECT r.*, s.name AS student_name, c.course_name
              FROM results r
              JOIN students s ON r.student_id = s.id
              JOIN courses c ON r.course_id = c.id
              ORDER BY r.submitted_at DESC";
    $stmt = $conn->prepare($query);
} else {
    // Student: fetch only their results
    $query = "SELECT r.*, c.course_name
              FROM results r
              JOIN courses c ON r.course_id = c.id
              WHERE r.student_id = ?
              ORDER BY r.submitted_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Quiz Results</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1><?= $is_admin ? 'All Students\' Quiz Results' : 'My Quiz Results' ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <?php if ($is_admin): ?>
                    <th>Student</th>
                <?php endif; ?>
                <th>Course</th>
                <th>Score</th>
                <th>Total</th>
                <th>Submitted At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <?php if ($is_admin): ?>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= $row['score'] ?></td>
                    <td><?= $row['total_questions'] ?></td>
                    <td><?= $row['submitted_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No quiz results found.</p>
    <?php endif; ?>

    <p><a href="<?= $is_admin ? '../admin/dashboard.php' : 'dashboard.php' ?>">Back to Dashboard</a></p>
</body>
</html>
