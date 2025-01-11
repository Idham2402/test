<?php
require_once '../includes/db_connect.php'; // Include database connection

// Enable error reporting for debugging during development
ini_set('display_errors', 0); // Turn off error display for production
error_reporting(E_ALL);

if (isset($_GET['transaction_id'])) {
    // Fetch transaction_id from the URL
    $transaction_id = $_GET['transaction_id'];

    try {
        // Fetch transaction details from both transfers and receives tables
        $query = "
            SELECT 
                t.id AS transaction_id, 
                t.source_user_name, 
                t.target_user_name, 
                t.amount_transfer AS amount, 
                t.remarks, 
                t.created_at,
                'transfer' AS transaction_type
            FROM transfers t
            WHERE t.id = :transaction_id

            UNION ALL

            SELECT 
                r.id AS transaction_id, 
                r.source_user_name, 
                r.target_user_name, 
                r.amount_receive AS amount, 
                r.remarks, 
                r.created_at,
                'receive' AS transaction_type
            FROM receives r
            WHERE r.id = :transaction_id
        ";

        // Use a prepared statement for safety and better debugging
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
        $stmt->execute();

        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($transaction) {
            echo "<h3>Transaction Details</h3>";
            echo "Transaction ID: " . htmlspecialchars($transaction['transaction_id']) . "<br>";
            echo "Source User: " . htmlspecialchars($transaction['source_user_name']) . "<br>";
            echo "Target User: " . htmlspecialchars($transaction['target_user_name']) . "<br>";
            echo "Amount: RM " . htmlspecialchars($transaction['amount']) . "<br>";
            echo "Remarks: " . htmlspecialchars($transaction['remarks']) . "<br>";
            echo "Created At: " . htmlspecialchars($transaction['created_at']) . "<br>";
            echo "Transaction Type: " . htmlspecialchars($transaction['transaction_type']) . "<br>";
        } else {
            echo "<p>No transaction found with the given ID.</p>";
        }
    } catch (PDOException $e) {
        // Handle SQL errors gracefully
        error_log("SQL Error: " . $e->getMessage());
        echo "<p>An error occurred while processing your request.</p>";
    }
} else {
    echo "<p>Invalid request. No transaction ID provided.</p>";
}
?>
