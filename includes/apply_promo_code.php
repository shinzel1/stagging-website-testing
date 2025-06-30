<?php
require_once("../config/database_connection.php");
session_start();

$response = ["success" => false, "message" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promo_code'], $_POST['user_id'])) {
    try {
        $promo_code = trim($_POST['promo_code']);
        $user_id = $_POST['user_id'];

        // Validate promo code in the database
        $stmt = $pdo->prepare("SELECT * FROM discounts WHERE promo_code = ? AND status = 'active' AND start_date <= NOW() AND end_date >= NOW()");
        $stmt->execute([$promo_code]);
        $discount = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$discount) {
            throw new Exception("Invalid or expired promo code.");
        }

        // Fetch user's cart total
        $stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) AS total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        $cart_total = $cart['total'] ?? 0;

        if ($cart_total == 0) {
            throw new Exception("Your cart is empty.");
        }

        // Apply discount
        $discount_amount = 0;
        if ($discount['discount_type'] == 'percentage') {
            $discount_amount = ($cart_total * $discount['discount_value']) / 100;
        } else {
            $discount_amount = $discount['discount_value'];
        }

        // Ensure discount doesn't exceed total price
        $updated_total = max($cart_total - $discount_amount, 0);

        // Store in session
        $_SESSION['promo_code'] = $promo_code;
        $_SESSION['promo_discount'] = $discount_amount;

        $response = ["success" => true, "discount" => $discount_amount, "updated_total" => $updated_total];
    } catch (Exception $e) {
        $response = ["success" => false, "message" => $e->getMessage()];
    }
}

echo json_encode($response);
?>
