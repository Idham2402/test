<?php
// Include necessary files
require_once '../includes/db_connect.php'; // Database connection
require_once '../process/auth.php'; // JWT validation

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // This should decode the JWT and give you user data

try {
    $stmt = $conn->prepare("SELECT id, username, email, phone_number, ic_card FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
}

// Handle user modification request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logic for user modification, activation, or suspension goes here
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-dark position-absolute w-100"></div>
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
                <a class="nav-link " href="../pages/UserActivities.php">
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
            <a class="nav-link active" href="../pages/userManagement.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">User Managements</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/adminProfile.php">
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
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="../pages/adminDashboard.php">InBank</a></li>
            <li class="breadcrumb-item text-sm text-white " aria-current="page">Dashboard</li>
          </ol>
          <h6 class="font-weight-bolder text-white mb-0">User's Managements</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="../pages/activities.php" class="nav-link text-white font-weight-bold px-0">
              <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none"><strong>Activities</strong></span>
              </a>
            </li>
            <li class="nav-item d-flex align-items-center mx-2"></li>
              <a href="../process/logout.php" class="nav-link text-white font-weight-bold px-0">
              <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none"><strong>Log Out</strong></span>
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                </div>
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
    <div class="container-fluid py-4">
      <div class="row">
                <div class="col mt-4">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <h4 class="mb-0">User's Management</h4>
                        </div>
                        <div class="card-body">
                            <!-- List Users -->
                            <?php if ($users) { ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>IC Card</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                                <td><?php echo htmlspecialchars($user['ic_card']); ?></td>
                                                <td>
                                                    <form action="" method="POST" style="display:inline;">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="modify_user" class="btn btn-sm btn-warning">Modify</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p>No users found.</p>
                            <?php } ?>
        
                            <!-- Modify User Form -->
                            <?php if (isset($_POST['modify_user'])) {
                                $user_id = $_POST['user_id'];
                                $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
                                $stmt->bindParam(':id', $user_id);
                                $stmt->execute();
                                $user_to_modify = $stmt->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <h4 class="mt-4">Modify User</h4>
                                <form action="../process/userManagement_process.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user_to_modify['id']; ?>">
                                    <div class="mb-3">
                                        <label for="new_username" class="form-label">New Username:</label>
                                        <input type="text" class="form-control" name="new_username" value="<?php echo htmlspecialchars($user_to_modify['username']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_email" class="form-label">New Email:</label>
                                        <input type="email" class="form-control" name="new_email" value="<?php echo htmlspecialchars($user_to_modify['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_phone_number" class="form-label">New Phone Number:</label>
                                        <input type="text" class="form-control" name="new_phone_number" value="<?php echo htmlspecialchars($user_to_modify['phone_number']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_ic_card" class="form-label">New IC Card:</label>
                                        <input type="text" class="form-control" name="new_ic_card" value="<?php echo htmlspecialchars($user_to_modify['ic_card']); ?>" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="save_changes" class="btn bg-gradient-dark w-100 my-4 mb-2">Save Changes</button>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </main>
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