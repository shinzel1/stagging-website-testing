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
include("pages/style.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Login/Signup</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <script src="assets/js/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="login-signup">
    <div class="form-container">
      <h2 class="text-center mb-4">Login or Signup</h2>
      <div id="alertMessage" style="display: none;"></div>

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
        <a href="forgetPassword.php">Forget Password</a>
        <button type="submit" class="btn btn-login w-100">Login</button>
        <button type="button" id="toggleForm" class="btn btn-link w-100 mt-2">Don't have an account? Signup</button>
      </form>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      const authForm = $('#authForm');
      const actionInput = $('#action');
      const toggleFormBtn = $('#toggleForm');
      const confirmPasswordField = $('#confirmPasswordField');
      const alertMessage = $('#alertMessage');

      // Toggle between login and signup forms
      toggleFormBtn.on('click', function () {
        if (actionInput.val() === 'login') {
          actionInput.val('signup');
          confirmPasswordField.show();
          toggleFormBtn.text('Already have an account? Login');
          authForm.find('button[type="submit"]').text('Signup')
            .removeClass('btn-login').addClass('btn-signup');
        } else {
          actionInput.val('login');
          confirmPasswordField.hide();
          toggleFormBtn.text("Don't have an account? Signup");
          authForm.find('button[type="submit"]').text('Login')
            .removeClass('btn-signup').addClass('btn-login');
        }
      });

      // Handle form submission
      authForm.on('submit', function (e) {
        e.preventDefault();

        // Clear previous messages
        alertMessage.hide().removeClass('alert-danger alert-success').text('');

        // Basic client-side validation
        if (actionInput.val() === 'signup' && $('#password').val() !== $('#confirm_password').val()) {
          showAlert('Passwords do not match.', 'danger');
          return;
        }

        $.ajax({
          url: 'includes/auth.php',
          method: 'POST',
          data: authForm.serialize(),
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              if (response.redirect) {
                window.location.href = response.redirect;
              } else {
                showAlert(response.message, 'success');
              }
            } else {
              showAlert(response.message, 'danger');
            }
          },
          error: function (xhr, status, error) {
            showAlert('An error occurred. Please try again.', 'danger');
            console.error(error);
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