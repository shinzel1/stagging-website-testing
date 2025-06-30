<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cartId = $_POST['cart_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT p.quantity AS stock_quantity 
            FROM cart c 
            INNER JOIN products p ON c.product_id = p.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$cartId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            echo json_encode(['status' => 'success', 'stock_quantity' => $product['stock_quantity']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
