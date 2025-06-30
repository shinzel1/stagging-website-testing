<?php
require_once("../config/database_connection.php");

$response = ["success" => false, "error" => "", "image_url" => "", "image_field" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST["product_id"];
    $image_field = $_POST["image_field"]; // Determines which image column to update

    // Validate if the field name exists in our database
    $allowed_fields = ["image_url", "image2", "image3", "image4", "image5"];
    if (!in_array($image_field, $allowed_fields)) {
        $response["error"] = "Invalid image field!";
        echo json_encode($response);
        exit;
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = realpath(path: dirname(__FILE__)) . "/../assets/images/uploads/";

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Validate file type and size
        $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES["image"]["type"], $allowedTypes) || $_FILES["image"]["size"] > $maxFileSize) {
            $response["error"] = "Invalid file type or size!";
            echo json_encode($response);
            exit;
        }

        // Generate unique file name
        $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $uploadDir . $fileName;
        $imageUrl = "assets/images/uploads/" . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Update the database with the new image path
            $stmt = $pdo->prepare("UPDATE products SET $image_field = ? WHERE id = ?");
            if ($stmt->execute([$imageUrl, $product_id])) {
                $response["success"] = true;
                $response["image_url"] = $imageUrl;
                $response["image_field"] = $image_field;
            } else {
                $response["error"] = "Database update failed!";
            }
        } else {
            $response["error"] = "File upload failed!";
        }
    } else {
        $response["error"] = "No file uploaded or error occurred!";
    }
} else {
    $response["error"] = "Invalid request!";
}

echo json_encode($response);
?>
