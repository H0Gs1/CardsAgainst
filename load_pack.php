<?php
// Database connection
include_once("db_connecter.php");

$sql = "SELECT Id, PackName FROM Pack";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['Id'] . '">' . htmlspecialchars($row['PackName']) . '</option>';
    }
} else {
    echo '<option value="">No Packs Available</option>';
}

$conn->close();
?>
