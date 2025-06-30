<?php
require_once("../config/database_connection.php");
session_start();

$response = ["success" => false, "message" => ""];

if (!isset($_SESSION['user_id'])) {
    $response["message"] = "User not logged in.";
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

try {
    // Check if product exists in the wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $wishlist_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($wishlist_item) {
        // Remove from wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $response["message"] = "Removed from wishlist.";
        $response["wishlist_status"] = false;
    } else {
        // Add to wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        $response["message"] = "Added to wishlist.";
        $response["wishlist_status"] = true;
    }

    $response["success"] = true;
} catch (PDOException $e) {
    $response["message"] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>
