<?php
require_once("../config/database_connection.php");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST["product_id"]);
    $user_id = $_POST["user_id"] ?? null;
    $rating = intval($_POST["rating"]);
    $review = trim($_POST["review"]);

    if ($rating < 1 || $rating > 5) {
        $response["error"] = "Invalid rating value.";
    } else {
        try {
            // Check if user has purchased this product
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders o 
                                   JOIN order_items oi ON o.order_id = oi.order_id 
                                   WHERE o.user_id = ? AND oi.product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $is_verified = $stmt->fetchColumn() > 0 ? 1 : 0;

            // Insert review
            $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, review, is_verified) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $user_id, $rating, $review, $is_verified]);

            // Update product's average rating
            $stmt = $pdo->prepare("UPDATE products SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?) WHERE id = ?");
            $stmt->execute([$product_id, $product_id]);

            $response["success"] = true;
        } catch (PDOException $e) {
            $response["error"] = "Database error: " . $e->getMessage();
        }
    }
}

echo json_encode($response);
?>
