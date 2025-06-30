<?php
require_once("../config/database_connection.php");

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST["product_id"];
    $image_field = $_POST["image_field"];

    if (!in_array($image_field, ["image_url", "image2", "image3", "image4", "image5"])) {
        $response["error"] = "Invalid image field!";
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE products SET $image_field = NULL WHERE id = ?");
    if ($stmt->execute([$product_id])) {
        $response["success"] = true;
    } else {
        $response["error"] = "Failed to delete image!";
    }
}

echo json_encode($response);
?>
