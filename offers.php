<?php include 'includes/header.php';
require_once("config/database_connection.php");
$current_date = date("Y-m-d H:i:s");
$stmt = $pdo->prepare("SELECT * FROM discounts WHERE status = 'active' AND start_date <= ? AND end_date >= ?");
$stmt->execute([$current_date, $current_date]);
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Add jQuery & DataTables CSS/JS -->
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>

<div>
    <h1>Offers Page</h1>
    <div class="container my-5">
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
                                class="fas fa-ice-cream fa-fw me-3"></i><span>Add product variations
                                (flavours)</span></a>
                        <a href="customer-query-dashboard.php"
                            class="list-group-item list-group-item-action py-2 ripple"><i
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
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
                data-bs-target="#discountModal">
                Add Discount and Offers
            </button>
            <h2 class="text-center">🔥 Special Discounts & Offers</h2>

            <div class="row">
                <table id="offersTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Promo Code</th>
                            <th>Description</th>
                            <th>Discount</th>
                            <th>Valid Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $index => $offer): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($offer['title']); ?></td>
                                <td><?= htmlspecialchars($offer['promo_code']); ?></td>
                                <td><?= htmlspecialchars($offer['description']); ?></td>
                                <td class="text-danger fw-bold">
                                    <?php if ($offer['discount_type'] == 'percentage'): ?>
                                        <?= $offer['discount_value']; ?>% OFF
                                    <?php else: ?>
                                        ₹<?= number_format($offer['discount_value'], 2); ?> Discount
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= date("d M Y", strtotime($offer['end_date'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-offer" data-id="<?= $offer['id']; ?>"
                                        data-title="<?= htmlspecialchars($offer['title']); ?>"
                                        data-promo="<?= htmlspecialchars($offer['promo_code']); ?>"
                                        data-description="<?= htmlspecialchars($offer['description']); ?>"
                                        data-discount-type="<?= $offer['discount_type']; ?>"
                                        data-discount-value="<?= $offer['discount_value']; ?>"
                                        data-start="<?= $offer['start_date']; ?>" data-end="<?= $offer['end_date']; ?>">
                                        ✏️ Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
    </div>
    </main>

    <!-- Add Discount Modal -->
    <div class="modal fade" id="discountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Discount and Offers</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="discountForm">
                        <label>Title:</label>
                        <input type="text" class="form-control" name="title" required>

                        <label>Promo Code:</label>
                        <input type="text" class="form-control" name="promo_code" required>

                        <label>Description:</label>
                        <textarea class="form-control" name="description"></textarea>

                        <label>Discount Type:</label>
                        <select class="form-control" name="discount_type">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>

                        <label>Discount Value:</label>
                        <input type="number" class="form-control" name="discount_value" required>

                        <label>Start Date:</label>
                        <input type="datetime-local" class="form-control" name="start_date" required>

                        <label>End Date:</label>
                        <input type="datetime-local" class="form-control" name="end_date" required>

                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Discount</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Discount Modal -->
    <div class="modal fade" id="editDiscountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Discount</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editDiscountForm">
                        <input type="hidden" name="id" id="edit_discount_id">

                        <label>Title:</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>

                        <label>Promo Code:</label>
                        <input type="text" class="form-control" name="promo_code" id="edit_promo_code" required>

                        <label>Description:</label>
                        <textarea class="form-control" name="description" id="edit_description"></textarea>

                        <label>Discount Type:</label>
                        <select class="form-control" name="discount_type" id="edit_discount_type">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>

                        <label>Discount Value:</label>
                        <input type="number" class="form-control" name="discount_value" id="edit_discount_value"
                            required>

                        <label>Start Date:</label>
                        <input type="datetime-local" class="form-control" name="start_date" id="edit_start_date"
                            required>

                        <label>End Date:</label>
                        <input type="datetime-local" class="form-control" name="end_date" id="edit_end_date" required>

                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Update Discount</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#offersTable").DataTable();

        // Open Edit Modal with Data
        $(".edit-offer").click(function () {
            $("#edit_discount_id").val($(this).data("id"));
            $("#edit_title").val($(this).data("title"));
            $("#edit_promo_code").val($(this).data("promo"));
            $("#edit_description").val($(this).data("description"));
            $("#edit_discount_type").val($(this).data("discount-type"));
            $("#edit_discount_value").val($(this).data("discount-value"));
            $("#edit_start_date").val($(this).data("start"));
            $("#edit_end_date").val($(this).data("end"));
            $("#editDiscountModal").modal("show");
        });

        // Handle Edit Form Submission
        $("#editDiscountForm").submit(function (e) {
            e.preventDefault();
            $.post("includes/edit_discount.php", $(this).serialize(), function (response) {
                if (response.success) {
                    alert("Offer updated successfully!");
                    location.reload();
                } else {
                    alert("Error: " + response.message);
                }
            }, "json");
        });

        $("#discountForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "includes/add_discount_offers.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $('#discountModal').modal('hide')
                        alert("Discount added successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + response.error);
                    }
                }
            });
        });
    });
</script>