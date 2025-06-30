<?php
require_once("../config/database_connection.php");

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $seller = $_POST['seller'];
        $rating = $_POST['rating'];
        $reviews = $_POST['reviews'];
        $flavour = $_POST['flavour'];
        $featured_product = $_POST['featured_product'] ?? "";
        $hide_product = $_POST['hide_product'] ?? "";
        $isfeatured = 0;
        if ($featured_product == "true") {
            $isfeatured = 1;
        } else {
            $isfeatured = 0;
        }
        $isHideProduct = 0;
        if ($hide_product == "true") {
            $isHideProduct = 1;
        } else {
            $isHideProduct = 0;
        }

        // Decode categories (handle JSON input)
        $categoryIds = explode(',', string: $_POST['categories']);

        // Ensure categoryIds is an array
        if (!is_array($categoryIds)) {
            $response['error'] = "Invalid category format.";
            echo json_encode($response);
            exit;
        }

        // Handle image upload
        $imageUrl = $_POST['image_url'] ?? ''; // Default to existing image if no new file is uploaded

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = realpath(dirname(__FILE__)) . "/../assets/images/uploads/";

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024;

            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxFileSize) {
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . '/' . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $imageUrl = "assets/images/uploads/" . $fileName;
                } else {
                    $response['error'] = "Failed to upload image.";
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['error'] = "Invalid file type or file is too large.";
                echo json_encode($response);
                exit;
            }
        }

        // Update product
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, quantity = ?, seller = ?, rating = ?, reviews = ?, image_url = ? , featured_product = ?, hide_product = ?,flavour = ? 
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $quantity, $seller, $rating, $reviews, $imageUrl, $isfeatured, $isHideProduct, $flavour, $id]);

        // Step 1: Clear existing category relationships
        $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$id]);

        // Step 2: Validate and insert category relationships
        $stmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $categoryId) {
            $categoryId = (int) trim($categoryId); // Ensure it's an integer

            // Check if category ID exists in categories table
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ?");
            $checkStmt->execute(params: [$categoryId]);
            $categoryExists = $checkStmt->fetchColumn();

            if ($categoryExists) {
                $stmt->execute([$id, $categoryId]);
            }
        }

        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = "Database error: " . $e->getMessage();
    }
} else {
    $response['error'] = "Invalid request method.";
}

echo json_encode($response);
?>