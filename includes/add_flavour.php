<?php
require_once("../config/database_connection.php");

$name = trim($_POST['name'] ?? '');
$desc = trim($_POST['description'] ?? $name);

if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Flavour name is required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO flavours (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $desc]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>