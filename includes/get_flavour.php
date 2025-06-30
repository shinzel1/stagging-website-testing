<?php
require_once('../config/database_connection.php');
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM flavours WHERE id = ?");
    $stmt->execute([$id]);
    $flavour = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($flavour) {
        echo json_encode(['success' => true, 'data' => $flavour]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Flavour not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>