<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $flavor_name = trim($_POST['flavor_name']);
    $price = floatval($_POST['price']);
    $parent_product_id = !empty($_POST['parent_product_id']) ? intval($_POST['parent_product_id']) : null;

    if (empty($name) || empty($flavor_name) || $price <= 0) {
        die("Invalid input. Please fill all fields.");
    }

    try {
        // Insert the new variation into products table
        $stmt = $pdo->prepare("INSERT INTO products (name, flavor_name, price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $flavor_name, $price]);

        $variant_product_id = $pdo->lastInsertId();

        // If linked to a parent product, save in relationship table
        if ($parent_product_id) {
            $stmt = $pdo->prepare("INSERT INTO product_variations (parent_product_id, variant_product_id) VALUES (?, ?)");
            $stmt->execute([$parent_product_id, $variant_product_id]);
        }

        echo "Product variation added successfully!";
        header("Location: ../add_flavor_product.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
