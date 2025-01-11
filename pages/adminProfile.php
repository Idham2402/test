<?php
// Enable error reporting to debug issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../process/auth.php'; // Include JWT validation
require_once '../includes/db_connect.php'; // Include database connection

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // Returns decoded JWT payload with user_id

if ($user->user_id !== 71 || $user->username !== 'admin') {
  die("Access denied. Unauthorized user.");
}

// Fetch current user details based on the 'id' parameter from the URL (no check for matching user)
$user_id = isset($_GET['id']) ? $_GET['id'] : $user->user_id;  // Default to the logged-in user if no 'id' is provided


try {
  $stmt = $conn->prepare("
      SELECT username, email, phone_number, ic_card, profile_picture, card_number
      FROM users 
      WHERE id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);  // Use the 'id' from the URL or JWT (no security check)
  $stmt->execute();
  $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Check if data was returned
  if (!empty($accounts)) {
      // Extract the values from the first row (assuming user_id is unique)
      $username = $accounts[0]['username'];
      $email = $accounts[0]['email'];
      $phone_number = $accounts[0]['phone_number'];
      $ic_card = $accounts[0]['ic_card'];
      $card_number = $accounts[0]['card_number'];
      $profile_picture = $accounts[0]['profile_picture'];
  } else {
      // Handle case where no data is returned
      $username = $email = $phone_number = $ic_card = null;
      $profile_picture = '../uploads/default.jpg';
      $card_number = null;
  }
} catch (PDOException $e) {
  die("Database query error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/inbank-icon.png">
  <title>
    InBank
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

<body class="g-sidenav-show bg-gray-100">
  <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/profile-layout-header.jpg'); background-position-y: 50%;">
    <span class="mask bg-primary opacity-6"></span>
  </div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.php " target="_blank">
        <img src="../assets/img/logo-ct-dark.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">inBank</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link " href="../pages/adminDashboard.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/userActivities.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">User Activities</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/userTransactions.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">User Transasctions</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/userManagement.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">User Managements</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="../pages/adminProfile.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Profile</span>
            </a>
          </li>
        </ul>
      </div>
    <div class="sidenav-footer mx-3 ">
      <div class="card card-plain shadow-none" id="sidenavCard">
  </aside>
  <div class="main-content position-relative max-height-vh-100 h-100">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg bg-transparent shadow-none position-absolute px-4 w-100 z-index-2 mt-n11">
      <div class="container-fluid py-1">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 ps-2 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="../pages/adminDashboard.php">InBank</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="text-white font-weight-bolder ms-2">Admin Profile</h6>
        </nav>
        <div class="collapse navbar-collapse me-md-0 me-sm-4 mt-sm-0 mt-2" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          </div>
          <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="../pages/activities.php" class="nav-link text-white font-weight-bold px-0">
              <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none"><strong>Activities</strong></span>
              </a>
            </li>
            <li class="nav-item d-flex align-items-center mx-2"></li>
              <a href="../pages/sign-in.php" class="nav-link text-white font-weight-bold px-0">
              <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none"><strong>Log Out</strong></span>
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 pe-0 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                  <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                  </div>
                </a>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="card shadow-lg mx-4 card-profile-bottom">
      <div class="card-body p-3">
        <div class="row gx-4">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
              <img src="../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                <h5 class="mb-1"><?php echo htmlspecialchars($username); ?></h5>
              </h5>
            </div>
          </div>
            <form action="../process/uploadpicprofileAdmin_process.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="profile_picture" class="form-control-label">Profile Picture</label>
              <input type="file" class="form-control" name="profile_picture" id="profile_picture" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload picture</button>
          </form>
        </div>
      </div>
    </div>
    <div class="container-fluid py-4">
      <div class="row">
        <!-- Left Column: Profile Form -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex align-items-center">
                <p class="mb-0">Profile</p>
                <button id="settings-btn" class="btn btn-primary btn-sm ms-auto" onclick="toggleEditMode()">Edit</button>
              </div>
            </div>
            <div class="card-body">
              <p class="text-uppercase text-sm">User Information</p>
              <form method="POST" action="../process/updateprofileAdmin_process.php">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="username" class="form-control-label">Username</label>
                      <input id="username-display" class="form-control editable-field" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" disabled>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone_number" class="form-control-label">Phone Number</label>
                      <input id="phone_number-display" class="form-control editable-field" type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" disabled>
                    </div>
                  </div>                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email" class="form-control-label">Email Address</label>
                      <input id="email-display" class="form-control" type="text" name="email" value="<?php 
                        $redacted_email = preg_replace_callback(
                            '/^[^@]+/', 
                            function ($matches) {
                                return str_repeat('*', strlen($matches[0])); // Replace with the same number of asterisks
                            }, 
                            $email
                        );
                        echo htmlspecialchars($redacted_email); 
                        ?>" disabled>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="ic_card" class="form-control-label">IC Number</label>
                      <input id="ic_card-display" class="form-control" type="text" name="ic_card" value="<?php echo htmlspecialchars($ic_card); ?>" disabled>
                    </div>
                  </div>
                </div>
                <button type="submit" id="save-btn" class="btn btn-success" style="display: none;">Save Changes</button>
              </form>
            </div>          
          </div>
        </div>    
      </div>
    </div>    
  <!--   Core JS Files   -->
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
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  <script>
  // Function to toggle between display and edit mode
  function toggleEditMode() {
    // Select only the inputs that should be editable
    const editableFields = document.querySelectorAll('.editable-field');
    const saveBtn = document.getElementById('save-btn');
    const settingsBtn = document.getElementById('settings-btn');

    // Toggle editable fields between editable and disabled state
    editableFields.forEach(field => {
      field.disabled = !field.disabled; // Toggle the 'disabled' attribute
    });

    // Toggle Save button visibility
    saveBtn.style.display = saveBtn.style.display === 'none' ? 'block' : 'none';

    // Change settings button text
    settingsBtn.textContent = settingsBtn.textContent === 'Settings' ? 'Edit Mode' : 'Settings';
  }
</script>
  <!--   Core JS Files   -->
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
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>
</html>