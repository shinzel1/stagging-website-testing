<?php

require_once("../config/database_connection.php");

// Process the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input from JSON request
    $input = json_decode(file_get_contents('php://input'), true);
    $query = $input['query'] ?? '';
    $category = $input['category'] ?? 'all';

    // Validate input
    if (strlen(trim($query)) < 3) {
        echo json_encode(['success' => false, 'products' => [], 'error' => 'Query too short']);
        exit;
    }

    try {
        // Base SQL query
        $sql = "SELECT id, name, price,image_url FROM products WHERE name LIKE :query";

        // Add category filter if not 'all'
        if ($category !== 'all') {
            $sql .= " AND category = :category";
        }

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindValue(':query', '%' . $query . '%');
        if ($category !== 'all') {
            $stmt->bindValue(':category', $category);
        }

        // Execute the query
        $stmt->execute();
        $products = $stmt->fetchAll();

        // Respond with products or empty list
        if ($products) {
            echo json_encode(['success' => true, 'products' => $products]);
        } else {
            echo json_encode(['success' => false, 'products' => []]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

?>
