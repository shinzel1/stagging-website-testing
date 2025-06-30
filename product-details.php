<?php include 'includes/header.php'; ?>
<link href="assets/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }

    #selectedValues {
        margin-top: 20px;
        font-weight: bold;
    }
</style>
<?php
$is_logged_in = isset($_SESSION['user_id']);

require_once("config/database_connection.php");
$product = [];
if (isset($_GET['id'])) {
    $product_Id = $_GET['id'];
    $specifications = null;

    try {
        // Updated query to fetch category names using JOIN
        $stmt = $pdo->prepare("
            SELECT p.*, 
                   GROUP_CONCAT(c.name SEPARATOR ', ') AS categories 
            FROM products p
            LEFT JOIN product_categories pc ON p.id = pc.product_id
            LEFT JOIN categories c ON pc.category_id = c.id
            WHERE p.id = ?
            GROUP BY p.id
        ");

        $stmt->execute([$product_Id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $specifications = json_decode($product['specification'] ?? "");

            // Convert category string to an array (optional if needed)
            $product['categories'] = explode(', ', $product['categories']);
        }
    } catch (PDOException $e) {
        die("Error fetching product details: " . $e->getMessage());
    }
}


$flavour_list = [
    "Vanilla",
    "Chocolate",
    "Cookies and Cream",
    "Caramel Macchiato",
    "Peanut Butter",
    "Mocha",
    "Birthday Cake",
    "Cinnamon Roll",
    "White Chocolate",
    "Strawberry",
    "Banana",
    "Mango",
    "Blueberry",
    "Raspberry",
    "Watermelon",
    "Pineapple",
    "Peach",
    "Apple Cinnamon",
    "Pomegranate",
    "Coconut",
    "Passion Fruit",
    "Dragon Fruit",
    "Lychee",
    "Kiwi",
    "Acai Berry",
    "Lemon",
    "Lime",
    "Orange",
    "Grapefruit",
    "Yuzu",
    "Mint Chocolate",
    "Matcha Green Tea",
    "Chai Latte",
    "Iced Coffee",
    "Honey Lemon",
    "Bubblegum",
    "Blue Raspberry",
    "Green Apple",
    "Grape",
    "Cherry Limeade",
    "Sour Candy",
    "Rainbow Sherbet"
]
    ?>

<style>
    .icon-hover:hover {
        border-color: #3b71ca !important;
        background-color: white !important;
        color: #3b71ca !important;
    }

    .icon-hover:hover i {
        color: #3b71ca !important;
    }

    .editProductForm {
        display: none;
        border-radius: 10px;
        box-shadow: 5px 5px 15px -2px rgba(0, 0, 0, 0.42);
    }

    .min-height-80 {
        min-height: 80%;
    }
</style>
<script>
    function hideDiv() {
        $('.editProductForm').css('display', 'none');
    }
</script>
<style>
    .form-check-input {
        margin-top: .5em !important;
    }
</style>
<!-- content -->
<section class="pt-5">
    <div class="container p-5">
        <div class="row gx-5">
            <aside class="col-lg-6">
                <div class="border rounded-4 mb-3 d-flex justify-content-center position-relative min-height-80">
                    <?php
                    $defaultImage = 'https://via.placeholder.com/150?text=+Add+Image';
                    $imageSrc = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : $defaultImage;
                    ?>
                    <img id="mainImage" style="max-width: 100%; max-height: 100vh; margin: auto;" class="rounded-4 fit"
                        src="<?= $imageSrc ?>" data-img="image_url" />


                    <?php if ($is_logged_in): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <label for="imageUpload" class="position-absolute" style="top: 10px; right: 10px; cursor: pointer">
                                <img src="assets/images/edit_icon.png" width="24" height="24" alt="Edit">
                            </label>
                            <button id="deleteImageBtn" class="btn btn-danger position-absolute"
                                style="top: 10px; left: 10px; display:<?php !empty($product['image_url']) ? "block" : "none" ?>;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>



                </div>

                <form id="imageUploadForm" enctype="multipart/form-data">
                    <input type="file" name="image" id="imageUpload" style="display: none;">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="image_field" id="imageField" value="image_url">
                </form>

                <div class="d-flex justify-content-center mb-3">
                    <img width="60" height="60" class="rounded-2 border mx-1 item-thumb" data-img="image_url"
                        onclick="changeImage(this)" src="<?= $imageSrc ?>" />

                    <?php if (!empty($product['image2'])): ?>
                        <img width="60" height="60" class="rounded-2 border mx-1 item-thumb" data-img="image2"
                            onclick="changeImage(this)"
                            src="<?= !empty($product['image2']) ? htmlspecialchars($product['image2']) : 'assets/images/add.png' ?>" />
                    <?php endif; ?>
                    <?php if (!empty($product['image3']) || ($is_logged_in && ($_SESSION['role'] == 'admin'))): ?>
                        <img width="60" height="60" class="rounded-2 border mx-1 item-thumb" data-img="image3"
                            onclick="changeImage(this)"
                            src="<?= !empty($product['image3']) ? htmlspecialchars($product['image3']) : 'assets/images/add.png' ?>" />
                    <?php endif; ?>
                    <?php if (!empty($product['image4']) || ($is_logged_in && ($_SESSION['role'] == 'admin'))): ?>
                        <img width="60" height="60" class="rounded-2 border mx-1 item-thumb" data-img="image4"
                            onclick="changeImage(this)"
                            src="<?= !empty($product['image4']) ? htmlspecialchars($product['image4']) : 'assets/images/add.png' ?>" />
                    <?php endif; ?>
                    <?php if (!empty($product['image5']) || ($is_logged_in && ($_SESSION['role'] == 'admin'))): ?>
                        <img width="60" height="60" class="rounded-2 border mx-1 item-thumb" data-img="image5"
                            onclick="changeImage(this)"
                            src="<?= !empty($product['image5']) ? htmlspecialchars($product['image5']) : 'assets/images/add.png' ?>" />
                    <?php endif; ?>
                </div>
            </aside>

            <script src="assets/js/jquery-3.7.1.min.js"></script>
            <script>
                function changeImage(element) {
                    let imageField = $(element).data("img");
                    let imageUrl = element.src;

                    $("#mainImage").attr("src", imageUrl);
                    $("#mainImage").attr("data-img", imageField);
                    $("#imageField").val(imageField);

                    if (imageUrl.includes("uploads")) {
                        $("#deleteImageBtn").show().data("img-field", imageField);
                    } else {
                        $("#deleteImageBtn").hide();
                    }
                }

                $(document).ready(function () {
                    $("#imageUpload").change(function () {
                        if (confirm("Are you sure you want to upload this image?")) {
                            let formData = new FormData($("#imageUploadForm")[0]);

                            $("#mainImage").after('<div id="loading" class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

                            $.ajax({
                                url: 'includes/upload.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function (response) {
                                    $("#loading").remove();
                                    if (response.success) {
                                        let imageUrl = response.image_url;
                                        let imageField = response.image_field;

                                        if ($("#mainImage").attr("data-img") === imageField) {
                                            $("#mainImage").attr("src", imageUrl);
                                            $("#deleteImageBtn").show().data("img-field", imageField);
                                        }

                                        $("img[data-img='" + imageField + "']").attr("src", imageUrl);
                                    } else {
                                        alert("Error: " + response.error);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error uploading image: ", error);
                                    $("#loading").remove();
                                }
                            });
                        }
                    });

                    $("#deleteImageBtn").click(function () {
                        let imageField = $(this).data("img-field");
                        let productId = $("input[name='product_id']").val();

                        if (confirm("Are you sure you want to delete this image?")) {
                            $.ajax({
                                url: 'includes/delete_image.php',
                                type: 'POST',
                                data: { product_id: productId, image_field: imageField },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        let defaultImage = 'assets/images/add.png';
                                        $("#mainImage").attr("src", defaultImage);
                                        $("img[data-img='" + imageField + "']").attr("src", defaultImage);
                                        $("#deleteImageBtn").hide();
                                    } else {
                                        alert("Error: " + response.error);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error deleting image: ", error);
                                }
                            });
                        }
                    });
                });
            </script>

            <main class="col-lg-6">
                <div class="ps-lg-3">
                    <h4 class="title text-dark">
                        <!-- Quality Men's Hoodie for Winter, Men's Fashion <br />
                        Casual Hoodie -->
                        <?= htmlspecialchars($product['name']) ?>
                    </h4>
                    <!-- <div class="mb-5"> -->
                    <?php if ($is_logged_in): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <button id="duplicateBtn" class="btn btn-secondary float-end"
                                data-product-id="<?= $product['id'] ?>">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                                Duplicate Product
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- </div> -->
                    <div class="d-flex flex-row my-3">
                        <div class="text-warning mb-1 me-2">
                            <a href="#reviewsContainer">
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
                                <span class="">
                                    <?= htmlspecialchars($product['rating']) ?>

                                </span>
                            </a>
                        </div>
                        <span class="text-muted"><i
                                class="fas fa-shopping-basket fa-sm mx-1"></i><?= htmlspecialchars($product['quantity']) ?></span>
                        <?php if ($product['quantity'] > 0): ?>
                            <span class="text-success ms-2">In stock</span>
                        <?php else: ?>
                            <span class="text-danger ms-2">Out of stock</span>
                        <?php endif; ?>

                    </div>

                    <div class="mb-3">
                        <span class="h5">₹<?= htmlspecialchars($product['price']) ?></span>
                        <span class="text-muted">/per item</span>
                    </div>

                    <p>
                        <?= htmlspecialchars($product['description']) ?>
                    </p>

                    <div class="row">
                        <p>
                            <?= implode(' | ', $product['categories']) ?>
                        </p>
                    </div>
                    <p><a href="#reviewButton" class="">Leave a review-></a></p>

                    <hr />

                    <!-- <div class="row mb-4">
                        <div class="col-md-4 col-6">
                            <label class="mb-2">Size</label>
                            <select class="form-select border border-secondary" style="height: 35px;">
                                <option>Small</option>
                                <option>Medium</option>
                                <option>Large</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-6 mb-3">
                            <label class="mb-2 d-block">Quantity</label>
                            <div class="input-group mb-3" style="width: 170px;">
                                <button class="btn btn-white border border-secondary px-3" type="button"
                                    id="button-addon1" data-mdb-ripple-color="dark">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="text" class="form-control text-center border border-secondary"
                                    placeholder="14" aria-label="Example text with button addon"
                                    aria-describedby="button-addon1" />
                                <button class="btn btn-white border border-secondary px-3" type="button"
                                    id="button-addon2" data-mdb-ripple-color="dark">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div> -->
                    <!-- <a href="#" class="btn btn-warning shadow-0"> Buy now </a> -->
                    <p>
                        <span
                            class="btn btn-primary shadow-0 add-to-cart-btn <?= $product['quantity'] == 0 ? 'disabled' : ''; ?>"
                            data-product-id="<?= htmlspecialchars($product['id']) ?>"> <i
                                class="me-1 fa fa-shopping-basket"></i> Add to cart
                        </span>
                    </p>

                    <!-- <a href="#" class="btn btn-light border border-secondary py-2 icon-hover px-3"> <i
                            class="me-1 fa fa-heart fa-lg"></i> Save </a> -->

                    <div class="mb-5">
                        <?php if ($is_logged_in): ?>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <button class="btn btn-secondary"
                                    onclick="deleteProduct('<?= htmlspecialchars($product['id']) ?>')">Delete</button>
                                <button class="btn btn-primary edit-btn"
                                    data-product-id='<?= htmlspecialchars($product['id']) ?>'>Edit</button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <!-- Duplicate Product Button -->
                    </div>
                    <?php
                    require_once("config/database_connection.php");

                    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                    // Fetch product details
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Fetch linked variations
                    $variations_stmt = $pdo->prepare("
                        SELECT p.* FROM product_variations pv
                        INNER JOIN products p ON (pv.product_id_1 = p.id OR pv.product_id_2 = p.id)
                        WHERE pv.product_id_1 = ? OR pv.product_id_2 = ?
                        GROUP BY p.id
                    ");
                    $variations_stmt->execute([$product_id, $product_id]);
                    $variations = $variations_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="container mt-5">
                        <?php if (!empty($variations)): ?>
                            <h4>Other Flavors Available</h4>
                            <div class="btn-group">
                                <?php foreach ($variations as $variation): ?>
                                    <?php if ($variation['id'] !== $product['id']): // Don't show the current product ?>
                                        <a href="product-details.php?id=<?= $variation['id']; ?>" class="btn btn-sm btn-secondary">
                                            <?= htmlspecialchars($variation['name']); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
        <div class="row gx-5">
            <div class="editProductForm form-group">
                <form id="editProductForm" class="p-5">
                    <input type="hidden" id="editProductId" name="id">
                    <h2 class="text-center">Edit Product</h2>
                    <div class="mb-3">
                        <label for="editName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="editImage" name="image_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="editImageShow" class="form-label">Image</label>
                        <div id="editImageShow"></div>

                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="editPrice" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="editQuantity" name="quantity" required>
                    </div>
                    <div id="multiSelectContainer" class="mb-3">
                        <input type="hidden" id="editCategories" name="categories" required>
                        <label for="multiSelect" class="form-label">Categories</label>
                        <select multiple class="form-control" id="multiSelect"></select>
                    </div>
                    <div class="mb-3">
                        <label for="editFlavour" class="form-label">Flavour</label>
                        <div class="input-group">
                            <select class="form-select" id="editFlavour" name="flavour">
                                <option selected disabled>Loading...</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary" id="addFlavourBtn"
                                title="Add New Flavour">+</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editSeller" class="form-label">Seller</label>
                        <input type="text" class="form-control" id="editSeller" name="seller" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRating" class="form-label">Rating</label>
                        <input type="number" class="form-control" id="editRating" name="rating" step="0.1" required>
                    </div>
                    <div class="mb-3">
                        <label for="editReviews" class="form-label">Reviews</label>
                        <input type="number" class="form-control" id="editReviews" name="reviews" required>
                    </div>
                    <div class="form-check">
                        <div class="col-md-12">
                            <div class="col-md-6"><label class="form-check-label" for="featured_product">
                                    Featured Product
                                </label>
                                <input class="form-check-input" type="checkbox" value="" name="featured_product"
                                    id="featured_product" />
                            </div>

                            <div class="col-md-6"><label class="form-check-label" for="hide_product">
                                    Hide Product
                                </label>
                                <input class="form-check-input" type="checkbox" value="" name="hide_product"
                                    id="hide_product" />
                            </div>
                        </div>


                    </div>
                    <div class="mb-3 text-center">
                        <span type="button" class="btn btn-secondary" onclick="hideDiv()">Close</span>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- content -->

<section class="py-4">

    <div class="container">
        <div class="row gx-4">
            <div class="col-lg-8 mb-4">
                <div class="border  rounded-2 px-3 py-2 bg-white">
                    <div class="container my-5">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                    role="tab" aria-controls="home" aria-selected="true">Specification</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    role="tab" aria-controls="profile" aria-selected="false">Warranty info</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                    role="tab" aria-controls="contact" aria-selected="false">Shipping info</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <p id="descriptionContent"></p>
                                <div class="row mb-2">
                                    <div class="col-12 col-md-6">
                                        <ul class="list-unstyled mb-0" id="leftFeaturesContent"></ul>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ul class="list-unstyled" id="rightFeaturesContent"></ul>
                                    </div>
                                </div>
                                <table class="table border mt-3 mb-2" id="specificationTable"></table>
                            </div>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <p id="warrantyInfo">Loading...</p>
                            </div>

                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <p id="shippingInfo">Loading...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field to store product ID -->


                    <script>
                        $(document).ready(function () {
                            var productId = "<?= htmlspecialchars($product['id']); ?>"

                            $.ajax({
                                url: "includes/fetch_product_specifications.php",
                                type: "POST",
                                data: { product_id: productId },
                                dataType: "json",
                                success: function (response) {
                                    if (response.success) {
                                        let spec = response.specification;
                                        if (spec) {
                                            $('#descriptionContent').html(spec.description)
                                            // Populate Features
                                            let leftFeaturesHTML = "";
                                            spec.leftFeatures.forEach(feature => {
                                                leftFeaturesHTML += `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`;
                                            });

                                            let rightFeaturesHTML = "";
                                            spec.rightFeatures.forEach(feature => {
                                                rightFeaturesHTML += `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`;
                                            });

                                            $("#leftFeaturesContent").html(leftFeaturesHTML);
                                            $("#rightFeaturesContent").html(rightFeaturesHTML);

                                            // Populate Specifications Table
                                            let specTableHTML = "";
                                            spec.specifications.forEach(item => {
                                                specTableHTML += `<tr><th class="py-2">${item.title}:</th><td class="py-2">${item.value}</td></tr>`;
                                            });

                                            $("#specificationTable").html(specTableHTML);

                                            // Populate Warranty & Shipping Info
                                            $("#warrantyInfo").text(spec.warrantyInfo);
                                            $("#shippingInfo").text(spec.shippingInfo);
                                        }
                                    } else {
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("AJAX Error:", error);
                                }
                            });
                        });

                    </script>

                    <?php if ($is_logged_in): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="container my-5">

                                <!-- Button trigger modal -->
                                <p class="text-end"><button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#productFormModal">
                                        Edit Specifications
                                    </button></p>

                                <!-- Modal -->
                                <div class="modal fade" id="productFormModal" data-bs-backdrop="static" data-bs-keyboard="false"
                                    tabindex="-1" aria-labelledby="productFormModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title fs-5" id="productFormModalLabel">Enter Specifications
                                                </h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="productForm">
                                                    <!-- Description -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Description:</label>
                                                        <textarea class="form-control" id="description" rows="3"></textarea>
                                                    </div>

                                                    <!-- Feature List -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h5>Left Features</h5>
                                                            <div id="leftFeatures">
                                                                <input type="text" class="form-control mb-2"
                                                                    placeholder="Enter left feature">
                                                            </div>
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="addFeature('leftFeatures')">Add More</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5>Right Features</h5>
                                                            <div id="rightFeatures">
                                                                <input type="text" class="form-control mb-2"
                                                                    placeholder="Enter right feature">
                                                            </div>
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="addFeature('rightFeatures')">Add More</button>
                                                        </div>
                                                    </div>

                                                    <!-- Specifications Table -->
                                                    <h5>Specifications</h5>
                                                    <div id="specifications">
                                                        <div class="row mb-2">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control"
                                                                    placeholder="Specification Title">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control"
                                                                    placeholder="Specification Value">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    onclick="removeRow(this)">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        onclick="addSpecification()">Add
                                                        Specification</button>

                                                    <!-- Warranty & Shipping Info -->
                                                    <div class="mt-3">
                                                        <label class="form-label">Warranty Info:</label>
                                                        <textarea class="form-control" id="warrantyInfo" rows="2"></textarea>
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="form-label">Shipping Info:</label>
                                                        <textarea class="form-control" id="shippingInfo" rows="2"></textarea>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <div class="text-end mt-3">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Specification</button>
                                                    </div>

                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function addFeature(containerId) {
                                    let container = document.getElementById(containerId);
                                    let input = document.createElement("input");
                                    input.type = "text";
                                    input.className = "form-control mb-2";
                                    input.placeholder = "Enter feature";
                                    container.appendChild(input);
                                }

                                function addSpecification() {
                                    let container = document.getElementById("specifications");
                                    let row = document.createElement("div");
                                    row.className = "row mb-2";

                                    row.innerHTML = `
                                <div class="col-md-5">
                                    <input type="text" class="form-control" placeholder="Specification Title">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" placeholder="Specification Value">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button>
                                </div>
                            `;
                                    container.appendChild(row);
                                }

                                function removeRow(button) {
                                    button.parentElement.parentElement.remove();
                                }

                                $("#productForm").on("submit", function (e) {
                                    e.preventDefault();

                                    // Collecting Features
                                    let leftFeatures = [];
                                    $("#leftFeatures input").each(function () {
                                        if ($(this).val().trim() !== "") {
                                            leftFeatures.push($(this).val().trim());
                                        }
                                    });

                                    let rightFeatures = [];
                                    $("#rightFeatures input").each(function () {
                                        if ($(this).val().trim() !== "") {
                                            rightFeatures.push($(this).val().trim());
                                        }
                                    });

                                    // Collecting Specifications
                                    let specifications = [];
                                    $("#specifications .row").each(function () {
                                        let title = $(this).find("input:eq(0)").val().trim();
                                        let value = $(this).find("input:eq(1)").val().trim();
                                        if (title !== "" && value !== "") {
                                            specifications.push({ title, value });
                                        }
                                    });

                                    // Prepare JSON data
                                    let specificationData = {
                                        description: $("#description").val().trim(),
                                        leftFeatures: leftFeatures,
                                        rightFeatures: rightFeatures,
                                        specifications: specifications,
                                        warrantyInfo: $("#warrantyInfo").val().trim(),
                                        shippingInfo: $("#shippingInfo").val().trim(),
                                    };

                                    var productId = "<?= htmlspecialchars($product['id']); ?>"
                                    // Send AJAX request to save data
                                    $.ajax({
                                        url: "includes/save_product_specification.php", // Backend file to handle saving
                                        type: "POST",
                                        data: {
                                            product_id: productId, // Replace with actual product ID
                                            specification: JSON.stringify(specificationData),
                                        },
                                        dataType: "json",
                                        success: function (response) {
                                            if (response.success) {
                                                alert("Product specifications saved successfully!");
                                            } else {
                                                alert("Error saving specifications: " + response.error);
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            console.error("AJAX Error:", error);
                                            alert("An error occurred while saving product specifications.");
                                        },
                                    });
                                });


                            </script>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="border  rounded-2 px-3 my-5 bg-white">
                    <div class="container my-5">
                        <h4>Customer Reviews</h4>

                        <!-- Sorting & Filtering -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary float-end" data-bs-toggle="modal"
                                id="reviewButton" data-bs-target="#reviewModal">
                                Leave a review
                            </button>
                            <label for="sortReviews">Sort By:</label>
                            <select id="sortReviews" class="form-select" style="width: 10em;">
                                <option value="latest">Latest</option>
                                <option value="highest">Highest Rating</option>
                                <option value="lowest">Lowest Rating</option>
                            </select>
                        </div>
                        <div id="reviewsContainer"></div>
                        <!-- Pagination -->
                        <div id="paginationControls"></div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="reviewModalLabel">Modal title</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="review-section pb-5">
                                    <h4>Leave a Review</h4>
                                    <form id="reviewForm">
                                        <input type="hidden" name="product_id"
                                            value="<?= htmlspecialchars($product['id']); ?>">
                                        <input type="hidden" name="user_id"
                                            value="<?= htmlspecialchars($_SESSION['user_id'] ?? ""); ?>">

                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating:</label>
                                            <select name="rating" id="rating" class="form-select" required
                                                style="width: 10em">
                                                <option value="5">⭐️⭐️⭐️⭐️⭐️</option>
                                                <option value="4">⭐️⭐️⭐️⭐️</option>
                                                <option value="3">⭐️⭐️⭐️</option>
                                                <option value="2">⭐️⭐️</option>
                                                <option value="1">⭐️</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="review" class="form-label">Your Review:</label>
                                            <textarea name="review" id="review" required
                                                class="form-control"></textarea>
                                        </div>

                                        <div class="text-end">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit Review</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>




                <script>
                    $(document).ready(function () {
                        let productId = $("input[name='product_id']").val();
                        let currentPage = 1;

                        function loadReviews(page = 1, sort = "latest") {
                            $.ajax({
                                url: 'includes/fetch_reviews.php',
                                type: 'GET',
                                data: { product_id: productId, page: page, sort: sort },
                                dataType: 'json',
                                success: function (response) {
                                    let reviewsHtml = "";
                                    if (response.success && response.reviews.length > 0) {
                                        response.reviews.forEach(review => {
                                            reviewsHtml += `
                                            <div class="review">
                                                <h5>${review.username} ${review.is_verified ? '<span class="badge bg-success">Verified</span>' : ''}</h5>
                                                <p>${'⭐️'.repeat(review.rating)} (${review.rating}/5)</p>
                                                <p>${review.review}</p>
                                                <hr>
                                            </div>`;
                                        });

                                        let paginationHtml = "";
                                        for (let i = 1; i <= response.totalPages; i++) {
                                            paginationHtml += `<button class="page-item" data-page="${i}">${i}</button> `;
                                        }

                                        $("#paginationControls").html(paginationHtml);
                                    } else {
                                        reviewsHtml = "<p class='text-muted'>No reviews yet. Be the first to review this product!</p>";
                                    }
                                    $("#reviewsContainer").html(reviewsHtml);
                                }
                            });
                        }

                        $("#reviewForm").on("submit", function (e) {
                            e.preventDefault();
                            $.ajax({
                                url: 'includes/submit_review.php',
                                type: 'POST',
                                data: $(this).serialize(),
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        alert("Review submitted successfully!");
                                        $("#reviewForm")[0].reset();
                                        loadReviews();
                                    } else {
                                        alert("Error: " + response.error);
                                    }
                                }
                            });
                        });

                        $(document).on("click", ".page-btn", function () {
                            let page = $(this).data("page");
                            loadReviews(page);
                        });

                        $("#sortReviews").on("change", function () {
                            loadReviews(1, $(this).val());
                        });

                        loadReviews();
                    });

                </script>
            </div>
            <div class="col-lg-4">
                <div class="px-0 rounded-2 shadow-0">
                    <div class="card">
                        <div class="card-body ">
                            <h5 class="card-title">Similar items</h5>

                            <div id="similarProducts">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $(".add-to-cart-btn").click(function (e) {
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
</script>
<script>
    $(document).ready(function () {
        // Handle Edit button click

        $(document).on("click", ".edit-btn", function (e) {
            e.preventDefault();

            // Correctly retrieve the data-product-id attribute
            var productId = $(this).data('product-id');

            $.ajax({
                url: 'includes/getByProductId.php', // Backend endpoint for fetching the product
                type: 'POST',
                data: {
                    product_Id: productId // Send the product ID in the request
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var product = response.product;
                        // Populate modal fields with the response data
                        $('#editProductId').val(product.id);
                        $('#editName').val(product.name);
                        $('#editImage').val(product.image_url);
                        $('#editImageShow').html(`<img src="${product.image_url}" alt="${product.name}" style="width: 50px; height: 50px;"/>`)
                        $('#editDescription').val(product.description);
                        $('#editPrice').val(product.price);
                        $('#editQuantity').val(product.quantity);
                        $('#editFlavour').val(product.flavour);

                        $('#editCategories').val(product.categories ? JSON.parse(product.categories).join(", ") : "");
                        $.ajax({
                            url: 'includes/fetch_categories.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                product_Id: productId // Send the product ID in the request
                            },
                            success: function (response) {
                                let $multiSelect = $('#multiSelect');

                                // Clear existing options (optional)
                                $multiSelect.empty();

                                // Populate the select element with new options
                                response.categories.forEach(function (option) {
                                    $multiSelect.append(new Option(option.name, option.id, false, false));
                                });

                                response.productCategories.forEach(function (option) {
                                    $multiSelect.append(new Option(option.name, option.id, true, true)); // Select existing categories
                                });
                                console.log(typeof $.fn.select2); // Should print "function"

                                // Initialize or Reinitialize Select2
                                $multiSelect.select2({
                                    placeholder: "please select",
                                    allowClear: true,
                                    tags: true,
                                    dropdownParent: $('#multiSelectContainer'),
                                    tokenSeparators: [',', ' ']
                                }).trigger('change'); // Ensure it updates

                            },
                            error: function (xhr, status, error) {
                                console.error("Error loading categories:", error);
                            }
                        });
                        $('#editSeller').val(product.seller);
                        $('#editRating').val(product.rating);
                        $('#editReviews').val(product.reviews);
                        if (product.featured_product == 1) {
                            $('#featured_product').prop('checked', true)
                        }
                        if (product.hide_product == 1) {
                            $('#hide_product').prop('checked', true)
                        }

                        // Show the modal
                        $('.editProductForm').css('display', 'block');
                        const body = document.querySelector('.editProductForm');
                        body.scrollIntoView({
                            behavior: 'smooth'
                        }, 300)
                    } else {
                        alert('Error: ' + (response.error || 'Unable to fetch product details.'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching the product details.');
                }
            });

        });
        // Handle form submission for editing a product
        $('#editProductForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            $('#editCategories').val($('#multiSelect').val())
            if ($('#featured_product').is(":checked")) {
                $('#featured_product').val(true)
            } else {
                $('#featured_product').val(false)
            }
            if ($('#hide_product').is(":checked")) {
                $('#hide_product').val(true)
            } else {
                $('#hide_product').val(false)
            }
            const form = document.getElementById('editProductForm'); // Get the form element
            const formData = new FormData(form); // Create FormData object from the form

            $.ajax({
                url: 'includes/edit_product.php', // Backend endpoint for editing the product
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the Content-Type header
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    if (response.success) {
                        alert('Product updated successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error updating product: ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while updating the product.');
                }
            });
        });

    });
    // Delete product using AJAX
    window.deleteProduct = function (id) {
        if (confirm("Are you sure you want to delete this product?")) {
            fetch(`includes/delete_product.php?id=${id}`, { method: "GET" })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert("Product deleted successfully!");
                        window.location = 'products.php';
                    } else {
                        alert("Error: " + result.message);
                    }
                })
                .catch(error => console.error("Error deleting product:", error));
        }
    };
</script>

<script>
    function fetchSimilarProducts(productId) {
        $.ajax({
            url: 'includes/fetch_similar_products.php',
            type: 'GET',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let similarProductsHtml = "";
                    response.products.forEach(product => {
                        similarProductsHtml += `
                            <div class="d-flex mb-3">
                                <a href="#" class="me-3">
                                    <img src="${product.image_url}"
                                        style="min-width: 96px; height: 96px;" class="img-md img-thumbnail" alt="${product.name}" />
                                </a>
                                <div class="info">
                                    <a href="product-details.php?id=${product.id}" class="nav-link mb-1">
                                        ${product.name}
                                    </a>
                                    <strong class="text-dark"> ₹${product.price}</strong>
                                </div>
                            </div>`;
                    });
                    $("#similarProducts").html(similarProductsHtml);
                } else {
                    $("#similarProducts").html(`<p class="text-danger">${response.error}</p>`);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching similar products:", error);
            }
        });
    }
    fetchSimilarProducts("<?= htmlspecialchars($product['id']) ?>");

</script>
<script>
    $(document).ready(function () {
        $('#duplicateBtn').on('click', function () {
            const productId = $(this).data('product-id');

            if (!confirm("Are you sure you want to duplicate this product?")) return;

            $.ajax({
                url: 'includes/duplicate_product.php',
                method: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert("Product duplicated successfully!");
                        window.location.href = `product-details.php?id=${response.new_id}`;
                    } else {
                        alert("Failed to duplicate: " + response.message);
                    }
                },
                error: function () {
                    alert("An error occurred while duplicating the product.");
                }
            });
        });
    });
