<?php
session_start();
include("../includes/db.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

$success = "";
$error = "";

// Fetch all faculty to show in dropdown
$faculty_result = $conn->query("SELECT id, full_name FROM faculty");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $faculty_id = intval($_POST['faculty_id']);
    $price = trim($_POST['price']);

    // Validate inputs
    if ($title === '') {
        $error = "Course title is required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Please enter a valid non-negative price in rupees.";
    } else {
        $price = (int)$price; // convert price to integer
        $stmt = $conn->prepare("INSERT INTO courses (title, description, faculty_id, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $title, $description, $faculty_id, $price);

        if ($stmt->execute()) {
            $success = "Course added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background-color: #f5f5f5; }
        .form-container {
            width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        .message { text-align: center; margin-top: 15px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Course</h2>

        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="title">Course Title</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4"></textarea>

            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
                    <option value="<?php echo $faculty['id']; ?>">
                        <?php echo htmlspecialchars($faculty['full_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="price">Price (₹)</label>
            <input type="number" name="price" id="price" min="0" step="1" value="0" required>

            <button type="submit">Add Course</button>
        </form>
    </div>
</body>
</html>
