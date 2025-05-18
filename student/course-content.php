<?php 
// student/course-content.php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Check if student is enrolled in this course
$enrollCheck = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$enrollCheck->bind_param("ii", $student_id, $course_id);
$enrollCheck->execute();
$enrolled = $enrollCheck->get_result()->fetch_assoc();

if (!$enrolled) {
    echo "<p>You are not enrolled in this course.</p>";
    echo '<p><a href="dashboard.php">Go Back</a></p>';
    exit();
}

// Fetch course details
$courseQuery = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$courseQuery->bind_param("i", $course_id);
$courseQuery->execute();
$course = $courseQuery->get_result()->fetch_assoc();

if (!$course) {
    echo "<p>Course not found.</p>";
    exit();
}

// Fetch course contents
$contentQuery = $conn->prepare("SELECT * FROM course_contents WHERE course_id = ? ORDER BY uploaded_on DESC");
$contentQuery->bind_param("i", $course_id);
$contentQuery->execute();
$contents = $contentQuery->get_result();

// Fetch assignments submitted by this student for this course
$assignmentQuery = $conn->prepare("SELECT a.*, s.full_name AS student_name FROM assignments a JOIN students s ON a.student_id = s.id WHERE a.course_id = ? AND a.student_id = ? ORDER BY a.uploaded_at DESC");
$assignmentQuery->bind_param("ii", $course_id, $student_id);
$assignmentQuery->execute();
$assignments = $assignmentQuery->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($course['title']) ?> - Course Content</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        /* Your existing styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h1 {
            font-weight: 700;
            font-size: 2.5rem;
            color: #1e293b;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        p.description {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 30px;
            white-space: pre-wrap;
        }

        h3 {
            font-weight: 600;
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h3::before {
            content: '📚';
            font-size: 1.5rem;
        }

        ul.contents-list {
            list-style: none;
            padding-left: 0;
        }

        ul.contents-list li {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 12px;
            border-left: 6px solid #3b82f6;
            transition: background 0.3s ease;
        }

        ul.contents-list li:hover {
            background: #e0e7ff;
        }

        ul.contents-list li a {
            color: #1e293b;
            font-weight: 600;
            text-decoration: none;
            font-size: 1.05rem;
            display: inline-block;
            vertical-align: middle;
        }

        ul.contents-list li small {
            display: block;
            color: #64748b;
            margin-top: 5px;
            font-weight: 400;
            font-size: 0.85rem;
        }

        .assignment-card {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 15px;
            border-left: 6px solid #6366f1;
        }

        .assignment-card h4 {
            margin-bottom: 8px;
            color: #4b5563;
        }

        .assignment-card p {
            margin-bottom: 6px;
            color: #374151;
        }

        .assignment-card a.assignment-file {
            font-weight: 600;
            color: #4338ca;
            text-decoration: none;
        }

        .assignment-card a.assignment-file:hover {
            text-decoration: underline;
        }

        a.back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        a.back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }
        
        /* Assignment upload form styles */
        .assignment-upload-form {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 6px solid #10b981;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4b5563;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
        }
        
        .submit-btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($course['title']) ?></h1>
        <p class="description"><?= nl2br(htmlspecialchars($course['description'])) ?></p>

        <h3>Course Materials</h3>

        <?php if ($contents->num_rows > 0): ?>
            <ul class="contents-list">
                <?php while ($row = $contents->fetch_assoc()): ?>
                    <li>
                        📄 
                        <a href="../uploads/course_content/<?= htmlspecialchars($row['filename']) ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($row['original_name'] ?: $row['filename']) ?>
                        </a>
                        <small>Uploaded on <?= date('d M Y, H:i', strtotime($row['uploaded_on'])) ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No course materials uploaded yet.</p>
        <?php endif; ?>

        <h3>Submit Assignment</h3>
        <div class="assignment-upload-form">
            <form action="submit-assignment.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                <div class="form-group">
                    <label for="assignment-file">Select file to upload:</label>
                    <input type="file" name="assignment" id="assignment-file" required>
                </div>
                <button type="submit" class="submit-btn">Upload Assignment</button>
            </form>
        </div>

        <h3>Your Assignments</h3>

        <?php if ($assignments->num_rows > 0): ?>
            <?php while ($assignment = $assignments->fetch_assoc()): ?>
                <div class="assignment-card">
                    <h4>Student: <?= htmlspecialchars($assignment['student_name']) ?></h4>
                    <p><strong>Submitted At:</strong> <?= date('d M Y, H:i', strtotime($assignment['uploaded_at'])) ?></p>
                    <p>
                        <a href="../uploads/assignments/<?= rawurlencode($assignment['filename']) ?>" class="assignment-file" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($assignment['filename']) ?>
                        </a>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No assignments submitted yet for this course.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>
