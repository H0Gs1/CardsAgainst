<?php
$conn = require __DIR__ . "/db_connecter.php";

$email = $_POST["Email"];

// Retrieve the username
$userSql = "SELECT UserName FROM Account WHERE Email = ?";
$stmt = $conn->prepare($userSql);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

$username = null;
if ($userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $username = $row['UserName'];
    }
}

// Generate the reset token
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", strtotime("+10 hours"));

$sql = "UPDATE Account
        SET ResetTokenHash = ?,
            ResetTokenExpiresAt = ?
        WHERE Email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($conn->affected_rows > 0) {
    $mail = require __DIR__ . "/Mailer.php";

    // Prepare email
    $template = file_get_contents('email.html');
    $resetLink = "https://elitex.co.za/2024CardsAgainst/ResetPassword.php?token=" . $token_hash;
    $subject = "Password Reset";
    $bodyText = "Oops! Looks like someone (hopefully you) requested a password reset for your account. No worries, we’ve got your back! Click the link below to reset your password before your friends start making fun of you for being that person.";
    $buttonText = "Reset Your Password Now";

    // Replace placeholders in the template with actual data
    $template = str_replace('[UserName]', $username, $template);
    $template = str_replace('[Subject]', $subject, $template);
    $template = str_replace('[EmailBody]', $bodyText, $template);
    $template = str_replace('[RESET_LINK]', "<a href='$resetLink' class='btn btn-outline-secondary'>$buttonText</a>", $template);

    $mail->setFrom("mbsinternex@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Uh-oh! Did Your Goldfish Forget Your Password?";
    $mail->Body = $template;

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        exit;
    }
}

$response['status'] = 'success';
$response['message'] = 'Message sent! Please check your inbox.';
echo json_encode($response);
?>
