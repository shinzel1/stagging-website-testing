<?php
require_once("config/database_connection.php");

try {

    // Step 1: Fetch all categories and store them in an associative array
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $categories = [];

    while ($row = $stmt->fetch()) {
        $categories[strtolower(trim($row["name"]))] = $row["id"]; // Store category names in lowercase for case-insensitive matching
    }

    // Step 2: Fetch products with JSON category names
    $stmt = $pdo->query("SELECT id, categories FROM products");

    // Prepare the insert statement for batch execution
    $insertStmt = $pdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)");

    while ($row = $stmt->fetch()) {
        $product_id = $row["id"];
        
        // Decode JSON string safely
        $category_names = json_decode($row["categories"], true);
        
        if (!is_array($category_names)) {
            continue; // Skip if decoding fails
        }

        foreach ($category_names as $category_name) {
            $category_name = trim($category_name); // Trim spaces

            // Check if category exists in the cached array
            $key = strtolower($category_name);
            if (isset($categories[$key])) {
                $category_id = $categories[$key];

                // Execute the insert statement
                $insertStmt->execute([
                    ':product_id' => $product_id,
                    ':category_id' => $category_id
                ]);
            }
        }
    }

    echo "Optimized category mapping completed!";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
