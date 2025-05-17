<?php
include("../includes/db.php");

$quiz_id = $_GET['quiz_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
    $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
    $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
    $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
    $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
    $correct_option = $_POST['correct_option'];

    $query = "INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
              VALUES ($quiz_id, '$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_option')";

    if (mysqli_query($conn, $query)) {
        $message = "Question added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; padding: 20px; }
        form { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-top: 5px; }
        select { width: 100%; padding: 10px; margin-top: 5px; }
        button { margin-top: 20px; width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; }
        .message { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Add Question to Quiz ID: <?php echo $quiz_id; ?></h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <label>Question Text</label>
        <textarea name="question_text" required></textarea>

        <label>Option A</label>
        <input type="text" name="option_a" required>

        <label>Option B</label>
        <input type="text" name="option_b" required>

        <label>Option C</label>
        <input type="text" name="option_c" required>

        <label>Option D</label>
        <input type="text" name="option_d" required>

        <label>Correct Option (A/B/C/D)</label>
        <select name="correct_option" required>
            <option value="">Select</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <button type="submit">Add Question</button>
    </form>
</body>
</html>
