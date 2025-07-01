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
    </main>
</div>