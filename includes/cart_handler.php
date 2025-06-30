<?php
session_start();
require_once("../config/database_connection.php");

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
            // Fetch the current stock
            $stmt = $pdo->prepare("SELECT p.quantity AS stock_quantity FROM cart c 
                                   INNER JOIN products p ON c.product_id = p.id 
                                   WHERE c.id = ?");
            $stmt->execute([$cartId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                exit;
            }

            if ($quantity > $product['stock_quantity']) {
                echo json_encode(['status' => 'error', 'message' => 'Requested quantity exceeds available stock']);
                exit;
            }

            // Proceed to update quantity
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
?>
