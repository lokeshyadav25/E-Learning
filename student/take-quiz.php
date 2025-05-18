<?php 
session_start();
include '../includes/db.php';

// Check if logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student-login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Validate enrollment
$checkEnroll = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$checkEnroll->bind_param("ii", $student_id, $course_id);
$checkEnroll->execute();
$enrollResult = $checkEnroll->get_result();

if ($enrollResult->num_rows === 0) {
    echo "<p class='error'>You are not enrolled in this course.</p>";
    exit();
}

// Fetch quiz
$getQuiz = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ?");
$getQuiz->bind_param("i", $course_id);
$getQuiz->execute();
$quizResult = $getQuiz->get_result();

if ($quizResult->num_rows === 0) {
    echo "<p class='error'>No quiz available for this course.</p>";
    exit();
}

$quiz = $quizResult->fetch_assoc();
$quiz_id = $quiz['id'];

// Fetch questions
$getQuestions = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$getQuestions->bind_param("i", $quiz_id);
$getQuestions->execute();
$questionsResult = $getQuestions->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 2rem;
        }
        .quiz-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .question-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #fefefe;
        }
        .question-card p {
            margin-bottom: 10px;
            font-weight: bold;
        }
        label {
            display: block;
            margin: 5px 0;
            padding-left: 10px;
        }
        .submit-btn {
            display: block;
            margin: 30px auto;
            padding: 12px 30px;
            font-size: 16px;
            background: #2e8b57;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #246b45;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
    <script>
        function confirmSubmission() {
            return confirm("Are you sure you want to submit the quiz?");
        }
    </script>
</head>
<body>

<div class="quiz-container">
    <h1>Quiz for Course ID: <?= htmlspecialchars($course_id) ?></h1>

    <?php if ($questionsResult->num_rows > 0): ?>
        <form method="post" action="submit-quiz.php" onsubmit="return confirmSubmission();">
            <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz_id) ?>">
            <?php
            $qno = 1;
            while ($question = $questionsResult->fetch_assoc()):
                $question_text = htmlspecialchars($question['question_text']);
                $option_a = htmlspecialchars($question['option_a']);
                $option_b = htmlspecialchars($question['option_b']);
                $option_c = htmlspecialchars($question['option_c']);
                $option_d = htmlspecialchars($question['option_d']);
            ?>
                <div class="question-card">
                    <p>Q<?= $qno ?>. <?= $question_text ?></p>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="A" required> A. <?= $option_a ?></label>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="B"> B. <?= $option_b ?></label>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="C"> C. <?= $option_c ?></label>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="D"> D. <?= $option_d ?></label>
                </div>
            <?php
                $qno++;
            endwhile;
            ?>
            <button type="submit" class="submit-btn">Submit Quiz</button>
        </form>
    <?php else: ?>
        <p class="error">No questions found for this quiz.</p>
    <?php endif; ?>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
