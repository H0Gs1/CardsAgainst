<?php
require 'vendor/autoload.php';
ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


echo '1';

$mail = new PHPMailer(true);

echo '1';
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mbsinternex@gmail.com';
    $mail->Password = 'rbgw xbat pwmj zcsy'; // Use an app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('mbsinternex@gmail.com');
    $mail->addAddress('recipient@example.com'); // Change to a valid recipient
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email.';

    $mail->send();
    echo 'Message sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
}