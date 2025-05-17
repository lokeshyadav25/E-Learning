<?php
session_start();
session_unset();
session_destroy();

// ✅ Redirect to correct login page inside 'auth' folder
header("Location: ../index.php");
exit;
?>
