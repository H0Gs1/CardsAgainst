<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the POST data
    $content = $_POST['content'];  
    $isanswer = $_POST['isAnswer'];  
    $iscommunity = '1';  
    $packid = $_POST['packId']; 
    $createdAt = date("Y-m-d H:i:s");;
    $status = 'new';

    // Log received data for debugging
    error_log("Received data: content=$content, isanswer=$isanswer, iscommunity=$iscommunity, packid=$packid");

    // Sanitize input data to prevent SQL injection
    $content = mysqli_real_escape_string($conn, $content);
    $isanswer = mysqli_real_escape_string($conn, $isanswer);
    $iscommunity = mysqli_real_escape_string($conn, $iscommunity);
    $packid = mysqli_real_escape_string($conn, $packid);
    $createdAt = mysqli_real_escape_string($conn, $createdAt);
    $status = mysqli_real_escape_string($conn, $status);
    // Log the sanitized data
    error_log("Sanitized data: content=$content, isanswer=$isanswer, iscommunity=$iscommunity, packid=$packid");

    // Prepare the SQL query to insert a new card
    $sql = "INSERT INTO Card (Content, IsAnswer, IsCommunity, PackId, CreatedAt, Status) 
            VALUES ('$content', '$isanswer', '$iscommunity', '$packid', '$createdAt', '$status')";  

    // Log the SQL query for debugging
    error_log("SQL query: $sql");

    // Execute the SQL query
    if (mysqli_query($conn, $sql)) {
        echo "Card added successfully!";
    } else {
        echo "Error adding card: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>


