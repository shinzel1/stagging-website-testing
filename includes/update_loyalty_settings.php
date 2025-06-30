<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $points_per_dollar = $_POST['points_per_dollar'];
    $redemption_rate = $_POST['redemption_rate'];

    $stmt = $pdo->prepare("UPDATE loyalty_settings SET points_per_dollar = ?, redemption_rate = ?");
    $stmt->execute([$points_per_dollar, $redemption_rate]);

    echo json_encode(["success" => true, "message" => "Loyalty settings updated successfully!"]);
}
?>
