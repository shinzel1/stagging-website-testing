<?php include 'includes/header.php'; ?>

<?php
require_once("config/database_connection.php");
$category = [];
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $category_Id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$category_Id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            die("Error: Category not found.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

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

    .editCategoryForm {
        border-radius: 10px;
        box-shadow: 5px 5px 15px -2px rgba(0, 0, 0, 0.42);
    }
</style>

<!-- content -->
<section class="py-5">
    <div class="container">
        <div class="row gx-5">
            <div class="editCategoryForm form-group">
                <form id="editCategoryForm" class="p-5">
                    <input type="hidden" id="editCategoryId" name="id"
                        value="<?= htmlspecialchars($category['id']) ?>" />
                    <h2 class="text-center">Edit Category</h2>
                    <div class="mb-3">
                        <label for="editName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="editName" name="name"
                            value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Category Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title"
                            value="<?= htmlspecialchars($category['title']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="editImage" name="editImage"
                            value="<?= htmlspecialchars($category['image']) ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="editImageShow" class="form-label">Image</label>
                        <div id="editImageShow"><img src="<?= htmlspecialchars($category['image']) ?>"
                                alt="<?= htmlspecialchars($category['name']) ?>" style="width: 50px; height: 50px;" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"
                            required><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>
                    <div class="mb-5 text-center">
                        <button class="btn btn-secondary"
                            onclick="deleteCategory('<?= htmlspecialchars($category['id']) ?>')">Delete</button>

                        <button class="btn btn-primary edit-btn" style="display: none;"
                            data-category-id='<?= htmlspecialchars($category['id']) ?>'>Edit</button>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        // Handle Edit button click
        $(document).on("click", ".edit-btn", function (e) {
            e.preventDefault();

            // Correctly retrieve the data-category-id attribute
            var categoryId = $(this).data('category-id');

            $.ajax({
                url: 'includes/getByCategoryId.php', // Backend endpoint for fetching the category
                type: 'POST',
                data: {
                    category_Id: categoryId // Send the category ID in the request
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var category = response.category;
                        // Populate modal fields with the response data
                        $('#editCategoryId').val(category.id);
                        $('#editName').val(category.name);
                        $('#editImage').val(category.image);
                        $('#editImageShow').html(`<img src="${category.image}" alt="${category.name}" style="width: 50px; height: 50px;"/>`)
                        $('#editDescription').val(category.description);
                        // Show the modal
                        $('.editCategoryForm').css('display', 'block');
                        const body = document.querySelector('.editCategoryForm');
                        body.scrollIntoView({
                            behavior: 'smooth'
                        }, 300)
                    } else {
                        alert('Error: ' + (response.error || 'Unable to fetch category details.'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching the category details.');
                }
            });

        });

        // Handle form submission for editing a category
        $('#editCategoryForm').on('submit', function (e) {
            e.preventDefault();

            if (!$('#editName').val().trim()) {
                alert('Category Name is required.');
                return;
            }
            if (!$('#editTitle').val().trim()) {
                alert('Category Title is required.');
                return;
            }
            if (!$('#editImage').val().trim()) {
                alert('Image URL is required.');
                return;
            }

            const form = document.getElementById('editCategoryForm');
            const formData = new FormData(form);

            $.ajax({
                url: 'includes/edit_category.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Category updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating category: ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while updating the category.');
                }
            });
        });


    });
    // Delete category using AJAX
    window.deleteCategory = function (id) {
        if (confirm("Are you sure you want to delete this category?")) {
            fetch(`includes/delete_category.php?id=${id}`, { method: "GET" })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert("Category deleted successfully!");
                        window.location = 'category.php';
                    } else {
                        alert("Error: " + result.message);
                    }
                })
                .catch(error => console.error("Error deleting category:", error));
        }
    };

</script>

<?php include 'includes/footer.php'; ?>