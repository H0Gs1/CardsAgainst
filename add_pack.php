<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data related to the pack
    $packname = $_POST['packname'];
    $packprice = $_POST['packprice'];
    $packcolour = $_POST['packcolour'];
    $packdescription = $_POST['packdescription'];

    // SQL query to insert data into the 'Pack' table
    $sql = "INSERT INTO Pack (PackName, PackPrice, PackColour, PackDescription) 
            VALUES ('$packname', '$packprice', '$packcolour', '$packdescription')";

    // Execute the query and handle success or failure
    if (mysqli_query($conn, $sql)) {
        echo "Pack added successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
