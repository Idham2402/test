<?php
require_once '../process/auth.php'; // Include JWT validation
require_once '../includes/db_connect.php'; // Include database connection

$user = validate_jwt(); // Validate JWT and get the logged-in user's details
$user_id = $user->user_id;

// Fetch the i-Spend account number
$i_spend_account_number = 'No i-Spend account found'; // Default message for i-Spend
$i_savings_account_number = 'No i-Savings account found'; // Default message for i-Savings


$transactions = [];
$favourites = [];

try {
    // Query to fetch favourites for the current user
    $stmt = $conn->prepare("SELECT id, name, account_number, phone_number, remarks FROM favourites WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $favourites = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all favourites
} catch (Exception $e) {
    error_log("Error fetching favourites: " . $e->getMessage());
    $favourites = [];
}


// Fetch combined transaction history for the current user
try {
  $stmt = $conn->prepare("
      SELECT 
          id AS transaction_id,
          'receive' AS type,
          source_user_name,
          target_user_name,
          amount_receive AS amount,
          remarks,
          created_at 
      FROM receives 
      WHERE target_account IN (
          SELECT account_number 
          FROM accounts 
          WHERE user_id = :user_id
      )
      
      UNION ALL
      
      SELECT 
          id AS transaction_id,
          'transfer' AS type,
          source_user_name,
          target_user_name,
          amount_transfer AS amount,
          remarks,
          created_at 
      FROM transfers 
      WHERE source_account IN (
          SELECT account_number 
          FROM accounts 
          WHERE user_id = :user_id
      )
      ORDER BY created_at DESC
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  die("Error fetching transaction history: " . $e->getMessage());
}







// Fetch i-Spend account number
try {
    $stmt = $conn->prepare("SELECT account_number FROM accounts WHERE user_id = :user_id AND account_name = 'i-Spend'");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $i_spend_account_number = $result['account_number'];
    }
} catch (Exception $e) {
    die("Error fetching i-Spend account: " . $e->getMessage());
}

// Fetch i-Savings account number
try {
    $stmt = $conn->prepare("SELECT account_number FROM accounts WHERE user_id = :user_id AND account_name = 'i-Savings'");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $i_savings_account_number = $result['account_number'];
    }
} catch (Exception $e) {
    die("Error fetching i-Savings account: " . $e->getMessage());
}

