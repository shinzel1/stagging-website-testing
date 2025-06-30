<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $response = [];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../assets/images/uploads/";

            // Check if directory exists, create if not
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxFileSize) {
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $imageUrl = "assets/images/uploads/" . $fileName;
                } else {
                    echo json_encode(['error' => "Failed to upload image."]);
                    exit;
                }
            } else {
                echo json_encode(['error' => "Invalid file type or file is too large."]);
                exit;
            }
        } else {
            $imageUrl = "";
        }

        // Decode categories
        $categoryIds = explode(',', $_POST['categories']);
        $categoryIds = array_map('trim', $categoryIds); // Trim whitespace

        // Ensure categoryIds is an array and contains valid integers
        if (empty($categoryIds) || !is_array($categoryIds)) {
            echo json_encode(['error' => "Invalid category format."]);
            exit;
        }

        // Insert product into the database
        $stmt = $pdo->prepare("INSERT INTO products 
            (name, image_url, description, original_price, price, discounted_price, quantity, seller, rating, reviews, flavour)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['name'],
            $imageUrl,
            $_POST['description'],
            $_POST['original_price'],
            $_POST['price'],
            $_POST['discounted_price'],
            $_POST['quantity'],
            $_POST['seller'],
            5, // Default rating (or adjust as needed)
            0, // Default reviews count
            $_POST['flavour']
        ]);

        // Get the last inserted product ID
        $productId = $pdo->lastInsertId();

        // Insert product-category relationships
        $stmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");

        foreach ($categoryIds as $categoryId) {
            $categoryId = (int) $categoryId; // Ensure integer

            // Check if category exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ?");
            $checkStmt->execute([$categoryId]);
            $categoryExists = $checkStmt->fetchColumn();

            if ($categoryExists) {
                $stmt->execute([$productId, $categoryId]);
            }
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
