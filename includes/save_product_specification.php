<?php
require_once("../config/database_connection.php");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST["product_id"]);
    $specification = $_POST["specification"] ?? "";

    if (!$product_id || empty($specification)) {
        $response["error"] = "Invalid product ID or missing specification data.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE products SET specification = ? WHERE id = ?");
            $stmt->execute([$specification, $product_id]);

            $response["success"] = true;
        } catch (PDOException $e) {
            $response["error"] = "Database error: " . $e->getMessage();
        }
    }
}

echo json_encode($response);
?>