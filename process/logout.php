<?php
// Check if the JWT cookie is set
if (isset($_COOKIE['jwt'])) {
    // Invalidate the JWT by setting its cookie with a past expiration time
    setcookie('jwt', '', time() - 3600, "/", "", false, true); // HttpOnly for security
}

// Redirect to the login page or show a logout confirmation
header("Location: ../pages/login.php?status=logout");
exit;
?>
