<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $ic_card = sanitizeInput($_POST['ic_card']);
    $phone_number = sanitizeInput($_POST['phone_number']);

    // Validate email format
    if (!validateEmail($email)) {
        header("Location: ../pages/register.php?status=error&message=Invalid email format.");
        exit;
    }

    // Hash the password
    $hashed_password = hashPassword($password);

    // Generate random card number and CVV
    $card_number = generateRandomNumber(16);
    $cvv = generateRandomNumber(3);
    $profile_picture = 'default.jpg';

    try {
        // Start a database transaction
        $conn->beginTransaction();

        // Insert user into `users` table
        $sql = "INSERT INTO users (username, password, card_number, cvv, ic_card, phone_number, email, profile_picture) 
                VALUES (:username, :password, :card_number, :cvv, :ic_card, :phone_number, :email , :profile_picture)";
        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':card_number' => $card_number,
            ':cvv' => $cvv,
            ':ic_card' => $ic_card,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':profile_picture' => $profile_picture,
        ]);

        // Get the user ID
        $user_id = $conn->lastInsertId();

        // Generate accounts for the user
        $account_number_1 = generateRandomNumber(12);
        $account_number_2 = generateRandomNumber(12);

        $account_sql = "INSERT INTO accounts (user_id, account_name, account_number, balance) VALUES (:user_id, :account_name, :account_number, :balance)";
        $account_stmt = $conn->prepare($account_sql);

        $account_stmt->execute([':user_id' => $user_id, ':account_name' => 'I-SPEND', ':account_number' => $account_number_1, ':balance' => 1000.00]);
        $account_stmt->execute([':user_id' => $user_id, ':account_name' => 'I-SAVINGS', ':account_number' => $account_number_2, ':balance' => 0.00]);

        // Commit transaction
        $conn->commit();

        header("Location: ../pages/register.php?status=success&message=Registration successful!");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../pages/register.php?status=error&message=Error processing your request.");
        exit;
    }
} else {
    header("Location: ../pages/register.php?status=error&message=Invalid request method.");
    exit;
}

// Generate a random number of given length
function generateRandomNumber($length) {
    return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}
?>
