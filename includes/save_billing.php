<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id']);
    $billing_name = trim($_POST['billing_name']);
    $address_line_1 = trim($_POST['address_line_1']);
    $address_line_2 = trim($_POST['address_line_2']);
    $billing_contact = trim($_POST['billing_contact']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);

    // Validation
    if (empty($billing_name) || empty($address_line_1) || empty($billing_contact) || empty($city) || empty($state) || empty($postal_code) || empty($country)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Check if the user already has an address
        $stmt = $pdo->prepare("SELECT id FROM addresses WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existing_address = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_address) {
            // Update existing address
            $stmt = $pdo->prepare("
                UPDATE addresses 
                SET billing_name = ?, address_line_1 = ?, address_line_2 = ?, billing_contact = ?, city = ?, state = ?, postal_code = ?, country = ? 
                WHERE user_id = ?
            ");
            $stmt->execute([
                $billing_name,
                $address_line_1,
                $address_line_2,
                $billing_contact,
                $city,
                $state,
                $postal_code,
                $country,
                $user_id
            ]);

            echo json_encode(['success' => true, 'message' => 'Billing address updated successfully.']);
        } else {
            // Insert new address
            $stmt = $pdo->prepare("
                INSERT INTO addresses (user_id, billing_name, address_line_1, address_line_2, billing_contact, city, state, postal_code, country) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $billing_name,
                $address_line_1,
                $address_line_2,
                $billing_contact,
                $city,
                $state,
                $postal_code,
                $country
            ]);

            echo json_encode(['success' => true, 'message' => 'Billing address saved successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
