<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Registration</title>
</head>
<body>
    <h1>Register</h1>
    <form action="process/register_process.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="card_number">Card Number:</label>
        <input type="text" name="card_number" maxlength="16" required><br><br>

        <label for="cvv">CVV:</label>
        <input type="text" name="cvv" maxlength="3" required><br><br>

        <label for="ic_card">IC Card:</label>
        <input type="text" name="ic_card" maxlength="12" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" maxlength="15" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
