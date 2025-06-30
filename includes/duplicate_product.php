<?php
require_once("../config/database_connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    try {
        // Fetch original product
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Original product not found']);
            exit;
        }

        // Prepare new product data
        $product['name'] .= ' (Copy)';
        unset($product['id']);

        $columns = array_keys($product);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $columnNames = implode(', ', $columns);

        $stmt = $pdo->prepare("INSERT INTO products ($columnNames) VALUES ($placeholders)");
        $stmt->execute(array_values($product));
        $newId = $pdo->lastInsertId();

        // Copy product_categories
        $catStmt = $pdo->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
        $catStmt->execute([$productId]);
        $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

        $mapStmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categories as $catId) {
            $mapStmt->execute([$newId, $catId]);
        }

        echo json_encode(['success' => true, 'message' => 'Duplicated', 'new_id' => $newId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>