<?php
include 'includes/header.php';
require_once("config/database_connection.php");

// Fetch all products
$stmt = $pdo->query("SELECT id, name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 p-5">
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
    <main>
    <h2>Associate Two Products as Variations</h2>
    <form action="includes/add_variation.php" method="POST">
        <div class="mb-3">
            <label for="product_1">Select First Product</label>
            <select name="product_id_1" id="product_1" class="form-control" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="product_2">Select Second Product</label>
            <select name="product_id_2" id="product_2" class="form-control" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Link Products</button>
    </form>
    </main>
</div>
