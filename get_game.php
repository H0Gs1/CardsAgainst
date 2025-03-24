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

$message ="";

$stmt1 = $conn->prepare("SELECT SessionId FROM PlayerTable WHERE UserId = ? AND SessionId IS NOT NULL ORDER BY SessionId DESC LIMIT 1;");
$stmt1->bind_param("i", $userId);
$stmt1->execute();
$stmt1->bind_result($gameId);
$stmt1->fetch();
if ($gameId === null) {
    echo "No Game ID found for the player.";
    exit;
}


$stmt1->free_result();

$stmtCheck = $conn->prepare("SELECT COUNT(*) AS Players FROM PlayerTable WHERE SessionId = ?");
$stmtCheck->bind_param("i", $gameId);
$stmtCheck->execute();
$stmtCheck->bind_result($players);
$stmtCheck->fetch();
$stmtCheck->free_result();

$stmtMax = $conn->prepare("SELECT PlayerAmount FROM GameSession WHERE Id = ?");
$stmtMax->bind_param("i", $gameId);
$stmtMax->execute();
$stmtMax->bind_result($max);
$stmtMax->fetch();
$stmtMax->free_result();

if ($gameId !== null || $players === 0 || $players === 1 || $players === 2) {
    echo "Game over or player left. " . $gameId . " " . $players;
}

if ($players === $max) {
    $message = "Ready";
    $time = date("Y-m-d H:i:s");
    $stmtReady = $conn->prepare("UPDATE GameSession SET Status = 'Active', StartDateTime = '$time'  WHERE Id = ?");
    $stmtReady->bind_param("i", $gameId,);
    $stmtReady->execute();
} else {
    $message = "Not Yet";
}

echo $message;

?>

