<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$showPaytm = isset($_GET['showpaytm']) ? true : false;

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "<p style='font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; color:#d32f2f; text-align:center; padding:20px;'>Invalid course selected.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Enroll Course - Technologia</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap');

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f0f5fa;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
        color: #1a237e;
    }
    .container {
        background: #fff;
        padding: 2rem 3rem;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(26, 35, 126, 0.15);
        max-width: 450px;
        text-align: center;
        width: 90%;
    }
    h1 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: #0d47a1;
    }
    p {
        font-size: 1.1rem;
        margin: 1rem 0;
        color: #333;
    }
    .button {
        background-color: #1976d2;
        color: #fff;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s ease;
        box-shadow: 0 6px 12px rgba(25, 118, 210, 0.3);
        border: none;
        cursor: pointer;
        font-size: 1rem;
        display: inline-block;
    }
    .button:hover {
        background-color: #1565c0;
    }
    .paytm-img {
        width: 180px;
        margin: 1rem auto;
        display: none;
    }
    #success-message {
        display: none;
        margin-top: 1.5rem;
    }
  </style>
</head>
<body>
<div class="container">
  <h1>Pay with Paytm</h1>
  <p>Pay and enroll in <strong><?php echo htmlspecialchars($course['title']); ?></strong>.</p>

  <img src="../paytm.jpg" alt="Paytm Logo" class="paytm-img" id="paytmLogo"
    <?php if ($showPaytm) echo 'style="display:block;"'; ?> />

  <button id="payBtn" class="button"
    <?php if ($showPaytm) echo ''; else echo 'style="display:none;"'; ?>>
    Pay ₹<?php echo number_format($course['price'], 2); ?>
  </button>

  <div id="success-message">
    <p><strong>Payment Successful!</strong><br>You are now enrolled in the course.</p>
    <a href="dashboard.php" class="button">Go to Dashboard</a>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const payBtn = document.getElementById("payBtn");

    payBtn.addEventListener("click", function () {
        const button = this;
        button.disabled = true;
        button.textContent = "Processing...";

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "process_payment.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText === "success") {
                document.getElementById("paytmLogo").style.display = "block";
                document.getElementById("success-message").style.display = "block";
                button.style.display = "none";
            } else {
                alert("Payment failed. Please try again.");
                button.disabled = false;
                button.textContent = "Pay Again";
            }
        };

        xhr.send("course_id=<?php echo $course_id; ?>");
    });
});
</script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
