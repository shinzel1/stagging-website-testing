<?php
include 'includes/header.php';
// Load the .env file
loadEnv(__DIR__ . '/../.env.test');

// Access environment variables
$host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
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
    $stmt = $pdo->prepare("SELECT c.id as cart_id,c.product_id, c.quantity,p.image_url, p.name, p.description, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
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
$totalItems = 0;
// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

$stmt = $pdo->prepare("SELECT loyalty_points FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['loyalty_points'] == 0) {
    $_SESSION['redeemed_discount'] = 0;
    $_SESSION['redeemed_points'] = 0;
}
$_SESSION['promo_code'] = 0;
$_SESSION['promo_discount'] = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" type="text/css" href="assets/css/cart.css">
</head>

<body>
    <style>
        .cart-item {
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .border-bottom {
            border-bottom: 1px solid #ddd;

        }

        .border-bottom:last-child {
            border-bottom: none;

        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .product-img {
            max-width: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-summary {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .cart-summary .total-price {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .btn-primary {

            border-radius: 25px;
        }


        .cart-summary .btn {
            width: 100%;
            padding: 12px;
        }

        .cart-summary .btn:disabled {
            background-color: #ddd;
        }

        .button-sm {
            padding: 6px !important;
            width: auto !important;
            float: right;
        }
    </style>

    <div class="container mt-5 pt-5 pb-5 rounded cart">
        <div class="row no-gutters">
            <div class="col-md-12">
                <?php if ($cartItems): ?>
                    <div class="product-details mr-2">
                        <div class="d-flex flex-row align-items-center">
                            <i class="fa fa-long-arrow-left"></i>
                            <a class="ml-2" href="products.php">Continue Shopping</a>
                        </div>
                        <div class="mt-5">
                            <div class="row">
                                <!-- Shopping Cart Items -->
                                <div class="col-md-8">
                                    <h3>Your Cart</h3>
                                    <span>You have <?= $totalItems ?> items in your cart</span>

                                    <!-- Cart Item 1 -->
                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="border-bottom pb-3">
                                            <div class="cart-item">
                                                <div class="d-flex">
                                                    <a href="product-details.php?id=<?= $item['product_id'] ?>">
                                                        <img src="<?= $item['image_url'] ?>" alt="Product"
                                                            class="product-img me-3">
                                                    </a>
                                                    <div>
                                                        <h6><?= substr($item['name'], 0, 50); ?></h6>
                                                        <span
                                                            class=" font-weight-bold subtotal">₹<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="d-flex">
                                                <div class="input-group" style="width: 10em;">
                                                    <span class="input-group-text remove-btn" id="basic-addon1"
                                                        data-cart-id="<?= $item['cart_id'] ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                            <path
                                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z">
                                                            </path>
                                                            <path
                                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                    <input class="form-control quantity-input" placeholder="Input group example"
                                                        aria-label="Input group example" aria-describedby="basic-addon1"
                                                        type="number" value="<?= $item['quantity'] ?>" min="1"
                                                        data-cart-id="<?= $item['cart_id'] ?>" />
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </div>
                                <!-- Cart Summary -->
                                <div class="col-md-4">
                                    <div class="cart-summary">
                                        <h5>Summary</h5>
                                        <ul class="list-unstyled">
                                            <li class="d-flex justify-content-between">
                                                <span class="total-price">Total:</span>
                                                <span id="orderTotal"
                                                    class="total-price">₹<?= number_format($totalPrice, 2) ?></span>
                                            </li>
                                        </ul>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span>Loyalty Points:</span>
                                                        <input type="hidden" name="redeemPointsInput" id="redeemPointsInput"
                                                            value="<?= $user['loyalty_points']; ?>" />
                                                        <strong><?= $user['loyalty_points']; ?></strong>
                                                    </div>
                                                    <span
                                                        class="btn btn-secondary button-sm <?= $user['loyalty_points'] == 0 ? 'disabled' : ''; ?>"
                                                        id="redeemPointsBtn">Redeem</span>
                                                </li>
                                            </div>
                                        </div>

                                        <!-- Promo Code Input -->
                                        <!-- Uncomment if needed -->

                                        <div class="input-group pb-3">
                                            <input type="text" id="promoCodeInput" class="form-control form-control-sm"
                                                placeholder="Enter Promo Code">
                                            <div class="input-group-append">
                                                <button type="button" id="applyPromoBtn"
                                                    class="btn btn-secondary button-sm">Apply</button>
                                            </div>
                                        </div>
                                        <div id="promoMessage" class="text-success"></div>

                                        <?php if ($userAddress): ?>
                                            <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                                        <?php else: ?>
                                            <button onclick="checkAddress()" class="btn btn-primary w-100">Proceed to
                                                Checkout</button>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Your cart is empty. <a href="products.php">Continue shopping</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Update quantity
        $(document).on('change', '.quantity-input', function () {
            const cartId = $(this).data('cart-id');
            const quantityInput = $(this);
            const quantity = parseInt(quantityInput.val());

            // Fetch available stock for the product
            $.post('includes/fetch_stock.php', { cart_id: cartId }, function (response) {
                const result = JSON.parse(response);

                if (result.status === 'success') {
                    const availableStock = parseInt(result.stock_quantity);

                    if (quantity > availableStock) {
                        alert(`Only ${availableStock} items are available in stock.`);
                        quantityInput.val(availableStock); // Reset input to max available stock
                        return;
                    }

                    // Proceed with updating the cart quantity
                    $.post('includes/cart_handler.php', { action: 'update', cart_id: cartId, quantity: quantity }, function (updateResponse) {
                        const updateResult = JSON.parse(updateResponse);
                        if (updateResult.status === 'success') {
                            location.reload(); // Reload page to update totals
                        } else {
                            alert(updateResult.message);
                        }
                    });

                } else {
                    alert("Error fetching product stock.");
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

        $(document).on("click", "#applyPromoBtn", function () {
            let promoCode = $("#promoCodeInput").val().trim();

            if (!promoCode) {
                alert("Please enter a promo code.");
                return;
            }

            $.ajax({
                url: "includes/apply_promo_code.php",
                type: "POST",
                data: { promo_code: promoCode, user_id: "<?= $userId; ?>" },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert("Promo code applied! Discount: ₹" + response.discount);

                        // Store the discounted price and promo in session via AJAX
                        $.post("includes/apply_discount_session.php", {
                            promo_code: promoCode,
                            discount: response.discount
                        });

                        $("#orderTotal").text("₹" + response.updated_total.toFixed(2));
                        $("#promoMessage").text(`Applied: ${promoCode} (₹${response.discount} Off)`).show();
                        $("#applyPromoBtn").prop("disabled", true);
                    } else {
                        alert(response.message);
                        $("#promoMessage").text("").hide();
                    }
                },
                error: function () {
                    alert("Error applying promo code.");
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

        $(document).on("click", "#redeemPointsBtn", function () {
            let pointsToRedeem = $("#redeemPointsInput").val();
            $.ajax({
                url: "includes/redeeem_loyalty_points.php",
                type: "POST",
                data: { redeem_points: pointsToRedeem, user_id: "<?= $userId; ?>" },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert("Points redeemed successfully! Discount: ₹" + response.discount);

                        // Store the discounted price and points in session via AJAX
                        $.post("includes/apply_discount_session.php", {
                            discount: response.discount,
                            points_used: pointsToRedeem
                        });

                        $("#orderTotal").text("₹" + response.updated_order_amount.toFixed(2));
                        $("#redeemPointsBtn").addClass('disabled');
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("Error redeeming points.");
                }
            });
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>

</html>