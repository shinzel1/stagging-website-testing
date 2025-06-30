<?php
require_once("../config/database_connection.php");

$response = ['queries' => []];

try {
    $stmt = $pdo->query("SELECT q.id, u.username AS user, q.subject, q.message, q.response, q.status
                         FROM queries q
                         JOIN users u ON q.user_id = u.id
                         ORDER BY q.created_at DESC");
    $response['queries'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $response['error'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>
