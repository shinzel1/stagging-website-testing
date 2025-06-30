<?php
require_once("../config/database_connection.php");

try {
    $stmt = $pdo->query("SELECT name FROM flavours ORDER BY name ASC");
    $flavours = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch just the name column
    echo json_encode(['success' => true, 'flavours' => $flavours]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>