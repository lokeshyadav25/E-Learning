<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in as faculty
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Fetch faculty details
$stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$faculty = $stmt->get_result()->fetch_assoc();

if (!$faculty) {
    echo "<p>Error: Faculty not found.</p>";
    exit();
}

// Fetch courses assigned to this faculty
$coursesStmt = $conn->prepare("SELECT * FROM courses WHERE faculty_id = ?");
$coursesStmt->bind_param("i", $faculty_id);
$coursesStmt->execute();
$coursesResult = $coursesStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Faculty Dashboard - Pragyan</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .profile-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .profile-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 2px;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .profile-item {
            padding: 15px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
        }
        
        .profile-item:hover {
            background: #e2e8f0;
            transform: translateX(5px);
        }
        
        .profile-item strong {
            color: #475569;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .profile-item span {
            color: #334155;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .courses-section {
            margin-top: 30px;
        }
        
        .course-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8, #7c3aed);
        }
        
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }
        
        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        
        .course-title {
            color: #1e293b;
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        
        .course-description {
            color: #64748b;
            margin-bottom: 15px;
            line-height: 1.7;
        }
        
        .course-price {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn.content {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn.content:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        .btn.quiz {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .btn.quiz:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        
        .btn.logout {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .btn.logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .assignments-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }
        
        .assignments-title {
            color: #374151;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .assignments-title::before {
            content: '📋';
            font-size: 1.3rem;
        }
        
        .assignment-card {
            background: #f8fafc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .assignment-card:hover {
            background: #f1f5f9;
            border-color: #3b82f6;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }
        
        .assignment-card h4 {
            margin: 0 0 12px;
            color: #1e293b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .assignment-card h4::before {
            content: '👤';
            font-size: 1.1rem;
        }
        
        .assignment-card p {
            margin: 8px 0;
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .assignment-file {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #dbeafe;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .assignment-file::before {
            content: '📎';
        }
        
        .assignment-file:hover {
            background: #bfdbfe;
            transform: translateY(-2px);
        }
        
        .no-assignments {
            font-style: italic;
            color: #9ca3af;
            text-align: center;
            padding: 30px;
            background: #f9fafb;
            border-radius: 12px;
            border: 2px dashed #d1d5db;
        }
        
        .no-courses {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            color: #6b7280;
            font-size: 1.1rem;
        }
        
        .clear {
            clear: both;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
        }
        
        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .course-card, .profile-section {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .course-card:nth-child(2) { animation-delay: 0.1s; }
        .course-card:nth-child(3) { animation-delay: 0.2s; }
        .course-card:nth-child(4) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($faculty['full_name'] ?? 'Faculty') ?>! 👋</h1>
            <a href="../index.php" class="btn logout">Logout</a>
        </div>

        <div class="profile-section">
            <h2 class="section-title">Your Profile</h2>
            <div class="profile-info">
                <div class="profile-item">
                    <strong>Name</strong>
                    <span><?= htmlspecialchars($faculty['full_name'] ?? '') ?></span>
                </div>
                <div class="profile-item">
                    <strong>Email</strong>
                    <span><?= htmlspecialchars($faculty['email'] ?? '') ?></span>
                </div>
                <!-- Add more profile fields here if needed -->
            </div>
        </div>

        <div class="courses-section">
            <h2 class="section-title">Your Courses</h2>

            <?php if ($coursesResult->num_rows === 0): ?>
                <div class="no-courses">
                    <p>🎓 You have not been assigned any courses yet.</p>
                </div>
            <?php else: ?>
                <?php while ($course = $coursesResult->fetch_assoc()): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h3 class="course-title"><?= htmlspecialchars($course['title'] ?? 'Untitled Course') ?></h3>
                                <p class="course-description"><?= nl2br(htmlspecialchars($course['description'] ?? 'No description')) ?></p>
                                <span class="course-price">₹<?= htmlspecialchars(number_format((float)($course['price'] ?? 0), 2)) ?></span>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="upload-content.php?course_id=<?= (int)($course['id'] ?? 0) ?>" class="btn content">
                                📁 Upload Content
                            </a>
                            <a href="manage-quiz.php?course_id=<?= (int)($course['id'] ?? 0) ?>" class="btn quiz">
                                📝 Manage Quizzes
                            </a>
                        </div>

                        <!-- Assignments Section -->
                        <div class="assignments-section">
                            <h4 class="assignments-title">Submitted Assignments</h4>
                            <?php
                            $assignmentStmt = $conn->prepare("
                                SELECT a.*, s.full_name AS student_name
                                FROM assignments a
                                JOIN students s ON a.student_id = s.id
                                WHERE a.course_id = ?
                                ORDER BY a.uploaded_at DESC
                            ");
                            $assignmentStmt->bind_param("i", $course['id']);
                            $assignmentStmt->execute();
                            $assignmentResult = $assignmentStmt->get_result();

                            if ($assignmentResult->num_rows === 0) {
                                echo '<p class="no-assignments">No assignments submitted yet.</p>';
                            } else {
                                while ($assignment = $assignmentResult->fetch_assoc()):
                            ?>
                                <div class="assignment-card">
                                    <h4>Student: <?= htmlspecialchars($assignment['student_name']) ?></h4>
                                    <p><strong>Submitted At:</strong> <?= htmlspecialchars($assignment['uploaded_at']) ?></p>
                                    <p>
                                        <a href="../uploads/assignments/<?= rawurlencode($assignment['filename']) ?>" class="assignment-file" target="_blank" rel="noopener noreferrer">
                                            <?= htmlspecialchars($assignment['filename']) ?>
                                        </a>
                                    </p>
                                </div>
                            <?php
                                endwhile;
                            }
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="clear"></div>
    </div>
</body>
</html>