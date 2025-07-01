<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<?php require_once("config/database_connection.php"); ?>

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
    <h2 class="mb-4 pt-5">Flavours</h2>
    <p class="text-end">
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addFlavourModal">Add New
            Flavour</button>
    </p>

    <div class="modal fade" id="addFlavourModal" tabindex="-1" aria-labelledby="addFlavourLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="flavourForm" class="modal-content">
                <input type="hidden" name="id" id="flavour_id">
                <div class="modal-header">
                    <h5 class="modal-title">Add Flavour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Flavour Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Flavour</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flavour Table -->
    <div class="card">
        <div class="card-header">Flavour List</div>
        <div class="card-body">
            <table id="flavour-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Flavour Name</th>
                        <th>description </th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Load Flavours
        const table = $('#flavour-table').DataTable({
            ajax: 'includes/fetch_flavours.php',
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' }, {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" data-name="${row.name}" data-description="${row.description}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Delete</button>
                        `;
                    }

                }
            ],
            destroy: true
        });
    });

    $(document).ready(function () {
        $('#flavourForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#flavour_id').val();
            const url = id ? 'includes/edit_flavour.php' : 'includes/add_flavour.php';

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#addFlavourModal').modal('hide');
                        $('#flavourForm')[0].reset();
                        $('#flavour-table').DataTable().ajax.reload();
                        alert(res.message || 'Flavour saved successfully');
                    } else {
                        alert(res.message || 'Failed to save flavour');
                    }
                },
                error: function () {
                    alert('An error occurred');
                }
            });
        });

        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');

            $.get('includes/get_flavour.php', { id: id }, function (response) {
                if (response.success && response.data) {
                    $('#flavour_id').val(response.data.id);
                    $('#name').val(response.data.name);
                    $('#description').val(response.data.description);
                    $('#addFlavourModal').modal('show');
                } else {
                    alert(response.message || 'Failed to load flavour');
                }
            }, 'json');
        });


        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            if (confirm('Delete this flavour?')) {
                $.ajax({
                    url: 'includes/delete_flavour.php',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        fetchFlavours();
                        alert('Flavour deleted successfully');
                    },
                    error: function () {
                        alert('Failed to delete flavour');
                    }
                });
            }
        });

        function fetchFlavours() {
            $('#flavour-table').DataTable().ajax.reload(null, false); // reload table without resetting pagination
        }

    });



</script>

<?php include 'includes/footer.php'; ?>