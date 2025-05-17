<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Get course ID from URL
$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header("Location: manage-courses.php");
    exit();
}

// Initialize variables
$title = $description = $faculty_id = "";
$error = "";
$success = "";

// Fetch faculty list for dropdown
$faculty_result = $conn->query("SELECT id, full_name FROM faculty ORDER BY full_name");

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course_result = $stmt->get_result();

if ($course_result->num_rows === 0) {
    // Course not found
    header("Location: manage-courses.php");
    exit();
}

$course = $course_result->fetch_assoc();
$title = $course['title'];
$description = $course['description'];
$faculty_id = $course['faculty_id'];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $faculty_id = $_POST['faculty_id'] ?? null;

    if (!$title || !$description || !$faculty_id) {
        $error = "All fields are required.";
    } else {
        // Update course in DB
        $update_stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, faculty_id = ? WHERE id = ?");
        $update_stmt->bind_param("ssii", $title, $description, $faculty_id, $course_id);

        if ($update_stmt->execute()) {
            $success = "Course updated successfully.";
            // Redirect to manage courses after 2 seconds
            header("refresh:2;url=manage-courses.php");
        } else {
            $error = "Error updating course: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Course - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; margin: auto; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], textarea, select {
            width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;
        }
        button {
            margin-top: 20px; padding: 10px 20px; background-color: #007BFF; border: none; color: white; border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
        a { display: inline-block; margin-top: 15px; text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <h1>Edit Course</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="title">Course Title</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>

        <label for="faculty_id">Faculty</label>
        <select name="faculty_id" id="faculty_id" required>
            <option value="">-- Select Faculty --</option>
            <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                <option value="<?php echo $faculty['id']; ?>" <?php echo ($faculty['id'] == $faculty_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($faculty['full_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Update Course</button>
    </form>

    <a href="manage-courses.php">← Back to Manage Courses</a>

</body>
</html>
