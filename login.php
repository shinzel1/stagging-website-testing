<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
include("pages/style.php");
$host = 'localhost';
$db_name = 'nutrizione';
$username = 'root';
$password = '';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

function sendVerificationEmail($email, $token)
{
  $mail = new PHPMailer(true);

  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sachinvvin@gmail.com';
    $mail->Password = 'oawtojazziscrnrq';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('nutrizone@gmail.com', 'Nutrizone');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Email Address';
    $verificationLink = "http://localhost/nutrizone/verify_email.php?email=$email&token=$token"; // Replace with your domain
    $mail->Body = "
            <h1>Verify Your Email Address</h1>
            <p>Please click the link below to verify your email address:</p>
            <a href='$verificationLink'>$verificationLink</a>
        ";

    $mail->send();
  } catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
  }
}





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'];
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if ($action === 'signup') {
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'user'; // Default role is 'user'

    if ($password !== $confirm_password) {
      $error = 'Passwords do not match.';
    } else {
      try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
          $error = 'Email already exists.';
        } else {
          $hashed_password = password_hash($password, PASSWORD_BCRYPT);
          $verification_token = bin2hex(random_bytes(16)); // Generate a unique token
          $stmt = $pdo->prepare("INSERT INTO users (email, password, role, verification_token, is_verified) VALUES (?, ?, ?, ?, 0)");
          $stmt->execute([$email, $hashed_password, $role, $verification_token]);

          // Send verification email
          sendVerificationEmail($email, $verification_token);
          $success = 'Signup successful! Please check your email to verify your account.';
        }
      } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
      }
    }
  } elseif ($action === 'login') {
    try {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 0) { // Check if the user is verified
          $error = 'Your account is not verified. Please check your email.';
        } else {
          session_start();
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['email'] = $user['email'];
          $_SESSION['role'] = $user['role'];
          if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
          } else {
            header('Location: index.php');
          }
          exit;
        }
      } else {
        $error = 'Invalid email or password.';
      }
    } catch (PDOException $e) {
      $error = 'Error: ' . $e->getMessage();
    }
  }
}

?>



<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <title></title>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<body>


  <div class="login-signup">
    <div class="form-container">
      <h2 class="text-center mb-4">Login or Signup</h2>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" id="authForm">
        <input type="hidden" name="action" id="action" value="login">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3" id="confirmPasswordField" style="display: none;">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
        <button type="button" id="toggleForm" class="btn btn-link w-100 mt-2">Don't have an account? Signup</button>
      </form>
    </div>
  </div>

  <script>
    const authForm = document.getElementById('authForm');
    const actionInput = document.getElementById('action');
    const toggleFormBtn = document.getElementById('toggleForm');
    const confirmPasswordField = document.getElementById('confirmPasswordField');

    toggleFormBtn.addEventListener('click', () => {
      if (actionInput.value === 'login') {
        actionInput.value = 'signup';
        confirmPasswordField.style.display = 'block';
        toggleFormBtn.textContent = 'Already have an account? Login';
        authForm.querySelector('button[type="submit"]').textContent = 'Signup';
      } else {
        actionInput.value = 'login';
        confirmPasswordField.style.display = 'none';
        toggleFormBtn.textContent = "Don't have an account? Signup";
        authForm.querySelector('button[type="submit"]').textContent = 'Login';
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