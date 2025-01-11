<?php
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Weak or known secret key (for demonstration purposes)
$key = "weak_key";

// JWT Validation Function
function validate_jwt() {
    global $key;

    // Check if the JWT cookie exists
    if (!isset($_COOKIE['jwt'])) {
        header("Location: login.php");
        exit;
    }

    $jwt = $_COOKIE['jwt'];

    try {
        // Decode the JWT and verify the signature using the secret key
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));  // Signature verification is active now

        // Debugging: Check the decoded data
        error_log("Decoded JWT: " . json_encode($decoded));  // Log decoded JWT for debugging

        return $decoded;

    } catch (\Exception $e) {
        // If signature verification fails, show the error for debugging purposes
        error_log("JWT decode failed: " . $e->getMessage());  // Log the error message
        // Allow access even if the JWT is invalid or the signature doesn't match
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $jwt)[1]))));
    }
}
?>
