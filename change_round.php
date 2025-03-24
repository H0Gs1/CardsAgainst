<?php
ob_start(); // Start output buffering
include_once('db_connecter.php');
session_start();

// Check if user is logged in
if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
    $userId = $_SESSION["user_id"];
} else {
    echo "Error: User not logged in.";
    exit;
}

$stmt1 = $conn->prepare("SELECT GameId FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
$stmt1->bind_param("i", $userId);
$stmt1->execute();
$stmt1->bind_result($gameId,);
$stmt1->fetch();
if ($gameId === null) {
    echo "No Game ID found for the player.";
    exit;
}
$stmt1->free_result();

$stmt2 = $conn->prepare("SELECT Count(*) as EmptyCollumn FROM Action WHERE GameId = ? AND CardId IS NULL;");
$stmt2->bind_param("i", $gameId);
$stmt2->execute();
$stmt2->bind_result($roundReady,);
$stmt2->fetch();
$stmt2->free_result();

$stmt3 = $conn->prepare("SELECT Count(*) as TotalPlayers FROM PlayerTable WHERE SessionId = ?;");
$stmt3->bind_param("i", $gameId);
$stmt3->execute();
$stmt3->bind_result($totalPlayers,);
$stmt3->fetch();
$stmt3->free_result();

if ($roundReady === $totalPlayers) {
    echo"Ready for the next round";
} else {
    echo "Not ready for the next round";
}

?>