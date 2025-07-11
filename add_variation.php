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

    <?php require_once("includes/side_menu.php"); ?>

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
<?php require_once("includes/footer_scripts.php"); ?>