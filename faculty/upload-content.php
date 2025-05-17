<?php
session_start();
include '../includes/db.php';

// Check faculty login
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Validate course_id from GET
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    die("Invalid Course ID.");
}
$course_id = (int)$_GET['course_id'];

// Verify that the course belongs to this faculty
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND faculty_id = ?");
$stmt->bind_param("ii", $course_id, $faculty_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("You do not have permission to upload content for this course.");
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['content_file']) && $_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/course_content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['content_file']['tmp_name'];
        $fileName = basename($_FILES['content_file']['name']);
        $fileSize = $_FILES['content_file']['size'];
        $fileType = $_FILES['content_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Allowed file extensions (adjust as needed)
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'avi', 'mov', 'zip'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert into database
                $insertStmt = $conn->prepare("INSERT INTO course_contents (course_id, filename, original_name, uploaded_on) VALUES (?, ?, ?, NOW())");
                $insertStmt->bind_param("iss", $course_id, $newFileName, $fileName);
                if ($insertStmt->execute()) {
                    $message = "File uploaded successfully!";
                } else {
                    $message = "Database error: Could not save file info.";
                }
            } else {
                $message = "Error moving the uploaded file.";
            }
        } else {
            $message = "Upload failed. Allowed file types: " . implode(", ", $allowedExtensions);
        }
    } else {
        $message = "No file uploaded or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Upload Content - <?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 0 8px rgba(0,0,0,0.1);}
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
