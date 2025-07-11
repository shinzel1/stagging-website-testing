<?php
require_once("config/database_connection.php");

try {
    $query = "SELECT name, title, description, image FROM categories LIMIT 7";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Failed to fetch categories: " . $e->getMessage());
}

?>
<style>
    .ProductImage {
        width: 13rem;
    }

    .wishlist-active {
        background-color: #e02725;
        color: white;
    }

    .out-of-stock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        background-color: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .out-of-stock-img {
        width: 10em;
        height: auto;
    }
</style>

<section class="pt-5"
    style="background-image: url('assets/images/victor-freitas-WvDYdXDzkhs-unsplash.jpg'); background-repeat: no-repeat; background-size: cover;">

    <div class="container-lg">
        <div class="row">
            <div class="col-lg-6 pt-5 mt-5">
                <h2 class="display-1 ls-1 text-white">
                    <span class="fw-bold">Fuel Your Strength</span> <span> –
                        Premium Supplements for Every Workout. </span>
                </h2>
                <!-- <p class="fs-4">Complete Nurition Store</p> -->
                <div class="text-white">
                    <a href="products.php"
                        class="btn btn-outline-white text-uppercase fs-6 rounded-pill px-4 py-3 mt-3 fw-bold">Start
                        Shopping</a>
                    <a href="login.php"
                        class="btn btn-outline-white text-uppercase fs-6 rounded-pill px-4 py-3 mt-3 fw-bold">Join
                        Now</a>
                </div>
                <div class="row my-5">
                    <?php
                    $stats = [
                        ['value' => '14k+', 'label' => 'Product Varieties'],
                        ['value' => '50k+', 'label' => 'Happy Customers'],
                        ['value' => '10+', 'label' => 'Store Locations']
                    ];
                    foreach ($stats as $stat): ?>
                        <div class="col">
                            <div class="row text-white">
                                <div class="col-auto">
                                    <p class="fs-1 fw-bold lh-sm mb-0"><?= $stat['value']; ?></p>
                                </div>
                                <div class="col">
                                    <p class="text-uppercase lh-sm mb-0"><?= $stat['label']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-3 g-0 justify-content-center">
            <?php
            $cards = [
                ['bg' => 'green', 'icon' => '#fresh', 'title' => 'Eco-Friendly', 'text' => 'Use biodegradable or recyclable packaging and clearly state it.'],
                ['bg' => 'grey', 'icon' => '#organic', 'title' => 'All Naturals', 'text' => 'Our protein powders are sourced from grass-fed cows.'],
                ['bg' => 'orange', 'icon' => '#delivery', 'title' => 'Fast Shipping', 'text' => 'Offers shipping in New Delhi and Delhi-NCR regions.']
            ];
            foreach ($cards as $card): ?>
                <div class="col">
                    <div class="card border-0 bg-<?= $card['bg']; ?> rounded-0 p-4 text-light">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <svg width="60" height="60">
                                    <use xlink:href="<?= $card['icon']; ?>"></use>
                                </svg>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body p-0">
                                    <h5 class="text-light"><?= $card['title']; ?></h5>
                                    <p class="card-text"><?= $card['text']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 overflow-hidden">
    <div class="container-lg">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                    <h2 class="section-title">Category</h2>

                    <div class="d-flex align-items-center">
                        <a href="products.php" class="btn btn-secondary me-2">View All</a>
                        <div class="swiper-buttons">
                            <button class="swiper-prev category-carousel-prev btn btn-secondary">❮</button>
                            <button class="swiper-next category-carousel-next btn btn-secondary">❯</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="category-carousel swiper">
                    <div class="swiper-wrapper">
                        <?php
                        // Loop through each category to generate the slides
                        foreach ($categories as $category): ?>
                            <a href="products.php?search=<?= $category['title']; ?>"
                                class="nav-link swiper-slide text-center">
                                <img src="<?= $category['image']; ?>" class="rounded-circle categoryRoundImage"
                                    alt="Category Thumbnail" />
                                <h4 class="fs-6 mt-3 fw-normal category-title"><?= $category['name']; ?></h4>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <style>
                    .categoryRoundImage {
                        max-height: 14rem;
                    }
                </style>
            </div>
        </div>
    </div>
