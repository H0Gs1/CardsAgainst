<?php
include_once('db_connecter.php');
include 'PasswordUtil.php';

$response = ["success" => false, "error" => ""]; // Unified response structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // List of required fields except userrole
    $requiredFields = ['id', 'username', 'password', 'email'];
    $data = [];

    // Validate input fields (excluding userrole from empty check)
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') { // Check if field is not set or empty
            $response["error"] = ucfirst($field) . " is required.";
            echo json_encode($response);
            exit;
        }
        $data[$field] = mysqli_real_escape_string($conn, $_POST[$field]);
    }

    // Get userrole, use default value 'user' if not provided
    $data['userrole'] = isset($_POST['userrole']) ? mysqli_real_escape_string($conn, $_POST['userrole']) : 'user';

    // Validate email using regex
    $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    if (!preg_match($emailRegex, $data['email'])) {
        $response["error"] = "{$data['email']} is an invalid email format.";
        echo json_encode($response);
        exit;
    }

    // Check if email is unique
    $checkEmailQuery = "SELECT 1 FROM Account WHERE Email = '{$data['email']}' AND Id != '{$data['id']}'";
    if (mysqli_num_rows(mysqli_query($conn, $checkEmailQuery)) > 0) {
        $response["error"] = "{$data['email']} is already in use.";
        echo json_encode($response);
        exit;
    }

    // Validate password
    $passwordValidation = validatePassword($data['password']);
    if ($passwordValidation !== true) {
        $response["error"] = $passwordValidation;
        echo json_encode($response);
        exit;
    }

    // Hash password and update account
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE Account SET 
        UserName = '{$data['username']}', 
        Password = '{$data['password']}', 
        Email = '{$data['email']}', 
        UserRole = '{$data['userrole']}' 
        WHERE Id = '{$data['id']}'";

    if (mysqli_query($conn, $updateQuery)) {
        $response["success"] = true;
    } else {
        $response["error"] = "Error updating account: " . mysqli_error($conn);
    }

    echo json_encode($response);
    mysqli_close($conn);
}
?>
