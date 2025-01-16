<section
    style="background-image: url('assets/images/meghan-holmes-buWcS7G1_28-unsplash.jpg'); background-repeat: no-repeat; background-size: cover;">
    <div class="container-lg">
        <div class="row">
            <div class="col-lg-6 pt-5 mt-5">
                <h2 class="display-1 ls-1">
                    <span class="fw-bold" style="color:#da2d1c">Fuel Your Strength</span> <span style="color:white"> –
                        Premium Supplements for Every Workout. </span>

                </h2>
                <!-- <p class="fs-4">Complete Nurition Store</p> -->
                <div class="d-flex gap-3">
                    <a href="#" class="btn text-uppercase fs-6 rounded-pill px-4 py-3 mt-3"
                        style="background-color:#3c2c93;color:white">Start
                        Shopping</a>
                    <a href="#" class="btn btn-dark text-uppercase fs-6 rounded-pill px-4 py-3 mt-3">Join Now</a>
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
                            <div class="row text-dark">
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
                ['bg' => 'primary', 'icon' => '#fresh', 'title' => 'Eco-Friendly', 'text' => 'Use biodegradable or recyclable packaging and clearly state it'],
                ['bg' => 'secondary', 'icon' => '#organic', 'title' => 'Natural Ingredients', 'text' => 'Our protein powders are sourced from grass-fed cows'],
                ['bg' => 'danger', 'icon' => '#delivery', 'title' => 'Fast Shipping', 'text' => 'Offers shipping in new Delhi and NCR regions']
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
                        <a href="#" class="btn btn-primary me-2">View All</a>
                        <div class="swiper-buttons">
                            <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                            <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
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
                        // Define categories as an array
                        $categories = [
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Whey protein'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Isolate'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Plant protein'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Preworkout'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'BCAA'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'EAA'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Weight gainers'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Mass gainers'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Fat burner'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'L carnitine'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Creatine'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'L arginine'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Glutamine'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Protein bars / Nutrition bars'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Citrulline'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Testosterone'],
                            ['image' => 'assets/images/scoop.jpg', 'title' => 'Peanut butter'],
                        ];
                        ;

                        // Loop through each category to generate the slides
                        foreach ($categories as $category): ?>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?= $category['image']; ?>" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title"><?= $category['title']; ?></h4>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

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
                        <a href="all-products.php" class="btn btn-primary rounded-1">View All</a>
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
                    $products = [
                        [
                            "title" => "MuscleTech NitroTech Performance Series",
                            "image" => "assets/images/products/nitro-tech-ripped-chocolate-fudge-brownie-1-8kg-1.png",
                            "price" => 18.00,
                            "original_price" => 24.00,
                            "rating" => 4.5,
                            "reviews" => 222
                        ],
                        [
                            "title" => "Pol nutrition",
                            "image" => "assets/images/products/pole-whey-protein.png",
                            "price" => 50.00,
                            "original_price" => 54.00,
                            "rating" => 4.5,
                            "reviews" => 41
                        ],
                        [
                            "title" => "GNC Pro Performance Creatine Monohydrate",
                            "image" => "assets/images/products/gnc-creatine.jpg",
                            "price" => 12.00,
                            "original_price" => 14.00,
                            "rating" => 4.5,
                            "reviews" => 32
                        ],
                        [
                            "title" => "Dymatize ISO100 Hydrolyzed - 100% Whey Protein Isolate",
                            "image" => "assets/images/products/pole-whey-protein.png",
                            "price" => 18.00,
                            "original_price" => 24.00,
                            "rating" => 4.5,
                            "reviews" => 222
                        ],
                        [
                            "title" => "One Science",
                            "image" => "assets/images/products/pole-whey-protein.png",
                            "price" => 18.00,
                            "original_price" => 24.00,
                            "rating" => 4.5,
                            "reviews" => 222
                        ]
                    ];

                    // Loop through products and display them
                    foreach ($products as $product) {
                        ?>
                        <div class="col">
                            <div class="product-item">
                                <figure>
                                    <a href="product-details.php?title=<?= urlencode($product['title']); ?>"
                                        title="<?= htmlspecialchars($product['title']); ?>">
                                        <img src="<?= htmlspecialchars($product['image']); ?>"
                                            alt="<?= htmlspecialchars($product['title']); ?>" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal"><?= htmlspecialchars($product['title']); ?></h3>
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
                                        <del>$<?= number_format($product['original_price'], 2); ?></del>
                                        <span
                                            class="text-dark fw-semibold">$<?= number_format($product['price'], 2); ?></span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                            <?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>%
                                            OFF
                                        </span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3">
                                                <input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"
                                                    min="1">
                                            </div>
                                            <div class="col-7">
                                                <a href="add-to-cart.php?title=<?= urlencode($product['title']); ?>"
                                                    class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart
                                                </a>
                                            </div>
                                            <div class="col-2">
                                                <a href="wishlist.php?title=<?= urlencode($product['title']); ?>"
                                                    class="btn btn-outline-dark rounded-1 p-2 fs-6">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg>
                                                </a>
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
                    $banners = [
                        [
                            "title" => "Items on SALE",
                            "description" => "Discounts up to 30%",
                            "image" => "assets/images/comp/whey-gnc-pol-muscle-punch.jpeg",
                            "link" => "#",
                            "bg_class" => "bg-info",
                            "block_class" => "block-1"
                        ],
                        [
                            "title" => "Combo offers",
                            "description" => "Discounts up to 50%",
                            "image" => "assets/images/comp/oats.jpeg",
                            "link" => "#",
                            "bg_class" => "bg-success-subtle",
                            "block_class" => "block-2"
                        ],
                        [
                            "title" => "Discount Coupons",
                            "description" => "Discounts up to 40%",
                            "image" => "assets/images/comp/aleksander-saks-lVZGEyL_j40-unsplash.jpg",
                            "link" => "#",
                            "bg_class" => "bg-danger",
                            "block_class" => "block-3"
                        ]
                    ];

                    // Loop through banners and display them
                    foreach ($banners as $banner) {
                        ?>
                        <div class="banner-ad d-flex align-items-center <?= htmlspecialchars($banner['bg_class']); ?> <?= htmlspecialchars($banner['block_class']); ?>"
                            style="background: url('<?= htmlspecialchars($banner['image']); ?>') no-repeat; background-size: cover;">
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


<?php
// Array of products
$products = [
    [
        "title" => "MuscleTech NitroTech Performance Series",
        "image" => "assets/images/products/nitro-tech-ripped-chocolate-fudge-brownie-1-8kg-1.png",
        "price" => 18.00,
        "original_price" => 24.00,
        'discounted_price' => 18.00,
        'discount' => '10% OFF',
        "rating" => 4.5,
        "reviews" => 222
    ],
    [
        "title" => "Pol nutrition",
        "image" => "assets/images/products/pole-whey-protein.png",
        "price" => 50.00,
        "original_price" => 54.00,
        'discounted_price' => 18.00,
        'discount' => '10% OFF',
        "rating" => 4.5,
        "reviews" => 41
    ],
    [
        "title" => "GNC Pro Performance Creatine Monohydrate",
        "image" => "assets/images/products/gnc-creatine.jpg",
        "price" => 12.00,
        "original_price" => 14.00,
        'discounted_price' => 18.00,
        'discount' => '10% OFF',
        "rating" => 4.5,
        "reviews" => 32
    ],
    [
        "title" => "Dymatize ISO100 Hydrolyzed - 100% Whey Protein Isolate",
        "image" => "assets/images/products/pole-whey-protein.png",
        "price" => 18.00,
        "original_price" => 24.00,
        'discounted_price' => 18.00,
        'discount' => '10% OFF',
        "rating" => 4.5,
        "reviews" => 222
    ],
    [
        "title" => "One Science",
        "image" => "assets/images/products/pole-whey-protein.png",
        "price" => 18.00,
        "original_price" => 24.00,
        'discounted_price' => 18.00,
        'discount' => '10% OFF',
        "rating" => 4.5,
        "reviews" => 222
    ]
];
?>

<section id="featured-products" class="products-carousel">
    <div class="container-lg overflow-hidden py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header d-flex flex-wrap justify-content-between my-4">
                    <h2 class="section-title">Featured products</h2>
                    <div class="d-flex align-items-center">
                        <a href="#" class="btn btn-primary me-2">View All</a>
                        <div class="swiper-buttons">
                            <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                            <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($products as $product): ?>
                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="<?= htmlspecialchars($product['title']) ?>">
                                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Thumbnail"
                                            class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal"><?= htmlspecialchars($product['title']) ?></h3>
                                    <div>
                                        <span class="rating">
                                            <?php
                                            $fullStars = floor($product['rating']);
                                            $halfStar = $product['rating'] - $fullStars > 0 ? 1 : 0;
                                            for ($i = 0; $i < $fullStars; $i++): ?>
                                                <svg width="18" height="18" class="text-warning">
                                                    <use xlink:href="#star-full"></use>
                                                </svg>
                                            <?php endfor;
                                            if ($halfStar): ?>
                                                <svg width="18" height="18" class="text-warning">
                                                    <use xlink:href="#star-half"></use>
                                                </svg>
                                            <?php endif; ?>
                                        </span>
                                        <span>(<?= htmlspecialchars($product['reviews']) ?>)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$<?= number_format($product['original_price'], 2) ?></del>
                                        <span
                                            class="text-dark fw-semibold">$<?= number_format($product['discounted_price'], 2) ?></span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">
                                            <?= htmlspecialchars($product['discount']) ?>
                                        </span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3">
                                                <input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1">
                                            </div>
                                            <div class="col-7">
                                                <a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart
                                                </a>
                                            </div>
                                            <div class="col-2">
                                                <a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6">
                                                    <svg width="18" height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg>
                                                </a>
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
                                        1024: { slidesPerView: 4, spaceBetween: 20 },
                                    },
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
                    <div class="col-md-5 p-3">
                        <form method="POST" action="submit.php">
                            <div class="mb-3">
                                <label for="name" class="form-label d-none">Name</label>
                                <input type="text" class="form-control form-control-md rounded-0" name="name" id="name"
                                    placeholder="Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label d-none">Email</label>
                                <input type="email" class="form-control form-control-md rounded-0" name="email"
                                    id="email" placeholder="Email Address" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-md rounded-0">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>