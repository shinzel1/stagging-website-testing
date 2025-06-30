<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id_1 = intval($_POST['product_id_1']);
    $product_id_2 = intval($_POST['product_id_2']);

    if ($product_id_1 === $product_id_2) {
        die("Error: You cannot link a product to itself.");
    }

    try {
        // Check if the relationship already exists
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) FROM product_variations 
            WHERE (product_id_1 = ? AND product_id_2 = ?) 
               OR (product_id_1 = ? AND product_id_2 = ?)
        ");
        $checkStmt->execute([$product_id_1, $product_id_2, $product_id_2, $product_id_1]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            die("These products are already linked.");
        }

        // Insert relationship in both directions
        $stmt = $pdo->prepare("INSERT INTO product_variations (product_id_1, product_id_2) VALUES (?, ?)");
        $stmt->execute([$product_id_1, $product_id_2]);

        echo "Products linked successfully!";
        header("Location: ../add_variation.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
