<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    die("Invalid Course ID.");
}
$course_id = (int)$_GET['course_id'];

// Check if the course belongs to this faculty
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND faculty_id = ?");
$stmt->bind_param("ii", $course_id, $faculty_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("You do not have permission to upload content for this course.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['content_file']) && $_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/course_content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp = $_FILES['content_file']['tmp_name'];
        $originalName = basename($_FILES['content_file']['name']);
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $newFileName = 'course_' . $course_id . '_' . time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $originalName);

        $allowedExts = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'avi', 'mov', 'zip'];

        if (in_array($fileExt, $allowedExts)) {
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Insert without original_name column
                $insert = $conn->prepare("INSERT INTO course_contents (course_id, filename, uploaded_on) VALUES (?, ?, NOW())");
                $insert->bind_param("is", $course_id, $newFileName);
                $insert->execute();

                $message = "File uploaded successfully!";
            } else {
                $message = "Error moving the uploaded file.";
            }
        } else {
            $message = "Invalid file type. Allowed: " . implode(', ', $allowedExts);
        }
    } else {
        $message = "No file uploaded or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Content - <?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        label, input, button { display: block; width: 100%; margin-top: 10px; }
        button { background: #17a2b8; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 15px; color: green; }
        .error { color: red; }
        a { display: inline-block; margin-top: 15px; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Content for <?= htmlspecialchars($course['title']) ?></h1>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'successfully') !== false ? 'message' : 'error' ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="content_file">Select file to upload:</label>
            <input type="file" name="content_file" id="content_file" required>
            <button type="submit">Upload</button>
        </form>

        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
