<?php
// view-questions.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

$quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

$query = "SELECT * FROM quizzes WHERE id = $quiz_id";
$quiz_result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($quiz_result);

$q_query = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id";
$questions_result = mysqli_query($conn, $q_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Questions - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 900px; margin: auto; }
        h2 { color: #007BFF; }
        .question { margin-bottom: 25px; }
        .options { margin-left: 20px; }
        .correct { color: green; font-weight: bold; }
        a.btn { text-decoration: none; background: #007BFF; color: #fff; padding: 8px 12px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Questions for Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h2>
        <a href="../admin/manage-quizzes.php" class="btn">Back to Manage Quizzes</a>
        <hr>

        <?php $i = 1; while ($row = mysqli_fetch_assoc($questions_result)): ?>
            <div class="question">
                <strong>Q<?php echo $i++; ?>: <?php echo htmlspecialchars($row['question_text']); ?></strong>
                <div class="options">
                    <div>A) <?php echo htmlspecialchars($row['option_a']); ?></div>
                    <div>B) <?php echo htmlspecialchars($row['option_b']); ?></div>
                    <div>C) <?php echo htmlspecialchars($row['option_c']); ?></div>
                    <div>D) <?php echo htmlspecialchars($row['option_d']); ?></div>
                </div>
                <div class="correct">Correct Answer: <?php echo strtoupper($row['correct_option']); ?></div>
            </div>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($questions_result) === 0): ?>
            <p>No questions found for this quiz.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<!-- take-quiz.php -->
<?php
// Assume student is logged in with $_SESSION['student_id']
session_start();
include("../includes/db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: /student-login.php");
    exit();
}

$quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

$q_query = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id";
$questions_result = mysqli_query($conn, $q_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score = 0;
    $total = count($_POST['answers']);
    
    foreach ($_POST['answers'] as $question_id => $selected) {
        $check = "SELECT correct_option FROM quiz_questions WHERE id = $question_id";
        $res = mysqli_query($conn, $check);
        $row = mysqli_fetch_assoc($res);
        if (strtolower($row['correct_option']) == strtolower($selected)) {
            $score++;
        }
    }
    echo "<h2>Your Score: $score / $total</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 900px; margin: auto; }
        .question { margin-bottom: 20px; }
        h2 { color: #007BFF; }
        input[type="radio"] { margin-right: 10px; }
        .submit-btn { background: #28a745; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Take Quiz</h2>
        <form method="post">
            <?php while ($row = mysqli_fetch_assoc($questions_result)): ?>
                <div class="question">
                    <strong><?php echo htmlspecialchars($row['question_text']); ?></strong><br>
                    <input type="radio" name="answers[<?php echo $row['id']; ?>]" value="a"> A) <?php echo htmlspecialchars($row['option_a']); ?><br>
                    <input type="radio" name="answers[<?php echo $row['id']; ?>]" value="b"> B) <?php echo htmlspecialchars($row['option_b']); ?><br>
                    <input type="radio" name="answers[<?php echo $row['id']; ?>]" value="c"> C) <?php echo htmlspecialchars($row['option_c']); ?><br>
                    <input type="radio" name="answers[<?php echo $row['id']; ?>]" value="d"> D) <?php echo htmlspecialchars($row['option_d']); ?><br>
                </div>
            <?php endwhile; ?>

            <button type="submit" class="submit-btn">Submit Quiz</button>
        </form>
    </div>
</body>
</html>
