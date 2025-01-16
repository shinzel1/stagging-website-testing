<?php
// admin_dashboard.php
session_start();
include("pages/style.php");
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

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

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
</head>

<body>





    <header style="box-shadow: -1px 7px 15px -4px rgba(0,0,0,0.76);">
        <div class="container-fluid">
            <div class="row py-3 border-bottom">

                <div
                    class="col-sm-4 col-lg-2 text-center text-sm-start d-flex gap-3 justify-content-center justify-content-md-start">
                    <div class="d-flex align-items-center my-3 my-sm-0">
                        <a href="index.php">
                            <img src="assets/images/logo.jpeg" alt="logo" class="img-fluid" style="height:40px">
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <use xlink:href="#menu"></use>
                        </svg>
                    </button>
                </div>

                <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-4">
                    <div class="search-bar row bg-light p-2 rounded-4">

                        <div class="col-11 col-md-11">
                            <form id="search-form" class="text-center" action="index.html" method="post">
                                <input type="text" class="form-control border-0 bg-transparent"
                                    placeholder="Product Search">
                            </form>
                        </div>
                        <div class="col-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <ul
                        class="navbar-nav list-unstyled d-flex flex-row gap-3 gap-lg-5 justify-content-center flex-wrap align-items-center mb-0 fw-bold text-uppercase text-dark">
                        <!-- <li class="nav-item">
                            <a href="?page=contact" class="nav-link">CRM</a>
                        </li> -->
                        <li class="nav-item active">
                            <a href="products.php" class="nav-link">products</a>
                        </li>
                        <li class="nav-item active">
                            <a href="editPages.php" class="nav-link">Pages</a>
                        </li>
                        <li class="nav-item">
                            <a href="inventory.php" class="nav-link">Inventory</a>
                        </li>
                    </ul>
                </div>

                <div
                    class="col-sm-8 col-lg-2 d-flex gap-5 align-items-center justify-content-center justify-content-sm-end">
                    <ul class="d-flex justify-content-end list-unstyled m-0">
                        <li>
                            <svg width="24" height="24">
                                <use xlink:href="#user"></use>
                            </svg> Admin
                        </li>
                        <li>
                            <a href="logout.php" class="p-2 mx-1">
                                <svg width="24" height="24">
                                    <use xlink:href="#user"></use>
                                </svg> Logout
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </header>


    <h1>Welcome, Admin</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="new_admin_email">Enter User Email to Promote to Admin:</label>
        <input type="email" id="new_admin_email" name="new_admin_email" required>
        <button type="submit">Promote to Admin</button>
    </form>
</body>

</html>