<?php
// Include database connection
require_once '../includes/db_connect.php';

// Check if a token is provided via GET request
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Decode and validate the Base64 token
    $decoded = base64_decode($token, true);
    if ($decoded) {
        parse_str($decoded, $data); // Parse the decoded string into an associative array

        // Ensure all required fields are present in the token
        if (isset($data['username'], $data['email'], $data['ic_card'], $data['phone_number'], $data['new_password'])) {
            // Extract the user data from the token
            $username = $data['username'];
            $email = $data['email'];
            $ic_card = $data['ic_card'];
            $phone_number = $data['phone_number'];
            $new_password = $data['new_password'];

            try {
                // Verify user details in the database (user whose password is to be reset)
                $stmt = $conn->prepare("
                    SELECT * 
                    FROM users 
                    WHERE username = :username 
                      AND email = :email 
                      AND ic_card = :ic_card
                      AND phone_number = :phone_number
                ");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':ic_card', $ic_card);
                $stmt->bindParam(':phone_number', $phone_number);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Update the user's password in the database based on the token info
                    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE username = :username");
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo "<p class='success'>Password reset successfully for user: " . htmlspecialchars($username) . "!</p>";
                    } else {
                        echo "<p class='error'>Error: Password update failed for user: " . htmlspecialchars($username) . ".</p>";
                    }
                } else {
                    echo "<p class='error'>Invalid token or account details. User not found.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>Invalid token format. Required keys are missing.</p>";
        }
    } else {
        echo "<p class='error'>Invalid token. Not a valid Base64 string.</p>";
    }
} else {
    echo "<p class='error'>No token provided. Unable to process password reset.</p>";
}
?>
