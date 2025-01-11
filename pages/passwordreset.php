<?php
// Include database connection
require_once '../includes/db_connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Sanitize username input
    $email = trim($_POST['email']); // Sanitize email input
    $card_number = trim($_POST['card_number']); // Sanitize card number input
    $ic_card = trim($_POST['ic_card']); // Sanitize IC card input
    $new_password = trim($_POST['new_password']); // Sanitize new password input
    $confirm_password = trim($_POST['confirm_password']); // Sanitize confirm password input

    // Check if the new passwords match
    if ($new_password === $confirm_password) {
        // Verify user details before updating the password
        try {
            // Prepare the SQL statement
            $stmt = $conn->prepare("
                SELECT * 
                FROM users 
                WHERE username = :username 
                  AND email = :email 
                  AND card_number = :card_number 
                  AND ic_card = :ic_card
            ");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':card_number', $card_number);
            $stmt->bindParam(':ic_card', $ic_card);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // If user details match
            if ($user) {
                // Update the password
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE username = :username");
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the password
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo "<p class='success'>Password reset successful! You can now <a href='login.php'>login</a>.</p>";
                } else {
                    echo "<p class='error'>Error: Password reset failed.</p>";
                }
            } else {
                echo "<p class='error'>Invalid details provided. Please check and try again.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>Passwords do not match. Please try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <form method="POST">
            <label for="username">Username:</label><br>
            <input type="text" name="username" id="username" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>

            <label for="card_number">Card Number:</label><br>
            <input type="text" name="card_number" id="card_number" required><br><br>

            <label for="ic_card">IC Card:</label><br>
            <input type="text" name="ic_card" id="ic_card" required><br><br>

            <label for="new_password">New Password:</label><br>
            <input type="password" name="new_password" id="new_password" required><br><br>

            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" name="confirm_password" id="confirm_password" required><br><br>

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
