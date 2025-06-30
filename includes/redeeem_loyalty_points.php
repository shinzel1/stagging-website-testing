<?php
require_once("../config/database_connection.php");

if (isset($_POST['redeem_points'])) {
    $user_id = $_POST['user_id'];
    $pointsToRedeem = (int) $_POST['redeem_points'];

    // Fetch user loyalty points
    $stmt = $pdo->prepare("SELECT loyalty_points FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pointsToRedeem > $user['loyalty_points']) {
        echo json_encode(["success" => false, "message" => "Not enough loyalty points"]);
        exit;
    }

    // Fetch cart items for the user
    try {
        $stmt = $pdo->prepare("SELECT p.price, c.quantity FROM cart c INNER JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die(json_encode(["success" => false, "message" => "Error fetching cart items: " . $e->getMessage()]));
    }

    // Calculate total order amount before applying points
    $order_amount = 0;
    foreach ($cart_items as $item) {
        $order_amount += $item['price'] * $item['quantity'];
    }

    // Convert points to discount (10 points = ₹1 discount)
    $discount = $pointsToRedeem / 10;
    $final_amount = $order_amount - $discount;
    if ($final_amount < 0) {
        $final_amount = 0; // Ensure no negative pricing
    }

    // Store redeem points temporarily
    $_SESSION['redeem_points'] = $pointsToRedeem;
    $_SESSION['redeem_discount'] = $discount;

    echo json_encode(["success" => true, "discount" => $discount, "updated_order_amount" => $final_amount]);
}
?>
