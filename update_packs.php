<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $packname = $_POST['packname'];
    $packprice = $_POST['packprice'];
    $packcolour = $_POST['packcolour'];
    $packdescription = $_POST['packdescription'];

    // Log the received data for debugging (optional)
    error_log("Received data: id=$id, packname=$packname, packprice=$packprice, packcolour=$packcolour, packdescription=$packdescription");

    // Sanitize the input data (to prevent SQL injection)
    $id = mysqli_real_escape_string($conn, $id);
    $packname = mysqli_real_escape_string($conn, $packname);
    $packprice = mysqli_real_escape_string($conn, $packprice);
    $packcolour = mysqli_real_escape_string($conn, $packcolour);
    $packdescription = mysqli_real_escape_string($conn, $packdescription);

    $sql = "UPDATE Pack SET PackName = '$packname', PackPrice = '$packprice', PackColour = '$packcolour', PackDescription = '$packdescription' WHERE Id = '$id'";

    // Log the SQL query for debugging (optional)
    error_log("SQL query: $sql");
    
    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // If the query is successful, return a success message
        echo "Pack updated successfully!";
    } else {
        // If the query fails, return an error message
        echo "Error updating pack: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
