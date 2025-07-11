<?php
session_start();
require_once("config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token=? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE users SET password=?, verification_token=NULL, reset_token_expiry=NULL WHERE verification_token=?");
        $stmt->execute([$new_password, $token]);
        echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token.']);
    }
    exit;
}
include("pages/style.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
</head>

<body>

    <div class="login-signup">
        <div class="form-container">
            <h2 class="text-center mb-4">Reset Password</h2>
            <div id="alertMessage" style="display: none;"></div>

            <form id="resetPasswordForm">
                <input type="hidden" id="token" name="token" value="">

                <div class="mb-3">
                    <label for="password" class="form-label">New Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-login w-100 mb-3">Reset Password</button>

            </form>
            <a href="login.php" class="float-end">Login</a>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const alertMessage = $('#alertMessage');

            $("#token").val(token);

            $("#resetPasswordForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "resetPassword.php",
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
        });
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