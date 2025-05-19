<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo "<p>Error: Student not found.</p>";
    exit();
}

// Fetch all courses
$allCourses = $conn->query("SELECT * FROM courses");

// Fetch enrolled courses for the student
$enrolledStmt = $conn->prepare("SELECT course_id FROM enrollments WHERE student_id = ?");
$enrolledStmt->bind_param("i", $student_id);
$enrolledStmt->execute();
$enrolledResult = $enrolledStmt->get_result();

$enrolledCourses = [];
while ($row = $enrolledResult->fetch_assoc()) {
    $enrolledCourses[] = $row['course_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard - Pragyan</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 0;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="rgba(255,255,255,0.05)" fill-rule="evenodd"><circle cx="5" cy="5" r="5"/><circle cx="25" cy="5" r="5"/><circle cx="5" cy="25" r="5"/><circle cx="25" cy="25" r="5"/></g></svg>');
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 300;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #3498db, #2980b9);
            border-radius: 2px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .course-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(52, 152, 219, 0.1);
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2980b9);
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .course-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .course-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #27ae60;
            margin-bottom: 1.5rem;
        }

        .course-price span {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn.enroll {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn.enroll:hover {
            box-shadow: 0 6px 16px rgba(52, 152, 219, 0.4);
            transform: translateY(-2px);
        }

        .btn.quiz {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }

        .btn.quiz:hover {
            box-shadow: 0 6px 16px rgba(243, 156, 18, 0.4);
            transform: translateY(-2px);
        }

        .btn.disabled {
            background: #ecf0f1;
            color: #95a5a6;
            pointer-events: none;
            box-shadow: none;
        }

        .upload-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px dashed #bdc3c7;
            transition: all 0.3s ease;
        }

        .upload-section:hover {
            border-color: #3498db;
            background: #f0f8ff;
        }

        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .upload-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .file-input {
            padding: 0.75rem;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            background: white;
            font-family: 'Segoe UI', sans-serif;
            transition: border-color 0.3s ease;
        }

        .file-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn.submit {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
            align-self: flex-start;
        }

        .btn.submit:hover {
            box-shadow: 0 6px 16px rgba(39, 174, 96, 0.4);
            transform: translateY(-2px);
        }

        .message {
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            font-weight: 500;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .message::before {
            content: '';
            width: 4px;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 2px;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .success::before {
            background-color: #4caf50;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .error::before {
            background-color: #f44336;
        }

        .logout-section {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #ecf0f1;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .logout-btn:hover {
            box-shadow: 0 6px 16px rgba(231, 76, 60, 0.4);
            transform: translateY(-2px);
        }

        .enrolled-badge {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .course-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .container {
                padding: 2rem 1rem;
            }
            
            .header-content {
                padding: 0 1rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }

        .icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1 class="welcome-title">Welcome, <?= htmlspecialchars($student['full_name'] ?? 'Student') ?>!</h1>
            <p class="welcome-subtitle">Your learning journey awaits</p>
        </div>
    </div>

    <div class="container">
        <!-- Show upload success/error messages -->
        <?php if (isset($_SESSION['upload_success'])): ?>
            <div class="message success">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <?= htmlspecialchars($_SESSION['upload_success']) ?>
            </div>
            <?php unset($_SESSION['upload_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['upload_error'])): ?>
            <div class="message error">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <?= htmlspecialchars($_SESSION['upload_error']) ?>
            </div>
            <?php unset($_SESSION['upload_error']); ?>
        <?php endif; ?>

        <h2 class="section-title">Available Courses</h2>

        <div class="course-grid">
            <?php while ($course = $allCourses->fetch_assoc()): ?>
                <div class="course-card">
                    <?php if (in_array($course['id'], $enrolledCourses)): ?>
                        <span class="enrolled-badge">Enrolled</span>
                    <?php endif; ?>
                    
                    <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                    <p class="course-description"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                    <div class="course-price">
                        <span>Price:</span> ₹<?= htmlspecialchars(number_format((float)$course['price'], 2)) ?>
                    </div>

                    <?php if (in_array($course['id'], $enrolledCourses)): ?>
                        <div class="btn-group">
                            <a href="course-content.php?course_id=<?= (int)$course['id'] ?>" class="btn enroll">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                                View Content
                            </a>
                            <a href="take-quiz.php?course_id=<?= (int)$course['id'] ?>" class="btn quiz">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                                Take Quiz
                            </a>
                        </div>

                        <!-- Assignment Upload Form -->
                        <div class="upload-section">
                            <form
                                class="upload-form"
                                action="upload-assignment.php"
                                method="POST"
                                enctype="multipart/form-data"
                            >
                                <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
                                <label for="assignment_<?= (int)$course['id'] ?>" class="upload-label">
                                    <svg class="icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; margin-right: 8px;">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                    Upload Assignment
                                </label>
                                <input
                                    type="file"
                                    name="assignment_file"
                                    id="assignment_<?= (int)$course['id'] ?>"
                                    class="file-input"
                                    accept=".pdf,.doc,.docx"
                                    required
                                />
                                <button type="submit" class="btn submit">
                                    <svg class="icon" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                    Submit Assignment
                                </button>
                            </form>
                        </div>

                    <?php else: ?>
                        <div class="btn-group">
                            <a href="pay.php?course_id=<?= $course['id'] ?>&showpaytm=1" class="btn enroll">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                                </svg>
                                Enroll & Pay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <svg class="icon" viewBox="0 0 24 24" style="margin-right: 8px;">
                    <path d="M16 17V14H9V10H16V7L21 12L16 17M14 2C14.6 2 15.2 2.4 15.4 3C15.7 3.5 15.9 4.1 15.9 4.8L16 6H18V8H16V16H18V18H16L15.9 19.2C15.9 19.9 15.7 20.4 15.4 21C15.2 21.5 14.6 22 14 22H4C2.9 22 2 21.1 2 20V4C2 2.9 2.9 2 4 2H14Z"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</body>
</html>