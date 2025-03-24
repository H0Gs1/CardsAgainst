<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

try {
    // Enable SMTP debugging for detailed output (comment out in production)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "mbsinternex@gmail.com"; // Must match the sender
    $mail->Password = "rbgw xbat pwmj zcsy"; // Use an app-specific password for Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom("mbsinternex@gmail.com"); // Align with `Username`

    $mail->isHTML(true); // Use HTML emails
} catch (Exception $e) {
    error_log("Mailer initialization failed: " . $e->getMessage());
    throw new Exception("Mailer setup failed.");
}

return $mail;
