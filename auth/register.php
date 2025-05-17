<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../includes/db.php");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($date_of_birth) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $error = "Please enter a valid phone number (10-15 digits).";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Combine first and last name for full_name
        $full_name = $first_name . ' ' . $last_name;
        
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert new student
            $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, full_name, email, phone, date_of_birth, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssss", $first_name, $last_name, $full_name, $email, $phone, $date_of_birth, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
                // Optionally redirect to login page after a delay
                // header("refresh:3;url=../index.php");
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration | E-Learning Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4ade80;
            --white: #ffffff;
            --radius: 12px;
            --shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, #e9f2ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark);
            line-height: 1.6;
            padding: 20px;
        }

        .registration-container {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 500px;
            width: 100%;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .logo svg {
            width: 28px;
            height: 28px;
        }

        h2 {
            color: var(--primary);
            margin-bottom: 24px;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4b5563;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-row .form-group {
            flex: 1;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            background-color: var(--primary);
            border: none;
            color: var(--white);
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        .message {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 500;
        }

        .error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .success {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .registration-container {
                padding: 30px 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            E-Learning
        </div>
        
        <h2>Student Registration</h2>

        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" 
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" 
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Back to Login</a>
        </div>
    </div>

    <script>
        // Client-side password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        });

        // Auto-hide success message and redirect
        <?php if ($success): ?>
        setTimeout(function() {
            window.location.href = '../index.php';
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>