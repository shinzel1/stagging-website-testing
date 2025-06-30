<?php
include 'includes/header.php';
require_once("config/database_connection.php");

// Fetch all products
$stmt = $pdo->query("SELECT id, name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 p-5">
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
</div>

<?php include 'includes/footer.php'; ?>
