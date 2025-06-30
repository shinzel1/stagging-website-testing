<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $change = (int) $_POST['change'];

    // Update points
    $stmt = $pdo->prepare("UPDATE users SET loyalty_points = loyalty_points + ? WHERE id = ?");
    $stmt->execute([$change, $user_id]);

    // Fetch new points value
    $stmt = $pdo->prepare("SELECT loyalty_points FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "new_points" => $user['loyalty_points']]);
}
?>
