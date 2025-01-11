<?php
require_once '../process/auth.php'; // Include JWT validation
require_once '../includes/db_connect.php'; // Include database connection

header('Content-Type: application/json'); // Set JSON response header

try {
    // Validate JWT and get user details
    $user = validate_jwt();
    $user_id = $user->user_id;

    // Fetch transaction history from both `transfers` and `receives`
    $stmt = $conn->prepare("
        SELECT 
            t.id AS transaction_id, 
            t.source_account, 
            t.target_account, 
            u.username AS target_user_name, 
            t.remarks, 
            t.created_at, 
            'transfer' AS transaction_type
        FROM transfers t
        LEFT JOIN users u ON t.target_account = u.account_number
        WHERE t.user_id = :user_id
        
        UNION ALL
        
        SELECT 
            r.id AS transaction_id, 
            r.source_account, 
            r.target_account, 
            u.username AS source_user_name, 
            r.remarks, 
            r.created_at, 
            'receive' AS transaction_type
        FROM receives r
        LEFT JOIN users u ON r.source_account = u.account_number
        WHERE r.target_account IN (
            SELECT account_number FROM accounts WHERE user_id = :user_id
        )
        
        ORDER BY created_at DESC
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return transaction data as JSON
    echo json_encode(['success' => true, 'data' => $transactions]);
} catch (Exception $e) {
    // Handle errors
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
