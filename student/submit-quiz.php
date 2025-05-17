<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
$answers = $_POST['answers'] ?? [];

$score = 0;
$total = 0;

// Fetch correct answers
$getQuestions = $conn->prepare("SELECT id, correct_option FROM quiz_questions WHERE quiz_id = ?");
$getQuestions->bind_param("i", $quiz_id);
$getQuestions->execute();
$result = $getQuestions->get_result();

while ($row = $result->fetch_assoc()) {
    $question_id = $row['id'];
    $correct = strtoupper($row['correct_option']);
    $selected = isset($answers[$question_id]) ? strtoupper($answers[$question_id]) : '';

    if ($selected === $correct) {
        $score++;
    }
    $total++;
}

// You can store score in a `results` table here if needed.

echo "<h2>Quiz Submitted!</h2>";
echo "<p>You scored $score out of $total.</p>";
?>
<p><a href="dashboard.php">Return to Dashboard</a></p>
