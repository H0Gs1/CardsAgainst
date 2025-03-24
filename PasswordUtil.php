<?php
// password_utils.php
function validatePassword($password) {
    if (strlen($password) <= 8) {
        return "Password must be longer than 8 characters.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/\d/', $password)) {
        return "Password must contain at least one number.";
    }
    return true;
}
?>
