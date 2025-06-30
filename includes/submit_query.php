<?php
require_once("../config/database_connection.php");

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);

        if (empty($subject) || empty($message)) {
            $response['error'] = "All fields are required.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO queries (user_id, subject, message) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $subject, $message]);
                $response['success'] = true;
            } catch (PDOException $e) {
                $response['error'] = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $user_id = null;
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);

        if (empty($subject) || empty($message)) {
            $response['error'] = "All fields are required.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO queries (user_id, subject, message) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $subject, $message]);
                $response['success'] = true;
            } catch (PDOException $e) {
                $response['error'] = "Database error: " . $e->getMessage();
            }
        }
    }
}

echo json_encode($response);
?>