<?php
// Database connection
include_once("db_connecter.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['Content'];
    if ($content === null) {
        echo 'Content cannot be blank';
        exit;
    }
    $isAnswer = $_POST['IsAnswer'];
    $packId = $_POST['PackId'];
    // $userId = $_SESSION['user_id'];

    // Default values for non-user-modifiable columns
    $isCommunity = 1; // Yes
    $createdAt = date('Y-m-d H:i:s'); // Current timestamp
    $likes = 0;
    $status = "new";

    // SQL to insert the card into the database
    $sql = "INSERT INTO Card (Content, IsAnswer, IsCommunity, PackId, CreatedAt, Likes, Status, UserId) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiisisi", $content, $isAnswer, $isCommunity, $packId, $createdAt, $likes, $status, $_SESSION["UserId"]);

    if ($stmt->execute()) {
        include_once('community_submit_success.html');
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
