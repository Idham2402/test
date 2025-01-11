<?php
// Include the necessary files for database connection and JWT validation
require_once '../includes/db_connect.php'; // Include the database connection
require_once '../process/auth.php'; // Include JWT validation

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // Returns decoded JWT payload with user_id

// Check if the form was submitted and a file was uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Get file details
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Simple check to make sure there were no errors
    if ($file_error === 0) {
        try {
            // Begin a transaction
            $conn->beginTransaction();

            // Introduce a fake file type validation using a blacklist
            $blacklisted_extensions = ['php', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'phtml', 'htaccess', 'pl', 'py', 'exe', 'sh'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Check if the file extension is in the blacklist
            if (in_array(strtolower($file_ext), $blacklisted_extensions)) {
                echo "Invalid file type. This file type is not allowed.";
                exit;
            }

            // Rename the file with a random hash but keep the original extension
            $new_file_name = md5(uniqid(rand(), true)) . '.' . $file_ext;

            // Save the file in the 'uploads' directory
            $file_destination = '../uploads/' . $new_file_name;


            move_uploaded_file($file_tmp, $file_destination);

            // Optionally, save the file path to the database for the user's profile picture
            $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
            $stmt->bindParam(':profile_picture', $new_file_name);
            $stmt->bindParam(':user_id', $user->user_id);
            $stmt->execute();

            // Log the activity of uploading a profile picture
            $activityStmt = $conn->prepare("
                INSERT INTO activities (user_id, username, activity_type, activity_time) 
                VALUES (:user_id, :username, 'Update Profile Picture', NOW()) 
                ON DUPLICATE KEY UPDATE activity_time = NOW()
            ");
            $activityStmt->bindParam(':user_id', $user->user_id);
            $activityStmt->bindParam(':username', $user->username);
            $activityStmt->execute();

            // Commit the transaction
            $conn->commit();

            echo "Profile picture uploaded successfully!";
            header("Location: ../pages/profile.php");
            exit;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "There was an error uploading your file.";
    }
} else {
    echo "No file was uploaded.";
}
?>
