<?php
session_start();
require_once("config/database_connection.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET verification_token=?, reset_token_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?");
        $stmt->execute([$token, $email]);

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sachinvvin@gmail.com';
            $mail->Password = 'oawtojazziscrnrq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('nutrizone@gmail.com', 'Nutrizone');
            $mail->addAddress($email);
            $val = $_SERVER['HTTP_REFERER'];
            $pos = strripos($val, '/');
            $val = substr($val, 0, $pos);

            $resetLink = $val . "/resetPassword.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "<p>Click the link below to reset your password:</p>
                           <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Reset link sent to your email.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Email could not be sent.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }
    exit;
}
include("pages/style.php");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
</head>

<body>



    <div class="login-signup">
        <div class="form-container">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <div id="alertMessage" style="display: none;"></div>

            <form id="forgotPasswordForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-login w-100">Send Reset Link</button>
            </form>
        </div>
    </div>

    <script>
        const alertMessage = $('#alertMessage');

        $("#forgotPasswordForm").submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: "forgetPassword.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    showAlert(response.message, 'success');
                }
            });
        });
        function showAlert(message, type) {
            alertMessage.text(message)
                .removeClass('alert-danger alert-success')
                .addClass('alert alert-' + type)
                .show();
        }
    </script>
    <style>
        .login-signup {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</body>

</html>