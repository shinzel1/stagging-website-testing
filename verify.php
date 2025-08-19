<?php
// Include Razorpay API
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Database Connection
require_once("config/database_connection.php");

// Initialize Razorpay API
$api_key = 'rzp_test_LZC9TP5K0xF5vx';
$api_secret = 'rPGh2AyXnt0uTwng699JEVwV';
$api = new Api($api_key, $api_secret);

// Payment Verification
$success = true;
$error = null;
$addresses = [];

$razorpay_order_id = htmlspecialchars($_POST['razorpay_order_id']);
$payment_id = htmlspecialchars($_POST['razorpay_payment_id']);
$razorpay_signature = htmlspecialchars($_POST['razorpay_signature']);
$payment = $api->payment->fetch($payment_id);

try {
    // Verify payment signature
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

// Proceed only if payment is successful
if ($success) {
    try {
        // Fetch User ID from temporary entries
        $stmt = $pdo->prepare("SELECT user_id FROM temporary_entries WHERE token = ?");
        $stmt->execute([$razorpay_order_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user["user_id"];

        // Fetch user email
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_email = $user["email"];

        // Fetch Billing Address (To Use as Shipping Address)
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
        $stmt->execute([$user_id]);
        $billing_address = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no address is found, throw an error
        if (!$billing_address) {
            die("Error: No billing address found. Please update your address before checkout.");
        }

        // Format the shipping address as a single string
        $shipping_address = implode(', ', [
            $billing_address['billing_name'],
            $billing_address['address_line_1'],
            $billing_address['address_line_2'],
            $billing_address['city'],
            $billing_address['postal_code'],
            $billing_address['state'],
            $billing_address['country']
        ]);

        // Fetch Cart Items
        $stmt = $pdo->prepare("SELECT c.id, p.name, p.price, c.quantity, p.id AS product_id 
                       FROM cart c 
                       INNER JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Order Total Before Discount
        $order_amount = 0;
        foreach ($cart_items as $item) {
            $order_amount += $item['price'] * $item['quantity'];
        }

        // Fetch the final amount paid from Razorpay response
        $final_amount = ($payment->amount) / 100; // Convert paisa to INR

        // Insert Order into Database (Including Shipping Address)
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, final_amount, currency, razorpay_order_id, razorpay_payment_id, payment_status, promo_code, shipping_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $order_amount,
            $final_amount,
            'INR',
            $razorpay_order_id,
            $payment_id,
            'paid',
            $_SESSION['promo_code'] ?? null,
            $shipping_address
        ]);


        $order_id = $pdo->lastInsertId();

        // Insert Order Items & Update Stock
        $order_items_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $order_items_stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

            // Reduce Stock
            $update_stock_stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $update_stock_stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Mark Out-of-Stock Products
        $set_out_of_stock_stmt = $pdo->prepare("UPDATE products SET status = 'out_of_stock' WHERE quantity <= 0");
        $set_out_of_stock_stmt->execute();

        // Clear User Cart
        $clear_cart_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart_stmt->execute([$user_id]);

        // Deduct Loyalty Points and Reset to Zero
        if ($redeem_points > 0) {
            $updateUserStmt = $pdo->prepare("UPDATE users SET loyalty_points = 0 WHERE id = ?");
            $updateUserStmt->execute([$user_id]);

            // Log Transaction
            $logStmt = $pdo->prepare("INSERT INTO loyalty_transactions (user_id, order_id, points, transaction_type) VALUES (?, ?, ?, 'redeemed')");
            $logStmt->execute([$user_id, $order_id, -$redeem_points]);
        }

        // Calculate Earned Loyalty Points
        $settingsStmt = $pdo->query("SELECT points_per_dollar FROM loyalty_settings LIMIT 1");
        $settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);
        $points_per_dollar = $settings['points_per_dollar'];
        $earned_points = floor($final_amount * $points_per_dollar);

        // Update User's Earned Points
        $updateUserStmt = $pdo->prepare("UPDATE users SET loyalty_points = ? WHERE id = ?");
        $updateUserStmt->execute([$earned_points, $user_id]);

        // Log Earned Points Transaction
        $logStmt = $pdo->prepare("INSERT INTO loyalty_transactions (user_id, order_id, points, transaction_type) VALUES (?, ?, ?, 'earn')");
        $logStmt->execute([$user_id, $order_id, $earned_points]);

        // Clear Session Variables for Redeemed Points
        unset($_SESSION['redeemed_points'], $_SESSION['redeemed_discount']);
        unset($_SESSION['promo_discount'], $_SESSION['promo_code']);
        // Send Order Confirmation Email
        sendOrderConfirmationEmail($user_email, $order_id, $cart_items, $final_amount);

        // Redirect to Order Confirmation
        header("Location: order-confirmation.php?order_id=" . urlencode($order_id));
        exit;
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Error processing order: " . $e->getMessage());
    }
} else {
    error_log("Payment failed: $error");
    echo "<h3>Payment Failed!</h3><p>$error</p>";
}

// Function to send order confirmation email
function sendOrderConfirmationEmail($email, $order_id, $cart_items, $total)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server (e.g., smtp.mailtrap.io for testing)
        $mail->SMTPAuth = true;
        $mail->Username = 'sachinvvin@gmail.com'; // Your email address
        $mail->Password = 'oawtojazziscrnrq'; // Your email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ENCRYPTION_SMTPS Use 'tls' if your server doesn't support 'ssl'
        $mail->Port = 587; // 587 for 'tls' 465

        // Email headers
        $mail->setFrom('no-reply@nutrizone.in', 'Nutrizone');
        $mail->addAddress($email);
        $mail->Subject = 'Order Confirmation - Order #' . $order_id;
        $mail->isHTML(true);

        // Email body content
        $body = "<h2>Thank you for your order!</h2>";
        $body .= "<p>Your order <strong>#$order_id</strong> has been placed successfully.</p>";
        $body .= "<h3>Order Summary:</h3>";
        $body .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
        $body .= "<tr><th>Product</th><th>Quantity</th><th>Price</th></tr>";
        foreach ($cart_items as $item) {
            $body .= "<tr><td>{$item['name']}</td><td>{$item['quantity']}</td><td>₹{$item['price']}</td></tr>";
        }
        $body .= "</table>";
        $body .= "<h3>Total: ₹$total</h3>";
        $body .= "<p>You can download your invoice from your account.</p>";
        $body .= "<p>Thank you for shopping with us!</p>";

        $mail->Body = $body;

        // Send email
        $mail->send();
        echo "Order confirmation email sent!";
    } catch (Exception $e) {
        error_log("Error sending email: " . $mail->ErrorInfo);
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>