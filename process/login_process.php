<?php
require_once '../includes/db_connect.php'; // Include database connection
require_once '../vendor/autoload.php'; // Include JWT library

use Firebase\JWT\JWT;

$key = "weak_key"; // Use a weak or known key

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Create a JWT token
            $payload = [
                'iss' => 'localhost',
                'iat' => time(),
                'exp' => time() + 3600,
                'user_id' => $user['id'],
                'username' => $user['username']
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');

            setcookie('jwt', $jwt, time() + 3600, "/", "", false, true);

            echo json_encode(['success' => true, 'message' => 'Login successful.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        exit;
    }
}
?>
