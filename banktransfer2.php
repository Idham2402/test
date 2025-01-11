<?php
require_once 'process/auth.php'; // Include JWT validation
$user = validate_jwt(); // Validate JWT and retrieve user details
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: center;
        }
        .button-container {
            margin-top: 20px;
        }
        .button-container button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Bank Transfer</h1>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user->username ?? 'Unknown'); ?></p>

        <div class="button-container">
            <button onclick="window.location.href='index.php'">Go to Dashboard</button>
        </div>
    </div>
</body>
</html>
