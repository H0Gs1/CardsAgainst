<?php
// Include your database connection here
include('db_connection.php');

// Query to get the pack names and their IDs
$sql = "SELECT Id, PackName FROM Pack";
$result = mysqli_query($conn, $sql);

$packs = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $packs[] = $row;
    }
}

// Return the result as JSON
echo json_encode($packs);
?>
