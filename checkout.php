<?php

use Razorpay\Api\Api;

// Start session and include database configuration

include 'includes/header.php';

// Database configuration
$host = 'localhost';
$db_name = 'nutrizione';
$username = 'root';
$password = '';

require 'razorpay-php/Razorpay.php';

// Razorpay credentials
$api_key = 'rzp_test_LZC9TP5K0xF5vx';
$api_secret = 'rPGh2AyXnt0uTwng699JEVwV';
$api = new Api($api_key, $api_secret);

// Example Usage
// Fetch billing address for the logged-in user
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// Ensure the user is logged in


$addresses = [];
// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($user_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
        $stmt->execute([$user_id]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching addresses: " . $e->getMessage());
    }
}


// Fetch cart items for the current user
try {
    $stmt = $pdo->prepare("SELECT c.id, p.name, p.price, c.quantity FROM cart c INNER JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching cart items: " . $e->getMessage());
}

// Calculate total amount
$order_amount = 0;
foreach ($cart_items as $item) {
    $order_amount += $item['price'] * $item['quantity'];
}

// Step 1: Create a new order in your database
$order_currency = 'INR';
$order_status = 'Pending';
$db_order_id = null;
$address = "";
if (sizeof($addresses) > 0) {
    try {
        // Insert a new order into the database
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, currency, payment_status,shipping_address) VALUES (?, ?, ?, ?,?)");
        $stmt->execute([$user_id, $order_amount, $order_currency, $order_status, implode(', ', [$addresses[0]['billing_name'], $addresses[0]['address_line_1'], $addresses[0]['address_line_2'], $addresses[0]['city'], $addresses[0]['postal_code'], $addresses[0]['state'], $addresses[0]['country']])]);
        // Get the last inserted order ID
        $db_order_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        die("Error creating order in database: " . $e->getMessage());
    }
    $address = implode(', ', [$addresses[0]['billing_name'], $addresses[0]['address_line_1'], $addresses[0]['address_line_2'], $addresses[0]['city'], $addresses[0]['postal_code'], $addresses[0]['state'], $addresses[0]['country']]);
}

// Step 2: Create a Razorpay order
try {
    $razorpayOrder = $api->order->create([
        'amount' => $order_amount,
        'currency' => $order_currency,
        'receipt' => "order_receipt_$db_order_id",
    ]);
    $razorpay_order_id = $razorpayOrder->id;

    // Update the database order with the Razorpay order ID
    $stmt = $pdo->prepare("UPDATE orders SET razorpay_order_id = ? WHERE order_id = ?");
    $stmt->execute([$razorpay_order_id, $db_order_id]);
} catch (Exception $e) {
    die("Error creating Razorpay order: " . $e->getMessage());
}


$callback_url = "http://localhost/nutrizone/verify.php";
?>
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="accordion" id="accordionExample">
        <!-- Billing Address Section -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                    aria-expanded="true">
                    Billing Address
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <p id="address">
                        <?= htmlspecialchars($address) ?>
                        <span data-bs-toggle="modal" data-bs-target="#billingModal"><b>Change <i
                                    class="fa-regular fa-pen-to-square"></i></b></span>
                    </p>

                    <!-- Billing Modal -->
                    <div class="modal fade" id="billingModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" id="billingForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Billing Address</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="csrf_token"
                                            value="<?= bin2hex(random_bytes(32)); ?>">

                                        <div class="mb-3">
                                            <label for="billing_name">Billing Name</label>
                                            <input type="text" class="form-control" id="billing_name"
                                                name="billing_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address_line_1">Address Line 1</label>
                                            <input type="text" class="form-control" id="address_line_1"
                                                name="address_line_1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address_line_2">Address Line 2 (Optional)</label>
                                            <input type="text" class="form-control" id="address_line_2"
                                                name="address_line_2">
                                        </div>
                                        <div class="mb-3">
                                            <label for="billing_contact">Contact Number</label>
                                            <input type="tel" class="form-control" id="billing_contact"
                                                name="billing_contact" required pattern="\d{10}">
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
                                            <input type="text" class="form-control" id="postal_code" name="postal_code"
                                                required pattern="\d{6}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="country">Country</label>
                                            <input type="text" class="form-control" id="country" name="country"
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Address</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo" aria-expanded="false">
                    Payment
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <button onclick="startPayment()" class="btn btn-primary">Pay with Razorpay</button>
                    <script>
                        function startPayment() {
                            var options = {
                                key: "<?= $api_key; ?>",
                                amount: "<?= $order_amount; ?>",
                                currency: "<?= $order_currency; ?>",
                                name: "nutrizone",
                                description: "Order Payment",
                                order_id: "<?= $razorpay_order_id; ?>",
                                callback_url: "<?= $callback_url; ?>",
                                theme: {
                                    color: "#007bff"
                                }
                            };

                            var rzp = new Razorpay(options);
                            rzp.open();
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


    $(document).ready(function () {
        $('#billingForm').submit(function (e) {
            e.preventDefault();
            $.post('includes/save_billing.php', $(this).serialize(), function (response) {
                alert('Billing Address Updated');
                $('#billingModal').modal('hide');
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php include 'includes/footer.php'; ?>
</body>

</html>