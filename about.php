<?php include 'includes/header.php'; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <meta name="description" content="About">
  <meta name="keywords" content="keywords">
  <link rel="canonical" href="https://nutrizone.com/about">
</head>
<div class="container pt-5">
    <section class="about section pt-5">
        <div class="">
            <div class="row">
                <div class="col-md-6">
                    <img class="img-fluid rounded" src="assets/images/andrey-khoviakov-wheRF9s3VtQ-unsplash.jpg" />
                </div>
                <div class="col-md-6">
                    <h2 class="mt-40">About Us</h2>
                    <p>Welcome to Nutrizone, your ultimate destination for premium gym supplements and fitness
                        solutions. We are more than just a store\u2014we are a community dedicated to helping you
                        achieve your health and fitness goals with the best tools, knowledge, and support available.</p>
                    <a href="contact.php" class="btn btn-main mt-20">Download Company Profile</a>
                </div>
            </div>
            <div class="row pt-5">
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png" />
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png" />
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png" />
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png" />
                </div>
            </div>
        </div>
    </section>
    <!-- <section class="team-members pt-5">
        <div class="container">
            <div class="row">
                <div class="title">
                    <h2>Team Members</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="team-member text-center">
                        <img class="img-circle" src="assets/images/team/team-1.jpg"/>
                        <h4>Jonathon Andrew</h4>
                        <p>Founder</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-member text-center">
                        <img class="img-circle" src="assets/images/team/team-2.jpg"/>
                        <h4>Adipisci Velit</h4>
                        <p>Developer</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-member text-center">
                        <img class="img-circle" src="assets/images/team/team-3.jpg"/>
                        <h4>John Fexit</h4>
                        <p>Shop Manager</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-member text-center">
                        <img class="img-circle" src="assets/images/team/team-1.jpg"/>
                        <h4>John Fexit</h4>
                        <p>Shop Manager</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- <section class="pt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white">
                    <img src="assets\images\chu-gummies-yBcXqNYJgsk-unsplash.jpg" class="card-img" alt="...">
                    <div class="card-img-overlay">
                        <h2 class="card-title">Our Mission</h2>
                        <p class="card-text">At Nutrizone, our mission is simple: to empower every individual to unlock
                            their full potential. We aim to
                            provide high-quality supplements that not only meet but exceed industry standards, ensuring
                            that you fuel your
                            body with only the best.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white">
                    <img src="assets\images\chu-gummies-yBcXqNYJgsk-unsplash.jpg" class="card-img" alt="...">
                    <div class="card-img-overlay">
                        <h2 class="card-title">Who We Are</h2>
                        <p>Founded in [Year], Nutrizone was born out of a passion for fitness and a commitment
                            to excellence. Our team comprises fitness enthusiasts, nutritionists, and industry experts who understand the
                            challenges and triumphs of a fitness journey.</p>
                    </div>
                </div>
            </div>
        </div>
        

    </section> -->
    <section class="pt-5">
        <h2>Why Choose Us?</h2>
        <ul>
            <li>
                <h5>Wide Range of Products</h5>
                <p>Whether you're looking to build muscle, boost endurance, or support recovery, we have a supplement
                    for every
                    goal. Explore our range of protein powders, pre-workouts, fat burners, vitamins, and more.</p>
            </li>
            <li>
                <h5>Transparency</h5>
                <p>We believe in honesty and openness. Thats why every product comes with a clear ingredient list and
                    detailed
                    nutritional information, so you know exactly what you're putting into your body.</p>
            </li>
            <li>
                <h5>Expert Guidance</h5>
                <p>Not sure where to start? Our team is here to help! From personalized product recommendations to
                    fitness tips,
                    we
                    are committed to supporting your journey every step of the way.</p>
            </li>
            <li>
                <h5>Community-Centered Approach</h5>
                <p>We are not just about selling products; were about building a community. Join our fitness family and
                    connect with like-minded individuals who inspire and motivate each other to be the best version of
                    themselves.
                </p>
            </li>
        </ul>
    </section>
    <!-- <h2>Our Promise</h2>

    <p>At Nutri zone, your health and satisfaction are our top priorities. We promise to:</p>

    <ul>
        <li>Deliver top-tier products that help you achieve your fitness goals.</li>
        <li>Continuously innovate to bring you the latest in fitness nutrition.</li>
        <li>Provide exceptional customer service to ensure a seamless shopping experience.</li>
    </ul> -->

    <?php
    // Banner details
    $bannerDetails = [
        'title' => 'Join Us Today',
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

    <!-- <h2>Join Us Today</h2>

    <p>Are you ready to take your fitness journey to the next level? Explore our products, read our blog for expert
        advice, and connect with us on social media. Together, lets build a stronger, healthier, and more
        confident you.</p>

    <p>Thank you for choosing [Your Brand Name]. Lets fuel your greatness!</p> -->
</div>

<?php include 'includes/footer.php'; ?>