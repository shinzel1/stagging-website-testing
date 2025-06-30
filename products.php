<?php include 'includes/header.php'; ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">
<link rel="stylesheet" href="assets/css/products.css">
<link href="assets/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }

    #selectedValues {
        margin-top: 20px;
        font-weight: bold;
    }

    .wishlist-active {
        background-color: #e02725;
        color: white;
    }
</style>
<?php

$is_logged_in = isset($_SESSION['user_id']);
// Database configuration
require_once("config/database_connection.php");

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
    // Check if the user has admin privileges
    if (!$isAdmin) {
        $error = 'You do not have permission to add products.';
    } else {
        // Get and sanitize input
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);
        $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
        $original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : null;
        $discounted_price = isset($_POST['discounted_price']) ? floatval($_POST['discounted_price']) : null;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
        $categories = isset($_POST['categories']) ? json_encode(explode(',', trim($_POST['categories']))) : json_encode([]);
        $seller = trim($_POST['seller']);
        $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;
        $reviews = isset($_POST['reviews']) ? intval($_POST['reviews']) : null;

        // Validate required fields
        if (!$name || !$description || !$image_url || !$price || !$original_price || !$discounted_price || !$quantity || !$seller || $rating === null || $reviews === null) {
            $error = 'All fields are required and must be valid.';
        } else {
            try {
                // Insert into database
                $stmt = $pdo->prepare("INSERT INTO products (name, image_url, description, price, original_price, discounted_price, quantity, categories, seller, rating, reviews) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $image_url, $description, $price, $original_price, $discounted_price, $quantity, $categories, $seller, $rating, $reviews]);

                $success = 'Product added successfully!';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
$categoriesName = "";
$products = [];

if (isset($_GET["search"])) {
    $echoVal = trim($_GET["search"]);

    // Get category name from database
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE title = ?");
    $stmt->execute([$echoVal]);
    $categories = $stmt->fetch(PDO::FETCH_ASSOC);

    // If category found, perform search
    if ($categories) {
        $categoriesName = $categories["name"];
        $searchTerm = '%' . $categoriesName . '%';

        try {
            $query = "
                SELECT DISTINCT p.* 
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE (p.name LIKE :searchTerm
                   OR p.description LIKE :searchTerm
                   OR c.name LIKE :searchTerm)
                   AND p.hide_product = 0
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            echo "<script>$('#search-input').val(\"$categoriesName\")</script>";
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Search error: " . $e->getMessage());
        }
    }
} else {
    try {
        // Fetch only products that are not hidden
        $stmt = $pdo->query("SELECT * FROM products WHERE hide_product = 0 ORDER BY created_at DESC LIMIT 10");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

$categories_stmt = $pdo->prepare("SELECT id, name, title, (SELECT COUNT(*) FROM product_categories WHERE category_id = categories.id) AS product_count FROM categories");
$categories_stmt->execute();
$categoriess = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
$flavour_list = [
    "Vanilla",
    "Chocolate",
    "Cookies and Cream",
    "Caramel Macchiato",
    "Peanut Butter",
    "Mocha",
    "Birthday Cake",
    "Cinnamon Roll",
    "White Chocolate",
    "Strawberry",
    "Banana",
    "Mango",
    "Blueberry",
    "Raspberry",
    "Watermelon",
    "Pineapple",
    "Peach",
    "Apple Cinnamon",
    "Pomegranate",
    "Coconut",
    "Passion Fruit",
    "Dragon Fruit",
    "Lychee",
    "Kiwi",
    "Acai Berry",
    "Lemon",
    "Lime",
    "Orange",
    "Grapefruit",
    "Yuzu",
    "Mint Chocolate",
    "Matcha Green Tea",
    "Chai Latte",
    "Iced Coffee",
    "Honey Lemon",
    "Bubblegum",
    "Blue Raspberry",
    "Green Apple",
    "Grape",
    "Cherry Limeade",
    "Sour Candy",
    "Rainbow Sherbet"
]
    ?>


<div class="container pt-5 pb-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Product Form (Visible only to admins) -->
    <?php if ($isAdmin): ?>
        <div class="pt-5">

            <!-- Button trigger modal -->
            <p style="text-align: end;">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    Add New Product
                </button>
            </p>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New Product</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <form id="product-form">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image_url" class="form-label">Upload Image</label>
                                            <input type="file" class="form-control" id="image_url" name="image" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Product Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"
                                                required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="original_price" class="form-label">Original Price</label>
                                            <input type="number" step="0.01" class="form-control" id="original_price"
                                                name="original_price" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" step="0.01" class="form-control" id="price" name="price"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="discounted_price" class="form-label">Discount Price</label>
                                            <input type="number" step="0.01" class="form-control" id="discounted_price"
                                                name="discounted_price" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity"
                                                required>
                                        </div>
                                        <div id="multiSelectContainer" class="mb-3">
                                            <input type="hidden" id="editCategories" name="categories" required>
                                            <label for="multiSelect" class="form-label">Categories</label>
                                            <select multiple class="form-control" id="multiSelect"></select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="flavour" class="form-label">Flavour</label>
                                            <select class="form-select" aria-label="select flavour" name="flavour">
                                                <option selected>select one</option>
                                                <?php foreach ($flavour_list as $flavour): ?>
                                                    <option value="<?= $flavour; ?>"><?= htmlspecialchars($flavour); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="seller" class="form-label">Seller</label>
                                            <input type="text" class="form-control" id="seller" name="seller" required>
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Table -->
            <div class="card">
                <div class="card-header">Product List</div>
                <div class="card-body">
                    <table class="table table-striped table-hover" id="product-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Categories</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Products will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    <?php endif; ?>
    <!-- <div class="row justify-content-center pt-5">
        <div class="input-group mb-3" style="width: 80%">
            <input type="text" id="searchInput" class="form-control input-text" placeholder="Search products...."
                aria-label="Recipient's username" aria-describedby="basic-addon2" />
            <div class="input-group-append">
                <button class="btn btn-outline-dark" onclick="searchProducts()" type="button"><i
                        class="fa fa-search"></i></button>
            </div>
        </div>
    </div> -->

    <div class="row pt-5">
        <div class="col-md-3">
            <div class="border rounded p-3">
                <div class="g-pr-15--lg g-pt-60 ">
                    <!-- Categories -->
                    <div class="g-mb-30">
                        <h3 class="h5 mb-3">Categories</h3>
                        <ul class="list-unstyled vh-50 overflow-auto">
                            <?php foreach ($categoriess as $category): ?>
                                <li class="my-3">
                                    <span class="d-block u-link-v5 g-color-gray-dark-v4 g-color-primary--hover"
                                        data-id="<?= urlencode($category['id']); ?>">
                                        <?= htmlspecialchars($category['name']); ?>
                                        <span class="float-end g-font-size-12"><?= $category['product_count']; ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- End Categories -->

                    <hr>

                    <!-- Pricing -->
                    <div class="g-mb-30">
                        <h3 class="h5 mb-3">Pricing</h3>

                        <div class="text-center">
                            <span class="d-block g-color-primary mb-4">₹(<span id="rangeSliderAmount3"></span>)</span>
                            <div id="rangeSlider1"
                                class="u-slider-v1-3 ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"
                                data-result-container="rangeSliderAmount3" data-range="true" data-default="0, 10000"
                                data-min="0" data-max="10000">
                                <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"
                                    style="left: 36%;"></span>
                                <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"
                                    style="left: 64%;"></span>
                            </div>

                        </div>
                    </div>
                    <hr>

                    <button class="btn btn-block u-btn-black g-font-size-12 text-uppercase g-py-12 g-px-25"
                        type="button">Reset</button>
                </div>
            </div>

        </div>
        <div class="col-md-9">
            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-3"
                id="product-results">
                <?php foreach ($products as $product): ?>
                    <div class="product-item position-relative">
                        <a href="product-details.php?id=<?= urlencode($product['id']); ?>"
                            title="<?= htmlspecialchars($product['name']); ?>">
                            <figure class="position-relative">

                                <img src="<?= htmlspecialchars($product['image_url']); ?>"
                                    alt="<?= htmlspecialchars($product['name']); ?>" class="tab-image">


                                <!-- Out of Stock Overlay -->
                                <?php if ($product['quantity'] <= 0): ?>
                                    <div class="out-of-stock-overlay">
                                        <img src="assets/images/sold_out.png" alt="Out of Stock" class="out-of-stock-img">
                                    </div>
                                <?php endif; ?>
                            </figure>
                        </a>

                        <div class="d-flex flex-column text-center">
                            <h3 class="fs-6 fw-normal"><?= htmlspecialchars($product['name']); ?></h3>
                            <div>
                                <span class="rating">
                                    <?php
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
                                    ?>
                                </span>
                                <span>(<?= htmlspecialchars($product['reviews']); ?>)</span>
                            </div>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <del>₹<?= number_format($product['original_price'], 2); ?></del>
                                <span class="text-dark fw-semibold">₹<?= number_format($product['price'], 2); ?></span>
                                <span
                                    class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                    <?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>%
                                    OFF
                                </span>
                            </div>
                            <div class="button-area p-3 pt-0">
                                <div class="row g-1 mt-2">
                                    <div class="col-3"></div>
                                    <div class="col-7">
                                        <span
                                            class="btn btn-primary rounded-1 p-2 fs-7 btn-cart add-to-cart-btn  <?= ($product['quantity'] <= 0) ? 'disabled' : ''; ?>"
                                            data-product-id="<?= htmlspecialchars($product['id']) ?>">
                                            <svg width="18" height="18">
                                                <use xlink:href="#cart"></use>
                                            </svg> Add to Cart
                                        </span>
                                    </div>
                                    <?php
                                    // Check if product is in the wishlist
                                    $user_id = $_SESSION['user_id'] ?? null;
                                    $product_id = $product['id'];

                                    $wishlist_check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                                    $wishlist_check->execute([$user_id, $product_id]);
                                    $is_wishlisted = $wishlist_check->fetch() ? true : false;
                                    ?>

                                    <div class="col-2">
                                        <span class="btn btn-secondary rounded-1 p-2 fs-6 wishlist-btn"
                                            data-product-id="<?= $product_id ?>">
                                            <svg width="18" height="18"
                                                class="<?= $is_wishlisted ? 'wishlist-active' : '' ?>">
                                                <use xlink:href="#heart"></use>
                                            </svg>
                                        </span>
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
<!-- jQuery -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<!-- jQuery UI for Range Slider -->
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script>
    $('#rangeSliderAmount3').html("0 - 10000")
    $(document).ready(function () {
        // Select filter elements
        const categoryLinks = $(".list-unstyled span");
        const ratingStars = $(".js-rating .click");

        function fetchProducts() {
            let category = $(".list-unstyled span.active").data("id") || "";
            let priceRange = $("#rangeSlider1").slider("values");

            let formData = new FormData();
            formData.append("category", category);
            formData.append("min_price", priceRange[0]);
            formData.append("max_price", priceRange[1]);

            $.ajax({
                url: 'includes/fetch_products.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        let products = response.products;
                        let resultsHtml = '';

                        if (products.length > 0) {
                            products.forEach(product => {
                                const fullStars = Math.floor(product.rating);
                                const halfStar = product.rating - fullStars >= 0.5;
                                let starIcons = "";
                                for (let i = 0; i < 5; i++) {
                                    if (i < fullStars) {
                                        starIcons += '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-full"></use></svg>';
                                    } else if (halfStar && i === fullStars) {
                                        starIcons += '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-half"></use></svg>';
                                    } else {
                                        starIcons += '<svg width="18" height="18" class="text-muted"><use xlink:href="#star-empty"></use></svg>';
                                    }
                                }

                                // Wishlist Button
                                let wishlistActive = product.in_wishlist ? 'wishlist-active' : '';

                                resultsHtml += `
                        <div class="col">
                            <div class="product-item position-relative">
                                <a href="product-details.php?id=${encodeURIComponent(product.id)}" title="${product.name}">
                                    <figure class="position-relative">
                                        <img src="${product.image_url}" alt="${product.name}" class="tab-image">
                                        ${product.quantity <= 0 ? `
                                            <div class="out-of-stock-overlay">
                                                <img src="assets/images/sold_out.png" alt="Out of Stock" class="out-of-stock-img">
                                            </div>
                                        ` : ''}
                                    </figure>
                                </a>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">${product.name}</h3>
                                    <div>
                                        <span class="rating">
                                            ${starIcons} ${product.rating}
                                        </span>
                                        <span>(${product.reviews}) reviews</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>₹${product.original_price}</del>
                                        <span class="text-dark fw-semibold">₹${product.price}</span>
                                        <span class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                            ${Math.round(((product.original_price - product.price) / product.original_price) * 100)}% OFF
                                        </span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"></div>
                                            <div class="col-7">
                                                <span class="btn btn-primary rounded-1 p-2 fs-7 btn-cart add-to-cart-btn ${product.quantity <= 0 ? 'disabled' : ''}"
                                                    data-product-id="${product.id}">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart
                                                </span>
                                            </div>
                                            <div class="col-2">
                                                <span class="btn btn-secondary rounded-1 p-2 fs-6 wishlist-btn ${wishlistActive}"
                                                    data-product-id="${product.id}">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                            });
                        } else {
                            resultsHtml = '<div class="result-item">No products found.</div>';
                        }

                        $('#product-results').html(resultsHtml).show();

                        // Attach event listener for wishlist toggle after content is loaded
                        attachWishlistEvent();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while fetching the data.');
                }
            });
        }
        function attachWishlistEvent() {
            $(".wishlist-btn").off("click").on("click", function (e) {
                e.preventDefault();
                let btn = $(this);
                let productId = btn.data("product-id");

                $.ajax({
                    url: "includes/wishlist_toggle.php",
                    method: "POST",
                    data: { product_id: productId },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            if (response.wishlist_status) {
                                btn.addClass("wishlist-active");
                            } else {
                                btn.removeClass("wishlist-active");
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert("An error occurred while updating the wishlist.");
                    }
                });
            });
        }

        // Category filter
        categoryLinks.on("click", function (e) {
            e.preventDefault();
            categoryLinks.removeClass("active");
            $(this).addClass("active");
            fetchProducts();
        });

        // Initialize Price Range Slider & Event
        $("#rangeSlider1").slider({
            range: true,
            min: 0,
            max: 10000,
            values: [0, 10000],
            slide: function (event, ui) {
                $("#rangeSliderAmount3").text(ui.values[0] + " - " + ui.values[1]);
            },
            change: function () {
                fetchProducts();
            }
        });

        // Rating filter
        ratingStars.on("click", function () {
            ratingStars.removeClass("selected");
            $(this).addClass("selected");
            fetchProducts();
        });


        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".wishlist-btn").forEach(function (btn) {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    let productId = this.getAttribute("data-product-id");
                    let heartIcon = this.querySelector("svg");

                    fetch("includes/wishlist_toggle.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "product_id=" + productId
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.wishlist_status) {
                                    heartIcon.classList.add("wishlist-active");
                                } else {
                                    heartIcon.classList.remove("wishlist-active");
                                }
                            } else {
                                alert(data.message);
                            }
                        });
                });
            });
        });


    });

    function searchProducts() {
        {
            let searchTerm = $("#searchInput").val().trim();
            if (searchTerm.length > 0) {
                $.ajax({
                    url: 'includes/fetch_products.php',
                    method: 'POST',
                    data: { searchTerm: searchTerm },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            let products = response.products;
                            let resultsHtml = '';

                            if (products?.length > 0) {
                                var productContainer = ""
                                products.forEach(product => {
                                    // Calculate rating stars
                                    const fullStars = Math.floor(product.rating);
                                    const halfStar = product.rating - fullStars >= 0.5;

                                    let starIcons = "";
                                    for (let i = 0; i < 5; i++) {
                                        if (i < fullStars) {
                                            starIcons += '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-full"></use></svg>';
                                        } else if (halfStar && i === fullStars) {
                                            starIcons += '<svg width="18" height="18" class="text-warning"><use xlink:href="#star-half"></use></svg>';
                                        } else {
                                            starIcons += '<svg width="18" height="18" class="text-muted"><use xlink:href="#star-empty"></use></svg>';
                                        }
                                    }
                                    // Wishlist Button
                                    let wishlistActive = product.in_wishlist ? 'wishlist-active' : '';
                                    // Create product HTML
                                    const productHTML = `
                                    <div class="col">
                                        <div class="product-item position-relative">
                                        <a href="product-details.php?id=${encodeURIComponent(product.id)}" title="${product.name}">
                                            <figure class="position-relative">
                                                    <img src="${product.image_url}" alt="${product.name}" class="tab-image">
                                                ${product.quantity <= 0 ? `
                                                    <div class="out-of-stock-overlay">
                                                        <img src="assets/images/sold_out.png" alt="Out of Stock" class="out-of-stock-img">
                                                    </div>
                                                ` : ''}
                                            </figure>
                                            </a>
                                            <div class="d-flex flex-column text-center">
                                                <h3 class="fs-6 fw-normal">${product.name}</h3>
                                                <div>
                                                    <span class="rating">
                                                        ${starIcons} ${product.rating}
                                                    </span>
                                                    <span>(${product.reviews}) reviews</span>
                                                </div>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <del>₹${product.original_price}</del>
                                                    <span class="text-dark fw-semibold">₹${product.price}</span>
                                                    <span class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                                        ${Math.round(((product.original_price - product.price) / product.original_price) * 100)}% OFF
                                                    </span>
                                                </div>
                                                <div class="button-area p-3 pt-0">
                                                    <div class="row g-1 mt-2">
                                                        <div class="col-3"></div>
                                                        <div class="col-7">
                                                            <span class="btn btn-primary rounded-1 p-2 fs-7 btn-cart add-to-cart-btn"
                                                                data-product-id="${product.id}"
                                                                ${product.quantity <= 0 ? 'disabled' : ''}>
                                                                <svg width="18" height="18">
                                                                    <use xlink:href="#cart"></use>
                                                                </svg> Add to Cart
                                                            </span>
                                                        </div>
                                                         <div class="col-2">
                                                            <span class="btn btn-secondary rounded-1 p-2 fs-6 wishlist-btn ${wishlistActive}"
                                                                data-product-id="${product.id}">
                                                                <svg width="18" height="18">
                                                                    <use xlink:href="#heart"></use>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                `;

                                    // Append to container
                                    resultsHtml = resultsHtml + (productHTML);
                                });
                            } else {
                                resultsHtml = '<div class="result-item">No products found.</div>';
                            }

                            $('#product-results').html(resultsHtml).show();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('An error occurred while fetching the data.');
                    }
                });
            } else {
                $('#product-results').hide();
            }
        }
    }

    $(document).ready(function () {
        // Use event delegation to handle dynamically added elements
        $(document).on("click", ".add-to-cart-btn", function (e) {
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
                    updateCartCount(); // Refresh cart count
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
        if (<?php echo json_encode($categoriesName); ?>) { $('#searchInput').val(<?php echo json_encode($categoriesName); ?>); }

    });

    document.addEventListener("DOMContentLoaded", function () {
        const productForm = document.getElementById("product-form");
        const productTable = document.getElementById("product-table")?.querySelector("tbody");

        function loadProducts() {
            $('#product-table')?.DataTable({
                processing: true,
                serverSide: true, // Enables AJAX pagination
                ajax: {
                    url: "includes/fetch_products_data.php",
                    type: "POST"
                },
                columns: [
                    {
                        data: "image_url", render: function (data, type, row) {
                            return `<img src="${data}" alt="${row.name}" style="width: 33px; height: 33px;">`;
                        }
                    },
                    {
                        data: "name", render: function (data, type, row) {
                            return `<a href="product-details.php?id=${row.id}" title="${data}">${data}</a>`;
                        }
                    },
                    { data: "description" },
                    { data: "price" },
                    { data: "quantity" },
                    {
                        data: "categories", render: function (data) {
                            return data ? JSON.parse(data).join(" | ") : "";
                        }
                    },
                    { data: "rating" }
                ],
                destroy: true, // Ensure table reloads properly
                searching: true, // Enables search
                paging: true, // Enables pagination
                lengthMenu: [10, 20, 50] // Items per page
            });
        }

        // Add product using AJAX
        productForm?.addEventListener("submit", function (e) {
            e.preventDefault();
            $('#editCategories').val($('#multiSelect').val())
            const formData = new FormData(productForm);
            fetch("includes/add_product.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert("Product added successfully!");
                        loadProducts();
                        productForm.reset();
                        $('#staticBackdrop').modal('hide')
                    } else {
                        alert("Error: " + result.message);
                    }
                })
                .catch(error => console.error("Error adding product:", error));
        });
        // Load products on page load
        loadProducts();
    });

    $(document).ready(function () {
        $.ajax({
            url: 'includes/fetch_categories.php',
            type: 'POST',
            dataType: 'json',
            data: {
                product_Id: null // Send the product ID in the request
            },
            success: function (response) {
                let $multiSelect = $('#multiSelect');

                // Clear existing options (optional)
                $multiSelect.empty();

                // Populate the select element with new options
                response.categories.forEach(function (option) {
                    $multiSelect.append(new Option(option.name, option.id, false, false));
                });

                response.productCategories.forEach(function (option) {
                    $multiSelect.append(new Option(option.name, option.id, true, true)); // Select existing categories
                });
                console.log(typeof $.fn.select2); // Should print "function"

                // Initialize or Reinitialize Select2
                $multiSelect.select2({
                    placeholder: "Please select your framework.",
                    allowClear: true,
                    tags: true,
                    dropdownParent: $('#multiSelectContainer'),
                    tokenSeparators: [',', ' ']
                }).trigger('change'); // Ensure it updates

            },
            error: function (xhr, status, error) {
                console.error("Error loading categories:", error);
            }
        });

        // Initialize Range Slider
        $("#rangeSlider1").slider({
            range: true,
            min: 0,
            max: 10000,
            values: [0, 10000],
            slide: function (event, ui) {
                $("#rangeSliderAmount3").text(ui.values[0] + " - " + ui.values[1]);
            }
        });

        // Checkbox and Radio Button Toggle
        $(".u-check input[type='checkbox'], .u-check input[type='radio']").change(function () {
            if ($(this).is(":checked")) {
                $(this).closest('.u-check').find('.u-check-icon-checkbox-v4').addClass('checked');
            } else {
                $(this).closest('.u-check').find('.u-check-icon-checkbox-v4').removeClass('checked');
            }
        });

        // Rating System
        $(".js-rating li").click(function () {
            var index = $(this).index();
            $(".js-rating li").removeClass("g-color-primary").addClass("g-color-gray-light-v3");
            $(".js-rating li").slice(0, index + 1).removeClass("g-color-gray-light-v3").addClass("g-color-primary");
        });

        // Reset Button
        $("button[type='button']").click(function () {
            // Reset Range Slider
            $("#rangeSlider1").slider("values", [0, 10000]);
            $("#rangeSliderAmount3").text("0 - 10000");

            // Reset Checkboxes and Radio Buttons
            $(".u-check input[type='checkbox'], .u-check input[type='radio']").prop("checked", false);
            $(".u-check-icon-checkbox-v4").removeClass("checked");

            // Reset Rating
            $(".js-rating li").removeClass("g-color-primary").addClass("g-color-gray-light-v3");
            $(".js-rating li").slice(0, 4).removeClass("g-color-gray-light-v3").addClass("g-color-primary");
        });
    });
</script>
<script src="assets/js/select2.min.js"></script>
<?php include 'includes/footer.php'; ?>