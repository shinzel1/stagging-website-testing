<?php

use Razorpay\Api\Api;

// Start session and include database configuration
include 'includes/header.php';

if (!$is_logged_in) {
    redirect("index.php");
    exit;
}

// Load Database Connection
require_once("config/database_connection.php");
require 'razorpay-php/Razorpay.php';

// Razorpay credentials
$api_key = 'rzp_test_LZC9TP5K0xF5vx';
$api_secret = 'rPGh2AyXnt0uTwng699JEVwV';
$api = new Api($api_key, $api_secret);

// User ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$addresses = [];

// Fetch Address
if ($user_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
        $stmt->execute([$user_id]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching addresses: " . $e->getMessage());
    }
}

// Fetch Cart Items
try {
    $stmt = $pdo->prepare("SELECT c.id, p.name, p.description, p.price, p.image_url, c.quantity 
                          FROM cart c 
                          INNER JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching cart items: " . $e->getMessage());
}

// Calculate Total Amount
$order_amount = 0;
foreach ($cart_items as $item) {
    $order_amount += $item['price'] * $item['quantity'];
}

// **Retrieve Redeemed Points from Session**
$redeemedDiscount = isset($_SESSION['redeemed_discount']) ? $_SESSION['redeemed_discount'] : 0;
$finalTotal = $order_amount - $redeemedDiscount;
if ($finalTotal < 0) {
    $finalTotal = 0; // Prevent negative pricing
}
// Apply promo discount if available
$promoDiscount = isset($_SESSION['promo_discount']) ? $_SESSION['promo_discount'] : 0;
$finalTotal = max($finalTotal - $promoDiscount, 0); // Ensure it doesn't go negative


// Create Razorpay Order with Updated Amount
try {
    $razorpayOrder = $api->order->create([
        'amount' => $finalTotal * 100, // Convert to paisa
        'currency' => 'INR',
        'receipt' => "order_receipt_" . bin2hex(random_bytes(10))
    ]);
    $razorpay_order_id = $razorpayOrder->id;
} catch (Exception $e) {
    die("Error creating Razorpay order: " . $e->getMessage());
}

// Store Temporary Order Entry
try {
    $stmt = $pdo->prepare("INSERT INTO temporary_entries (user_id, token, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $razorpay_order_id]);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$callback_url = dirname($_SERVER['HTTP_REFERER']) . "/verify.php";

$stmt = $pdo->prepare("SELECT loyalty_points FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['discount'])) {
    $_SESSION['promo_discount'] = $_POST['discount'];
    $_SESSION['promo_code'] = $_POST['promo_code'] ?? null;
}
?>
<style>
    .pe-auto {
        cursor: pointer;
    }
</style>


<div class="container pt-5 pb-5">
    <div class="p-2 text-center">
    </div>

    <div class="row">
        <div class="col-md-5 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
                <span class="badge badge-secondary badge-pill"><?= count($cart_items); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">
                                <img src="<?= $item['image_url']; ?>" style="width: 50px;" />
                                <?= substr($item['name'], 0, 40); ?>
                            </h6>
                            <small class="text-muted"><?= substr($item['description'], 0, 80); ?></small>
                        </div>
                        <span class="text-muted">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </li>
                <?php endforeach; ?>

                <?php if ($promoDiscount > 0): ?>
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <div class="text-success">
                            <h6 class="my-0">Promo Code Discount</h6>
                            <small>Code: <?= $_SESSION['promo_code'] ?? ''; ?></small>
                        </div>
                        <span class="text-success">-₹<?= number_format($promoDiscount, 2) ?></span>
                    </li>
                <?php endif; ?>

                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (₹)</span>
                    <strong id="orderTotal">₹<?= number_format($finalTotal, 2) ?></strong>
                </li>
            </ul>

        </div>
        <div class="col-md-7 order-md-1">
            <span data-bs-toggle="modal" data-bs-target="#billingModal" class="pe-auto float-end"><b>Change <i
                        class="fa-regular fa-pen-to-square"></i></b></span>
            <h4 class="mb-3">Address</h4>
            <div class="needs-validation">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="billing_name">Name</label>
                        <input type="text" class="form-control form-control-sm" id="billing_name" name="billing_name"
                            value="<?= htmlspecialchars($addresses[0]['billing_name']) ?>" required disabled />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="billing_contact">Contact Number</label>
                        <input type="tel" class="form-control form-control-sm" id="billing_contact"
                            name="billing_contact" required disabled pattern="\d{10}"
                            value="<?= htmlspecialchars($addresses[0]['billing_contact']) ?>" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address_line_1">Address Line 1</label>
                    <input type="text" class="form-control form-control-sm" id="address_line_1" name="address_line_1"
                        value="<?= htmlspecialchars($addresses[0]['address_line_1']) ?>" required disabled />
                </div>
                <div class="mb-3">
                    <label for="address_line_2">Address Line 2 (Optional)</label>
                    <input type="text" class="form-control form-control-sm" id="address_line_2" name="address_line_2"
                        disabled value="<?= htmlspecialchars($addresses[0]['address_line_2']) ?>" />
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control form-control-sm" id="city" name="city" required disabled
                            value="<?= htmlspecialchars($addresses[0]['city']) ?>" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" class="form-control form-control-sm" id="postal_code" name="postal_code"
                            required disabled pattern="\d{6}"
                            value="<?= htmlspecialchars($addresses[0]['postal_code']) ?>" />
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state">State</label>
                        <input type="text" class="form-control form-control-sm" id="state" name="state" required
                            disabled value="<?= htmlspecialchars($addresses[0]['state']) ?>" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country">Country</label>
                        <input type="text" class="form-control form-control-sm" id="country" name="country" required
                            disabled value="<?= htmlspecialchars($addresses[0]['country']) ?>" />
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button onclick="startPayment()" class="btn btn-primary btn-lg btn-block" type="submit">Pay with
                        Razorpay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
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
                    <input type="hidden" name="csrf_token" value="<?= bin2hex(random_bytes(32)); ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

                    <div class="mb-3">
                        <label for="billing_name">Billing Name</label>
                        <input type="text" class="form-control" id="billing_name" name="billing_name"
                            value="<?= htmlspecialchars($addresses[0]['billing_name']) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label for="address_line_1">Address Line 1</label>
                        <input type="text" class="form-control" id="address_line_1" name="address_line_1"
                            value="<?= htmlspecialchars($addresses[0]['address_line_1']) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label for="address_line_2">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control" id="address_line_2" name="address_line_2"
                            value="<?= htmlspecialchars($addresses[0]['address_line_2']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="billing_contact">Contact Number</label>
                        <input type="tel" class="form-control" id="billing_contact" name="billing_contact" required
                            pattern="\d{10}" value="<?= htmlspecialchars($addresses[0]['billing_contact']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" required
                            value="<?= htmlspecialchars($addresses[0]['city']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" required
                            value="<?= htmlspecialchars($addresses[0]['state']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" required
                            pattern="\d{6}" value="<?= htmlspecialchars($addresses[0]['postal_code']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country" required
                            value="<?= htmlspecialchars($addresses[0]['country']) ?>" />
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

    function startPayment() {
        var orderTotal = $('#orderTotal').html().replace("₹", "").trim();
        var options = {
            key: "<?= $api_key; ?>",
            amount: orderTotal * 100, // Convert to paisa
            currency: "INR",
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
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/razorPaycheckout.js"></script>
<?php include 'includes/footer.php'; ?>