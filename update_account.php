<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // User ID
    $field = $_POST['field'] ?? null; // Field to update (username, email, etc.)
    $value = $_POST['value'] ?? null; // New value

    if (!$id || !$field || !$value) {
        echo json_encode(["success" => false, "error" => "Invalid input."]);
        exit;
    }

    // Sanitize inputs
    $id = mysqli_real_escape_string($conn, $id);
    $field = mysqli_real_escape_string($conn, $field);
    $value = mysqli_real_escape_string($conn, $value);

    // Only allow updating username or email
    if ($field !== 'username' && $field !== 'email') {
        echo json_encode(["success" => false, "error" => "Invalid field."]);
        exit;
    }

    // Check if username already exists (only for username updates)
    if ($field === 'username') {
        $checkQuery = "SELECT * FROM Account WHERE UserName = '$value' AND Id != '$id'";
        $result = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(["success" => false, "error" => "Username already in use."]);
            exit;
        }
    }

    // Update the specified field
    $dbField = ($field === 'username') ? 'UserName' : 'Email';
    $sql = "UPDATE Account SET $dbField = '$value' WHERE Id = '$id'";
    error_log("SQL Query: $sql");
    error_log("Field received: $field");

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update database."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
