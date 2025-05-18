<?php
session_start();
include '../includes/db.php';

// Check if faculty logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

if (!isset($_GET['quiz_id']) || !is_numeric($_GET['quiz_id'])) {
    echo "Invalid quiz ID.";
    exit();
}

$quiz_id = (int)$_GET['quiz_id'];

// Fetch quiz details
$stmt = $conn->prepare("
    SELECT q.* 
    FROM quizzes q
    JOIN courses c ON q.course_id = c.id
    WHERE q.id = ? AND c.faculty_id = ?
");
$stmt->bind_param("ii", $quiz_id, $faculty_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    echo "Quiz not found or you don't have permission to edit it.";
    exit();
}

// Handle quiz update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quiz'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    $updateStmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $title, $description, $quiz_id);
    if ($updateStmt->execute()) {
        header("Location: edit-quiz.php?quiz_id=$quiz_id&success=1");
        exit();
    } else {
        $error = "Failed to update quiz.";
    }
}

// Handle add question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'] ?? '';
    $option_a = $_POST['option_a'] ?? '';
    $option_b = $_POST['option_b'] ?? '';
    $option_c = $_POST['option_c'] ?? '';
    $option_d = $_POST['option_d'] ?? '';
    $correct_option = $_POST['correct_option'] ?? '';

    if ($question_text && $option_a && $option_b && $correct_option) {
        $insertStmt = $conn->prepare("
            INSERT INTO quizzes 
            (course_id, title, question, option_a, option_b, option_c, option_d, correct_option, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertStmt->bind_param(
            "isssssss",
            $quiz['course_id'],
            $quiz['title'],
            $question_text,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_option
        );
        if ($insertStmt->execute()) {
            header("Location: edit-quiz.php?quiz_id=$quiz_id&question_added=1");
            exit();
        } else {
            $error = "Failed to add question.";
        }
    } else {
        $error = "Please fill all required fields.";
    }
}

// Fetch questions
$questionsStmt = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ? AND title = ? ORDER BY created_at ASC");
$questionsStmt->bind_param("is", $quiz['course_id'], $quiz['title']);
$questionsStmt->execute();
$questionsResult = $questionsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: linear-gradient(to right, #e3f2fd, #bbdefb);
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    h1, h2 {
        color: #1565c0;
        margin-top: 0;
    }

    form {
        margin-bottom: 40px;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: #333;
    }

    input[type="text"], textarea, select {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, textarea:focus, select:focus {
        border-color: #42a5f5;
        outline: none;
    }

    button {
        margin-top: 20px;
        background: #1976d2;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #1565c0;
    }

    .error {
        color: #d32f2f;
        margin: 10px 0;
    }

    .success {
        color: #2e7d32;
        margin: 10px 0;
    }

    .question {
        background: #f1f8ff;
        padding: 15px;
        border: 1px solid #90caf9;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .question ul {
        padding-left: 20px;
    }

    .question li {
        margin: 4px 0;
    }

    a {
        color: #1976d2;
        text-decoration: none;
        font-weight: 600;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Edit Quiz: <?= htmlspecialchars($quiz['title']) ?></h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">Quiz updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['question_added'])): ?>
        <div class="success">Question added successfully!</div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="update_quiz" value="1">
        <label for="title">Quiz Title</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>

        <button type="submit">Update Quiz</button>
    </form>

    <h2>Add New Question</h2>
    <form method="post">
        <input type="hidden" name="add_question" value="1">

        <label for="question_text">Question Text</label>
        <textarea name="question_text" id="question_text" rows="3" required></textarea>

        <label for="option_a">Option A</label>
        <input type="text" name="option_a" id="option_a" required>

        <label for="option_b">Option B</label>
        <input type="text" name="option_b" id="option_b" required>

        <label for="option_c">Option C</label>
        <input type="text" name="option_c" id="option_c">

        <label for="option_d">Option D</label>
        <input type="text" name="option_d" id="option_d">

        <label for="correct_option">Correct Option</label>
        <select name="correct_option" id="correct_option" required>
            <option value="">Select correct option</option>
            <option value="option_a">Option A</option>
            <option value="option_b">Option B</option>
            <option value="option_c">Option C</option>
            <option value="option_d">Option D</option>
        </select>

        <button type="submit">Add Question</button>
    </form>

    <h2>Existing Questions</h2>
    <?php if ($questionsResult->num_rows === 0): ?>
        <p>No questions added yet.</p>
    <?php else: ?>
        <?php while ($q = $questionsResult->fetch_assoc()): ?>
            <div class="question">
                <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>
                <ul>
                    <?php
                        foreach (['a', 'b', 'c', 'd'] as $opt) {
                            $optKey = "option_$opt";
                            if (!empty($q[$optKey])) {
                                $correct = ($q['correct_option'] === $optKey) ? " ✅" : "";
                                echo "<li>" . htmlspecialchars($q[$optKey]) . "$correct</li>";
                            }
                        }
                    ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <p><a href="manage-quiz.php?course_id=<?= (int)$quiz['course_id'] ?>">⬅ Back to Manage Quizzes</a></p>
</div>
</body>
</html>
