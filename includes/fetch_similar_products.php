<?php
require_once("../config/database_connection.php");
$response = ["success" => false, "products" => []];
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    try {
        // Step 1: Fetch categories from product_categories table
        $stmt = $pdo->prepare("
            SELECT GROUP_CONCAT(category_id) AS categories 
            FROM product_categories 
            WHERE product_id = ?
        ");
        $stmt->execute([$product_id]);
        $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoryData || empty($categoryData['categories'])) {
            $response["error"] = "No categories found for this product.";
        } else {
            $categories = explode(',', $categoryData['categories']); // Convert CSV string to array
            if (!empty($categories)) {
                // Step 2: Fetch similar products based on category matches
                $placeholders = implode(',', array_fill(0, count($categories), '?'));
                $stmt = $pdo->prepare("
                SELECT DISTINCT p.id, p.name, p.image_url, p.description, p.price 
                FROM products p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                WHERE pc.category_id IN ($placeholders) 
                AND p.id != ? 
                LIMIT 5
            ");
                $stmt->execute([...$categories, $product_id]);
                $similar_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response["success"] = true;
                $response["products"] = $similar_products;
            } else {
                $response["error"] = "No categories found for this product.";
            }
        }
    } catch (PDOException $e) {
        $response["error"] = "Database error: " . $e->getMessage();
    }
} else {
    $response["error"] = "No product ID provided.";
}

echo json_encode($response);
?>