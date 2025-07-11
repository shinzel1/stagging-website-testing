<?php
include 'includes/header.php';

// Database configuration
require_once("config/database_connection.php");

// Get user ID from session
$user_id = $_SESSION['user_id']; // Ensure this is set after user login

if (!$user_id) {
    die("Please log in to view your order details.");
}

// Get the order ID from the query parameter
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Invalid order ID.");
}
$order_id = $_GET['order_id'];

// Fetch the order details
try {
    $order_query = "
        SELECT o.order_id, o.total_amount, o.final_amount, o.created_at, o.payment_status, o.shipping_address, 
               o.promo_code, u.email, u.loyalty_points
        FROM orders o
        INNER JOIN users u ON o.user_id = u.id
        WHERE o.order_id = ? AND o.user_id = ?
    ";
    $order_stmt = $pdo->prepare($order_query);
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }

    // Fetch the items in the order
    $items_query = "
        SELECT oi.id, oi.quantity, oi.price, p.name, p.image_url
        FROM order_items oi
        INNER JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ";
    $items_stmt = $pdo->prepare($items_query);
    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_id']) ?> Details</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container p-5 mt-5">
        <h2 class="mb-4">Order Details</h2>

        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Order Summary</h5>
                <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                <p><strong>Order Date:</strong> <?= htmlspecialchars(date('d F Y', strtotime($order['created_at']))) ?>
                </p>
                <p><strong>Total Amount (Before Discount):</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>

                <?php if (!empty($order['promo_code'])): ?>
                    <p><strong>Promo Code Applied:</strong> <?= htmlspecialchars($order['promo_code']) ?></p>
                <?php endif; ?>

                <?php
                // Calculate loyalty points discount (Assuming 10 points = ₹1 discount)
                $loyalty_discount = ($order['loyalty_points'] / 10);
                if ($loyalty_discount > 0): ?>
                    <p><strong>Loyalty Points Redeemed:</strong> -₹<?= number_format($loyalty_discount, 2) ?></p>
                <?php endif; ?>

                <p><strong>Final Amount Paid:</strong> <span
                        class="text-success">₹<?= number_format($order['final_amount'], 2) ?></span></p>
                <p><strong>Payment Status:</strong> <?= htmlspecialchars(ucfirst($order['payment_status'])) ?></p>
                <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Items in Your Order</h5>
                <ul class="list-group">
                    <?php foreach ($items as $item): ?>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product"
                                    style="width: 100px; height: 100px; margin-right: 15px;">
                                <div>
                                    <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                    Quantity: <?= $item['quantity'] ?><br>
                                    Price: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Order Actions -->
        <div class="d-flex justify-content-end">
            <a href="generate_invoice.php?order_id=<?= $order['order_id'] ?>" class="btn btn-success">Download
                Invoice</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>