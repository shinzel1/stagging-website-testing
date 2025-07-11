<?php include 'includes/header.php'; ?>
<?php
$is_logged_in = isset($_SESSION['user_id']);
require_once("config/database_connection.php");
if (!isset($_SESSION['user_id'])) {
    $isAdmin = null;
} else {
    // Check if the user is an admin
    $isAdmin = $_SESSION['role'] === 'admin';
}
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xontact</title>
  <meta name="description" content="Contact Us">
  <meta name="keywords" content="Keywords">
  <!-- <meta name="robots" content="index, follow"> -->
  <link rel="canonical" href="https://nutrizone.in/contact">
</head>

<section class="pt-5 pb-5">
    <div class="contact-section">
        <div class="container pt-5">
            <div class="row">
                <!-- Contact Form -->
                <div class="col contact-form col-md-8 ">
                    <form id="queryForm" method="post" action="" role="form">

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

                        <div id="cf-submit">
                            <input type="submit" id="contact-submit" class="btn btn-primary" value="Submit">
                        </div>

                    </form>
                    <div id="queryResponse"></div>
                    <script>
                    $(document).ready(function () {
                        $("#queryForm").submit(function (e) {
                            e.preventDefault();

                            $.ajax({
                                url: 'includes/submit_query.php',
                                type: 'POST',
                                data: $(this).serialize(),
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        $("#queryResponse").html('<div class="alert">Query submitted successfully!</div>');
                                        $("#queryForm")[0].reset();
                                    } else {
                                        $("#queryResponse").html('<div class="alert alert-danger">' + response.error + '</div>');
                                    }
                                }
                            });
                        });
                    });
                </script>
                </div>

                <!-- Contact Details -->
                <div class="col contact-details col-md-4">
                    <div class="google-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d17646.718720933626!2d77.11561063137842!3d28.643170282751633!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03bf4f1c6a75%3A0x6970d3c7e363b171!2sNutrizone%20%7C%20Supplement%20Store%20in%20Delhi!5e0!3m2!1sen!2sin!4v1742180658724!5m2!1sen!2sin"
                            width="auto" height="auto" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <ul class="contact-short-info">
                        <li>
                            <i class="tf-ion-ios-home"></i>
                            <span>Shop No4, Ground Floor, J13/1, Rajouri Garden, New Delhi, Delhi 110027</span>
                        </li>
                        <li>
                            <i class="tf-ion-android-phone-portrait"></i>
                            <span>Phone: +919891289789</span>
                        </li>
                        <li>
                            <i class="tf-ion-android-mail"></i>
                            <span>Email: nutrizone@gmail.com</span>
                        </li>
                    </ul>
                    <!-- Footer Social Links -->
                    <div class="social-icon">
                        <ul>
                            <!-- <li><a class="fb-icon" href="https://www.facebook.com/themefisher"><i
                                        class="fa-brands fa-amazon"></i></a></li> -->
                            <li><a href="https://www.instagram.com/nutrizone.delhi" rel="noopener noreferrer"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <!-- <li><a href="https://themefisher.com/"><i class="fa-brands fa-x-twitter"></i></i></a>
                            </li> -->
                            <li><a href="https://www.facebook.com/nutrizonecompletenutritionstore" target="_blank" rel="noopener noreferrer"> <i class="fa-brands fa-facebook"></i></a>
                            </li>
                            <!-- <li><a href="https://themefisher.com/"><i class="fa-brands fa-pinterest"></i></a>
                            </li> -->
                        </ul>
                    </div>
                    <!--/. End Footer Social Links -->
                </div>

            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>

</html>