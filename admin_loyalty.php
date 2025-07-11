<?php
include 'includes/header.php';

require_once("config/database_connection.php");

// Fetch users with loyalty points
$usersStmt = $pdo->query("SELECT id, username, email, loyalty_points FROM users");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch loyalty settings
$settingsStmt = $pdo->query("SELECT * FROM loyalty_settings LIMIT 1");
$settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);

// Fetch loyalty transactions
$transactionsStmt = $pdo->query("
    SELECT lt.*, u.username, o.order_id AS order_id
    FROM loyalty_transactions lt
    LEFT JOIN users u ON lt.user_id = u.id
    LEFT JOIN orders o ON lt.order_id = o.order_id
    ORDER BY lt.created_at DESC
");
$transactions = $transactionsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container p-5 mt-4">
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

    <?php require_once("includes/side_menu.php"); ?>
    <main>
        <h2 class="mb-4">Loyalty Program Management</h2>

        <!-- Settings Section -->
        <div class="card mb-4">
            <div class="card-header">Loyalty Settings</div>
            <div class="card-body">
                <form id="loyaltySettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Points Per ₹1 Spent:</label>
                            <input type="number" class="form-control" name="points_per_dollar"
                                value="<?= $settings['points_per_dollar']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Redemption Rate (₹ per point):</label>
                            <input type="number" step="0.01" class="form-control" name="redemption_rate"
                                value="<?= $settings['redemption_rate']; ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Settings</button>
                </form>
            </div>
        </div>

        <!-- Users Section -->
        <div class="card mb-4">
            <div class="card-header">Users & Loyalty Points</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td id="points-<?= $user['id']; ?>"><?= $user['loyalty_points']; ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm add-points" data-user-id="<?= $user['id']; ?>">+10
                                        Points</button>
                                    <button class="btn btn-danger btn-sm remove-points"
                                        data-user-id="<?= $user['id']; ?>">-10
                                        Points</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="card">
            <div class="card-header">Loyalty Transactions</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Order ID</th>
                            <th>Points</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['username']); ?></td>
                                <td><?= $transaction['order_id'] ? "#" . $transaction['order_id'] : "N/A"; ?></td>
                                <td><?= $transaction['points']; ?></td>
                                <td
                                    class="<?= $transaction['transaction_type'] == 'earn' ? 'text-success' : 'text-danger'; ?>">
                                    <?= ucfirst($transaction['transaction_type']); ?>
                                </td>
                                <td><?= $transaction['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {

        // Update Loyalty Settings
        $("#loyaltySettingsForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "includes/update_loyalty_settings.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    alert(response.message);
                }
            });
        });

        // Adjust User Points
        $(".add-points, .remove-points").on("click", function () {
            let userId = $(this).data("user-id");
            let change = $(this).hasClass("add-points") ? 10 : -10;

            $.ajax({
                url: "includes/update_loyalty_points.php",
                type: "POST",
                data: { user_id: userId, change: change },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $("#points-" + userId).text(response.new_points);
                    } else {
                        alert("Failed to update points.");
                    }
                }
            });
        });

    });
</script>
<?php require_once("includes/footer_scripts.php"); ?>