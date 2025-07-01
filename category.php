<?php include 'includes/header.php'; ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">

<!-- jQuery & DataTables JS -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>

<?php

$is_logged_in = isset($_SESSION['user_id']);
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

<div class="container p-5">
    <style>
        @media (min-width: 991.98px) {
            main {
                padding-left: 240px;
            }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 58px 0 0;
            /* Height of navbar */
            box-shadow: -1px 7px 15px -4px rgba(0, 0, 0, 0.76);
            width: 240px;
            z-index: 600;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 100%;
            }
        }

        .sidebar .active {
            border-radius: 5px;
            box-shadow: 0 2px 5px 0 rgb(0 0 0 / 16%), 0 2px 10px 0 rgb(0 0 0 / 12%);
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
            /* Scrollable contents if viewport is shorter than content. */
        }
    </style>

    <header>
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="collapse d-lg-block sidebar collapse bg-white">
            <div class="position-sticky">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <!-- <span class="list-group-item list-group-item-action py-2 ripple active" aria-current="true">
                        <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Admin dashboard</span>
                    </span> -->
                    <a href="category.php" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="fa-solid fa-layer-group me-3"></i><span>Category</span>
                    </a>
                    <a href="admin_loyalty.php" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="fas fa-chart-pie fa-fw me-3"></i><span>Loyalty program</span>
                    </a>
                    <a href="offers.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-hand-holding-dollar fa-fw me-3"></i><span>Offers</span></a>
                    <a href="add_variation.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-ice-cream fa-fw me-3"></i><span>Add product variations (flavours)</span></a>
                    <a href="customer-query-dashboard.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-users fa-fw me-3"></i><span>Customer Query Dashboard</span></a>
                    <a href="flavours.php" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-ice-cream fa-fw me-3"></i><span>Add new Flavour</span></a>
                    <!-- 
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-globe fa-fw me-3"></i><span>International</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-building fa-fw me-3"></i><span>Partners</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-calendar fa-fw me-3"></i><span>Calendar</span></a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-users fa-fw me-3"></i><span>Users</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-chart-bar fa-fw me-3"></i><span>Orders</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple ">
                        <i class="fas fa-chart-area fa-fw me-3"></i><span>Webiste traffic</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-lock fa-fw me-3"></i><span>Password</span></a> -->
                    <!-- <a href="#" class="list-group-item list-group-item-action py-2 ripple"><i
                            class="fas fa-chart-line fa-fw me-3"></i><span>Analytics</span></a> -->

                </div>
            </div>
        </nav>
        <!-- Sidebar -->
    </header>

<main>
    <h2 class="mb-4 pt-5">Categories</h2>
    <p style="text-align: end;">
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Add New Category
        </button>
    </p>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New Category</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form id="category-form">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image_url" class="form-label">Upload Image</label>
                                    <input type="file" class="form-control" id="image_url" name="image" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Category Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        required></textarea>
                                </div>
                                <div class="mb-3 text-end">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Categories List</div>
        <div class="card-body">
            <table class="table table-striped table-hover" id="category-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Products will be dynamically loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
<!-- Category Details Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="categoryImage" src="" alt="Category Image" class="img-fluid mb-3">
                <p id="categoryDescription"></p>
            </div>
        </div>
    </div>
</div>

<script>



    $(document).ready(function () {
        // Load Categories
        function loadCategories() {
            $('#category-table').DataTable({
                processing: true,
                serverSide: true, // Enables AJAX pagination
                ajax: {
                    url: "includes/fetch_categories.php",
                    type: "POST"
                },
                columns: [
                    {
                        data: "image", render: function (data, type, row) {
                            return `<img src="${data}" alt="${row.name}" style="width: 32px; height: 32px;">`;
                        }
                    },
                    {
                        data: "name", render: function (data, type, row) {
                            return `<a href="category-details.php?id=${row.id}" title="${data}">${data}</a>`;
                        }
                    },
                    { data: "description" }
                ],
                destroy: true, // Ensure table reloads properly
                searching: true, // Enables search
                paging: true, // Enables pagination
                lengthMenu: [10, 20, 50] // Items per page
            });
        }

        // Load Products by Category
        $(document).on("click", ".filter-products", function () {
            let categoryId = $(this).data("id");
            $.ajax({
                url: 'includes/fetch_products_by_category.php',
                type: 'POST',
                data: { category_id: categoryId },
                dataType: 'json',
                success: function (response) {
                    let productsHtml = "";
                    if (response.success && response.products.length > 0) {
                        response.products.forEach(product => {
                            productsHtml += `
                            <div class="col-md-3">
                                <div class="card mb-3">
                                    <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                                    <div class="card-body">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="card-text">${product.description.substring(0, 50)}...</p>
                                        <p><strong>Price:</strong> $${product.price}</p>
                                    </div>
                                </div>
                            </div>`;
                        });
                    } else {
                        productsHtml = "<p class='text-warning'>No products found in this category.</p>";
                    }
                    $("#productList").html(productsHtml);
                },
                error: function (xhr, status, error) {
                    console.error("Error loading products:", error);
                }
            });
        });

        // Open Category Details Modal
        $(document).on("click", ".view-category", function () {
            let name = $(this).data("name");
            let desc = $(this).data("desc");
            let img = $(this).data("img");

            $("#categoryModalLabel").text(name);
            $("#categoryDescription").text(desc);
            $("#categoryImage").attr("src", img);

            $("#categoryModal").modal("show");
        });

        // Add Category using AJAX
        const categoryForm = document.getElementById("category-form");
        if (categoryForm) {
            categoryForm.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(categoryForm);
                fetch("includes/add_category.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert("Category added successfully!");
                            loadCategories();
                            categoryForm.reset();
                            $('#staticBackdrop').modal('hide');
                        } else {
                            alert("Error: " + result.message);
                        }
                    })
                    .catch(error => console.error("Error adding product:", error));
            });
        }

        // Load categories on page load
        loadCategories();
    });

</script>