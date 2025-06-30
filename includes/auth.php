<?php
session_start();
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
  // Return JSON response for AJAX
  header('Content-Type: application/json');
  echo json_encode(['redirect' => 'index.php']);
  exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require_once("../config/database_connection.php");

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
    $val = $_SERVER['HTTP_REFERER'];
    $pos = strripos($val, '/');
    $val = substr($val, 0, $pos);
    $verificationLink = $val . "/verify_email.php?email=$email&token=$token";
    $mail->Body = "
            <h1>Verify Your Email Address</h1>
            <p>Please click the link below to verify your email address:</p>
            <a href='$verificationLink'>$verificationLink</a>
        ";

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  $response = ['success' => false, 'message' => '', 'redirect' => ''];
  
  $action = $_POST['action'];
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if ($action === 'signup') {
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'user';

    if ($password !== $confirm_password) {
      $response['message'] = 'Passwords do not match.';
    } else {
      try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
          $response['message'] = 'Email already exists.';
        } else {
          $hashed_password = password_hash($password, PASSWORD_BCRYPT);
          $verification_token = bin2hex(random_bytes(16));
          $stmt = $pdo->prepare("INSERT INTO users (email, password, role, verification_token, is_verified) VALUES (?, ?, ?, ?, 0)");
          $stmt->execute([$email, $hashed_password, $role, $verification_token]);

          if (sendVerificationEmail($email, $verification_token)) {
            $response['success'] = true;
            $response['message'] = 'Signup successful! Please check your email to verify your account.';
          } else {
            $response['message'] = 'Signup successful but failed to send verification email. Please contact support.';
          }
        }
      } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
      }
    }
  } elseif ($action === 'login') {
    try {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 0) {
          $response['message'] = 'Your account is not verified. Please check your email.';
        } else {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['email'] = $user['email'];
          $_SESSION['role'] = $user['role'];
          $response['success'] = true;
          $response['redirect'] = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'index.php';
        }
      } else {
        $response['message'] = 'Invalid email or password.';
      }
    } catch (PDOException $e) {
      $response['message'] = 'Error: ' . $e->getMessage();
    }
  }
  
  echo json_encode($response);
  exit;
}
?>