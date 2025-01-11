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

            // Fetch account details for the user
            $stmt = $conn->prepare("SELECT card_number, username AS card_holder, cvv FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account) {
                echo json_encode([
                    "success" => true,
                    "card_number" => $account['card_number'],
                    "card_holder" => $account['card_holder'],
                    "cvv" => $account['cvv']
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No account details found."
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid token."
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
