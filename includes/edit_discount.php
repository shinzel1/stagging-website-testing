<?php
require_once("../config/database_connection.php");

$response = ["success" => false, "error" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title']);
        $promo_code = trim($_POST['promo_code']);
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Check if ID exists
        if (!$id) {
            throw new Exception("Invalid discount ID.");
        }

        // Check if required fields are filled
        if (empty($title) || empty($promo_code) || empty($discount_type) || empty($discount_value) || empty($start_date) || empty($end_date)) {
            throw new Exception("All fields are required.");
        }

        // Check if promo code is unique (excluding current record)
        $checkStmt = $pdo->prepare("SELECT id FROM discounts WHERE promo_code = ? AND id != ?");
        $checkStmt->execute([$promo_code, $id]);
        if ($checkStmt->rowCount() > 0) {
            throw new Exception("Promo code already exists. Choose another.");
        }

        // Prepare SQL Query
        $query = "UPDATE discounts 
                  SET title = ?, promo_code = ?, description = ?, discount_type = ?, discount_value = ?, start_date = ?, end_date = ? 
                  WHERE id = ?";

        $stmt = $pdo->prepare($query);

        // Execute query
        if ($stmt->execute([$title, $promo_code, $description, $discount_type, $discount_value, $start_date, $end_date, $id])) {
            $response = ["success" => true, "message" => "Discount updated successfully."];
        } else {
            throw new Exception("Database error: Could not update discount.");
        }
    } catch (Exception $e) {
        $response = ["success" => false, "error" => $e->getMessage()];
    }
}

// Return JSON response
echo json_encode($response);
?>
