<?php include 'includes/header.php'; ?>
<div class="container pt-5">
    <section class="about section">
        <div class="container">
            <div class="row pt-5">

                <div class="col-md-6">
                    <h1 class="mt-40">About Us</h1>
                    <p>Welcome to Nutrizone, your ultimate destination for premium gym supplements and fitness
                        solutions. We are more than just a store\u2014we are a community dedicated to helping you
                        achieve your health and fitness goals with the best tools, knowledge, and support available.</p>
                </div>
                <div class="col-md-6">
                    <img class="img-fluid rounded" src="assets/images/andrey-khoviakov-wheRF9s3VtQ-unsplash.jpg">
                </div>
            </div>
            <!-- <div class="row p-5">
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png">
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png">
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png">
                </div>
                <div class="col-md-3 text-center">
                    <img src="assets/images/awards-logo.png">
                </div>
            </div> -->
        </div>
    </section>


    <section class="pt-5">
        <div class="row">
            <div class="col">
                <div class="card text-white">
                    <img src="assets\images\chu-gummies-yBcXqNYJgsk-unsplash.jpg" class="card-img" alt="...">
                    <div class="card-img-overlay">
                        <h2 class="card-title">Our Mission</h2>
                        <p class="card-text">At Nutrizone, our mission is simple: to empower every individual to unlock
                            their full potential. We aim to
                            provide high-quality supplements that not only meet but exceed industry standards, ensuring
                            that you fuel your
                            body with only the best.</p>
                        <!-- <p class="card-text">Last updated 3 mins ago</p> -->
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="assets\images\chu-gummies-yBcXqNYJgsk-unsplash.jpg" class="card-img" alt="...">
                    <div class="card-img-overlay">
                        <h2 class="card-title">Who We Are</h2>
                        <p>Founded in [Year], [Your Brand Name] was born out of a passion for fitness and a commitment
                            to excellence. Our
                            team comprises fitness enthusiasts, nutritionists, and industry experts who understand the
                            challenges and
                            triumphs of a fitness journey.</p>
                    </div>
                </div>
            </div>
        </div>

    </section>
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
                        <div class="col-md-5 p-3">
                            <form method="POST" action="submit.php">
                                <div class="mb-3">
                                    <label for="name" class="form-label d-none">Name</label>
                                    <input type="text" class="form-control form-control-md rounded-0" name="name"
                                        id="name" placeholder="Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label d-none">Email</label>
                                    <input type="email" class="form-control form-control-md rounded-0" name="email"
                                        id="email" placeholder="Email Address" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark btn-md rounded-0">Submit</button>
                                </div>
                            </form>
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
