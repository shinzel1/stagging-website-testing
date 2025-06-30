<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to index.php
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
require_once("config/database_connection.php");
?>
<div class="container p-5">
    <div class="pt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Response</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="queryTable">
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <form id="respondQueryForm">
        <input type="hidden" name="query_id" id="query_id">

        <div class="mb-3">
            <label>Response</label>
            <textarea class="form-control" name="response" id="query_response" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Response</button>
    </form>

    <script>
        $(document).on("click", ".respond-btn", function () {
            let queryId = $(this).data("id");
            $("#query_id").val(queryId);
            $("#query_response").val("");

            $("#respondQueryForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'includes/respond_query.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert("Response submitted successfully!");
                            loadQueries();
                        } else {
                            alert("Error: " + response.error);
                        }
                    }
                });
            });
        });
    </script>



    <script>
        function loadQueries() {
            $.ajax({
                url: 'includes/fetch_queries.php',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    let tableData = "";
                    response.queries.forEach(query => {
                        tableData += `<tr>
                    <td>${query.id}</td>
                    <td>${query.user}</td>
                    <td>${query.subject}</td>
                    <td>${query.message}</td>
                    <td>${query.response || 'No response yet'}</td>
                    <td>${query.status}</td>
                    <td><button class="btn btn-sm btn-primary respond-btn" data-id="${query.id}">Respond</button></td>
                </tr>`;
                    });
                    $("#queryTable").html(tableData);
                }
            });
        }

        // Load queries on page load
        $(document).ready(loadQueries);
    </script>






</div>

<?php include 'includes/footer.php'; ?>