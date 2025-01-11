<?php
require_once '../includes/db_connect.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "weak_key";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['token'])) {
        try {
            // Decode JWT token
            $decoded = JWT::decode($data['token'], new Key($key, 'HS256'));
            $user_data = (array) $decoded->data;
            $user_id = $user_data['user_id'];

            // Fetch accounts for the logged-in user
            $stmt = $conn->prepare("SELECT account_name, account_number, balance FROM accounts WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "accounts" => $accounts
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid token or error occurred."
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Token not provided."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
?>
