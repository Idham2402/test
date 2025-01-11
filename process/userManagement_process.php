<?php
// Include necessary files
require_once '../includes/db_connect.php'; // Database connection
require_once '../process/auth.php'; // JWT validation

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // This should decode the JWT and give you user data

// Handle the form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
        $user_id = $_POST['user_id'];
        $new_username = isset($_POST['new_username']) ? trim($_POST['new_username']) : '';
        $new_email = isset($_POST['new_email']) ? trim($_POST['new_email']) : '';
        $new_phone_number = isset($_POST['new_phone_number']) ? trim($_POST['new_phone_number']) : '';
        $new_ic_card = isset($_POST['new_ic_card']) ? trim($_POST['new_ic_card']) : '';

        try {
            // Prepare the update query
            $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, phone_number = :phone_number, ic_card = :ic_card WHERE id = :id");
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':email', $new_email);
            $stmt->bindParam(':phone_number', $new_phone_number);
            $stmt->bindParam(':ic_card', $new_ic_card);
            $stmt->bindParam(':id', $user_id);

            // Execute the query
            $stmt->execute();

            // Check if any rows were updated
            if ($stmt->rowCount() > 0) {
                // Redirect to UserManagement page after success
                header("Location: ../pages/userManagement.php");
            } else {
                echo "Error: No changes made or user not found.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>
