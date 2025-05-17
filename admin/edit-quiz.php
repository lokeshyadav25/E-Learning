<?php
include("../includes/db.php");

$id = $_GET['id'];
$query = "SELECT * FROM quizzes WHERE id = $id";
$result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $course_id = (int)$_POST['course_id'];
    $total_questions = (int)$_POST['total_questions'];

    $update = "UPDATE quizzes SET title='$title', course_id=$course_id, total_questions=$total_questions WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        header("Location: manage-quizzes.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <style>
        body { font-family: Arial; background: #f3f4f6; padding: 20px; }
        form { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { text-align: center; color: #007BFF; }
        label { display: block; margin-top: 15px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Edit Quiz</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Quiz Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
        <label>Course ID:</label>
        <input type="number" name="course_id" value="<?php echo $quiz['course_id']; ?>" required>
        <label>Total Questions:</label>
        <input type="number" name="total_questions" value="<?php echo $quiz['total_questions']; ?>" required>
        <button type="submit">Update Quiz</button>
    </form>
</body>
</html>
