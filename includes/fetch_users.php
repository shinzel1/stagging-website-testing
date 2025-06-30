<?php
// Database connection
require_once("../config/database_connection.php");
// Fetch users
try {
    $query = "SELECT id, username, email, role, created_at, is_verified FROM users";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "users" => $users]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Failed to fetch users: " . $e->getMessage()]);
}
?>
