<?php
error_reporting(0);
require_once __DIR__ . '/v2/config.php';
require_once __DIR__ . '/v2/package/PhonePeHelper.php';
require_once "config/database_connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// -----------------------------
// STEP 1: VALIDATE ORDER ID
// -----------------------------
if (empty($_GET['order_id'])) {
    die("❌ Invalid or missing order_id in URL");
}

$order_id_param = $_GET['order_id'];

// -----------------------------
// STEP 2: VERIFY PAYMENT STATUS USING HELPER
// -----------------------------
try {
    $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, 'UAT');
    $response = $phonePeHelper->checkPaymentStatus($order_id_param);
    $payment = $response['paymentDetails'] ?? [];
    $state = strtoupper($response['state'] ?? 'FAILED');
    $amount = ($response['amount'] ?? 0) / 100;
    $phonepe_txn_id = $response['orderId'] ?? '';
} catch (Exception $e) {
    die("Error verifying payment: " . $e->getMessage());
}

// -----------------------------
// STEP 3: HANDLE INCOMPLETE PAYMENT
// -----------------------------
if ($state !== 'COMPLETED') {
    $reponse_path = RESPONSE_PATH . "?order_id=" . $_GET['order_id'];
    $response1 = $phonePeHelper->createPayment($_GET['order_id'], $response['amount'], $reponse_path);
    $again_url = $response1['redirectUrl'] ?? '';
} else {
    $again_url = '';
}

// -----------------------------
// STEP 4: IF PAYMENT SUCCESS, PROCESS ORDER
// -----------------------------
if ($state === 'COMPLETED') {
    try {

        // Fetch temporary entry
        $stmt = $pdo->prepare("SELECT user_id FROM temporary_entries WHERE token = ?");
        $stmt->execute([$order_id_param]);
        $temp = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $temp['user_id'] ?? null;

        if (!$user_id)
            die("User not found for this transaction.");

        // Fetch user email
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_email = $stmt->fetchColumn();

        // Fetch billing address
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
        $stmt->execute([$user_id]);
        $billing_address = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$billing_address)
            die("No billing address found for user.");

        // Build formatted address string
        $shipping_address = implode(', ', array_filter([
            $billing_address['billing_name'] ?? '',
            $billing_address['address_line_1'] ?? '',
            $billing_address['address_line_2'] ?? '',
            $billing_address['city'] ?? '',
            $billing_address['postal_code'] ?? '',
            $billing_address['state'] ?? '',
            $billing_address['country'] ?? ''
        ]));

        // Fetch cart items
        $stmt = $pdo->prepare("SELECT c.id, p.name, p.price, c.quantity, p.id AS product_id 
                               FROM cart c 
                               INNER JOIN products p ON c.product_id = p.id 
                               WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cart_items))
            die("Cart is empty for this user.");

        // Calculate totals
        $order_amount = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart_items));
        $final_amount = $amount ?: $order_amount;
        echo "debug";

        // error point
        // Check if order already exists for this PhonePe transaction
        $checkOrder = $pdo->prepare("SELECT order_id FROM orders WHERE razorpay_order_id = ?");
        $checkOrder->execute([$order_id_param]);
        if ($checkOrder->fetch()) {
            header("Location: order-confirmation.php?order_id=" . urlencode($order_id_param));
            exit;
        }
        echo "debug";

        // Create new order
        $stmt = $pdo->prepare("INSERT INTO orders 
            (user_id, total_amount, final_amount, currency, razorpay_order_id, razorpay_payment_id, payment_status, promo_code, shipping_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $order_amount,
            $final_amount,
            'INR',
            $order_id_param,
            $phonepe_txn_id,
            'paid',
            $_SESSION['promo_code'] ?? null,
            $shipping_address
        ]);

        $order_id = $pdo->lastInsertId();
        echo "debug";

        // Insert order items and update stock
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")
                ->execute([$item['quantity'], $item['product_id']]);
        }

        // Update stock status and clear cart
        $pdo->query("UPDATE products SET status = 'out_of_stock' WHERE quantity <= 0");
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

        // Loyalty points handling
        $redeem_points = $_SESSION['redeemed_points'] ?? 0;
        if ($redeem_points > 0) {
            $pdo->prepare("UPDATE users SET loyalty_points = 0 WHERE id = ?")->execute([$user_id]);
            $pdo->prepare("INSERT INTO loyalty_transactions (user_id, order_id, points, transaction_type)
                           VALUES (?, ?, ?, 'redeemed')")->execute([$user_id, $order_id, -$redeem_points]);
        }

        $settings = $pdo->query("SELECT points_per_dollar FROM loyalty_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $points_rate = $settings['points_per_dollar'] ?? 1;
        $earned_points = floor($final_amount / $points_rate);

        $pdo->prepare("UPDATE users SET loyalty_points = ? WHERE id = ?")->execute([$earned_points, $user_id]);
        $pdo->prepare("INSERT INTO loyalty_transactions (user_id, order_id, points, transaction_type)
                       VALUES (?, ?, ?, 'earn')")->execute([$user_id, $order_id, $earned_points]);

        unset($_SESSION['redeemed_points'], $_SESSION['redeemed_discount'], $_SESSION['promo_discount'], $_SESSION['promo_code']);

        sendOrderConfirmationEmail($user_email, $order_id, $cart_items, $final_amount);
        header("Location: order-confirmation.php?order_id=" . urlencode($order_id));

    } catch (Exception $e) {
        error_log("Order Processing Error: " . $e->getMessage());
    }
}

// -----------------------------
// STEP 5: SHOW RESPONSE PAGE
// -----------------------------
?>

<?php
// -----------------------------
// EMAIL FUNCTION
// -----------------------------
function sendOrderConfirmationEmail($email, $order_id, $cart_items, $total)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sachinvvin@gmail.com';
        $mail->Password = 'oawtojazziscrnrq'; // use app password only
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@nutrizone.in', 'Nutrizone');
        $mail->addAddress($email);
        $mail->Subject = 'Order Confirmation - Order #' . $order_id;
        $mail->isHTML(true);

        $body = "<h2>Thank you for your order!</h2>
                 <p>Your order <strong>#$order_id</strong> has been placed successfully.</p>
                 <table border='1' cellpadding='5' cellspacing='0' width='100%'>
                 <tr><th>Product</th><th>Qty</th><th>Price</th></tr>";

        foreach ($cart_items as $item) {
            $body .= "<tr><td>{$item['name']}</td><td>{$item['quantity']}</td><td>₹{$item['price']}</td></tr>";
        }

        $body .= "</table><h3>Total: ₹$total</h3><p>We appreciate your business!</p>";
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
    }
}
?>