</section>
<section class="pb-5">
    <div class="container-lg">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header d-flex flex-wrap justify-content-between my-4">
                    <h2 class="section-title">Best Selling Products</h2>
                    <div class="d-flex align-items-center">
                        <a href="products.php" class="btn btn-secondary rounded-1">View All</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div
                    class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                    <?php
                    // Sample array of products
                    try {
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 0,5");
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }

                    try {
                        $stmt = $pdo->query("SELECT * FROM products WHERE featured_product = 1 ORDER BY created_at DESC LIMIT 0,5");
                        $featureProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                    // Loop through products and display them
                    foreach ($products as $product) {
                        ?>
                        <div class="col">
                            <div class="product-item position-relative">
                                <a href="product/<?= htmlspecialchars($product['slug']); ?>-<?= urlencode($product['id']); ?>"
                                    title="<?= htmlspecialchars($product['name']); ?>">
                                    <figure class="position-relative">

                                        <img src="<?= htmlspecialchars($product['image_url']); ?>"
                                            alt="<?= htmlspecialchars($product['name']); ?>" class="tab-image">


                                        <!-- Out of Stock Overlay -->
                                        <?php if ($product['quantity'] == 0): ?>
                                            <div class="out-of-stock-overlay">
                                                <img src="assets/images/sold_out.png" alt="Out of Stock"
                                                    class="out-of-stock-img">
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
                                        <span
                                            class="text-dark fw-semibold">₹<?= number_format($product['price'], 2); ?></span>
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
                                                    class="btn rounded-1 p-2 fs-7 btn-cart add-to-cart-btn  btn-add-to-cart <?= $product['quantity'] == 0 ? 'disabled' : ''; ?>"
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
                                                <span
                                                    class="btn btn-secondary rounded-1 p-2 fs-6 wishlist-btn <?= $is_wishlisted ? 'wishlist-active' : '' ?>"
                                                    data-product-id="<?= $product_id ?>">
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
                        <?php
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-3">
    <div class="container-lg">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-blocks">
                    <?php
                    // Array of banners
                    try {
                        $stmt = $pdo->query("SELECT * FROM banners");
                        $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }

                    // Loop through banners and display them
                    foreach ($banners as $banner) {
                        ?>
                        <div class="banner-ad d-flex align-items-center <?= htmlspecialchars($banner['bg_class']); ?> <?= htmlspecialchars($banner['block_class']); ?>"
                            style="background: url('<?= htmlspecialchars($banner['background_image']); ?>') no-repeat; background-size: cover;">
                            <div class="banner-content p-5">
                                <div class="content-wrapper text-light">
                                    <h3 class="banner-title text-light"><?= htmlspecialchars($banner['title']); ?></h3>
                                    <p><?= htmlspecialchars($banner['description']); ?></p>
                                    <a href="<?= htmlspecialchars($banner['link']); ?>" class="btn-link text-white">Shop
                                        Now</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- / Banner Blocks -->
            </div>
        </div>
    </div>
</section>

<section id="featured-products" class="products-carousel">
    <div class="container-lg overflow-hidden py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header d-flex flex-wrap justify-content-between my-4">
                    <h2 class="section-title">Featured products</h2>
                    <div class="d-flex align-items-center">
                        <a href="products.php" class="btn btn-secondary me-2">View All</a>
                        <div class="swiper-buttons">
                            <button class="swiper-prev products-carousel-prev btn btn-secondary">❮</button>
                            <button class="swiper-next products-carousel-next btn btn-secondary">❯</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($featureProducts as $product): ?>
                            <div class="product-item position-relative swiper-slide">
                                <a href="product/<?= htmlspecialchars($product['slug']); ?>-<?= urlencode($product['id']); ?>"
                                    title="<?= htmlspecialchars($product['name']); ?>">
                                    <figure class="position-relative">

                                        <img src="<?= htmlspecialchars($product['image_url']); ?>"
                                            alt="<?= htmlspecialchars($product['name']); ?>" class="tab-image">
                                        <!-- Out of Stock Overlay -->
                                        <?php if ($product['quantity'] == 0): ?>
                                            <div class="out-of-stock-overlay">
                                                <img src="assets/images/sold_out.png" alt="Out of Stock"
                                                    class="out-of-stock-img">
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
                                        <span
                                            class="text-dark fw-semibold">₹<?= number_format($product['price'], 2); ?></span>
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
                                                    class="btn btn-primary rounded-1 p-2 fs-7 btn-cart add-to-cart-btn  <?= $product['quantity'] == 0 ? 'disabled' : ''; ?>"
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
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const productsSwiper = new Swiper('.products-carousel .swiper', {
                                    slidesPerView: 5, // Adjust the number of slides to display
                                    spaceBetween: 20, // Space between slides in pixels
                                    navigation: {
                                        nextEl: '.products-carousel-next', // Match your button class
                                        prevEl: '.products-carousel-prev', // Match your button class
                                    },
                                    loop: true, // Enable infinite looping
                                    breakpoints: {
                                        320: { slidesPerView: 1, spaceBetween: 10 },
                                        768: { slidesPerView: 2, spaceBetween: 15 },
                                        1024: { slidesPerView: 5, spaceBetween: 20 },
                                    },
                                });
                            });
                            document.addEventListener("DOMContentLoaded", function () {
                                document.querySelectorAll(".wishlist-btn").forEach(function (btn) {
                                    btn.addEventListener("click", function (e) {
                                        e.preventDefault();
                                        let productId = this.getAttribute("data-product-id");
                                        let heartIcon = this

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
                        </script>

                    </div>
                </div>
                <!-- / products-carousel -->
            </div>
        </div>
    </div>
</section>


<?php
// Banner details
$bannerDetails = [
    'title' => 'Get 25% Discount on your first purchase',
    'description' => 'Just Sign Up & Register it now to become a member.',
    'background_image' => 'assets/images/chu-gummies-qRQowcPy4Cs-unsplash.jpg'
];
?>

<section>
    <div class="container-lg">
        <div class="bg-secondary text-light py-5 my-5"
            style="background: url('<?= htmlspecialchars($bannerDetails['background_image']) ?>') no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5 p-3">
                        <div class="section-header">
                            <h2 class="section-title display-5 text-light">
                                <?= htmlspecialchars($bannerDetails['title']) ?>
                            </h2>
                        </div>
                        <p><?= htmlspecialchars($bannerDetails['description']) ?></p>
                    </div>
                    <div class="col-md-5 p-5">

                        <div class="d-grid gap-2">
                            <a href="login.php" type="submit" class="btn btn-secondary btn-md rounded-0">Login</a>

                            <a type="login.php" class="btn btn-primary btn-md rounded-0">Signup</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
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
    });
</script>