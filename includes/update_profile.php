<?php
require_once("../config/database_connection.php");

$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, phone = ?, gender = ?, date_of_birth = ?, bio = ? WHERE id = ?");
        $stmt->execute([
            $_POST['username'],
            $_POST['phone'],
            $_POST['gender'],
            $_POST['date_of_birth'],
            $_POST['bio'],
            $_POST['user_id']
        ]);

        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = $e->getMessage();
    }
}

echo json_encode($response);
?>