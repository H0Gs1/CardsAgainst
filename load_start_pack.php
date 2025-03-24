<?php
// Database connection
include_once("db_connecter.php");
session_start();

if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
    $playerId = $_SESSION["user_id"];
}

$sql = "SELECT Purchase.PackId, Pack.PackName 
        FROM Purchase 
        INNER JOIN Pack ON Purchase.PackId = Pack.Id 
        WHERE Purchase.UserId = ? 
        ORDER BY Purchase.PackId ASC";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind the playerId to the prepared statement
$stmt->bind_param("i", $playerId); // "i" for integer (UserId is an integer)

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['PackId'] . '">' . htmlspecialchars($row['PackName']) . '</option>';
    }
} else {
    echo '<option value="">No Packs Available</option>';
}
?>
