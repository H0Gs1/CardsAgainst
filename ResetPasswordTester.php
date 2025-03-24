<?php

ob_start(); // Start output buffering
include 'PasswordUtil.php'; 
$validPassword = false;

$mysqli = require __DIR__ ."/db_connecter.php";

$token = $_GET["token"];
// $token_hash = hash("sha256", $token); // Optional if using hashing, but make sure it's consistent

// Log the token and hash for debugging
error_log("Test token: $token");
// error_log("Computed token hash: $token_hash");

$sql = "SELECT Password, ResetTokenExpiresAt, Id FROM Account WHERE ResetTokenHash = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token); // Bind the token as a parameter
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["ResetTokenExpiresAt"]) <= time()) {
    die("token has expired");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = filter_input(INPUT_POST, "password1", FILTER_SANITIZE_SPECIAL_CHARS);

    // Ensure passwords match
    if ($_POST["password1"] !== $_POST["password2"]) {
        $error = "Passwords must match.";
    } elseif (empty($password)) {
        $error = "Password cannot be empty.";
    } else {
        // Validate password strength
        $validationResult = validatePassword($password);
        if ($validationResult !== true) {
            $error = $validationResult;
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Update the database
            $updateSql = "UPDATE Account SET Password = ?, ResetTokenHash = NULL, ResetTokenExpiresAt = NULL WHERE ResetTokenHash = ?";
            $stmt = $mysqli->prepare($updateSql);
            $stmt->bind_param("ss", $passwordHash, $token); // Bind both password hash and token

            if ($stmt->execute()) {
                header("Location: ResetPasswordSuccess.html");
                exit();
            } else {
                $error = "Error updating password. Please try again.";
            }
        }
    }
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <script src="scripts.js" defer></script>
    <title>Forgot Password Page</title>
    <style>
        .forgot-container {
            border-radius: 1rem;
            width: 48rem;
            height: 24rem;
            background-color: var(--bs-primary);
            overflow-x: hidden;
            padding-top: 5rem;
            padding-right: 2rem;
            padding-bottom: 5rem;
            padding-left: 2rem;
            border: 0.1rem solid black;
            margin-top: 2rem;
            margin-bottom: 2rem;
            margin-right: 2rem;
            margin-left: 2rem;
        }
    </style>
</head>

<body
    style="background-image: url('assets/bootstrap-5.3.3/Images/friends_cards.png'); background-repeat: no-repeat; background-size:cover;">
    <form method="POST" style="color: white">
        <div class="row">
            <div class="col sm 2"></div>
            <div class="col-sm-8">
                <div class="col-sm-12 forgot-container">
                    <div class="row">
                        <h1>New Password</h1>
                        <input type="password" name="password1" id="password1" required>
                    </div>
                    <div class="row">
                        <h1>Confirm Password</h1>
                        <input type="password" name="password2" id="password2" required>
                    </div>
                    <div class="row">
                        <button id="Confirm-Password" type="submit">Confirm New Password</button>
                    </div>
                    <?php if (!empty($error)) : ?>
                        <div class="row" style="color: red;">
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col sm 2"></div>
        </div>
    </form>

</body>

</html>
