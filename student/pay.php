<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$course = null;
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "<p style='font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; color:#d32f2f; text-align:center; padding:20px;'>Invalid course selected.</p>";
    exit();
}

// Simulate payment success (for now)
$insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $student_id, $course_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Enroll Course - Technologia</title>
<style>
    /* Technologia theme & Segoe UI font */
    @import url('https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap');

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f0f5fa;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
        color: #1a237e; /* Deep Indigo */
    }
    .container {
        background: #fff;
        padding: 2rem 3rem;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(26, 35, 126, 0.15);
        max-width: 450px;
        text-align: center;
        width: 90%;
    }
    h1 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: #0d47a1; /* Bright Blue */
    }
    p {
        font-size: 1.1rem;
        margin: 1rem 0;
        color: #333;
    }
    strong {
        color: #1565c0;
    }
    a.button {
        display: inline-block;
        background-color: #1976d2;
        color: #fff;
        padding: 12px 28px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 1.5rem;
        transition: background-color 0.3s ease;
        box-shadow: 0 6px 12px rgba(25, 118, 210, 0.3);
    }
    a.button:hover {
        background-color: #1565c0;
        box-shadow: 0 8px 16px rgba(21, 101, 192, 0.5);
    }
    .error {
        color: #d32f2f;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 1.5rem;
    }
</style>
</head>
<body>
    <div class="container">
        <?php if ($insert->execute()): ?>
            <h1>Payment Successful!</h1>
            <p>You are now enrolled in <strong><?php echo htmlspecialchars($course['title']); ?></strong>.</p>
            <a href="dashboard.php" class="button">Go to Dashboard</a>
        <?php else: ?>
            <p class="error">Error enrolling in course. Please try again later.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$stmt->close();
$insert->close();
$conn->close();
?>
