<?php
include_once('db_connecter.php');
header('Content-Type: application/json');

if (isset($_POST['id']) && isset($_POST['status']) && isset($_POST['content'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $status = htmlspecialchars($_POST['status']);
    $content = htmlspecialchars($_POST['content']);

    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Update the card status
    $sql = "UPDATE Card SET Status = ? WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $status, $id);
        if ($stmt->execute()) {
            // Fetch UserId
            $userQuery = "
            SELECT Card.UserId, Pack.PackName 
            FROM Card 
            INNER JOIN Pack ON Card.PackId = Pack.Id 
            WHERE Card.Id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param('i', $id);
        $userStmt->execute();
        $userStmt->bind_result($userId, $packName);
        $userStmt->fetch();
        $userStmt->close();
        

            if (!empty($userId)) {
                // Fetch Email

                $emailQuery = "SELECT Email, UserName FROM Account WHERE Id = ?";
                $emailStmt = $conn->prepare($emailQuery);
                
                if ($emailStmt) {
                    $emailStmt->bind_param("i", $userId);
                    $emailStmt->execute();
                    $result = $emailStmt->get_result();
                
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $email = $row['Email'];
                        $username = $row['UserName'];
                    } else {
                        $email = null;
                        $username = null;
                    }
                
                    $emailStmt->close();
                } else {
                    die("Failed to prepare email query: " . $conn->error);
                }

                if (!empty($email)) {
                    // Load email template
                    $templatePath = __DIR__ . "/email-card-answ.html";
                    if (file_exists($templatePath)) {
                        $template = file_get_contents($templatePath);

                        // Replace placeholders
                        $template = str_replace('[UserName]', htmlspecialchars($username), $template);
                        $template = str_replace('[content]', htmlspecialchars($content), $template);
                        $template = str_replace('[packname]', htmlspecialchars($packName), $template);
                        $template = str_replace('[STATUS]', htmlspecialchars($status), $template);

                        $mail = require __DIR__ . "/Mailer.php";
                        $mail->setFrom("mbsinternex@gmail.com");
                        $mail->addAddress($email);
                        $mail->Subject = "Update on Your Card Status";
                        $mail->Body = $template;
                        $mail->isHTML(true); // Ensure email is sent as HTML

                        try {
                            $mail->send();
                            echo json_encode(['success' => true, 'message' => 'Card updated and email sent successfully.']);
                        } catch (Exception $e) {
                            echo json_encode(['success' => false, 'message' => "Mailer error: {$mail->ErrorInfo}"]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Email template not found.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email not found for the user.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
$conn->close();
?>
