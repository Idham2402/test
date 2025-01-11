<?php
require_once '../includes/db_connect.php'; // Include database connection
require_once '../process/auth.php'; // Include user authentication

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validate JWT and get logged-in user details
$user = validate_jwt();
$user_id = $user->user_id;

if (!empty($data['name']) && !empty($data['account_number']) && !empty($data['phone_number']) && !empty($data['remarks'])) {
    $name = $data['name'];
    $accountNumber = $data['account_number'];
    $phoneNumber = $data['phone_number'];
    $remarks = $data['remarks'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO favourites (user_id, name, account_number, phone_number, remarks)
            VALUES (:user_id, :name, :account_number, :phone_number, :remarks)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':account_number', $accountNumber);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
}
?>
