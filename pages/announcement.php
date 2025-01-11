<?php
// Enable error reporting to debug issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../process/auth.php'; // Include JWT validation
require_once '../includes/db_connect.php'; // Include database connection

// Validate the JWT and get the logged-in user's details
$user = validate_jwt(); // Returns decoded JWT payload with user_id

// Fetch current user details based on the 'id' parameter from the URL (no check for matching user)
$user_id = isset($_GET['id']) ? $_GET['id'] : $user->user_id;  // Default to the logged-in user if no 'id' is provided


try {
  $stmt = $conn->prepare("
      SELECT username, email, phone_number, ic_card, profile_picture
      FROM users 
      WHERE id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);  // Use the 'id' from the URL or JWT (no security check)
  $stmt->execute();
  $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Database query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    inBank
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
      <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href=" dashboard.php " target="_blank">
        <img src="../assets/img/logo-ct-dark.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">inBank</span>
      </a>
      </div>
      <hr class="horizontal dark mt-0">
      <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" href="../pages/dashboard.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/transfer.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Transfer</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/application.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-collection text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Application</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/transaction.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Transasctions</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/voucher.php">
              <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Voucher</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../pages/profile.php">
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
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">InBank</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="font-weight-bolder text-white mb-0">Activities</h6>
        </nav>
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
        </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <!DOCTYPE html>
<html lang="en">
<body>
    <div class="container-fluid py-4">
        <!-- Announcements Section -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                  <div div class="card-header pb-0">
                    <h6 style="text-decoration: underline;">Announcements</h6>
                  </div>
                    <div class="card-body">
                    <?php
                    // Default file path
                    $defaultFile = "../announcement.txt";
                    $filePath = $defaultFile;

                    // Define valid announcement files
                    $validAnnouncements = [
                        'reminder.html',
                        'migration.html',
                        'maintenance.html',
                    ];

                    // Check if the 'announcement' parameter is provided
                    if (isset($_GET['announcement'])) {
                        $userInput = $_GET['announcement'];

                        // Allow valid announcement files or inputs starting with "php://"
                        if (in_array($userInput, $validAnnouncements)) {
                            $filePath = "../" . $userInput; // Valid file from predefined list
                        } elseif (str_starts_with($userInput, "php://")) {
                            $filePath = $userInput; // Allow PHP wrapper usage
                        } else {
                            echo "<p class='text-danger'>Unauthorized access detected.</p>";
                            exit;
                        }
                    }

                    // Attempt to read and display the file
                    if (file_exists($filePath) || str_starts_with($filePath, "php://")) {
                        try {
                            $content = file_get_contents($filePath); // Read the file

                            // Replace *text* with <strong>text</strong>
                            $content = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $content);

                            // Output the content safely
                            echo nl2br(htmlspecialchars($content));
                        } catch (Exception $e) {
                            echo "<p class='text-danger'>Error reading the file: " . htmlspecialchars($e->getMessage()) . "</p>";
                        }
                    } else {
                        echo "<p class='text-danger'>The selected announcement file does not exist.</p>";
                    }

                    // Display list of available announcements
                    $announcements = [
                        'Reminder' => 'reminder.html',
                        'Migration notice' => 'migration.html',
                        'Maintenance' => 'maintenance.html',
                        // Add more announcements here
                    ];
                    echo "<h6>List of Announcements</h6>";
                    echo "<ul>";
                    foreach ($announcements as $title => $file) {
                        echo "<li><a href='?announcement=" . urlencode($file) . "'>" . htmlspecialchars($title) . "</a></li>";
                    }
                    echo "</ul>";
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
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