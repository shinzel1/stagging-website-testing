<?php
require_once("../config/database_connection.php");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST["product_id"]);

    if (!$product_id) {
        $response["error"] = "Invalid product ID.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT specification FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result["specification"])) {
                $response["success"] = true;
                $response["specification"] = json_decode($result["specification"], true);
            } else {
                $response["error"] = "No specification found for this product.";
            }
        } catch (PDOException $e) {
            $response["error"] = "Database error: " . $e->getMessage();
        }
    }
}

echo json_encode($response);
?>
