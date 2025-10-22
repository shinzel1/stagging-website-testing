<?php
include 'includes/header.php';
require_once("config/database_connection.php");

if (!$is_logged_in) {
    redirect("index.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$addresses = [];

// Fetch Billing Address
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
    $stmt->execute([$user_id]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch Cart Items
$stmt = $pdo->prepare("SELECT c.id, p.name, p.description, p.price, p.image_url, c.quantity 
                       FROM cart c 
                       INNER JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$order_amount = 0;
foreach ($cart_items as $item) {
    $order_amount += $item['price'] * $item['quantity'];
}
$redeemedDiscount = $_SESSION['redeemed_discount'] ?? 0;
$promoDiscount = $_SESSION['promo_discount'] ?? 0;
$finalTotal = max($order_amount - $redeemedDiscount - $promoDiscount, 0);

// Create unique order ID
$order_id = uniqid('order_');

// Temporary entry for tracking
try {
    $stmt = $pdo->prepare("INSERT INTO temporary_entries (user_id, token, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $order_id]);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<div class="container pt-5 pb-5">
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
            <h4 class="mb-3">Billing Address</h4>
            <span data-bs-toggle="modal" data-bs-target="#billingModal" class="pe-auto float-end">
                <b>Change <i class="fa-regular fa-pen-to-square"></i></b>
            </span>
            <h4 class="mb-3">Address</h4>
            <div class="needs-validation">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['billing_name'] ?? '') ?>" disabled />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Contact Number</label>
                        <input type="tel" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['billing_contact'] ?? '') ?>" disabled />
                    </div>
                </div>

                <div class="mb-3">
                    <label>Address Line 1</label>
                    <input type="text" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($addresses[0]['address_line_1'] ?? '') ?>" disabled />
                </div>
                <div class="mb-3">
                    <label>Address Line 2</label>
                    <input type="text" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($addresses[0]['address_line_2'] ?? '') ?>" disabled />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>City</label>
                        <input type="text" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['city'] ?? '') ?>" disabled />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Postal Code</label>
                        <input type="text" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['postal_code'] ?? '') ?>" disabled />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>State</label>
                        <input type="text" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['state'] ?? '') ?>" disabled />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Country</label>
                        <input type="text" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($addresses[0]['country'] ?? '') ?>" disabled />
                    </div>
                </div>
                <form action="payment_gateway.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                    <input type="hidden" name="order_amount" value="<?= $finalTotal ?>">
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery-3.7.1.min.js"></script>
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