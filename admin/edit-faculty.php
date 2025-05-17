<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

$success = "";
$error = "";
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage-faculty.php");
    exit();
}

$faculty = null;
$query = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $faculty = $result->fetch_assoc();
} else {
    $error = "Faculty not found.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($full_name) || empty($email)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $faculty['password'];

        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM faculty WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $stmt = $conn->prepare("UPDATE faculty SET full_name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $full_name, $email, $hashed_password, $id);
            if ($stmt->execute()) {
                $success = "Faculty details updated successfully!";
            } else {
                $error = "Failed to update faculty. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Faculty</h1>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($faculty): ?>
        <form method="POST">
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($faculty['full_name']); ?>" placeholder="Full Name" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="New Password (leave empty to keep current)">
            <button type="submit">Update Faculty</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
