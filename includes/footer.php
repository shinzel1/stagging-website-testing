<footer class="py-5" style="box-shadow: -1px -7px 15px -4px rgba(0,0,0,0.76);">
    <div class="container-lg">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-menu">
                    <img src="assets/images/logo.jpeg" width="100%" alt="logo">
                    <div class="social-links mt-3" >
                        <ul class="d-flex list-unstyled gap-2" style="justify-content: center;" >
                            <li>
                                <a href="https://www.facebook.com/nutrizonecompletenutritionstore" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light" >
                                    <svg width="16" height="16">
                                        <use xlink:href="#facebook"></use>
                                    </svg>
                                </a>
                            </li>
                            <!-- <li>
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
                            </li> -->
                            <li>
                                <a href="https://www.instagram.com/nutrizone.delhi" class="btn btn-outline-light"  target="_blank" rel="noopener noreferrer">
                                    <svg width="16" height="16">
                                        <use xlink:href="#instagram"></use>
                                    </svg>
                                </a>
                            </li>
                            <!-- <li>
                                <a href="#" class="btn btn-outline-light"  target="_blank" rel="noopener noreferrer">
                                    <svg width="16" height="16">
                                        <use xlink:href="#amazon"></use>
                                    </svg>
                                </a>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <!-- <div class="footer-menu">
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
                </div> -->
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Nutri Zone</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="/" class="nav-link">Home</a>
                        </li>
                        <li class="menu-item">
                            <a href="products" class="nav-link">Featured Products</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="menu-item">
                        <a href="cart.php" class="nav-link">Cart</a>
                    </li>
                    <?php }?>
                        
                        <!-- <li class="menu-item">
                            <a href="/about.php" class="nav-link">About us</a>
                        </li> -->
                        <li class="menu-item">
                            <a href="contact.php" class="nav-link">Contact us </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Customer Service</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="contact.php" class="nav-link">Contact</a>
                        </li>
                        <li class="menu-item">
                            <a href="privacy-policy.php" class="nav-link">Privacy Policy</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Subscribe Us</h5>
                    <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                    <form id="newsletterForm" class="d-flex mt-3 gap-0">
                        <input id="newsletterEmail" class="form-control rounded-start rounded-0 bg-light" type="email"
                            placeholder="Email Address" required>
                        <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
                    </form>
                    <div id="newsletterMessage" class="mt-2 text-small"></div>
                </div>
            </div>


        </div>
    </div>
</footer>
<?php require_once("footer_scripts.php"); ?>
