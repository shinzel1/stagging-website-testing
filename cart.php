<?php
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
    die("Connection failed: " . $e->getMessage());
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch cart items
try {
    $stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.name, p.description, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching cart items: " . $e->getMessage());
}

// Check if the user has an address
try {
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
    $stmt->execute([$userId]);
    $userAddress = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching address: " . $e->getMessage());
}

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
</head>

<body>
    <div class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if ($cartItems): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <tr data-cart-id="<?= $item['cart_id'] ?>">
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                            <td>
                                <input type="number" value="<?= $item['quantity'] ?>" min="1"
                                    class="form-control quantity-input" data-cart-id="<?= $item['cart_id'] ?>">
                            </td>
                            <td class="subtotal"><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-btn"
                                    data-cart-id="<?= $item['cart_id'] ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-end">
                <h4>Total: ₹<span id="total-price"><?= number_format($totalPrice, 2) ?></span></h4>

                <!-- Proceed to Checkout Button -->
                <?php if ($userAddress): ?>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                <?php else: ?>
                    <button onclick="checkAddress()" class="btn btn-primary">Proceed to Checkout</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="products.php">Continue shopping</a>.</p>
        <?php endif; ?>
    </div>

    <script>
        // Update quantity
        $(document).on('change', '.quantity-input', function () {
            const cartId = $(this).data('cart-id');
            const quantity = $(this).val();

            $.post('includes/cart_handler.php', { action: 'update', cart_id: cartId, quantity: quantity }, function (response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    location.reload(); // Reload the page to update totals
                } else {
                    alert(result.message);
                }
            });
        });

        // Remove item
        $(document).on('click', '.remove-btn', function () {
            const cartId = $(this).data('cart-id');

            $.post('includes/cart_handler.php', { action: 'remove', cart_id: cartId }, function (response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    location.reload(); // Reload the page to remove the item
                } else {
                    alert(result.message);
                }
            });
        });
    </script>



    <div class="modal fade" id="billingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="billingForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Billing Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= bin2hex(random_bytes(32)); ?>">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
                        <div class="mb-3">
                            <label for="billing_name">Billing Name</label>
                            <input type="text" class="form-control" id="billing_name" name="billing_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_1">Address Line 1</label>
                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" required>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_2">Address Line 2 (Optional)</label>
                            <input type="text" class="form-control" id="address_line_2" name="address_line_2">
                        </div>
                        <div class="mb-3">
                            <label for="billing_contact">Contact Number</label>
                            <input type="tel" class="form-control" id="billing_contact" name="billing_contact" required
                                pattern="\d{10}">
                        </div>
                        <div class="mb-3">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="mb-3">
                            <label for="state">State</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                        </div>
                        <div class="mb-3">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required
                                pattern="\d{6}">
                        </div>
                        <div class="mb-3">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkAddress() {
            $('#billingModal').modal('show')
        }


        $(document).ready(function () {
            $('#billingForm').submit(function (e) {
                e.preventDefault();
                $.post('includes/save_billing.php', $(this).serialize(), function (response) {
                    alert('Billing Address Updated');
                    $('#billingModal').modal('hide');
                    location.reload();
                });
            });
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>

</html>