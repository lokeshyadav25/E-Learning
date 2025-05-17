<?php
session_start();
include("../includes/db.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin-login.php");
    exit();
}

// Fetch the number of students
$student_query = "SELECT COUNT(*) AS total_students FROM students";
$student_result = $conn->query($student_query);
$student_count = $student_result->fetch_assoc()['total_students'];

// Fetch the number of faculty
$faculty_query = "SELECT COUNT(*) AS total_faculty FROM faculty";
$faculty_result = $conn->query($faculty_query);
$faculty_count = $faculty_result->fetch_assoc()['total_faculty'];

// Fetch the number of courses
$course_query = "SELECT COUNT(*) AS total_courses FROM courses";
$course_result = $conn->query($course_query);
$course_count = $course_result->fetch_assoc()['total_courses'];

// Fetch the number of quizzes
$quiz_query = "SELECT COUNT(*) AS total_quizzes FROM quizzes";
$quiz_result = $conn->query($quiz_query);
$quiz_count = $quiz_result->fetch_assoc()['total_quizzes'];

// Fetch the number of payments
$payment_query = "SELECT COUNT(*) AS total_payments FROM payments";
$payment_result = $conn->query($payment_query);
$payment_count = $payment_result->fetch_assoc()['total_payments'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Vidya</title>
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
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3rem 0;
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
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="rgba(255,255,255,0.03)" fill-rule="evenodd"><rect x="0" y="0" width="30" height="30"/><rect x="30" y="30" width="30" height="30"/></g></svg>');
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .admin-title {
            font-size: 3rem;
            font-weight: 300;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .admin-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .stats-section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2980b9);
            border-radius: 2px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(52, 152, 219, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, #3498db, #2980b9);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
        }

        .stat-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .action-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(52, 152, 219, 0.1);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2980b9);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(52, 152, 219, 0.3);
        }

        .action-icon svg {
            width: 28px;
            height: 28px;
            fill: white;
        }

        .action-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .action-description {
            font-size: 0.95rem;
            color: #7f8c8d;
            line-height: 1.5;
        }

        .logout-section {
            text-align: center;
            padding-top: 2rem;
            border-top: 2px solid #ecf0f1;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 1rem 3rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logout-btn:hover {
            box-shadow: 0 10px 30px rgba(231, 76, 60, 0.4);
            transform: translateY(-3px);
            text-decoration: none;
            color: white;
        }

        @media (max-width: 768px) {
            .admin-title {
                font-size: 2.2rem;
            }
            
            .container {
                padding: 2rem 1rem;
            }
            
            .header-content {
                padding: 0 1rem;
            }
            
            .stats-grid,
            .admin-actions {
                grid-template-columns: 1fr;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }

        .icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Smooth loading animation */
        .stat-number {
            animation: countUp 1s ease-out;
        }

        @keyframes countUp {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="admin-badge">Administrator</div>
            <h1 class="admin-title">Admin Dashboard</h1>
            <p class="admin-subtitle">Comprehensive System Management</p>
        </div>
    </div>

    <div class="container">
        <div class="stats-section">
            <h2 class="section-title">System Overview</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V17C3 18.1 3.9 19 5 19H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V19H19C20.1 19 21 18.1 21 17V9M19 17H5V3H13V9H19Z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $student_count; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M16 4C16.55 4 17 4.45 17 5V8.5L12 6.5L7 8.5V5C7 4.45 7.45 4 8 4H16M18 2H6C4.89 2 4 2.89 4 4V17L12 13.5L20 17V4C20 2.89 19.11 2 18 2M22 18V21H20V18H18L20 16L22 18M9 7V10.5L12 9.25L15 10.5V7H9Z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $faculty_count; ?></div>
                    <div class="stat-label">Faculty Members</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3M5 19V5H19V19H5M7 7H9V9H7V7M11 7H17V9H11V7M7 11H9V13H7V11M11 11H17V13H11V11M7 15H9V17H7V15M11 15H17V17H11V15"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $course_count; ?></div>
                    <div class="stat-label">Active Courses</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M9.5 3C11.71 3 13.5 4.79 13.5 7C13.5 9.21 11.71 11 9.5 11C7.29 11 5.5 9.21 5.5 7C5.5 4.79 7.29 3 9.5 3M14.5 7C14.5 6.23 14.21 5.5 13.76 4.9C15.32 5.06 16.5 6.38 16.5 8C16.5 9.62 15.32 10.94 13.76 11.1C14.21 10.5 14.5 9.77 14.5 9V7M20 17L18.5 18.5L17 17L18.5 15.5L20 17Z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $quiz_count; ?></div>
                    <div class="stat-label">Quiz Assessments</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V17C3 18.1 3.9 19 5 19H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V19H19C20.1 19 21 18.1 21 17V9M19 17H5V3H13V9H19V17M16 11L11 13.5L16 16V11Z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $payment_count; ?></div>
                    <div class="stat-label">Payment Records</div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Management Tools</h2>
        <div class="admin-actions">
            <a href="../admin/manage-students.php" class="action-card">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M16 4C16.55 4 17 4.45 17 5V8.5L12 6.5L7 8.5V5C7 4.45 7.45 4 8 4H16M18 2H6C4.89 2 4 2.89 4 4V17L12 13.5L20 17V4C20 2.89 19.11 2 18 2Z"/>
                    </svg>
                </div>
                <div class="action-title">Manage Students</div>
                <div class="action-description">View, edit, and manage student accounts and enrollments</div>
            </a>

            <a href="../admin/manage-faculty.php" class="action-card">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M16 8C16 10.21 14.21 12 12 12C9.79 12 8 10.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 8M12 14C16.42 14 20 15.79 20 18V20H4V18C4 15.79 7.58 14 12 14Z"/>
                    </svg>
                </div>
                <div class="action-title">Manage Faculty</div>
                <div class="action-description">Oversee faculty profiles and course assignments</div>
            </a>

            <a href="../admin/manage-courses.php" class="action-card">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3M5 19V5H19V19H5Z"/>
                    </svg>
                </div>
                <div class="action-title">Manage Courses</div>
                <div class="action-description">Create, update, and organize course content</div>
            </a>

            <a href="../admin/manage-quizzes.php" class="action-card">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 11H7V13H9V11M13 11H11V13H13V11M17 11H15V13H17V11M19 3H5C3.89 3 3 3.89 3 5V19C3 20.11 3.89 21 5 21H19C20.11 21 21 20.11 21 19V5C21 3.89 20.11 3 19 3M19 19H5V8H19V19Z"/>
                    </svg>
                </div>
                <div class="action-title">Manage Quizzes</div>
                <div class="action-description">Design and monitor quiz assessments</div>
            </a>

            <a href="../admin/manage-payments.php" class="action-card">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 4H4C2.89 4 2 4.89 2 6V18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4M20 18H4V12H20V18M20 8H4V6H20V8Z"/>
                    </svg>
                </div>
                <div class="action-title">Manage Payments</div>
                <div class="action-description">Track transactions and financial records</div>
            </a>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M16 17V14H9V10H16V7L21 12L16 17M14 2C14.6 2 15.2 2.4 15.4 3C15.7 3.5 15.9 4.1 15.9 4.8L16 6H18V8H16V16H18V18H16L15.9 19.2C15.9 19.9 15.7 20.4 15.4 21C15.2 21.5 14.6 22 14 22H4C2.9 22 2 21.1 2 20V4C2 2.9 2.9 2 4 2H14Z"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</body>
</html>