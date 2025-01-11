<?php
require_once '../process/auth.php'; // Include JWT validation
require_once '../includes/db_connect.php'; // Include database connection

// Validate JWT and get user details
$user = validate_jwt();
$user_id = $user->user_id;
$source_user_name = $user->username; // Fetch the current user's username

$message = ''; // Feedback message
$user_accounts = [];

// Fetch user's accounts (i-SPEND and i-SAVINGS) with balance
try {
    $stmt = $conn->prepare("
        SELECT account_name, account_number, balance 
        FROM accounts 
        WHERE user_id = :user_id AND (account_name = 'i-SPEND' OR account_name = 'i-SAVINGS')
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching accounts: " . $e->getMessage());
}

// Function to verify TAC code with the API
function verify_tac_code($tac_code) {
    $api_url = 'http://159.89.192.235/api/verify_code.php';

    $data = json_encode(['code' => $tac_code]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($api_url, false, $context);

    if ($result === false) {
        return false; // If API call fails
    }

    $response = json_decode($result, true);
    return isset($response['success']) && $response['success'] === true;
}

// Handle transfer logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source_account = trim($_POST['source_account']);
    $target_account = trim($_POST['target_account']);
    $amount = (float) trim($_POST['amount']);
    $remarks = trim($_POST['remarks']);
    $tac_code = trim($_POST['tac_code']);

    // Validate input
    if (empty($source_account) || empty($target_account) || $amount <= 0 || empty($remarks) || empty($tac_code)) {
        $message = "All fields are required, including the TAC code.";
    } elseif (!verify_tac_code($tac_code)) {
        $message = "Invalid TAC code. Please try again.";
    } elseif ($source_account === $target_account) {
        $message = "Source and target accounts cannot be the same.";
    } else {
        try {
            $conn->beginTransaction();

            // Fetch source account balance
            $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_number = :account_number AND user_id = :user_id");
            $stmt->bindParam(':account_number', $source_account);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $source = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$source || $source['balance'] < $amount) {
                $message = "Insufficient balance in the source account.";
            } else {
                // Fetch target account details
                $stmt = $conn->prepare("
                    SELECT accounts.account_number, users.username, users.id AS target_user_id 
                    FROM accounts 
                    INNER JOIN users ON accounts.user_id = users.id 
                    WHERE accounts.account_number = :account_number
                ");
                $stmt->bindParam(':account_number', $target_account);
                $stmt->execute();
                $target = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$target) {
                    $message = "Invalid target account.";
                } else {
                    // Deduct amount from source account
                    $stmt = $conn->prepare("UPDATE accounts SET balance = balance - :amount WHERE account_number = :account_number");
                    $stmt->bindParam(':amount', $amount);
                    $stmt->bindParam(':account_number', $source_account);
                    $stmt->execute();

                    // Add amount to target account
                    $stmt = $conn->prepare("UPDATE accounts SET balance = balance + :amount WHERE account_number = :account_number");
                    $stmt->bindParam(':amount', $amount);
                    $stmt->bindParam(':account_number', $target_account);
                    $stmt->execute();

                    // Log the transfer in `transfers` table
                    $stmt = $conn->prepare("
                        INSERT INTO transfers (user_id, source_account, source_user_name, target_account, target_user_name, amount_transfer, remarks, created_at)
                        VALUES (:user_id, :source_account, :source_user_name, :target_account, :target_user_name, :amount_transfer, :remarks, NOW())
                    ");
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->bindParam(':source_account', $source_account);
                    $stmt->bindParam(':source_user_name', $source_user_name);
                    $stmt->bindParam(':target_account', $target_account);
                    $stmt->bindParam(':target_user_name', $target['username']);
                    $stmt->bindParam(':amount_transfer', $amount);
                    $stmt->bindParam(':remarks', $remarks);
                    $stmt->execute();

                    // Log the receipt in `receives` table
                    $stmt = $conn->prepare("
                        INSERT INTO receives (user_id, source_account, source_user_name, target_account, target_user_name, amount_receive, remarks, created_at)
                        VALUES (:target_user_id, :source_account, :source_user_name, :target_account, :target_user_name, :amount_receive, :remarks, NOW())
                    ");
                    $stmt->bindParam(':target_user_id', $target['target_user_id']);
                    $stmt->bindParam(':source_account', $source_account);
                    $stmt->bindParam(':source_user_name', $source_user_name);
                    $stmt->bindParam(':target_account', $target_account);
                    $stmt->bindParam(':target_user_name', $target['username']);
                    $stmt->bindParam(':amount_receive', $amount);
                    $stmt->bindParam(':remarks', $remarks);
                    $stmt->execute();

                    $conn->commit();
                    $message = "Transfer successful!";
                }
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $message = "Error processing transfer: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #fff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
        }
        select, input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 15px;
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
        }
        .btn-primary {
            border-radius: 0.5rem;
        }
        .btn-outline-primary {
            border-radius: 0.5rem;
        }
        .message {
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container shadow-lg p-4 bg-white rounded">
        <h1 class="mb-4 text-center text-primary">Transfer Money</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'successful') !== false ? 'alert-success' : 'alert-danger'; ?> text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="source_account" class="form-label">From Account</label>
                <select id="source_account" name="source_account" class="form-select" required>
                    <?php foreach ($user_accounts as $account): ?>
                        <option value="<?php echo htmlspecialchars($account['account_number']); ?>">
                            <?php echo htmlspecialchars($account['account_name']); ?> 
                            (<?php echo htmlspecialchars($account['account_number']); ?> - 
                            RM <?php echo number_format($account['balance'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="target_account" class="form-label">To Account (Enter Account Number)</label>
                <input type="text" id="target_account" name="target_account" class="form-control" placeholder="Enter target account number" required />
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" id="amount" name="amount" class="form-control" min="0.01" step="0.01" placeholder="Enter amount" required />
            </div>

            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Enter remarks" required></textarea>
            </div>

            <div class="mb-3">
                <label for="tac_code" class="form-label">TAC Code</label>
                <div class="input-group">
                    <input type="text" id="tac_code" name="tac_code" class="form-control" placeholder="Enter the TAC code" required />
                    <a href="http://159.89.192.235/api/generate_code.php" target="_blank" class="btn btn-outline-primary">Request TAC Code</a>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Transfer</button>
            </div>
             
            <br>

            <div class="d-grid">
    <a href="billing.php" class="btn btn-primary btn-lg">Back to Accounts</a>
</div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
