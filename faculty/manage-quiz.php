<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Validate course_id from GET
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    echo "Invalid Course ID.";
    exit();
}

$course_id = intval($_GET['course_id']);

// Check if the course belongs to this faculty
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND faculty_id = ?");
$stmt->bind_param("ii", $course_id, $faculty_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    echo "Course not found or you do not have permission to manage it.";
    exit();
}

// Handle quiz deletion
if (isset($_GET['delete_quiz']) && is_numeric($_GET['delete_quiz'])) {
    $quiz_id_to_delete = intval($_GET['delete_quiz']);

    // Check if the quiz belongs to this course
    $checkQuiz = $conn->prepare("SELECT id FROM quizzes WHERE id = ? AND course_id = ?");
    $checkQuiz->bind_param("ii", $quiz_id_to_delete, $course_id);
    $checkQuiz->execute();
    $quizExists = $checkQuiz->get_result()->fetch_assoc();
    $checkQuiz->close();

    if ($quizExists) {
        // Delete related questions first
        $delQuestions = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
        $delQuestions->bind_param("i", $quiz_id_to_delete);
        $delQuestions->execute();
        $delQuestions->close();

        // Then delete the quiz
        $delQuiz = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $delQuiz->bind_param("i", $quiz_id_to_delete);
        if ($delQuiz->execute()) {
            $success = "Quiz deleted successfully.";
        } else {
            $error = "Error deleting quiz.";
        }
        $delQuiz->close();
    } else {
        $error = "Quiz not found or does not belong to this course.";
    }
}

// Handle new quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_title = trim($_POST['quiz_title'] ?? '');
    if ($quiz_title === '') {
        $error = "Quiz title cannot be empty.";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO quizzes (course_id, title, created_on) VALUES (?, ?, NOW())");
        $insertStmt->bind_param("is", $course_id, $quiz_title);
        if ($insertStmt->execute()) {
            $success = "Quiz added successfully.";
        } else {
            $error = "Error adding quiz: " . $conn->error;
        }
        $insertStmt->close();
    }
}

// Fetch quizzes for this course
$quizStmt = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ? ORDER BY created_on DESC");
$quizStmt->bind_param("i", $course_id);
$quizStmt->execute();
$quizzes = $quizStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Quizzes - <?= htmlspecialchars($course['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        h1 { color: #007BFF; }
        form { margin-bottom: 30px; }
        input[type="text"] {
            padding: 8px; width: 80%; border: 1px solid #ccc; border-radius: 4px;
            margin-right: 10px;
        }
        button {
            padding: 8px 16px; background-color: #007BFF; border: none; color: white; border-radius: 4px; cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        ul { list-style-type: none; padding-left: 0; }
        li { padding: 10px; background: #f1f1f1; margin-bottom: 10px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        a { color: #007BFF; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .message { margin-bottom: 20px; padding: 10px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .back-link { display: inline-block; margin-top: 20px; color: #555; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .actions a { margin-left: 10px; color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Quizzes for: <?= htmlspecialchars($course['title']) ?></h1>

        <?php if (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="quiz_title" placeholder="Enter new quiz title" required>
            <button type="submit">Add Quiz</button>
        </form>

        <?php if ($quizzes->num_rows > 0): ?>
            <ul>
                <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                    <li>
                        <div>
                            <strong><a href="edit-quiz.php?quiz_id=<?= $quiz['id'] ?>">
                                <?= htmlspecialchars($quiz['title']) ?>
                            </a></strong>
                            <br>
                            <small>Created on <?= date('d M Y', strtotime($quiz['created_on'])) ?></small>
                        </div>
                        <div class="actions">
                            <a href="?course_id=<?= $course_id ?>&delete_quiz=<?= $quiz['id'] ?>" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No quizzes found for this course yet.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$quizStmt->close();
$conn->close();
?>
