<?php
session_start();
$_SESSION = []; // Clear session array
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Delete session cookie

echo "Logging out... Redirecting in 3 seconds.";
header("refresh:3;url=customer_login.php"); // Redirect after 3 seconds
exit();
?>