</script>
<script>
$(document).ready(function () {
    function loadFlavours() {
        $.getJSON('includes/get_flavour_list.php', function (res) {
            if (res.success) {
                const dropdown = $('#editFlavour');
                dropdown.empty();
                dropdown.append(`<option value="" disabled selected>Select one</option>`);
                res.flavours.forEach(f => {
                    dropdown.append(`<option value="${f}">${f}</option>`);
                });
            } else {
                alert("Failed to load flavours: " + res.message);
            }
        });
    }

    // Load flavours on page load
    loadFlavours();

    // Handle Add Flavour button
    $('#addFlavourBtn').on('click', function () {
        const newFlavour = prompt("Enter new flavour name:");
        if (newFlavour) {
            $.post('includes/add_flavour.php', { name: newFlavour, description: newFlavour }, function (res) {
                try {
                    const response = JSON.parse(res);
                    if (response.success) {
                        alert("Flavour added!");
                        loadFlavours();
                        $('#editFlavour').val(newFlavour); // auto-select new flavour
                    } else {
                        alert("Error: " + response.message);
                    }
                } catch (e) {
                    alert("Unexpected error. Check console.");
                    console.error(e, res);
                }
            });
        }
    });
});
</script>

<script src="assets/js/select2.min.js"></script>
<?php include 'includes/footer.php'; ?>