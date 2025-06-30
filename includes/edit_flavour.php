<?php
require_once('../config/database_connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$id || !$name || !$description) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE flavours SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);

        echo json_encode(['success' => true, 'message' => 'Flavour updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>