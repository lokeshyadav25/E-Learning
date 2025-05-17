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

// Fetch quiz details to edit - join with courses to check faculty ownership
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

// Handle form submission to update quiz title or description
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

// Handle form submission to add a new question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'] ?? '';
    $option_a = $_POST['option_a'] ?? '';
    $option_b = $_POST['option_b'] ?? '';
    $option_c = $_POST['option_c'] ?? '';
    $option_d = $_POST['option_d'] ?? '';
    $correct_option = $_POST['correct_option'] ?? '';

    if ($question_text && $option_a && $option_b && $correct_option) {
        // Insert into quizzes table — based on your table structure, I assume questions are stored in 'quizzes' table (check this below)
        // But since your earlier quiz structure has question, option_a.. correct_option fields in quizzes, 
        // it means one row per question (so your quiz is multiple rows in quizzes table?)
        // To maintain this structure, new question insert is just a new row in quizzes table linked to same course_id with same title?

        // But that would duplicate quiz title, not ideal.
        // So better to insert question row as a new record in quizzes table with course_id same as original quiz's course_id, question fields filled.

        $insertStmt = $conn->prepare("
            INSERT INTO quizzes 
            (course_id, title, question, option_a, option_b, option_c, option_d, correct_option, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertStmt->bind_param(
            "isssssss",
            $quiz['course_id'],
            $quiz['title'],  // Keep same quiz title for new question
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

// Fetch all questions for this quiz (all rows with same title and course_id)
$questionsStmt = $conn->prepare("
    SELECT * FROM quizzes WHERE course_id = ? AND title = ? ORDER BY created_at ASC
");
$questionsStmt->bind_param("is", $quiz['course_id'], $quiz['title']);
$questionsStmt->execute();
$questionsResult = $questionsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
    h1, h2 { color: #333; }
    form { margin-bottom: 30px; }
    label { display: block; margin: 10px 0 5px; }
    input[type=text], textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
    button { padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .error { color: red; }
    .success { color: green; }
    .question { border-bottom: 1px solid #ddd; padding: 10px 0; }
</style>
</head>
<body>
<div class="container">
    <h1>Edit Quiz: <?= htmlspecialchars($quiz['title']) ?></h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">Quiz updated successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['question_added'])): ?>
        <p class="success">Question added successfully!</p>
    <?php endif; ?>

    <!-- Update quiz details -->
    <form method="post">
        <input type="hidden" name="update_quiz" value="1" />
        <label for="title">Quiz Title</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($quiz['title']) ?>" required />

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>

        <button type="submit">Update Quiz</button>
    </form>

    <h2>Add New Question</h2>
    <form method="post">
        <input type="hidden" name="add_question" value="1" />

        <label for="question_text">Question Text</label>
        <textarea name="question_text" id="question_text" rows="3" required></textarea>

        <label for="option_a">Option A</label>
        <input type="text" name="option_a" id="option_a" required />

        <label for="option_b">Option B</label>
        <input type="text" name="option_b" id="option_b" required />

        <label for="option_c">Option C</label>
        <input type="text" name="option_c" id="option_c" />

        <label for="option_d">Option D</label>
        <input type="text" name="option_d" id="option_d" />

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
                        for ($i = 'a'; $i <= 'd'; $i++) {
                            $optName = "option_$i";
                            $optKey = "option_" . $i;
                            $optValue = $q[$optKey];
                            if (!empty($optValue)) {
                                $correctMark = ($q['correct_option'] === $optKey) ? "&#10004;" : "";
                                echo "<li>" . htmlspecialchars($optValue) . " $correctMark</li>";
                            }
                        }
                    ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <p><a href="manage-quiz.php?course_id=<?= (int)$quiz['course_id'] ?>">Back to Manage Quizzes</a></p>
</div>
</body>
</html>
