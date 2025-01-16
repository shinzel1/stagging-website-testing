<?php
// Include the Razorpay PHP library
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

// Database configuration
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

// Initialize Razorpay API
$api_key = 'rzp_test_LZC9TP5K0xF5vx';
$api_secret = 'rPGh2AyXnt0uTwng699JEVwV';

$api = new Api($api_key, $api_secret);

// Payment verification status
$success = true;
$error = null;

// Get payment details from Razorpay callback
$razorpay_order_id = htmlspecialchars($_POST['razorpay_order_id']);
$payment_id = htmlspecialchars($_POST['razorpay_payment_id']);
$razorpay_signature = htmlspecialchars($_POST['razorpay_signature']);

try {
    // Verify Razorpay payment signature
    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $razorpay_signature,
    ];
    $api->utility->verifyPaymentSignature($attributes);
} catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
    $success = false;
    $error = 'Razorpay Signature Verification Failed: ' . $e->getMessage();
}

if ($success) {
    try {
        // Update order as 'paid' in the database
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', razorpay_payment_id = ? WHERE razorpay_order_id = ?");
        $stmt->execute([$payment_id, $razorpay_order_id]);
        $stmt = $pdo->prepare("SELECT user_id ,order_id FROM orders WHERE razorpay_order_id = ?");
        $stmt->execute([$razorpay_order_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "1";
        $user_id = $result['user_id'];
        $order_id = $result['order_id'];
        // Fetch cart items for the user
        $cart_query = "SELECT c.product_id, c.quantity, p.price FROM cart c INNER JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
        $cart_stmt = $pdo->prepare($cart_query);
        $cart_stmt->execute([$user_id]);
        $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "1";
        if (empty($cart_items)) {
            throw new Exception("Cart is empty. Cannot proceed to checkout.");
        }
        echo "1";
        // Insert cart items iz nto order_items table
        $order_items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $order_items_stmt = $pdo->prepare($order_items_query);

        foreach ($cart_items as $item) {
            $order_items_stmt->execute([
                $order_id,           // Order ID
                $item['product_id'], // Product ID
                $item['quantity'],   // Quantity
                $item['price']       // Price per item
            ]);
        }
        echo "1";
        // Clear the cart for the user after transfer
        $clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
        $clear_cart_stmt = $pdo->prepare($clear_cart_query);
        $clear_cart_stmt->execute([$user_id]);

        echo "Cart items successfully transferred to order_items and cart cleared.";

        // Redirect to order confirmation
        header("Location: order-confirmation.php?order_id=" . urlencode($order_id));
        exit;
    } catch (Exception $e) {
        // Handle database errors
        error_log("Database error: " . $e->getMessage());
        die("Error fetching cart items: " . $e->getMessage());
    }
} else {
    // Payment failed, log the error and inform the user
    error_log("Payment failed: $error");
    echo "<h3>Payment Failed!</h3><p>$error</p>";
}
?>