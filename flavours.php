<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<?php require_once("config/database_connection.php"); ?>

<div class="container p-5">
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