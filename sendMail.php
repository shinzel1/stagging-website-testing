<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server (e.g., smtp.mailtrap.io for testing)
    $mail->SMTPAuth = true;
    $mail->Username = 'sachinvvin@gmail.com'; // Your email address
    $mail->Password = 'oawtojazziscrnrq'; // Your email password or app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ENCRYPTION_SMTPS Use 'tls' if your server doesn't support 'ssl'
    $mail->Port = 587; // 587 for 'tls' 465

    // Email Settings
    $mail->setFrom('sachinvvin@gmail.com', 'Your Name'); // Sender's email and name
    $mail->addAddress('crowndevour@gmail.com', 'crown'); // Add recipient
    // $mail->addReplyTo('your_email@gmail.com', 'Your Name'); // Reply-to email (optional)

    // Add CC or BCC (optional)
    // $mail->addCC('cc_email@example.com');
    // $mail->addBCC('bcc_email@example.com');

    // Attachments (optional)
    // $mail->addAttachment('/file.pdf', 'OptionalFileName.pdf');

    // Email Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'new Inquiry';
    $mail->Body = '<h1>Hello!</h1><p>This is a test email sent using <b>PHPMailer</b>.</p>';
    // $mail->AltBody = 'Hello! This is a test email sent using PHPMailer.';

    // Send Email
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}
?>
