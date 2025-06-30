<?php
require_once("config/database_connection.php");
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_token = ?");
        $stmt->execute([$email, $token]);

        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE email = ?");
            $stmt->execute([$email]);
            echo "Your email has been verified successfully!";
        } else {
            echo "Invalid verification link or email already verified.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>