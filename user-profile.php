<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to index.php
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
require_once("config/database_connection.php");

$address = [];

if ($user_id) {
    // Fetch user data
    $stmt = $pdo->prepare("SELECT username, email, profile_image, phone, gender, date_of_birth, bio, social_links FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND address_type = 'billing'");
        $stmt->execute([$user_id]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($addresses)) {
            $address = $addresses[0];
        }
    } catch (PDOException $e) {
        die("Error fetching addresses: " . $e->getMessage());
    }
}
?>

<div class="container pt-5">
    <div class="container my-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab"
                    aria-controls="profile" aria-selected="true">Profile</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab"
                    aria-controls="home" aria-selected="false">Address</a>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="false">Profile</a>
            </li> -->
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button"
                    role="tab" aria-controls="contact" aria-selected="false">Query</a>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row">
                    <!-- Profile Image Section -->
                    <div class="col-md-4 text-center">
                        <?php
                        $defaultAvatar = 'assets/images/default-avatar.png';
                        $profileImage = isset($user['profile_image']) && !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : $defaultAvatar;
                        ?>
                        <img id="profileImage" src="<?= $profileImage ?>" class="rounded-circle" width="150">

                        <form id="uploadProfileForm" enctype="multipart/form-data" class="mt-2">
                            <input type="file" id="profileImageUpload" name="profile_image" style="display: none;">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
                            <button type="button" class="btn btn-primary btn-sm"
                                onclick="$('#profileImageUpload').click()">Change Photo</button>
                        </form>
                    </div>

                    <!-- User Information Form -->
                    <div class="col-md-8">
                        <form id="updateProfileForm">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
                            <div class="mb-3">
                                <p>Current loyalty Points : <?= htmlspecialchars($user['loyalty_points'] ?? '0'); ?></p>
                            </div>
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username"
                                    value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email"
                                    value="<?= htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone"
                                    value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label>Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="Male" <?= (isset($user['gender']) && $user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?= (isset($user['gender']) && $user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?= (isset($user['gender']) && $user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth"
                                    value="<?= htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label>Bio</label>
                                <textarea class="form-control"
                                    name="bio"><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <form method="POST" id="billingForm">
                                    <input type="hidden" name="csrf_token" value="<?= bin2hex(random_bytes(32)); ?>">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_name">Name</label>
                                            <input type="text" class="form-control form-control-sm" id="billing_name"
                                                name="billing_name"
                                                value="<?= htmlspecialchars($addresses[0]['billing_name'] ?? "") ?>"
                                                required />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_contact">Contact Number</label>
                                            <input type="tel" class="form-control form-control-sm" id="billing_contact"
                                                name="billing_contact" required pattern="\d{10}"
                                                value="<?= htmlspecialchars($addresses[0]['billing_contact'] ?? "") ?>" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address_line_1">Address Line 1</label>
                                        <input type="text" class="form-control form-control-sm" id="address_line_1"
                                            name="address_line_1"
                                            value="<?= htmlspecialchars($addresses[0]['address_line_1'] ?? "") ?>"
                                            required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="address_line_2">Address Line 2 (Optional)</label>
                                        <input type="text" class="form-control form-control-sm" id="address_line_2"
                                            name="address_line_2"
                                            value="<?= htmlspecialchars($addresses[0]['address_line_2'] ?? "") ?>" />
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control form-control-sm" id="city"
                                                name="city" required
                                                value="<?= htmlspecialchars($addresses[0]['city'] ?? "") ?>" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" class="form-control form-control-sm" id="postal_code"
                                                name="postal_code" required pattern="\d{6}"
                                                value="<?= htmlspecialchars($addresses[0]['postal_code'] ?? "") ?>" />
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control form-control-sm" id="state"
                                                name="state" required
                                                value="<?= htmlspecialchars($addresses[0]['state'] ?? "") ?>" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="country">Country</label>
                                            <input type="text" class="form-control form-control-sm" id="country"
                                                name="country" required
                                                value="<?= htmlspecialchars($addresses[0]['country'] ?? "") ?>" />
                                        </div>
                                    </div>

                                    <div class="form-check d-flex justify-content-center mb-2">
                                        <button class="btn btn-primary" type="submit">
                                            Save Address
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <form id="queryForm">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">

                    <div class="mb-3">
                        <label>Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>

                    <div class="mb-3">
                        <label>Message</label>
                        <textarea class="form-control" name="message" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Query</button>
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
                                        $("#queryResponse").html('<div class="alert alert-success">Query submitted successfully!</div>');
                                        $("#queryForm")[0].reset();
                                    } else {
                                        $("#queryResponse").html('<div class="alert alert-danger">' + response.error + '</div>');
                                    }
                                }
                            });
                        });
                    });
                </script>
                <div class="pt-5">
                    <h2>FAQ</h2>
                    <div class="accordion " id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Accordion Item #1
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the first item's accordion body.</strong> It is shown by default,
                                    until
                                    the collapse plugin adds the appropriate classes that we use to style each element.
                                    These classes control the overall appearance, as well as the showing and hiding via
                                    CSS
                                    transitions. You can modify any of this with custom CSS or overriding our default
                                    variables. It's also worth noting that just about any HTML can go within the
                                    <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Accordion Item #2
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the second item's accordion body.</strong> It is hidden by default,
                                    until the collapse plugin adds the appropriate classes that we use to style each
                                    element. These classes control the overall appearance, as well as the showing and
                                    hiding
                                    via CSS transitions. You can modify any of this with custom CSS or overriding our
                                    default variables. It's also worth noting that just about any HTML can go within the
                                    <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Accordion Item #3
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the third item's accordion body.</strong> It is hidden by default,
                                    until
                                    the collapse plugin adds the appropriate classes that we use to style each element.
                                    These classes control the overall appearance, as well as the showing and hiding via
                                    CSS
                                    transitions. You can modify any of this with custom CSS or overriding our default
                                    variables. It's also worth noting that just about any HTML can go within the
                                    <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#billingForm').on('submit', function (e) {
                e.preventDefault();

                let form = document.getElementById('billingForm');
                let formData = new FormData(form);

                $.ajax({
                    url: 'includes/save_billing.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Address updated successfully!');
                            location.reload();
                        } else {
                            alert('Error updating address');
                        }
                    },
                    error: function () {
                        alert('An error occurred while updating the address.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Update Profile Data
            $("#updateProfileForm").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                    url: "includes/update_profile.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            alert("Profile updated successfully!");
                        } else {
                            alert("Error: " + response.error);
                        }
                    }
                });
            });

            // Upload Profile Image
            $("#profileImageUpload").change(function () {
                if (confirm("Are you sure you want to upload this image?")) {
                    let formData = new FormData($("#uploadProfileForm")[0]);
                    $.ajax({
                        url: "includes/upload_profile_image.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                $("#profileImage").attr("src", response.image_url);
                                alert("Profile image updated successfully!");
                            } else {
                                alert("Error: " + response.error);
                            }
                        }
                    });
                }
            });
        });
    </script>
</div>

<?php include 'includes/footer.php'; ?>