<?php
require_once("../config/database_connection.php");

$response = ["success" => false, "error" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $title = trim($_POST['title']);
        $promo_code = trim($_POST['promo_code']);
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        $applicable_on = $_POST['applicable_on'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Check if required fields are filled
        if (empty($title) || empty($promo_code) || empty($discount_type) || empty($discount_value) || empty($applicable_on) || empty($start_date) || empty($end_date)) {
            throw new Exception("All fields are required.");
        }

        // Check if promo code is unique
        $checkStmt = $pdo->prepare("SELECT id FROM discounts WHERE promo_code = ?");
        $checkStmt->execute([$promo_code]);
        if ($checkStmt->rowCount() > 0) {
            throw new Exception("Promo code already exists. Choose another.");
        }

        // Prepare SQL Query
        $query = "INSERT INTO discounts (title, promo_code, description, discount_type, discount_value, applicable_on, applicable_id, start_date, end_date, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";

        $stmt = $pdo->prepare($query);

        // Set category_id or product_id based on applicable_on
        $applicable_id = null;
        if ($applicable_on === "category") {
            $applicable_id = $_POST['category_id'] ?? null;
            if (!$applicable_id) {
                throw new Exception("Please select a category.");
            }
        } elseif ($applicable_on === "product") {
            $applicable_id = $_POST['product_id'] ?? null;
            if (!$applicable_id) {
                throw new Exception("Please select a product.");
            }
        }

        // Execute query
        if ($stmt->execute([$title, $promo_code, $description, $discount_type, $discount_value, $applicable_on, $applicable_id, $start_date, $end_date])) {
            $response = ["success" => true, "message" => "Discount added successfully."];
        } else {
            throw new Exception("Database error: Could not save discount.");
        }
    } catch (Exception $e) {
        $response = ["success" => false, "error" => $e->getMessage()];
    }
}

// Return JSON response
echo json_encode($response);
?>
