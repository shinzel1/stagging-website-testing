<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/products.css">

<? $is_logged_in = isset($_SESSION['user_id']);
// Database configuration
require_once("config/database_connection.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $isAdmin = null;
} else {
    // Check if the user is an admin
    $isAdmin = $_SESSION['role'] === 'admin';
}

?>
<div class="container p-5 mt-5">
    <h2>My Wishlist</h2>
    <div id="wishlist-items" class="row">
        <!-- Wishlist products will be loaded here via AJAX -->
    </div>
</div>

<script>
    function fetchWishlist() {
    $.ajax({
        url: 'includes/fetch_wishlist.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            let resultsHtml = '';
            if (response.success) {
                let products = response.products;

                if (products.length > 0) {
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
                        resultsHtml += `
                        <div class="col">
                            <div class="product-item position-relative">
                                <a href="product.php?id=${encodeURIComponent(product.id)}" title="${product.name}">
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
                                                    </svg> Move to Cart
                                                </span>
                                            </div>
                                            <div class="col-2">
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

                // ✅ Append generated HTML to the wishlist container
                $('#wishlist-items').html(resultsHtml);
            } else {
                $('#wishlist-items').html('<p>Error loading wishlist.</p>');
            }
        },
        error: function () {
            $('#wishlist-items').html('<p>Failed to load wishlist.</p>');
        }
    });
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
    });



    $(document).on('click', '.remove-wishlist', function () {
        let productId = $(this).data("product-id");
        $.ajax({
            url: 'includes/wishlist_toggle.php',
            method: 'POST',
            data: { product_id: productId, action: "remove" },
            success: function () {
                fetchWishlist(); // Reload wishlist after removal
            }
        });
    });

    // Load wishlist on page load
    fetchWishlist();
</script>

<?php include 'includes/footer.php'; ?>