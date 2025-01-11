<?php
require_once '../includes/db_connect.php'; // Include database connection
require_once '../vendor/autoload.php'; // Include JWT library

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "weak_key"; // Secret key for JWT
$error = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Sanitize username input
    $password = trim($_POST['password']); // Sanitize password input

    // Use PDO for secure database queries
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Create a JWT token
            $payload = [
                'iss' => 'localhost',        // Issuer
                'iat' => time(),             // Issued at
                'exp' => time() + 3600,      // Expiration time (1 hour)
                'user_id' => $user['id'],    // User ID
                'username' => $user['username'] // Username
            ];
            $jwt = JWT::encode($payload, $key, 'HS256'); // Encode the JWT

            // Set the JWT as a cookie
            setcookie('jwt', $jwt, time() + 3600, "/", "", false, true); // HttpOnly for security

            // Insert or update last_login timestamp and username in the activities table for the current user
            $updateStmt = $conn->prepare("
                INSERT INTO activities (user_id, username, activity_type, activity_time) 
                VALUES (:user_id, :username, 'Login', NOW()) 
                ON DUPLICATE KEY UPDATE activity_time = NOW()");
                
            $updateStmt->bindParam(':user_id', $user['id']);
            $updateStmt->bindParam(':username', $user['username']);
            $updateStmt->execute();

            // Check if the user is admin (user_id = 71 and username = 'admin')
            if ($user['id'] == 71 && $user['username'] == 'admin') {
                // Redirect to the admin dashboard
                header("Location: ../pages/adminDashboard.php");
            } else {
                // Redirect to the regular user dashboard
                header("Location: ../pages/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!--link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png"-->
  <link rel="icon" type="image/png" href="../assets/img/inbank-icon.png">
  <title>
    InBank
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>
<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="container-fluid py-4">
              <div class="row">
                  <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                      <div class="card card-plain">
                          <div class="card-header pb-0 text-start">
                              <h4 class="font-weight-bolder">Sign In</h4>
                              <p class="mb-0">Enter your username and password to sign in</p>
                              <?php if ($error): ?>
                                  <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
                              <?php endif; ?>
                          </div>
                          <div class="card-body">
                              <form method="POST" role="form">
                                  <div class="mb-3">
                                      <input type="text" class="form-control form-control-lg" name="username" placeholder="Username" aria-label="Username" required>
                                  </div>
                                  <div class="mb-3">
                                      <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" aria-label="Password" required>
                                  </div>
                                  <div class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" id="rememberMe">
                                      <label class="form-check-label" for="rememberMe">Remember me</label>
                                  </div>
                                  <div class="text-center">
                                      <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Sign in</button>
                                  </div>
                              </form>
                          </div>
                          <div class="card-footer text-center pt-0 px-lg-2 px-1">
                              <p class="mb-4 text-sm mx-auto">
                                  Don't have an account?
                                  <a href="register.php" class="text-primary text-gradient font-weight-bold">Sign up</a>
                              </p>
                              <p class="mb-4 text-sm mx-auto">
                                  Forgot password?
                                  <a href="resetpasswordLogin.php" class="text-primary text-gradient font-weight-bold">Reset password</a>
                              </p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg');
          background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Attention is the new currency"</h4>
                <p class="text-white position-relative">The more effortless the writing looks, the more effort the writer actually put into the process.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>

