<?php
session_start();
// Database configuration
$host = 'localhost';
$db_name = 'nutrizione';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $e->getMessage()]));
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $cartId = $_POST['cart_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    try {
        if ($action === 'update' && $cartId && $quantity && is_numeric($quantity) && $quantity > 0) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$quantity, $cartId, $userId]);
            echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
        } elseif ($action === 'remove' && $cartId) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cartId, $userId]);
            echo json_encode(['status' => 'success', 'message' => 'Item removed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action or parameters']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
    }
}
