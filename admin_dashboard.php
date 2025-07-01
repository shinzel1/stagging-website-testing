<?php include 'includes/header.php'; ?>

<?php
// admin_dashboard.php
include("pages/style.php");
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
require_once("config/database_connection.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_admin_email = trim($_POST['new_admin_email']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$new_admin_email]);

        if ($stmt->rowCount() === 0) {
            $error = 'User does not exist.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
            $stmt->execute([$new_admin_email]);
            $success = 'User has been successfully promoted to admin.';
        }
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<body>
    <!-- PLUGINS CSS STYLE -->
    <link href="assets/admin/assets/plugins/simplebar/simplebar.css" rel="stylesheet" />
    <link href="assets/admin/assets/plugins/nprogress/nprogress.css" rel="stylesheet" />
    <!-- No Extra plugin used -->
    <link href='assets/admin/assets/plugins/jvectormap/jquery-jvectormap-2.0.3.css' rel='stylesheet'>
    <link href='assets/admin/assets/plugins/daterangepicker/daterangepicker.css' rel='stylesheet'>
    <link href='assets/admin/assets/plugins/toastr/toastr.min.css' rel='stylesheet'>
    <!-- SLEEK CSS -->
    <link id="sleek-css" rel="stylesheet" href="assets/admin/assets/css/sleek.css" />

    <!-- FAVICON -->
    <link href="assets/admin/assets/img/favicon.png" rel="shortcut icon" />
    <link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css" />

    <!--
      HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
    -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="assets/admin/assets/plugins/nprogress/nprogress.js"></script>
    <style>
        @media (min-width: 991.98px) {
            main {
                padding-left: 240px;
            }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 58px 0 0;
            /* Height of navbar */
            box-shadow: -1px 7px 15px -4px rgba(0, 0, 0, 0.76);
            width: 240px;
            z-index: 600;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 100%;
            }
        }

        .sidebar .active {
            border-radius: 5px;
            box-shadow: 0 2px 5px 0 rgb(0 0 0 / 16%), 0 2px 10px 0 rgb(0 0 0 / 12%);
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
            /* Scrollable contents if viewport is shorter than content. */
        }
    </style>
    <header>
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="collapse d-lg-block sidebar collapse bg-white">
            <div class="position-sticky">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <!-- <span class="list-group-item list-group-item-action py-2 ripple active" aria-current="true">
                        <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Admin dashboard</span>
                    </span> -->
                    <a href="category.php" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="fa-solid fa-layer-group me-3"></i><span>Category</span>
                    </a>
                    <a href="admin_loyalty.php" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="fas fa-chart-pie fa-fw me-3"></i><span>Loyalty program</span>
                    </a>
                    <a href="offers.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-hand-holding-dollar fa-fw me-3"></i><span>Offers</span></a>
                    <a href="add_variation.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-ice-cream fa-fw me-3"></i><span>Add product variations (flavours)</span></a>
                    <a href="customer-query-dashboard.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-users fa-fw me-3"></i><span>Customer Query Dashboard</span></a>
                    <a href="flavours.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-ice-cream fa-fw me-3"></i><span>Add new Flavour</span></a>
                    <!-- 
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-globe fa-fw me-3"></i><span>International</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-building fa-fw me-3"></i><span>Partners</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-calendar fa-fw me-3"></i><span>Calendar</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-users fa-fw me-3"></i><span>Users</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-chart-bar fa-fw me-3"></i><span>Orders</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple ">
                        <i class="fas fa-chart-area fa-fw me-3"></i><span>Webiste traffic</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-lock fa-fw me-3"></i><span>Password</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-chart-line fa-fw me-3"></i><span>Analytics</span></a> -->

                </div>
            </div>
        </nav>
        <!-- Sidebar -->
    </header>
    <!--Main Navigation-->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <!--Main layout-->

    <main style="margin-top: 58px;">
        <div class="content-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Recent Order Table -->
                        <div class="card card-table-border-none recent-orders" id="recent-orders">
                            <div class="card-header justify-content-between">
                                <h2>Admin Management Dashboard</h2>
                                <div class="">
                                    <span>
                                        <button class="btn btn-danger btn-sm me-3" id="add-user-btn">Add User</button>
                                        <div class="float-end">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="new_admin_email">Enter User Email to Promote to
                                                        Admin:</label>
                                                    <input type="email" id="new_admin_email" name="new_admin_email"
                                                        required>
                                                    <button type="submit" class="btn btn-danger btn-sm">Promote to
                                                        Admin</button>
                                                </div>
                                            </form>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-5" style="overflow-x: scroll;">
                                <table class="table table-striped table-hover"
                                    style="width:100% ;" id="user-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th class="d-none d-lg-table-cell">Email</th>
                                            <th class="d-none d-lg-table-cell">Role</th>
                                            <th class="d-none d-lg-table-cell">Created At</th>
                                            <th>Is Verified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Top Statistics -->
                <div class="row">
                    <div class="col-xl-3 col-sm-6">
                        <div class="card card-mini mb-4">
                            <div class="card-body">
                                <h2 class="mb-1">71,503</h2>
                                <p>Online Signups</p>
                                <div class="chartjs-wrapper">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card card-mini  mb-4">
                            <div class="card-body">
                                <h2 class="mb-1">9,503</h2>
                                <p>New Visitors Today</p>
                                <div class="chartjs-wrapper">
                                    <canvas id="dual-line"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card card-mini mb-4">
                            <div class="card-body">
                                <h2 class="mb-1">71,503</h2>
                                <p>Monthly Total Order</p>
                                <div class="chartjs-wrapper">
                                    <canvas id="area-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card card-mini mb-4">
                            <div class="card-body">
                                <h2 class="mb-1">9,503</h2>
                                <p>Total Revenue This Year</p>
                                <div class="chartjs-wrapper">
                                    <canvas id="line"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-8 col-md-12">

                        <!-- Sales Graph -->
                        <div class="card card-default">
                            <div class="card-header">
                                <h2>Sales Of The Year</h2>
                            </div>
                            <div class="card-body">
                                <canvas id="linechart" class="chartjs"></canvas>
                            </div>
                            <div class="card-footer d-flex flex-wrap bg-white p-0">
                                <div class="col-6 px-0">
                                    <div class="text-center p-4">
                                        <h4>$6,308</h4>
                                        <p class="mt-2">Total orders of this year</p>
                                    </div>
                                </div>
                                <div class="col-6 px-0">
                                    <div class="text-center p-4 border-left">
                                        <h4>$70,506</h4>
                                        <p class="mt-2">Total revenue of this year</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-4 col-md-12">

                        <!-- Doughnut Chart -->
                        <div class="card card-default">
                            <div class="card-header justify-content-center">
                                <h2>Orders Overview</h2>
                            </div>
                            <div class="card-body">
                                <canvas id="doChart"></canvas>
                            </div>
                            <a href="#" class="pb-5 d-block text-center text-muted"><i
                                    class="mdi mdi-download mr-2"></i> Download overall report</a>
                            <div class="card-footer d-flex flex-wrap bg-white p-0">
                                <div class="col-6">
                                    <div class="py-4 px-4">
                                        <ul class="d-flex flex-column justify-content-between">
                                            <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #4c84ff"></i>Order Completed</li>
                                            <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #80e1c1 "></i>Order Unpaid</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-6 border-left">
                                    <div class="py-4 px-4 ">
                                        <ul class="d-flex flex-column justify-content-between">
                                            <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #8061ef"></i>Order Pending</li>
                                            <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #ffa128"></i>Order Canceled</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-12">

                        <!-- Polar and Radar Chart -->
                        <div class="card card-default">
                            <div class="card-header justify-content-center">
                                <h2>Sales Overview</h2>
                            </div>
                            <div class="card-body pt-0">
                                <ul class="nav nav-pills mb-5 mt-5 nav-style-fill nav-justified" id="pills-tab"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill"
                                            href="#pills-home" role="tab" aria-controls="pills-home"
                                            aria-selected="true">Sales Status</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill"
                                            href="#pills-profile" role="tab" aria-controls="pills-profile"
                                            aria-selected="false">Monthly Sales</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                        aria-labelledby="pills-home-tab">
                                        <canvas id="polar"></canvas>
                                    </div>
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                        aria-labelledby="pills-profile-tab">
                                        <canvas id="radar"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-4 col-lg-6 col-12">

                        <!-- Top Sell Table -->
                        <div class="card card-table-border-none">
                            <div class="card-header justify-content-between">
                                <h2>Sold by Units</h2>
                                <div>
                                    <button class="text-black-50 mr-2 font-size-20"><i
                                            class="mdi mdi-cached"></i></button>
                                    <div class="dropdown show d-inline-block widget-dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                            id="dropdown-units" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-display="static"></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-units">
                                            <li class="dropdown-item"><a href="#">Action</a></li>
                                            <li class="dropdown-item"><a href="#">Another action</a></li>
                                            <li class="dropdown-item"><a href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-0 compact-units" data-simplebar style="height: 384px;">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td class="text-dark">Backpack</td>
                                            <td class="text-center">9</td>
                                            <td class="text-right">33% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">T-Shirt</td>
                                            <td class="text-center">6</td>
                                            <td class="text-right">150% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Coat</td>
                                            <td class="text-center">3</td>
                                            <td class="text-right">50% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Necklace</td>
                                            <td class="text-center">7</td>
                                            <td class="text-right">150% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Jeans Pant</td>
                                            <td class="text-center">10</td>
                                            <td class="text-right">300% <i
                                                    class="mdi mdi-arrow-down-bold text-danger pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Shoes</td>
                                            <td class="text-center">5</td>
                                            <td class="text-right">100% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">T-Shirt</td>
                                            <td class="text-center">6</td>
                                            <td class="text-right">150% <i
                                                    class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="card-footer bg-white py-4">
                                <a href="#" class="btn-link py-3 text-uppercase">View Report</a>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-4 col-12">

                        <!-- Notification Table -->
                        <div class="card card-default">
                            <div class="card-header justify-content-between mb-1">
                                <h2>Latest Notifications</h2>
                                <div>
                                    <button class="text-black-50 mr-2 font-size-20"><i
                                            class="mdi mdi-cached"></i></button>
                                    <div class="dropdown show d-inline-block widget-dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                            id="dropdown-notification" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-display="static"></a>
                                        <ul class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="dropdown-notification">
                                            <li class="dropdown-item"><a href="#">Action</a></li>
                                            <li class="dropdown-item"><a href="#">Another action</a></li>
                                            <li class="dropdown-item"><a href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body compact-notifications" data-simplebar style="height: 434px;">
                                <div class="media pb-3 align-items-center justify-content-between">
                                    <div
                                        class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-primary text-white">
                                        <i class="mdi mdi-cart-outline font-size-20"></i>
                                    </div>
                                    <div class="media-body pr-3 ">
                                        <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New Order</a>
                                        <p>Selena has placed an new order</p>
                                    </div>
                                    <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10
                                        AM</span>
                                </div>

                                <div class="media py-3 align-items-center justify-content-between">
                                    <div
                                        class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-success text-white">
                                        <i class="mdi mdi-email-outline font-size-20"></i>
                                    </div>
                                    <div class="media-body pr-3">
                                        <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New Enquiry</a>
                                        <p>Phileine has placed an new order</p>
                                    </div>
                                    <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 9
                                        AM</span>
                                </div>


                                <div class="media py-3 align-items-center justify-content-between">
                                    <div
                                        class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-warning text-white">
                                        <i class="mdi mdi-stack-exchange font-size-20"></i>
                                    </div>
                                    <div class="media-body pr-3">
                                        <a class="mt-0 mb-1 font-size-15 text-dark" href="#">Support Ticket</a>
                                        <p>Emma has placed an new order</p>
                                    </div>
                                    <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10
                                        AM</span>
                                </div>

                                <div class="media py-3 align-items-center justify-content-between">
                                    <div
                                        class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-primary text-white">
                                        <i class="mdi mdi-cart-outline font-size-20"></i>
                                    </div>
                                    <div class="media-body pr-3">
                                        <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New order</a>
                                        <p>Ryan has placed an new order</p>
                                    </div>
                                    <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10
                                        AM</span>
                                </div>

                                <div class="media py-3 align-items-center justify-content-between">
                                    <div
                                        class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-info text-white">
                                        <i class="mdi mdi-calendar-blank font-size-20"></i>
                                    </div>
                                    <div class="media-body pr-3">
                                        <a class="mt-0 mb-1 font-size-15 text-dark" href="">Comapny Meetup</a>
                                        <p>Phileine has placed an new order</p>
                                    </div>
                                    <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10
                                        AM</span>
                                </div>
                            </div>
                            <div class="mt-3"></div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12">

                        <!-- Recent Order Table -->
                        <div class="card card-table-border-none recent-orders" id="recent-orders">
                            <div class="card-header justify-content-between">
                                <h2>Recent Orders</h2>
                                <div class="date-range-report ">
                                    <span></span>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-5">
                                <table class="table card-table table-responsive table-responsive-large"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Product Name</th>
                                            <th class="d-none d-lg-table-cell">Units</th>
                                            <th class="d-none d-lg-table-cell">Order Date</th>
                                            <th class="d-none d-lg-table-cell">Order Cost</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>24541</td>
                                            <td>
                                                <a class="text-dark" href=""> Coach Swagger</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">1 Unit</td>
                                            <td class="d-none d-lg-table-cell">Oct 20, 2018</td>
                                            <td class="d-none d-lg-table-cell">$230</td>
                                            <td>
                                                <span class="badge badge-success">Completed</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="" role="button"
                                                        id="dropdown-recent-order1" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdown-recent-order1">
                                                        <li class="dropdown-item">
                                                            <a href="#">View</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="#">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>24541</td>
                                            <td>
                                                <a class="text-dark" href=""> Toddler Shoes, Gucci Watch</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">2 Units</td>
                                            <td class="d-none d-lg-table-cell">Nov 15, 2018</td>
                                            <td class="d-none d-lg-table-cell">$550</td>
                                            <td>
                                                <span class="badge badge-warning">Delayed</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                                        id="dropdown-recent-order2" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdown-recent-order2">
                                                        <li class="dropdown-item">
                                                            <a href="#">View</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="#">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>24541</td>
                                            <td>
                                                <a class="text-dark" href=""> Hat Black Suits</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">1 Unit</td>
                                            <td class="d-none d-lg-table-cell">Nov 18, 2018</td>
                                            <td class="d-none d-lg-table-cell">$325</td>
                                            <td>
                                                <span class="badge badge-warning">On Hold</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                                        id="dropdown-recent-order3" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdown-recent-order3">
                                                        <li class="dropdown-item">
                                                            <a href="#">View</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="#">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>24541</td>
                                            <td>
                                                <a class="text-dark" href=""> Backpack Gents, Swimming Cap Slin</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">5 Units</td>
                                            <td class="d-none d-lg-table-cell">Dec 13, 2018</td>
                                            <td class="d-none d-lg-table-cell">$200</td>
                                            <td>
                                                <span class="badge badge-success">Completed</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                                        id="dropdown-recent-order4" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdown-recent-order4">
                                                        <li class="dropdown-item">
                                                            <a href="#">View</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="#">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>24541</td>
                                            <td>
                                                <a class="text-dark" href=""> Speed 500 Ignite</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">1 Unit</td>
                                            <td class="d-none d-lg-table-cell">Dec 23, 2018</td>
                                            <td class="d-none d-lg-table-cell">$150</td>
                                            <td>
                                                <span class="badge badge-danger">Cancelled</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                                        id="dropdown-recent-order5" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdown-recent-order5">
                                                        <li class="dropdown-item">
                                                            <a href="#">View</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="#">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">

                        <!-- To Do list -->
                        <div class="card card-default todo-table" id="todo">
                            <div class="card-header d-block pb-0 ">
                                <div class="todo-single-item mb-0" id="todo-input">
                                    <form class="todo-form">
                                        <div class="input-group mb-0">
                                            <input type="text" class="form-control border-right-0"
                                                placeholder="Add Todo" required="" autofocus>
                                            <div class="input-group-append ml-0">
                                                <button class="input-group-text border-0 btn bg-primary" type="submit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="18"
                                                        viewBox="0 0 448 448" width="18" class="">
                                                        <g>
                                                            <path
                                                                d="m408 184h-136c-4.417969 0-8-3.582031-8-8v-136c0-22.089844-17.910156-40-40-40s-40 17.910156-40 40v136c0 4.417969-3.582031 8-8 8h-136c-22.089844 0-40 17.910156-40 40s17.910156 40 40 40h136c4.417969 0 8 3.582031 8 8v136c0 22.089844 17.910156 40 40 40s40-17.910156 40-40v-136c0-4.417969 3.582031-8 8-8h136c22.089844 0 40-17.910156 40-40s-17.910156-40-40-40zm0 0"
                                                                data-original="#000000" class="active-path"
                                                                data-old_color="#000000" fill="#FFFFFF" />
                                                        </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card-body compact-to-do-list" data-simplebar style="height: 450px;">
                                <div class="todo-list" id="todo-list">
                                    <div id="item"
                                        class="todo-single-item todo-item d-flex flex-row justify-content-between finished alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Finish Dashboard UI Kit update</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between current alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Create new prototype for the landing page</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Add new Google Analytics code to all main files sed auctor lacus in sem
                                            interdum, ac gravida tortor elementum. Cras magna enim.</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Integer et porta odio, pulvinar pretium eros. Curabitur vel tellus
                                            erat.</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Pellentesque blandit ut eros sed vehicula.</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec felis
                                            ligula, fringilla in volutpat sit amet, viverra nec mi. Donec at dui
                                            dolor.</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-single-item d-flex flex-row justify-content-between mb-1 alert alert-dismissible fade show"
                                        role="alert">
                                        <i class="mdi"></i>
                                        <span>Update parallax scroll on team page</span>

                                        <div class="task-content">
                                            <span data-dismiss="alert" aria-label="Close">
                                                <svg class="remove-task" id="Capa_1"
                                                    enable-background="new 0 0 515.556 515.556" height="16"
                                                    viewBox="0 0 515.556 515.556" width="16"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path class=""
                                                        d="m64.444 451.111c0 35.526 28.902 64.444 64.444 64.444h257.778c35.542 0 64.444-28.918 64.444-64.444v-322.222h-386.666z" />
                                                    <path
                                                        d="m322.222 32.222v-32.222h-128.889v32.222h-161.111v64.444h451.111v-64.444z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3"></div>
                        </div>

                    </div>

                    <div class="col-xl-6">

                        <!-- area chart -->
                        <div class="card card-default">
                            <div class="card-header d-block d-md-flex justify-content-between">
                                <h2>World Wide Customer </h2>
                                <div class="dropdown show d-inline-block widget-dropdown ml-auto">
                                    <a class="dropdown-toggle" href="#" role="button" id="world-dropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        data-display="static">
                                        World Wide
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="world-dropdown">
                                        <li class="dropdown-item"><a href="#">Continetal chart</a></li>
                                        <li class="dropdown-item"><a href="#">Sub-continental</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body vector-map-world">
                                <div id="world" style="height: 100%; width: 100%;"></div>
                            </div>
                            <div class="card-footer d-flex flex-wrap bg-white p-0">
                                <div class="col-6">
                                    <div class="p-4">
                                        <ul class="d-flex flex-column justify-content-between">
                                            <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #29cc97"></i>America <span
                                                    class="float-right">5k</span></li>
                                            <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #4c84ff "></i>Australia <span
                                                    class="float-right">3k</span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-4 border-left">
                                        <ul class="d-flex flex-column justify-content-between">
                                            <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #ffa128"></i>Europe <span
                                                    class="float-right">4k</span></li>
                                            <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                                    style="color: #fe5461"></i>Africa <span
                                                    class="float-right">2k</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-5">

                        <!-- New Customers -->
                        <div class="card card-table-border-none">
                            <div class="card-header justify-content-between ">
                                <h2>New Customers</h2>
                                <div>
                                    <button class="text-black-50 mr-2 font-size-20">
                                        <i class="mdi mdi-cached"></i>
                                    </button>
                                    <div class="dropdown show d-inline-block widget-dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                            id="dropdown-customar" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-display="static">
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="dropdown-customar">
                                            <li class="dropdown-item"><a href="#">Action</a></li>
                                            <li class="dropdown-item"><a href="#">Another action</a></li>
                                            <li class="dropdown-item"><a href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0" data-simplebar style="height: 468px;">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="media-image mr-3 rounded-circle">
                                                        <a href="profile.html"><img class="rounded-circle w-45"
                                                                src="assets/admin/assets/img/user/u1.jpg"
                                                                alt="customer image"></a>
                                                    </div>
                                                    <div class="media-body align-self-center">
                                                        <a href="profile.html">
                                                            <h6 class="mt-0 text-dark font-weight-medium">Selena Wagner
                                                            </h6>
                                                        </a>
                                                        <small>@selena.oi</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>2 Orders</td>
                                            <td class="text-dark d-none d-md-block">$150</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="media-image mr-3 rounded-circle">
                                                        <a href="profile.html"><img class="rounded-circle w-45"
                                                                src="assets/admin/assets/img/user/u2.jpg"
                                                                alt="customer image"></a>
                                                    </div>
                                                    <div class="media-body align-self-center">
                                                        <a href="profile.html">
                                                            <h6 class="mt-0 text-dark font-weight-medium">Walter Reuter
                                                            </h6>
                                                        </a>
                                                        <small>@walter.me</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>5 Orders</td>
                                            <td class="text-dark d-none d-md-block">$200</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="media-image mr-3 rounded-circle">
                                                        <a href="profile.html"><img class="rounded-circle w-45"
                                                                src="assets/admin/assets/img/user/u3.jpg"
                                                                alt="customer image"></a>
                                                    </div>
                                                    <div class="media-body align-self-center">
                                                        <a href="profile.html">
                                                            <h6 class="mt-0 text-dark font-weight-medium">Larissa
                                                                Gebhardt</h6>
                                                        </a>
                                                        <small>@larissa.gb</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>1 Order</td>
                                            <td class="text-dark d-none d-md-block">$50</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="media-image mr-3 rounded-circle">
                                                        <a href="profile.html"><img class="rounded-circle w-45"
                                                                src="assets/admin/assets/img/user/u4.jpg"
                                                                alt="customer image"></a>
                                                    </div>
                                                    <div class="media-body align-self-center">
                                                        <a href="profile.html">
                                                            <h6 class="mt-0 text-dark font-weight-medium">Albrecht
                                                                Straub</h6>
                                                        </a>
                                                        <small>@albrech.as</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>2 Orders</td>
                                            <td class="text-dark d-none d-md-block">$100</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    <div class="media-image mr-3 rounded-circle">
                                                        <a href="profile.html"><img class="rounded-circle w-45"
                                                                src="assets/admin/assets/img/user/u5.jpg"
                                                                alt="customer image"></a>
                                                    </div>
                                                    <div class="media-body align-self-center">
                                                        <a href="profile.html">
                                                            <h6 class="mt-0 text-dark font-weight-medium">Leopold Ebert
                                                            </h6>
                                                        </a>
                                                        <small>@leopold.et</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>1 Order</td>
                                            <td class="text-dark d-none d-md-block">$60</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-7">

                        <!-- Top Products -->
                        <div class="card card-default">
                            <div class="card-header justify-content-between mb-4">
                                <h2>Top Products</h2>
                                <div>
                                    <button class="text-black-50 mr-2 font-size-20"><i
                                            class="mdi mdi-cached"></i></button>
                                    <div class="dropdown show d-inline-block widget-dropdown">
                                        <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                            id="dropdown-product" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" data-display="static">
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="dropdown-product">
                                            <li class="dropdown-item"><a href="#">Update Data</a></li>
                                            <li class="dropdown-item"><a href="#">Detailed Log</a></li>
                                            <li class="dropdown-item"><a href="#">Statistics</a></li>
                                            <li class="dropdown-item"><a href="#">Clear Data</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body py-0">
                                <div class="media d-flex mb-5">
                                    <div class="media-image align-self-center mr-3 rounded">
                                        <a href="#"><img src="assets/admin/assets/img/products/p1.jpg"
                                                alt="customer image"></a>
                                    </div>
                                    <div class="media-body align-self-center">
                                        <a href="#">
                                            <h6 class="mb-3 text-dark font-weight-medium"> Coach Swagger</h6>
                                        </a>
                                        <p class="float-md-right"><span class="text-dark mr-2">20</span>Sales</p>
                                        <p class="d-none d-md-block">Statement belting with double-turnlock hardware
                                            adds “swagger” to a simple.</p>
                                        <p class="mb-0">
                                            <del>$300</del>
                                            <span class="text-dark ml-3">$250</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="media d-flex mb-5">
                                    <div class="media-image align-self-center mr-3 rounded">
                                        <a href="#"><img src="assets/admin/assets/img/products/p2.jpg"
                                                alt="customer image"></a>
                                    </div>
                                    <div class="media-body align-self-center">
                                        <a href="#">
                                            <h6 class="mb-3 text-dark font-weight-medium"> Coach Swagger</h6>
                                        </a>
                                        <p class="float-md-right"><span class="text-dark mr-2">20</span>Sales</p>
                                        <p class="d-none d-md-block">Statement belting with double-turnlock hardware
                                            adds “swagger” to a simple.</p>
                                        <p class="mb-0">
                                            <del>$300</del>
                                            <span class="text-dark ml-3">$250</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="media d-flex mb-5">
                                    <div class="media-image align-self-center mr-3 rounded">
                                        <a href="#"><img src="assets/admin/assets/img/products/p3.jpg"
                                                alt="customer image"></a>
                                    </div>
                                    <div class="media-body align-self-center">
                                        <a href="#">
                                            <h6 class="mb-3 text-dark font-weight-medium"> Gucci Watch</h6>
                                        </a>
                                        <p class="float-md-right"><span class="text-dark mr-2">10</span>Sales</p>
                                        <p class="d-none d-md-block">Statement belting with double-turnlock hardware
                                            adds “swagger” to a simple.</p>
                                        <p class="mb-0">
                                            <del>$300</del>
                                            <span class="text-dark ml-3">$50</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>





            </div> <!-- End Content -->
        </div>
    </main>
    <!--Main layout-->
    <!-- Add User Modal -->
    <div class="modal" id="add-user-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="add-user-form">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#user-table').DataTable({
                ajax: {
                    url: 'includes/fetch_users.php',
                    dataSrc: function (json) {
                        return json.success ? json.users : [];
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'username' },
                    { data: 'email' },
                    { data: 'role' },
                    { data: 'created_at' },
                    {
                        data: 'is_verified',
                        render: function (data) {
                            return data ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: 'id',
                        render: function (id) {
                            return `
                        <button class="btn btn-danger btn-sm delete-user-btn" data-id="${id}">
                            Delete
                        </button>
                    `;
                        }
                    }
                ],
                destroy: true,
                paging: true,
                searching: true
            });

            // Delete user
            $(document).on('click', '.delete-user-btn', function () {
                const userId = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.post('includes/delete_user.php', { id: userId }, function (response) {
                        if (response.success) {
                            table.ajax.reload(null, false); // Reload without resetting pagination
                        } else {
                            alert(response.message);
                        }
                    }, 'json');
                }
            });

            // Add user form submit
            $('#add-user-form').on('submit', function (e) {
                e.preventDefault();
                $.post('includes/add_user.php', $(this).serialize(), function (response) {
                    if (response.success) {
                        $('#add-user-modal').modal('hide');
                        $('#add-user-form')[0].reset();
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                }, 'json');
            });

            // Show add user modal
            $('#add-user-btn').on('click', function () {
                $('#add-user-modal').modal('show');
            });
        });

    </script>
    <script src="assets/admin/assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/admin/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/admin/assets/plugins/simplebar/simplebar.min.js"></script>

    <script src='assets/admin/assets/plugins/charts/Chart.min.js'></script>
    <script src='assets/admin/assets/js/chart.js'></script>
    <script src='assets/admin/assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js'></script>
    <script src='assets/admin/assets/plugins/jvectormap/jquery-jvectormap-world-mill.js'></script>
    <script src='assets/admin/assets/js/vector-map.js'></script>
    <script src='assets/admin/assets/plugins/daterangepicker/moment.min.js'></script>
    <script src='assets/admin/assets/plugins/daterangepicker/daterangepicker.js'></script>
    <script src='assets/admin/assets/js/date-range.js'></script>
    <script src='assets/admin/assets/plugins/toastr/toastr.min.js'></script>
    <script src="assets/admin/assets/js/sleek.js"></script>
    <link href="assets/admin/assets/options/optionswitch.css" rel="stylesheet">
    <script src="assets/admin/assets/options/optionswitcher.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>