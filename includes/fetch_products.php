<?php
// Database connection
require_once("../config/database_connection.php");
session_start(); // Start session to get user ID

// Get logged-in user ID (if available)
$user_id = $_SESSION['user_id'] ?? null;

// Fetch products based on search term
if (isset($_POST['searchTerm'])) {
    $searchTerm = '%' . $_POST['searchTerm'] . '%';
    try {
        $query = "
            SELECT * FROM products 
            WHERE (products.name LIKE :searchTerm 
                OR products.description LIKE :searchTerm 
                OR products.categories LIKE CONCAT('%,', :searchTerm, ',%')
                OR products.categories LIKE CONCAT(:searchTerm, ',%')
                OR products.categories LIKE CONCAT('%,', :searchTerm)
                OR products.categories LIKE :searchTerm)
            AND products.hide_product = 0  -- Ensure only visible products are fetched
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add wishlist status if user is logged in
        if ($user_id) {
            foreach ($products as &$product) {
                $wishlist_stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                $wishlist_stmt->execute([$user_id, $product['id']]);
                $product['in_wishlist'] = $wishlist_stmt->fetch() ? true : false;
            }
        }

        echo json_encode(["success" => true, "products" => $products]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to fetch products: " . $e->getMessage()]);
    }
} 

// Fetch products based on category, price, and rating filters
elseif (isset($_POST['category']) || isset($_POST['min_price']) || isset($_POST['rating'])) {
    // Retrieve filter values with default fallbacks
    $category = !empty($_POST['category']) ? trim($_POST['category']) : "";
    $price1 = isset($_POST['min_price']) ? (int) $_POST['min_price'] : 0;
    $price2 = isset($_POST['max_price']) ? (int) $_POST['max_price'] : 10000;

    // Base SQL query with JOIN on product_categories
    $query = "SELECT p.* 
              FROM products p
              INNER JOIN product_categories pc ON p.id = pc.product_id
              INNER JOIN categories c ON pc.category_id = c.id
              WHERE p.hide_product = 0";  // Ensure only visible products are fetched

    $params = [];

    // Add category filter if provided
    if (!empty($category)) {
        $query .= " AND c.id = :category";
        $params[":category"] = $category;
    }
    
    // Add price filter if provided
    if ($price1 >= 0 && $price2 > $price1) {
        $query .= " AND p.price BETWEEN :price1 AND :price2";
        $params[":price1"] = $price1;
        $params[":price2"] = $price2;
    }

    // Group by product ID to prevent duplicates and order by rating
    $query .= " GROUP BY p.id ORDER BY p.rating DESC";

    try {
        $stmt = $pdo->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, is_int($value) || is_float($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add wishlist status if user is logged in
        if ($user_id) {
            foreach ($products as &$product) {
                $wishlist_stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                $wishlist_stmt->execute([$user_id, $product['id']]);
                $product['in_wishlist'] = $wishlist_stmt->fetch() ? true : false;
            }
        }

        echo json_encode(["success" => true, "products" => $products]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Database Error: " . $e->getMessage()]);
    }
}

?>
