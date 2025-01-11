<?php
// Include database connection
require_once '../includes/db_connect.php';

// 1. Handle form submission to generate token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $ic_card = trim($_POST['ic_card']);
    $phone_number = trim($_POST['phone_number']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate password confirmation
    if ($new_password !== $confirm_password) {
        echo "<p class='error'>Passwords do not match. Please try again.</p>";
        exit;
    }

    try {
        // Verify user details
        $stmt = $conn->prepare("
            SELECT * 
            FROM users 
            WHERE username = :username 
              AND email = :email 
              AND ic_card = :ic_card
              AND phone_number = :phone_number
        ");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ic_card', $ic_card);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate token with Base64 encoding (the user's data)
            $token_data = http_build_query([
                'username' => $username,
                'email' => $email,
                'ic_card' => $ic_card,
                'phone_number' => $phone_number,
                'new_password' => $new_password
            ]);
            $token = base64_encode($token_data);

            // Display reset link (replace with your actual server URL)
            $reset_url = "../process/validatetoken_process.php?token=$token";
            echo "<p class='success'>Password reset request generated! Click <a href='$reset_url'>here</a> to reset your password.</p>";
        } else {
            echo "<p class='error'>Invalid details provided. Please try again.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
    }
}

// 2. Handle token-based password reset (when the user clicks the link with the token)
elseif (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Decode and validate the Base64 token
    $decoded = base64_decode($token, true);
    if ($decoded) {
        parse_str($decoded, $data); // Parse the decoded string into an associative array

        // Ensure required fields are present in the token
        if (isset($data['username'], $data['email'], $data['ic_card'], $data['phone_number'], $data['new_password'])) {
            $username = $data['username'];
            $email = $data['email'];
            $ic_card = $data['ic_card'];
            $phone_number = $data['phone_number'];
            $new_password = $data['new_password'];

            try {
                // Verify user details
                $stmt = $conn->prepare("
                    SELECT * 
                    FROM users 
                    WHERE username = :username 
                      AND email = :email 
                      AND ic_card = :ic_card
                      AND phone_number = :phone_number
                ");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':ic_card', $ic_card);
                $stmt->bindParam(':phone_number', $phone_number);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Update the password
                    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE username = :username");
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo "<p class='success'>Password reset successfully through token!</p>";
                    } else {
                        echo "<p class='error'>Error: Password update failed.</p>";
                    }
                } else {
                    echo "<p class='error'>Invalid token or account details.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>Invalid token format. Required keys missing.</p>";
        }
    } else {
        echo "<p class='error'>Invalid token. Not a valid Base64 string.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon-inbank.png">
  <title>
    inBank
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="">
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-50 pt-5 m-3 border-radius-lg" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 text-center mx-auto">
            <h1 class="text-white mb-2 mt-5">inBank</h1>
            <p class="text-lead text-white">Join us and become an inBank user today.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row mt-n10 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
          <div class="card z-index-0">
            <div class="card-header text-center pt-3">
              <h5>Reset password</h5>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="mb-2">
                  <label for="username">Username:</label><br>
                  <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="mb-2">
                  <label for="email">Email:</label><br>
                  <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-2">
                  <label for="phone_number">Phone Number:</label><br>
                  <input type="text" class="form-control" name="phone_number" id="phone_number" required>
                </div>
                <div class="mb-2">
                  <label for="ic_card">NRIC:</label><br>
                  <input type="text" class="form-control" name="ic_card" id="ic_card" required>
                </div>
                <div class="mb-2">
                  <label for="new_password">Password:</label><br>
                  <input type="password" class="form-control" name="new_password" id="new_password" required>
                </div>
                <div class="mb-2">
                  <label for="confirm_password">Confirm Password:</label><br>
                  <input type="password" class="form-control" name="confirm_password" id="confirm_password"required>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Reset Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<script src="../assets/js/core/popper.min.js"></>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
</body>
</html>
