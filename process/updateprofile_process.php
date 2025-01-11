<?php
// Include the necessary files for database connection and JWT validation
require_once '../includes/db_connect.php'; // Include the database connection
require_once '../process/auth.php'; // Include JWT validation

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // Returns decoded JWT payload with user_id

// Check if the user is valid
if (!$user) {
    die("Invalid user.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated data from the form
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];

    // Update the user profile in the database
    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Update the user's profile
        $stmt = $conn->prepare("
            UPDATE users
            SET username = :username, phone_number = :phone_number
            WHERE id = :user_id
        ");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':user_id', $user->user_id); // Use the user ID from the JWT payload
        $stmt->execute();

        // Log the update profile activity
        $activityStmt = $conn->prepare("
            INSERT INTO activities (user_id, username, activity_type, activity_time) 
            VALUES (:user_id, :username, 'Update Profile', NOW()) 
            ON DUPLICATE KEY UPDATE activity_time = NOW()
        ");
        $activityStmt->bindParam(':user_id', $user->user_id);
        $activityStmt->bindParam(':username', $username);
        $activityStmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the profile page
        echo "Profile updated successfully!";
        header("Location: ../pages/profile.php?id=" . urlencode($user->user_id));
        exit;
    } catch (PDOException $e) {
        // Roll back the transaction in case of an error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
