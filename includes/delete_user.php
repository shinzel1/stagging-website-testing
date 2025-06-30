<?php
// Database connection
require_once("../config/database_connection.php");

// Delete user
if (isset($_POST['id'])) {
    try {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "User deleted successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Failed to delete user: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
