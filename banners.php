<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/dataTable/jquery.dataTables.min.css">
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<?php require_once("config/database_connection.php"); ?>

<div class="container p-5">
    <h2 class="mb-4">Manage Banners
        <div class="text-end">
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bannerModal">Add New
                Banner</button>
        </div>
    </h2>

    <!-- Banner Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="bannerForm" class="modal-content" enctype="multipart/form-data" method="post">
                <input type="hidden" name="id" id="banner_id">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Background Image</label>
                        <input type="file" class="form-control" name="background_image_file" id="background_image_file"
                            accept="image/*">
                        <!-- Optional: Preview uploaded image -->
                        <img id="current_preview" src="#" style="max-height: 60px; margin-top: 5px; display: none;" />
                        <input type="hidden" name="existing_background_image" id="existing_background_image">

                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link</label>
                        <input type="text" class="form-control" name="link" id="link">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BG Class</label>
                        <input type="text" class="form-control" name="bg_class" id="bg_class">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Block Class</label>
                        <input type="text" class="form-control" name="block_class" id="block_class">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
    <?php require_once("includes/side_menu.php"); ?>
    <main style="margin-top: 58px;" class="container">
        <!-- Banner Table -->
        <div class="card">
            <div class="card-header">Banner List</div>
            <div class="card-body">
                <table id="banner-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    $(document).ready(function () {
        const table = $('#banner-table').DataTable({
            ajax: 'includes/banner_actions.php?action=fetch',
            columns: [
                { data: 'id' },
                { data: 'title' },
                { data: 'description' },
                {
                    data: 'background_image',
                    render: data => `<img src="${data}" style="width:60px;height:40px;">`
                },
                { data: 'link' },                {
                    data: null,
                    render: row => `
                    <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Delete</button>
                `
                }
            ]
        });

        // Save (Add or Edit)
        $('#bannerForm').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            $.ajax({
                url: 'includes/banner_actions.php',
                type: 'POST',
                data: formData,
                processData: false, // required
                contentType: false, // required
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#bannerModal').modal('hide');
                        $('#bannerForm')[0].reset();
                        $('#banner-table').DataTable().ajax.reload();
                        alert(res.message);
                    } else {
                        alert(res.message || 'Failed to save banner');
                    }
                },
                error: function () {
                    alert('An error occurred');
                }
            });
        });


        // Edit
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            $.get('includes/banner_actions.php?action=get&id=' + id, function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#banner_id').val(d.id);
                    $('#title').val(d.title);
                    $('#description').val(d.description);
                    $('#background_image').val(d.background_image);
                    $('#background_image_file').val('');
                    $('#existing_background_image').val(d.background_image);
                    $('#link').val(d.link);
                    $('#bg_class').val(d.bg_class);
                    $('#block_class').val(d.block_class);
                    $('#bannerModal').modal('show');
                    if (d.background_image) {
                        $('#current_preview').attr('src', d.background_image).show();
                    } else {
                        $('#current_preview').hide();
                    }
                } else {
                    alert(res.message || 'Failed to load banner');
                }
            }, 'json');
        });

        // Delete
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            if (confirm('Delete this banner?')) {
                $.post('includes/banner_actions.php', { action: 'delete', id: id }, function (res) {
                    if (res.success) {
                        table.ajax.reload();
                        alert('Banner deleted successfully');
                    } else {
                        alert(res.message || 'Failed to delete banner');
                    }
                }, 'json');
            }
        });
    });

</script>
<?php require_once("includes/footer_scripts.php"); ?>