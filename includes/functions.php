<?php
// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Hash password securely
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
