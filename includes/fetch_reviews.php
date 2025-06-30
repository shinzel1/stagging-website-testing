<?php
require_once("../config/database_connection.php");

$product_id = intval($_GET["product_id"]);
$sort = $_GET["sort"] ?? "latest";
$page = intval($_GET["page"] ?? 1);
$limit = 5;
$offset = ($page - 1) * $limit;
$response = ["success" => false];

$orderBy = "r.created_at DESC";
if ($sort == "highest")
    $orderBy = "r.rating DESC";
if ($sort == "lowest")
    $orderBy = "r.rating ASC";

try {
    $stmt = $pdo->prepare("SELECT r.*, u.username 
                           FROM reviews r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.product_id = ? 
                           ORDER BY $orderBy 
                           LIMIT $limit OFFSET $offset");
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total reviews for pagination
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $totalReviews = $stmt->fetchColumn();
    $totalPages = ceil($totalReviews / $limit);

    $response["success"] = true;
    $response["reviews"] = $reviews;
    $response["totalPages"] = $totalPages;
} catch (PDOException $e) {
    $response["error"] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>