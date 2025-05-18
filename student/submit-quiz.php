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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #dbeafe, #eff6ff); /* Light blue tones */
            color: #1e3a8a; /* Deep blue */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .result-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 50, 0.1);
            text-align: center;
            max-width: 400px;
        }

        h2 {
            color: #2563eb; /* Bright blue */
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h2>Quiz Submitted!</h2>
        <p>You scored <?php echo $score; ?> out of <?php echo $total; ?>.</p>
        <a href="dashboard.php">Return to Dashboard</a>
    </div>
</body>
</html>
