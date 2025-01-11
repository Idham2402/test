<?php
require_once '../includes/db_connect.php'; 
require_once '../process/auth.php'; 

$user = validate_jwt();
$user_id = $user->user_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $account_number = trim($_POST['account_number']);
    $phone_number = trim($_POST['phone_number']);
    $remarks = trim($_POST['remarks']);

    try {
       
        $stmt = $conn->prepare("
            INSERT INTO favourites (user_id, name, account_number, phone_number, remarks) 
            VALUES (:user_id, :name, :account_number, :phone_number, :remarks)
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':account_number', $account_number);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->execute();

        
        header("Location: billing.php");
        exit;
    } catch (Exception $e) {
        $error_message = "Error adding favourite: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Favourite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Add Favourite</h3>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" id="account_number" name="account_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <input type="text" id="remarks" name="remarks" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Favourite</button>
            <a href="billing.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
