<?php
include("../includes/db.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM quizzes WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: manage-quizzes.php");
        exit();
    } else {
        echo "Error deleting quiz: " . mysqli_error($conn);
    }
} else {
    header("Location: manage-quizzes.php");
    exit();
}
