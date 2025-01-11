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
        }
        .button-container {
            margin-top: 20px;
            text-align: center;
        }
        .button-container button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <script>
        async function loadForm() {
            const token = localStorage.getItem('jwt');
            if (!token) {
                alert('You are not logged in. Redirecting to login page.');
                window.location.href = 'login.php';
                return;
            }

            const response = await fetch('process/get_user_accounts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ token })
            });

            const result = await response.json();

            if (result.success) {
                const fromAccount = document.getElementById('from_account');

                result.accounts.forEach(account => {
                    const option = document.createElement('option');
                    option.value = account.account_number;
                    option.textContent = `${account.account_name} - ${account.account_number} (RM ${account.balance})`;
                    fromAccount.appendChild(option);
                });
            } else {
                alert(result.message);
                window.location.href = 'login.php';
            }
        }

        async function submitTransfer(event) {
            event.preventDefault();

            const token = localStorage.getItem('jwt');
            if (!token) {
                alert('You are not logged in. Redirecting to login page.');
                window.location.href = 'login.php';
                return;
            }

            const formData = {
                token,
                from_account: document.getElementById('from_account').value,
                to_account: document.getElementById('to_account').value,
                amount: document.getElementById('amount').value,
            };

            console.log("Submitting transfer with data:", formData);

            const response = await fetch('process/transfer_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            console.log("Transfer response:", result);

            if (result.success) {
                alert('Transfer successful!');
                window.location.href = 'banktransfer.php'; // Redirect after success
            } else {
                alert(result.message);
            }
        }

        function goToDashboard() {
            window.location.href = 'pages/dashboard.php';
        }

        window.onload = loadForm;
    </script>
</head>
<body>
    <div class="container">
        <h1>Bank Transfer</h1>
        <form onsubmit="submitTransfer(event)">
            <label for="from_account">From Account:</label><br>
            <select name="from_account" id="from_account" required></select><br><br>

            <label for="to_account">Recipient Account Number:</label><br>
            <input type="text" name="to_account" id="to_account" placeholder="Enter recipient account number" required><br><br>

            <label for="amount">Amount to Transfer:</label><br>
            <input type="number" name="amount" id="amount" min="1" step="0.01" required><br><br>

            <button type="submit">Confirm Transfer</button>
        </form>

        <div class="button-container">
            <button onclick="goToDashboard()">Go to Dashboard</button>
        </div>
    </div>
</body>
</html>
