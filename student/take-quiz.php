<?php
// student/take-quiz.php
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
    echo "<p>You are not enrolled in this course.</p>";
    exit();
}

// Fetch quiz for the course
$getQuiz = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ?");
$getQuiz->bind_param("i", $course_id);
$getQuiz->execute();
$quizResult = $getQuiz->get_result();

if ($quizResult->num_rows === 0) {
    echo "<p>No quiz available for this course.</p>";
    exit();
}

$quiz = $quizResult->fetch_assoc();
$quiz_id = $quiz['id'];

// Fetch questions for the quiz
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
</head>
<body>
    <h1>Quiz for Course ID: <?= htmlspecialchars($course_id) ?></h1>

    <?php if ($questionsResult->num_rows > 0): ?>
        <form method="post" action="submit-quiz.php">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <?php
            $qno = 1;
            while ($question = $questionsResult->fetch_assoc()):
                $question_text = htmlspecialchars($question['question_text']);
                $option_a = htmlspecialchars($question['option_a']);
                $option_b = htmlspecialchars($question['option_b']);
                $option_c = htmlspecialchars($question['option_c']);
                $option_d = htmlspecialchars($question['option_d']);
            ?>
                <div>
                    <p><strong>Q<?= $qno ?>: <?= $question_text ?></strong></p>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="A" required> <?= $option_a ?></label><br>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="B"> <?= $option_b ?></label><br>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="C"> <?= $option_c ?></label><br>
                    <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="D"> <?= $option_d ?></label><br>
                </div>
                <hr>
            <?php
                $qno++;
            endwhile;
            ?>
            <button type="submit">Submit Quiz</button>
        </form>
    <?php else: ?>
        <p>No questions found for this quiz.</p>
    <?php endif; ?>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
