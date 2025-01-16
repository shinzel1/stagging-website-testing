<?php
// Include database configuration
include 'includes/header.php';

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

// Get user ID from session
$user_id = $_SESSION['user_id']; // Make sure this is set when the user logs in

if (!$user_id) {
    die("Please log in to view your orders.");
}

// Fetch orders for the logged-in user
try {
    $orders_query = "
        SELECT o.order_id, o.total_amount, o.created_at, o.payment_status
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ";
    $orders_stmt = $pdo->prepare($orders_query);
    $orders_stmt->execute([$user_id]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
</head>

<body>
    <div class="container py-5">
        <h2 class="mb-4">Your Orders</h2>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">You have not placed any orders yet.</div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Order Placed:</strong>
                            <?= htmlspecialchars(date('d F Y', strtotime($order['created_at']))) ?><br>
                            <strong>Total:</strong> ₹<?= number_format($order['total_amount'], 2) ?>
                        </div>
                        <div>
                            <strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5>Order Items:</h5>
                        <ul class="list-group mb-3">
                            <?php
                            // Fetch items for each order
                            $items_query = "
                                SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image_url
                                FROM order_items oi
                                INNER JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = ?
                            ";
                            $items_stmt = $pdo->prepare($items_query);
                            $items_stmt->execute([$order['order_id']]);
                            $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($items as $item): ?>
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
                        <div class="d-flex justify-content-end">
                            <!-- <a href="buy-again.php?product_id=<?= $item['product_id'] ?>" class="btn btn-warning">Buy it
                                Again</a> -->
                            <a href="order-description.php?order_id=<?= $order['order_id'] ?>" class="btn btn-primary">View Order</a>
                            <!-- <a href="track-package.php?order_id=<?= $order['order_id'] ?>" class="btn btn-secondary">Track
                                Package</a> -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>

</body>

</html>