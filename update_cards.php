<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $content = $_POST['content'];
    $isanswer = $_POST['isanswer'];
    $iscommunity = $_POST['iscommunity'];
    $packid = $_POST['packid'];  

    // Log the received data for debugging (optional)
    error_log("Received data: id=$id, content=$content, isanswer=$isanswer, iscommunity=$iscommunity, packid=$packid");

    // Sanitize the input data (to prevent SQL injection)
    $id = mysqli_real_escape_string($conn, $id);
    $content = mysqli_real_escape_string($conn, $content);
    $isanswer = mysqli_real_escape_string($conn, $isanswer);
    $iscommunity = mysqli_real_escape_string($conn, $iscommunity);
    $packid = mysqli_real_escape_string($conn, $packid); 

    // Prepare the SQL query to update the card in the database using PackId
    $sql = "UPDATE Card SET Content = '$content', IsAnswer = '$isanswer', IsCommunity = '$iscommunity', PackId = '$packid' WHERE Id = '$id'";

    // Log the SQL query for debugging (optional)
    error_log("SQL query: $sql");
    error_log("POST data: " . print_r($_POST, true));

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        echo "Card updated successfully!";
    } else {
        echo "Error updating card: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}


?>
