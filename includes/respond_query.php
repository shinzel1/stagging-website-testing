<?php
require_once("../config/database_connection.php");

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query_id = $_POST['query_id'];
    $response_text = trim($_POST['response']);

    if (empty($response_text)) {
        $response['error'] = "Response cannot be empty.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE queries SET response = ?, status = 'resolved' WHERE id = ?");
            $stmt->execute([$response_text, $query_id]);
            $response['success'] = true;
        } catch (PDOException $e) {
            $response['error'] = "Database error: " . $e->getMessage();
        }
    }
}

echo json_encode($response);
?>
