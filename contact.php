<?php include 'includes/header.php'; ?>
<section class="">
    <div class="contact-section">
        <div class="container pt-5">
            <div class="row p-5">
                <!-- Contact Form -->
                <div class="contact-form col-md-6 ">
                    <form id="contact-form" method="post" action="" role="form">

                        <div class="form-group">
                            <input type="text" placeholder="Your Name" class="form-control" name="name" id="name">
                        </div>

                        <div class="form-group">
                            <input type="email" placeholder="Your Email" class="form-control" name="email" id="email">
                        </div>

                        <div class="form-group">
                            <input type="text" placeholder="Subject" class="form-control" name="subject" id="subject">
                        </div>

                        <div class="form-group">
                            <textarea rows="6" placeholder="Message" class="form-control" name="message"
                                id="message"></textarea>
                        </div>

                        <!-- <div id="mail-success" class="success">
                            Thank you. The Mailman is on His Way :
                        </div>

                        <div id="mail-fail" class="error">
                            Sorry, don't know what happened. Try later :
                        </div> -->

                        <div id="cf-submit">
                            <input type="submit" id="contact-submit" class="btn btn-transparent" value="Submit">
                        </div>

                    </form>
                </div>

                <!-- Contact Details -->
                <div class="contact-details col-md-6 ">
                    <div class="google-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d112173.03012636!2d77.12658424806516!3d28.527478163551585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd5b347eb62d%3A0x52c2b7494e204dce!2sNew%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1735839032772!5m2!1sen!2sin"
                            width="450" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <ul class="contact-short-info">
                        <li>
                            <i class="tf-ion-ios-home"></i>
                            <span>Khaja Road, Bayzid, Chittagong, Bangladesh</span>
                        </li>
                        <li>
                            <i class="tf-ion-android-phone-portrait"></i>
                            <span>Phone: +880-31-000-000</span>
                        </li>
                        <li>
                            <i class="tf-ion-android-globe"></i>
                            <span>Fax: +880-31-000-000</span>
                        </li>
                        <li>
                            <i class="tf-ion-android-mail"></i>
                            <span>Email: hello@example.com</span>
                        </li>
                    </ul>
                    <!-- Footer Social Links -->
                    <div class="social-icon">
                        <ul>
                            <li><a class="fb-icon" href="https://www.facebook.com/themefisher"><i
                                        class="fa-brands fa-amazon"></i></a></li>
                            <li><a href="https://www.twitter.com/themefisher"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li><a href="https://themefisher.com/"><i class="fa-brands fa-x-twitter"></i></i></a>
                            </li>
                            <li><a href="https://themefisher.com/"><i class="fa-brands fa-facebook"></i></a>
                            </li>
                            <li><a href="https://themefisher.com/"><i class="fa-brands fa-pinterest"></i></a>
                            </li>
                        </ul>
                    </div>
                    <!--/. End Footer Social Links -->
                </div>
                <!-- / End Contact Details -->



            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>