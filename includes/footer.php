<footer class="py-5" style="box-shadow: -1px -7px 15px -4px rgba(0,0,0,0.76);">
    <div class="container-lg">
        <div class="row">

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-menu">
                    <img src="assets/images/logo.jpeg" width="240" alt="logo">
                    <div class="social-links mt-3">
                        <ul class="d-flex list-unstyled gap-2">
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="16" height="16">
                                        <use xlink:href="#facebook"></use>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="16" height="16">
                                        <use xlink:href="#twitter"></use>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="16" height="16">
                                        <use xlink:href="#youtube"></use>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="16" height="16">
                                        <use xlink:href="#instagram"></use>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="16" height="16">
                                        <use xlink:href="#amazon"></use>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Nutri Zone</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="#" class="nav-link">Home</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Featured Products</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Cart</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">About us</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Contact us </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Quick Links</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="#" class="nav-link">Offers</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Discount Coupons</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Stores</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Track Order</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Shop</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Info</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Customer Service</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="#" class="nav-link">FAQ</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Contact</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Privacy Policy</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Returns & Refunds</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Cookie Guidelines</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Delivery Information</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Subscribe Us</h5>
                    <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                    <form class="d-flex mt-3 gap-0" action="index.html">
                        <input class="form-control rounded-start rounded-0 bg-light" type="email"
                            placeholder="Email Address" aria-label="Email Address">
                        <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySwiper = new Swiper('.category-carousel', {
            slidesPerView: 5,
            spaceBetween: 20,
            navigation: {
                nextEl: '.category-carousel-next',
                prevEl: '.category-carousel-prev',
            },
            loop: true,
            breakpoints: {
                320: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 2, spaceBetween: 15 },
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/script.js"></script>