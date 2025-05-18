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
        /* Technologia Themed Styles */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f9ff;
            color: #003366;
            margin: 20px;
        }
        h1 {
            color: #0059b3;
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0, 91, 187, 0.2);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #004080;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1.5px solid #99c2ff;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #0059b3;
            box-shadow: 0 0 8px #80b3ff;
            background-color: #f0f7ff;
        }
        button {
            margin-top: 25px;
            padding: 12px 25px;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error, .success {
            max-width: 600px;
            margin: 15px auto 0;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            box-sizing: border-box;
            text-align: center;
        }
        .error {
            background-color: #ffd6d6;
            color: #b30000;
            border: 1.5px solid #b30000;
        }
        .success {
            background-color: #d6f5d6;
            color: #2d662d;
            border: 1.5px solid #2d662d;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
            transition: color 0.3s ease;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
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
            <?php 
            // Reset pointer for re-use since fetch_assoc was called before
            $faculty_result->data_seek(0);
            while ($faculty = $faculty_result->fetch_assoc()): ?>
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
