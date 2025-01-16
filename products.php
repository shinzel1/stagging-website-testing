<?php include 'includes/header.php'; ?>
<?php
// Start the session
// session_start();
$is_logged_in = isset($_SESSION['user_id']);
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
    $isAdmin = null;
} else {
    // Check if the user is an admin
    $isAdmin = $_SESSION['role'] === 'admin';
}

$error = '';
$success = '';

// Handle product form submission (only for admins)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        $error = 'You do not have permission to add products.';
    } else {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);
        $price = trim($_POST['price']);
        $quantity = trim($_POST['quantity']);
        $categories = isset($_POST['categories']) ? json_encode(explode(',', $_POST['categories'])) : json_encode([]);
        $seller = trim($_POST['seller']);
        $stars = trim($_POST['stars']);
        $reviews = trim($_POST['reviews']);

        if (!$name || !$image_url || !$description || !$price || !$quantity || !$seller || !$stars || !$reviews) {
            $error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name,image_url, description, price, quantity, categories, seller, stars, reviews) 
                                       VALUES (?,?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $image_url, $description, $price, $quantity, $categories, $seller, $stars, $reviews]);
                $success = 'Product added successfully!';
            } catch (PDOException $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch all products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error: ' . $e->getMessage();
}


?>

<div class="container p-5">

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Product Form (Visible only to admins) -->
    <?php if ($isAdmin): ?>
        <div class="card mb-4">
            <div class="card-header">Add New Product</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Image</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Product Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="categories" class="form-label">Categories (comma-separated)</label>
                        <input type="text" class="form-control" id="categories" name="categories" required>
                    </div>
                    <div class="mb-3">
                        <label for="seller" class="form-label">Seller</label>
                        <input type="text" class="form-control" id="seller" name="seller" required>
                    </div>
                    <div class="mb-3">
                        <label for="stars" class="form-label">Rating (Stars)</label>
                        <input type="number" step="0.1" class="form-control" id="stars" name="stars" required>
                    </div>
                    <div class="mb-3">
                        <label for="reviews" class="form-label">Number of Reviews</label>
                        <input type="number" class="form-control" id="reviews" name="reviews" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card">
            <div class="card-header">Product List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Categories</th>
                            <th>Seller</th>
                            <th>Stars</th>
                            <th>Reviews</th>
                            <th>Added On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['id']) ?></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['image_url']) ?></td>
                                    <td><?= htmlspecialchars($product['description']) ?></td>
                                    <td><?= htmlspecialchars($product['price']) ?></td>
                                    <td><?= htmlspecialchars($product['quantity']) ?></td>
                                    <td><?= implode(', ', json_decode($product['categories'], true)) ?></td>
                                    <td><?= htmlspecialchars($product['seller']) ?></td>
                                    <td><?= htmlspecialchars($product['stars']) ?></td>
                                    <td><?= htmlspecialchars($product['reviews']) ?></td>
                                    <td><?= htmlspecialchars($product['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <div
                class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="product-item">
                            <figure>
                                <a href="product-details.php?title=<?= urlencode($product['name']); ?>"
                                    title="<?= htmlspecialchars($product['name']); ?>">
                                    <img src="<?= htmlspecialchars($product['image_url']); ?>"
                                        alt="<?= htmlspecialchars($product['name']); ?>" class="tab-image">
                                </a>
                            </figure>
                            <div class="d-flex flex-column text-center">
                                <h3 class="fs-6 fw-normal"><?= htmlspecialchars($product['name']); ?></h3>
                                <div>
                                    <span class="rating">
                                        <!-- <?php
                                        $fullStars = floor($product['rating']);
                                        $halfStar = $product['rating'] - $fullStars >= 0.5;
                                        for ($i = 0; $i < 5; $i++) {
                                            if ($i < $fullStars) {
                                                echo '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-full"></use></svg>';
                                            } elseif ($halfStar && $i == $fullStars) {
                                                echo '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-half"></use></svg>';
                                            } else {
                                                echo '<svg width="18" height="18" class="text-muted"><use xlink:href="#star-empty"></use></svg>';
                                            }
                                        }
                                        ?> -->
                                    </span>
                                    <span>(<?= htmlspecialchars($product['reviews']); ?>) reviews</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <!-- <del>₹<?= number_format($product['price'], 2); ?></del> -->
                                    <span class="text-dark fw-semibold">₹<?= number_format($product['price'], 2); ?></span>
                                    <span
                                        class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                        <!-- <?= round((($product['price'] - $product['price']) / $product['price']) * 100); ?>%
                                        OFF -->
                                        price
                                    </span>
                                </div>
                                <div class="button-area p-3 pt-0">
                                    <div class="row g-1 mt-2">
                                        <div class="col-3">
                                            <!-- <input type="number" name="quantity"
                                                class="form-control border-dark-subtle input-number quantity" value="1"
                                                min="1"> -->
                                        </div>
                                        <div class="col-7">
                                            <a href="add-to-cart.php?title=<?= urlencode($product['name']); ?>"
                                                class="btn btn-primary rounded-1 p-2 fs-7 btn-cart add-to-cart-btn" data-product-id="<?= htmlspecialchars($product['id']) ?>">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#cart"></use>
                                                </svg> Add to Cart
                                            </a>
                                        </div>
                                        <div class="col-2">
                                            <!-- <a href="wishlist.php?title=<?= urlencode($product['name']); ?>"
                                                class="btn btn-outline-dark rounded-1 p-2 fs-6">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#heart"></use>
                                                </svg>
                                            </a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".add-to-cart-btn").click(function (e) {
            e.preventDefault();

            const productId = $(this).data("product-id");
            const quantity = 1; // You can make this dynamic if needed

            $.ajax({
                url: "includes/addToCart.php", // PHP backend script
                type: "POST",
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        alert(response.message); // Show success message
                    } else {
                        alert(response.message); // Show error message
                    }
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
</body>

</html>