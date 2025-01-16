<?php
// Database connection
$host = 'localhost';
$db_name = 'nutrizione';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


// Check if the form data is posted
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
        echo 'All fields are required.';
        exit;
    }

    // Save data to the database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO addresses (user_id, billing_name, address_line_1, address_line_2, billing_contact, city, state, postal_code, country)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id, // Replace with actual user ID
            $billing_name,
            $address_line_1,
            $address_line_2,
            $billing_contact,
            $city,
            $state,
            $postal_code,
            $country
        ]);

        echo 'Billing information saved successfully!';
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>