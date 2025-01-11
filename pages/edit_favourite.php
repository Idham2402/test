<?php
require_once '../includes/db_connect.php'; // Include database connection
require_once '../process/auth.php'; // Include user authentication

// Validate JWT to fetch the logged-in user details
$user = validate_jwt();
$user_id = $user->user_id; // Get the current user's ID

// Get the ID from the query string
if (!isset($_GET['id'])) {
    die("Invalid request. No ID provided.");
}

$id = intval($_GET['id']); // Convert the ID to an integer

try {
    // Fetch the favourite details (allow fetching based only on the ID, ignoring user ownership)
    $stmt = $conn->prepare("SELECT * FROM favourites WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $favourite = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$favourite) {
        die("Favourite not found.");
    }

    // Check ownership only for browser responses
    if ($favourite['user_id'] !== $user_id) {
        // Allow access only via tools like Burp Suite by showing a blank response
        if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false) {
            die("You are not authorized to view this page.");
        }
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $account_number = trim($_POST['account_number']);
        $phone_number = trim($_POST['phone_number']);
        $remarks = trim($_POST['remarks']);

        // Update the record (no user_id validation here, making it vulnerable via interception)
        $updateStmt = $conn->prepare("
            UPDATE favourites
            SET name = :name, account_number = :account_number, phone_number = :phone_number, remarks = :remarks
            WHERE id = :id
        ");
        $updateStmt->bindParam(':name', $name);
        $updateStmt->bindParam(':account_number', $account_number);
        $updateStmt->bindParam(':phone_number', $phone_number);
        $updateStmt->bindParam(':remarks', $remarks);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Redirect to the billing page after a successful update
        header("Location: billing.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Favourite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Favourite</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($favourite['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" id="account_number" name="account_number" class="form-control" value="<?php echo htmlspecialchars($favourite['account_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($favourite['phone_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <input type="text" id="remarks" name="remarks" class="form-control" value="<?php echo htmlspecialchars($favourite['remarks']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="billing.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