// Fetch card details for the current user
try {
    $stmt = $conn->prepare("
        SELECT card_number, cvv, username 
        FROM users 
        WHERE id = :user_id
    ");
    $stmt->bindParam(':user_id', $user->user_id);
    $stmt->execute();
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$card) {
        die("Card details not found for the current user.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Argon Dashboard 3 by Creative Tim
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
        <span class="ms-1 font-weight-bold">Creative Tim</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link " href="../pages/dashboard.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../pages/announcement.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Announcements</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="../pages/billing.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Accounts</span>
          </a>
        </li>
        <li class="nav-item">
          
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../pages/activities.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-world-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Activities</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../pages/profile.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../process/logout.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-collection text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Log Out</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer mx-3 ">
          </div>
        </div>
      </div>
    </div>
  </aside>
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Accounts</li>
          </ol>

          <!-- Disyplay the name based on current user login-->
          <h6 class="font-weight-bolder text-white mb-0">
          Hai <?php echo htmlspecialchars($card['username']); ?>
          </h6>

          
          </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Type here...">
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            
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
                  </a>
                </li>
                <li class="mb-2">
                    <div class="d-flex py-1">
                      <div class="d-flex flex-column justify-content-center">
                      </div>
                    </div>
                  </a>
                </li>
                
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-8">
          <div class="row">
            <div class="col-xl-6 mb-xl-0 mb-4">
              <div class="card bg-transparent shadow-xl">
                <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/card-visa.jpg');">
                  <span class="mask bg-gradient-dark"></span>
                  <div class="card-body position-relative z-index-1 p-3">
                    <i class="fas fa-wifi text-white p-2"></i>

                    <h5 class="text-white mt-4 mb-5 pb-2">
                    <?php echo htmlspecialchars(chunk_split($card['card_number'], 4)); ?>
                    </h5> <!-- Card Number -->
                    <div class="d-flex">
                      <div class="d-flex">
                        <div class="me-4">
                          <p class="text-white text-sm opacity-8 mb-0">Card Holder</p> <!-- -->
                          <h6 class="text-white mb-0"><?php echo htmlspecialchars($card['username']); ?></h6> <!--Card Holder Name -->
                        </div>
                        <div>
                          <p class="text-white text-sm opacity-8 mb-0">CVV</p>
                          <h6 class="text-white mb-0"><?php echo htmlspecialchars($card['cvv']); ?></h6> <!--CVV Holder Number -->


                        </div>
                      </div>
                      <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                        <img class="w-60 mt-2" src="../assets/img/logos/mastercard.png" alt="logo">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6">
              <div class="row">

                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      
                    <!-- Click button to transfer money-->
                    <a href="transfer.php">
                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                      <i class="ni ni-money-coins text-white text-lg" aria-hidden="true"></i>
                        <i class="fas fa-landmark opacity-10"></i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-5 text-center">
                      <h6 class="text-center mb-0">Transfer</h6>
                      <span class="text-xs">Transfer Your Money</span>
                    </div>
                    </div>
                    </a>

                    
                  </div>
                    <div class="col-md-6 mt-md-0 mt-4">
                    <div class="card">
                      <div class="">

                      <!-- Click button to claim voucher-->
                      
                    </div>
                     
                    </div>
                    </a>


                </div>
              </div>
            </div>

            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                  <div class="row">
                    <div class="col-6 d-flex align-items-center">
                      <h6 class="mb-0">Accounts Number Details</h6>
                    </div>
      
                  </div>
                </div>
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-md-6 mb-md-0 mb-4">
                      <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                        <img class="w-10 me-3 mb-0" src="../assets/img/logos/mastercard.png" alt="logo">
                        
                        <h6 class="mb-0"><?php echo htmlspecialchars($i_spend_account_number); ?> <br>i-SPEND</h6>
                        <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Card"></i>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                        <img class="w-10 me-3 mb-0" src="../assets/img/logos/visa.png" alt="logo">
                        <h6 class="mb-0"><?php echo htmlspecialchars($i_savings_account_number); ?> <br>i-SAVINGS</h6>
                        <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


<!--  Donwload Transaction-->

        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6 d-flex align-items-center">
                  <h6 class="mb-0">Donwload Transaction</h6>
                </div>
                <div class="col-6 text-end">
                  <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                </div>
              </div>
            </div>
            <div class="card-body p-3 pb-0">
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark font-weight-bold text-sm">March, 01, 2020</h6>
                    <span class="text-xs">#MS-415646</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    $180
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">February, 10, 2021</h6>
                    <span class="text-xs">#RV-126749</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    $250
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">April, 05, 2020</h6>
                    <span class="text-xs">#FB-212562</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    $560
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">June, 25, 2019</h6>
                    <span class="text-xs">#QW-103578</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    $120
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="text-dark mb-1 font-weight-bold text-sm">March, 01, 2019</h6>
                    <span class="text-xs">#AR-803481</span>
                  </div>
                  <div class="d-flex align-items-center text-sm">
                    $300
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

<!--  End Donwload Transaction-->
      <div class="row">
        <div class="col-md-7 mt-4">
          <div class="card">
            <div class="card-header pb-0 px-3">

              <h6 class="mb-0">Favourites</h6>
            </div>
            <div class="card-body pt-4 p-3">
              <ul class="list-group">

  <div class="container mt-3">
  <ul class="list-group">
      <div class="d-flex flex-column">
  </ul>
</div>

<?php if (!empty($favourites)): ?>
                <?php foreach ($favourites as $favourite): ?>
                    <li class="list-group-item border-0 d-flex p-4 mb-2">
                        <div class="d-flex flex-column">
                            <h6 class="mb-3 text-sm" id="display-name"><?php echo htmlspecialchars($favourite['name']); ?></h6>
                            <span class="mb-2 text-xs">Account Number:
                                <span class="text-dark font-weight-bold ms-sm-2" id="display-account-number"><?php echo htmlspecialchars($favourite['account_number']); ?></span>
                            </span>
                            <span class="mb-2 text-xs">Phone Number:
                                <span class="text-dark ms-sm-2 font-weight-bold" id="display-phone-number"><?php echo htmlspecialchars($favourite['phone_number']); ?></span>
                            </span>
                            <span class="text-xs">Remarks:
                                <span class="text-dark ms-sm-2 font-weight-bold" id="display-remarks"><?php echo htmlspecialchars($favourite['remarks']); ?></span>
                            </span>
                        </div>
                        <div class="ms-auto text-end">
                            <a href="edit_favourite.php?id=<?php echo $favourite['id']; ?>" class="btn btn-link text-dark px-3 mb-0">
                                <i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-center">
                    <span class="text-muted">No favourites found.</span>
                </li>
                <div class="add-button-container">
                    <a href="../pages/add_favourite.php" class="btn btn-primary">Add Favourite</a>
                </div>
            <?php endif; ?>



<div class="container mt-3">
  <ul class="list-group">
      <div class="d-flex flex-column">
        

      


      </div>
    </li>
  </ul>
</div>

<!-- Popup Form Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit User Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-form">
          <div class="mb-3">
            <label for="edit-name" class="form-label">Name</label>
            <input type="text" class="form-control" id="edit-name">
          </div>
          <div class="mb-3">
            <label for="edit-account-number" class="form-label">Account Number</label>
            <input type="text" class="form-control" id="edit-account-number">
          </div>
          <div class="mb-3">
            <label for="edit-phone-number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="edit-phone-number">
          </div>
          <div class="mb-3">
            <label for="edit-remarks" class="form-label">Remarks</label>
            <input type="text" class="form-control" id="edit-remarks">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="save-btn">Save Changes</button>
      </div>
      
    </div>
  </div>
</div>
  </ul>
    </div>
</div>
</div>
        
        <div class="col-md-5 mt-4">
    <div class="card h-100 mb-4">
        <div class="card-header pb-1 px-3">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-0">Transaction History</h6>
                </div>
            </div>
        </div>
        <div class="card-body pt-4 p-3">
            <?php if (!empty($transactions)): ?>
                <ul class="list-group">
                    <?php foreach ($transactions as $transaction): ?>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <!-- Check transaction type -->
                                <?php if ($transaction['type'] === 'transfer'): ?>
                                    <!-- Outgoing transaction -->
                                    <a href="transaction_details.php?transaction_id=<?php echo $transaction['transaction_id']; ?>" class="btn btn-icon-only btn-rounded btn-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                        <i class="fas fa-arrow-down"></i>
                                    </a>
                                <?php else: ?>
                                    <!-- Incoming transaction -->
                                    <a href="transaction_details.php?transaction_id=<?php echo $transaction['transaction_id']; ?>" class="btn btn-icon-only btn-rounded btn-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                        <i class="fas fa-arrow-up"></i>
                                    </a>
                                <?php endif; ?>

                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">
                                        <?php 
                                        if ($transaction['type'] === 'transfer') {
                                            echo 'To: ' . htmlspecialchars($transaction['target_user_name']);
                                        } else {
                                            echo 'From: ' . htmlspecialchars($transaction['source_user_name']);
                                        }
                                        ?>
                                    </h6>
                                    <span class="text-xs"><?php echo htmlspecialchars(date("d M Y, h:i A", strtotime($transaction['created_at']))); ?></span>
                                    <span class="text-xs text-muted">
                                        Remarks: <?php echo htmlspecialchars($transaction['remarks']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-<?php echo $transaction['type'] === 'transfer' ? 'danger' : 'success'; ?> text-gradient text-sm font-weight-bold">
                                <?php echo $transaction['type'] === 'transfer' ? '-' : '+'; ?>
                                RM <?php echo number_format($transaction['amount'], 2); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted text-center">No transactions yet.</p>
            <?php endif; ?>
        </div>
        <!-- Single Show Details Button -->
        
    </div>
</div>








    </div>
</div>

     
      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              
            </div>
            
          </div>
        </div>
      </footer>
    </div>
  </main>
  <div class="fixed-plugin">
    
      <i class="fa fa-cog py-2"> </i>
    </a>
    
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