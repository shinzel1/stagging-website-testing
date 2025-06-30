<?php
session_start();
require_once("../config/database_connection.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "count" => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "count" => $result['count']]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "count" => 0, "error" => $e->getMessage()]);
}
?>
