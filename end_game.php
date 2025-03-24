<?php
include_once('db_connecter.php');

session_start();

header('Content-Type: application/json'); // Ensure JSON response
if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
    $userId = $_SESSION["user_id"];
} else {
    echo "Error: User not logged in.";
    exit;
}

$stmt1 = $conn->prepare("SELECT SessionId FROM PlayerTable WHERE UserId = ? ORDER BY SessionId DESC LIMIT 1;");
$stmt1->bind_param("i", $userId);
$stmt1->execute();
$stmt1->bind_result($gameId);
$stmt1->fetch();
if ($gameId === null) {
    echo "No Game ID found for the player.";
    exit;
}
 $stmt1->free_result();

 $time = date("Y-m-d H:i:s");

$stmtComplete = $conn->prepare("UPDATE GameSession SET Status = 'Complete', EndDateTime = '$time' WHERE ID = ?");
$stmtComplete->bind_param("i", $gameId);
$stmtComplete->execute();


echo"yay";