<?php 
require_once("../config/database_connection.php");
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['username'], $_POST['email'], $_POST['role'])) {
    try {
        $pdo->beginTransaction(); // Start transaction

        // Generate a random password
        $plainPassword = generateRandomPassword();
        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT); // Hash the password
        $inviteToken = bin2hex(random_bytes(32)); // Generate a unique invite token

        // Insert user data
        $query = "INSERT INTO users (username, email, password, role, verification_token) VALUES (:username, :email, :password, :role, :verification_token)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':role', $_POST['role'], PDO::PARAM_STR);
        $stmt->bindParam(':verification_token', $inviteToken, PDO::PARAM_STR);
        $stmt->execute();

        // Send invite email with login credentials
        $inviteLink = dirname($_SERVER['HTTP_REFERER']) . "/accept_invite.php?token=" . urlencode($inviteToken);
        sendInviteEmail($_POST['email'], $_POST['username'], $plainPassword, $inviteLink);

        $pdo->commit(); // Commit transaction

        echo json_encode(["success" => true, "message" => "User added successfully. Credentials sent!"]);
    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback on error
        echo json_encode(["success" => false, "message" => "Failed to add user: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

// Function to generate a random password
function generateRandomPassword($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($characters), 0, $length);
}

// Function to send invite email
function sendInviteEmail($email, $username, $plainPassword, $inviteLink) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Your email address
        $mail->Password = 'your-email-password'; // Your email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
        $mail->Port = 587;

        // Email headers
        $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
        $mail->addAddress($email);
        $mail->Subject = 'Your Account Credentials & Invite Link';

        // Email body
        $body = "<h2>Welcome, $username!</h2>";
        $body .= "<p>You have been added to our platform. Here are your login credentials:</p>";
        $body .= "<p><strong>Username:</strong> $email</p>";
        $body .= "<p><strong>Password:</strong> $plainPassword</p>";
        $body .= "<p><strong>Login Here:</strong> <a href='https://yourwebsite.com/login.php'>Login</a></p>";
        $body .= "<p>To activate your account, please click the link below:</p>";
        $body .= "<p><a href='$inviteLink' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Activate Account</a></p>";
        $body .= "<p>If you did not request this, please ignore this email.</p>";

        $mail->isHTML(true);
        $mail->Body = $body;

        // Send email
        $mail->send();
    } catch (Exception $e) {
        error_log("Invite email failed: " . $mail->ErrorInfo);
    }
}
?>
