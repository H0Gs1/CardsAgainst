<?php
// Database connection
include_once("db_connecter.php");

if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
    $playerId = $_SESSION["user_id"];
}

$stmt1 = $conn->prepare("SELECT SessionId FROM PlayerTable WHERE UserId = ? ORDER BY SessionId DESC LIMIT 1;");
$stmt1->bind_param("i", $playerId);
$stmt1->execute();
$stmt1->bind_result($gameId);
$stmt1->fetch();
if ($gameId === null) {
    echo "No Game ID found for the player.";
    exit;
}
$stmt1->free_result();

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT Player FROM PlayerTable WHERE SessionId = ?");

// Bind the playerId to the prepared statement
$stmt->bind_param("i", $gameId); // "i" for integer (UserId is an integer)

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<li>' . $row['Player'] . '</li>';
    }
} else {
    echo '<li>No Players</li>';
}
?>