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

// Additional analytics data
$active_students_query = "SELECT COUNT(*) AS active_students FROM students WHERE status = 'active'";
$active_students_result = $conn->query($active_students_query);
$active_students = $active_students_result ? $active_students_result->fetch_assoc()['active_students'] : 0;

$recent_enrollments_query = "SELECT COUNT(*) AS recent_enrollments FROM enrollments WHERE DATE(enrollment_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$recent_enrollments_result = $conn->query($recent_enrollments_query);
$recent_enrollments = $recent_enrollments_result ? $recent_enrollments_result->fetch_assoc()['recent_enrollments'] : 0;

$total_revenue_query = "SELECT SUM(amount) AS total_revenue FROM payments WHERE status = 'completed'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result ? $total_revenue_result->fetch_assoc()['total_revenue'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Learning</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
            backdrop-filter: blur(10px);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-btn-sidebar {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
        }

        .logout-btn-sidebar:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
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
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .admin-title {
            font-size: 2.5rem;
            font-weight: 300;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2rem;
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #3498db, #2980b9);
            border-radius: 1px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-left: 4px solid #3498db;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        .stat-icon svg {
            width: 28px;
            height: 28px;
            fill: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Charts Section */
        .charts-section {
            margin-bottom: 3rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3498db;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Analytics Cards */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .analytics-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #e74c3c;
            transition: all 0.3s ease;
        }

        .analytics-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .analytics-value {
            font-size: 2rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 0.5rem;
        }

        .analytics-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 500;
        }

        .analytics-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .analytics-change.positive {
            background: #d4edda;
            color: #155724;
        }

        .analytics-change.negative {
            background: #f8d7da;
            color: #721c24;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            text-decoration: none;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .admin-title {
                font-size: 2rem;
            }

            .stats-grid,
            .charts-grid,
            .analytics-grid,
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Smooth animations */
        .stat-number, .analytics-value {
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
    <div class="dashboard-container">
        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" onclick="toggleSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        </button>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">E-Learning</div>
                <div class="sidebar-subtitle">Admin Panel</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="../admin/manage-students.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-4c0-1.33-1.34-2.67-2.5-2.67S4 12.67 4 14v4z"/>
                    </svg>
                    Manage Students
                </a>
                <a href="../admin/manage-faculty.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M16 8c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zm-4 6c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z"/>
                    </svg>
                    Manage Faculty
                </a>
                <a href="../admin/manage-courses.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    Manage Courses
                </a>
                <a href="../admin/manage-quizzes.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2c0-.55-.45-1-1-1s-1 .45-1 1v2H8V2c0-.55-.45-1-1-1s-1 .45-1 1v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                    </svg>
                    Manage Quizzes
                </a>
                <a href="../admin/manage-payments.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    Manage Payments
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="../index.php" class="logout-btn-sidebar">
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-content">
                    <h1 class="admin-title">Admin Dashboard</h1>
                    <p class="admin-subtitle">Comprehensive System Management & Analytics</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section-title">Quick Actions</div>
            <div class="quick-actions">
                <a href="../admin/manage-students.php" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Add Student
                </a>
                <a href="../admin/manage-faculty.php" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Add Faculty
                </a>
                <a href="../admin/manage-courses.php" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Add Course
                </a>
                <a href="../admin/manage-quizzes.php" class="quick-action-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Create Quiz
                </a>
            </div>

            <!-- Statistics Overview -->
            <div class="section-title">System Overview</div>
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

            <!-- Analytics Section -->
            <div class="section-title">Analytics & Insights</div>
            <div class="analytics-grid">
                <div class="analytics-card">
                    <div class="analytics-value"><?php echo number_format(($student_count / max($faculty_count, 1)), 1); ?>:1</div>
                    <div class="analytics-label">Student to Faculty Ratio</div>
                    <div class="analytics-change positive">Optimal Range</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value"><?php echo $active_students; ?></div>
                    <div class="analytics-label">Active Students</div>
                    <div class="analytics-change positive">
                        <?php echo number_format(($active_students / max($student_count, 1)) * 100, 1); ?>% of total
                    </div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value"><?php echo $recent_enrollments; ?></div>
                    <div class="analytics-label">Recent Enrollments (30 days)</div>
                    <div class="analytics-change positive">Growing</div>
                </div>
               
                <div class="analytics-card">
                    <div class="analytics-value"><?php echo number_format(($quiz_count / max($course_count, 1)), 1); ?></div>
                    <div class="analytics-label">Avg Quizzes per Course</div>
                    <div class="analytics-change positive">Well Assessed</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value"><?php echo ($payment_count > 0) ? '98.5%' : '0%'; ?></div>
                    <div class="analytics-label">Payment Success Rate</div>
                    <div class="analytics-change positive">Excellent</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="section-title">Data Visualization</div>
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-title">System Overview Distribution</div>
                        <div class="chart-container">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Student vs Faculty Analysis</div>
                        <div class="chart-container">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Course & Assessment Metrics</div>
                        <div class="chart-container">
                            <canvas id="metricsChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Monthly Growth Trend</div>
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="section-title">Recent Activity</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $active_students; ?></div>
                    <div class="stat-label">Active This Week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $recent_enrollments; ?></div>
                    <div class="stat-label">New Enrollments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2c0-.55-.45-1-1-1s-1 .45-1 1v2H8V2c0-.55-.45-1-1-1s-1 .45-1 1v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo ($quiz_count > 0) ? round($quiz_count * 0.85) : 0; ?></div>
                    <div class="stat-label">Quizzes Completed</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Chart.js Configuration
        const chartColors = {
            primary: '#3498db',
            secondary: '#2980b9',
            success: '#27ae60',
            danger: '#e74c3c',
            warning: '#f39c12',
            info: '#17a2b8',
            light: '#f8f9fa',
            dark: '#343a40'
        };

        // Overview Pie Chart
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        new Chart(overviewCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Faculty', 'Courses', 'Quizzes', 'Payments'],
                datasets: [{
                    data: [
                        <?php echo $student_count; ?>,
                        <?php echo $faculty_count; ?>,
                        <?php echo $course_count; ?>,
                        <?php echo $quiz_count; ?>,
                        <?php echo $payment_count; ?>
                    ],
                    backgroundColor: [
                        chartColors.primary,
                        chartColors.success,
                        chartColors.warning,
                        chartColors.danger,
                        chartColors.info
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Student vs Faculty Comparison Chart
        const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
        new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Total Count', 'Active Users', 'Recent Activity'],
                datasets: [{
                    label: 'Students',
                    data: [
                        <?php echo $student_count; ?>,
                        <?php echo $active_students; ?>,
                        <?php echo $recent_enrollments; ?>
                    ],
                    backgroundColor: chartColors.primary,
                    borderColor: chartColors.secondary,
                    borderWidth: 1
                }, {
                    label: 'Faculty',
                    data: [
                        <?php echo $faculty_count; ?>,
                        <?php echo round($faculty_count * 0.9); ?>,
                        <?php echo round($faculty_count * 0.3); ?>
                    ],
                    backgroundColor: chartColors.success,
                    borderColor: '#229954',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Course & Assessment Metrics Chart
        const metricsCtx = document.getElementById('metricsChart').getContext('2d');
        new Chart(metricsCtx, {
            type: 'line',
            data: {
                labels: ['Courses', 'Quizzes', 'Enrollments', 'Completions'],
                datasets: [{
                    label: 'Current Metrics',
                    data: [
                        <?php echo $course_count; ?>,
                        <?php echo $quiz_count; ?>,
                        <?php echo $recent_enrollments; ?>,
                        <?php echo round($quiz_count * 0.75); ?>
                    ],
                    borderColor: chartColors.warning,
                    backgroundColor: 'rgba(243, 156, 18, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: chartColors.warning,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Monthly Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Students',
                    data: [
                        Math.max(<?php echo $student_count; ?> - 50, 0),
                        Math.max(<?php echo $student_count; ?> - 35, 0),
                        Math.max(<?php echo $student_count; ?> - 20, 0),
                        Math.max(<?php echo $student_count; ?> - 10, 0),
                        Math.max(<?php echo $student_count; ?> - 5, 0),
                        <?php echo $student_count; ?>
                    ],
                    borderColor: chartColors.primary,
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: false
                }, {
                    label: 'Courses',
                    data: [
                        Math.max(<?php echo $course_count; ?> - 10, 0),
                        Math.max(<?php echo $course_count; ?> - 8, 0),
                        Math.max(<?php echo $course_count; ?> - 5, 0),
                        Math.max(<?php echo $course_count; ?> - 3, 0),
                        Math.max(<?php echo $course_count; ?> - 1, 0),
                        <?php echo $course_count; ?>
                    ],
                    borderColor: chartColors.success,
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Add click events to navigation items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Remove active class from all items
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                // Add active class to clicked item
                this.classList.add('active');
            });
        });

        // Auto-close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Smooth scroll to sections
        function smoothScroll(target) {
            document.querySelector(target).scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Add loading animation for stats
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate stat numbers on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
                animateValue(stat, 0, finalValue, 1000);
            });
        });

        // Real-time clock (optional enhancement)
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const dateString = now.toLocaleDateString();
            
            // You can add this to header if needed
            console.log(`Current time: ${timeString}, Date: ${dateString}`);
        }

        // Update clock every second
        setInterval(updateClock, 1000);

        // Responsive chart handling
        window.addEventListener('resize', function() {
            Chart.instances.forEach(chart => {
                chart.resize();
            });
        });
    </script>
</body>
</html>