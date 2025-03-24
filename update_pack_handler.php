<?php
include 'db_connecter.php'; // Replace with your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize input data
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $packname = mysqli_real_escape_string($conn, $_POST['packname']);
    $packprice = mysqli_real_escape_string($conn, $_POST['packprice']);
    $packcolour = mysqli_real_escape_string($conn, $_POST['packcolour']);
    $packdescription = mysqli_real_escape_string($conn, $_POST['packdescription']);

    // Construct the SQL update query
    $sql = "UPDATE Pack SET PackName = '$packname', PackPrice = '$packprice', PackColour = '$packcolour', PackDescription = '$packdescription' WHERE Id = '$id'";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        echo "Pack updated successfully!";
    } else {
        echo "Error updating pack: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>