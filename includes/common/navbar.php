<style>
    .badge {
        padding-left: 9px !important;
        padding-right: 9px !important;
        -webkit-border-radius: 9px !important;
        -moz-border-radius: 9px !important;
        border-radius: 9px !important;
    }

    .label-warning[href],
    .badge-warning[href] {
        background-color: #c67605;
    }

    #lblCartCount {
        font-size: 14px;
        background: rgb(214, 35, 35);
        color: #fff;
        padding: 0 5px !important;
        vertical-align: top !important;
        margin-left: -6px !important;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.jpeg" alt="logo" class="img-fluid" style="height:40px">
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
            aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarResponsive">

            <!-- Search Bar -->
            <div class="mx-auto my-2 my-lg-0 w-100 px-3">
                <form id="search-form" class="d-flex">
                    <input id="search-input" type="text" class="form-control search-border"
                        placeholder="Search for more than 20,000 products" autocomplete="off">
                </form>
                <div id="search-results" class="bg-white border rounded mt-2 d-none search-results"></div>
            </div>

            <!-- Nav Links -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-bold text-uppercase text-dark align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active' : ''; ?>"
                        href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>"
                        href="contact.php">Contact</a>
                </li>

                <?php if ($is_logged_in): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a href="admin_dashboard.php" class="nav-link">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="cart.php" class="nav-link position-relative">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="position-absolute start-100 translate-middle rounded-pill badge badge-warning"
                                    id="lblCartCount"></span>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-user"></i> User
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="user-profile.php"><i class="fa fa-user"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fa-solid fa-check"></i> Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fa fa-heart"></i> Wishlist</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout.php"><i
                                            class="fa-solid fa-arrow-right-from-bracket"></i>
                                        Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"> Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="p-3"></div>
<style>
    .search-results {
        position: absolute;
        z-index: 2;
        font-size: medium;
    }

    .search-border {
        --bs-border-opacity: 1;
        border-color: rgba(0, 0, 0, 0.76)
    }

    .navbar {
        background-color: white;
        box-shadow: -1px 7px 15px -4px rgba(0, 0, 0, 0.76);

    }

    .navbar-nav {
        @media (max-width: 1000px) {
            text-align: center;
        }
    }

    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
    }

    .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
    }

    .bi {
        vertical-align: -.125em;
        fill: currentColor;
    }

    .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
    }

    .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
    }

    .bd-mode-toggle {
        z-index: 1500;
    }

    .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        const categorySelect = document.getElementById('category');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim();
            const category = categorySelect.value;

            if (query.length > 2) {
                // Show loader or results div
                searchResults.classList.remove('d-none');
                searchResults.innerHTML = 'Searching...';


                $.ajax({
                    url: 'includes/fetch_products.php',
                    method: 'POST',
                    data: { searchTerm: query },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success && response.products.length > 0) {
                            // Render product results
                            const resultsHtml = response.products
                                .map(product => `
                <div class="p-2">
                  <a href="product/${product.slug}-${product.id}" class="d-block text-dark">
                  <img src="${product.image_url}" alt="${product.name}" style="width: 50px; height: 50px;"/>
                    ${product.name?.substring(0, 100)}
                  </a>
                </div>
              `)
                                .join('');
                            searchResults.innerHTML = resultsHtml;
                        } else {
                            searchResults.innerHTML = '<div class="p-2 text-muted">Press Enter to search ' + query + '</div>';
                        }
                    },
                    error: function () {
                        searchResults.innerHTML = '<div class="p-2 text-danger">Error loading results</div>';
                        console.error(err);
                    }
                });
            } else {
                // Hide results if input is cleared or too short
                searchResults.classList.add('d-none');
            }
        });
    });

    function updateCartCount() {
        $.ajax({
            url: 'includes/fetch_cart_count.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $("#lblCartCount").text(response.count);
                } else {
                    $("#lblCartCount").text(0);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching cart count:", error);
                $("#lblCartCount").text(0);
            }
        });
    }

    // Call the function when the page loads
    $(document).ready(function () {
        updateCartCount();
    });

</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/brands.min.css">
<a href="https://www.instagram.com/nutrizone.delhi" class="float instagram-icon" target="_blank">
    <i class="fa-brands fa-instagram my-float"></i>
</a>
<a href="https://www.facebook.com/nutrizonecompletenutritionstore" class="float facebook-icon" target="_blank">
    <i class="fa-brands fa-facebook my-float"></i>
</a>
<a href="https://api.whatsapp.com/send?phone=+919891289789&text=Hola%21%20Welcome%20to%20NutriZone%2C%20connect%20us%20via%20WhatsApp%2E"
    class="float whatsapp-icon" target="_blank">
    <i class="fa-brands fa-whatsapp my-float"></i>
</a>

<style>
    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
    }

    .whatsapp-icon {
        bottom: 40px;
        right: 1rem;
        color: #FFF;
        background-color: #25d366;

    }

    .facebook-icon {
        bottom: 107px;
        right: 1rem;
        color: #1877F2;
        background-color: #fff;
    }

    .instagram-icon {
        bottom: 170px;
        right: 1rem;
        background-color: #fff;

    }

    .my-float {
        margin-top: 16px;
    }
</style>