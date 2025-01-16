<?php
// Include database configuration
include 'includes/header.php';
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

// Check if order_id is provided in the query string
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    echo "<div class='container py-5'><h2>Invalid Order</h2><p>No order ID provided.</p></div>";
    exit;
}

try {
    // Fetch order details
    $stmt = $pdo->prepare("SELECT o.*, u.email FROM orders o INNER JOIN users u ON o.user_id = u.id WHERE o.order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<div class='container py-5'><h2>Order Not Found</h2><p>The order ID provided does not exist.</p></div>";
        exit;
    }

    // Fetch order items
    $itemsStmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi INNER JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $itemsStmt->execute([$order_id]);
    $order_items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>

<body>
    <div class="container py-5">
        <h2 class="mb-4">Order Confirmed!!!</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Thank you for your order, <?= htmlspecialchars($order['email']) ?>!</h5>
                <p>Your order has been successfully placed. Below are the details:</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Order Details</div>
            <div class="card-body">
                <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <p><strong>Total Amount:</strong> &#8377;<?= number_format($order['total_amount'], 2) ?></p>
                <p><strong>Payment Status:</strong> <?= htmlspecialchars(ucfirst($order['payment_status'])) ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Shipping Address</div>
            <div class="card-body">
                <p><?= htmlspecialchars($order['shipping_address']) ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Order Items</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>&#8377;<?= number_format($item['price'] / 100, 2) ?></td>
                                <td>&#8377;<?= number_format(($item['price'] * $item['quantity']) / 100, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

</body>

</html>