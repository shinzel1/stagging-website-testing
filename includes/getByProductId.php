<?php
require_once("../config/database_connection.php");

try {
    // Prepare the SQL query
    $stmt = $pdo->prepare("SELECT id, name ,slug, image_url, description, price, quantity, categories, seller, rating, reviews,featured_product,hide_product,flavour, created_at FROM products WHERE id = ?");

    // Execute the query with the provided product ID
    $stmt->execute([$_POST['product_Id']]);

    // Fetch the product
    $product = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch instead of fetchAll for a single record

    // Check if a product was found
    if ($product) {
        echo json_encode(["success" => true, "product" => $product]);
    } else {
        echo json_encode(["success" => false, "error" => "Product not found."]);
    }
} catch (PDOException $e) {
    // Handle any exceptions
    echo json_encode(["success" => false, "error" => "Error fetching product: " . $e->getMessage()]);
}
?>