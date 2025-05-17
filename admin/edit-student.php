<?php
session_start();
include("../includes/db.php");

$success = "";
$error = "";

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

// Get student ID from the URL
$student_id = $_GET['id'] ?? null;
if (!$student_id) {
    header("Location: manage-students.php");
    exit();
}

// Fetch student data from the database
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form fields
    if (empty($full_name) || empty($email)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE students SET full_name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $full_name, $email, $hashed_password, $student_id);
        } else {
            $update_query = "UPDATE students SET full_name = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $full_name, $email, $student_id);
        }

        // Execute the update query
        if ($stmt->execute()) {
            $success = "Student details updated successfully!";
        } else {
            $error = "Failed to update student details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 0 auto;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }
        .error {
            background-color: #ffdddd;
            color: red;
        }
        .success {
            background-color: #ddffdd;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Student</h1>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" placeholder="Full Name" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="New Password (Leave empty to keep current)">
            <input type="password" name="confirm_password" placeholder="Confirm New Password">
            <button type="submit">Update Student</button>
        </form>
    </div>
</body>
</html>
