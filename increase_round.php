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

$stmt1 = $conn->prepare("SELECT GameId, Round FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
$stmt1->bind_param("i", $userId);
$stmt1->execute();
$stmt1->bind_result($gameId, $round);
$stmt1->fetch();
if ($gameId === null) {
    echo "No Game ID found for the player.";
    exit;
}
$stmt1->free_result();

$stmtRoundUpdate = $conn->prepare("
    UPDATE Action 
    SET Round = Round + 1, CardState = NULL, CardId = NULL, CardContent = NULL 
    WHERE GameId = ? AND PlayerId = ?;
");
$stmtRoundUpdate->bind_param("ii", $gameId, $userId);
$stmtRoundUpdate->execute();

$stmtBlack = $conn->prepare("UPDATE Black SET CardId = NULL, CardContent = NULL WHERE GameId = ?");
$stmtBlack->bind_param("i", $gameId) ;
$stmtBlack->execute();

$stmtReady = $conn->prepare("UPDATE PlayerTable SET IsReady = 0 WHERE SessionId = ?;");
$stmtReady->bind_param("i", $gameId) ;
$stmtReady->execute();

$stmtCard = $conn->prepare("SELECT COUNT(*) as Count FROM Action WHERE CardId IS NOT NULL AND GameId = ?");
$stmtCard->bind_param("i", $gameId);
$stmtCard->execute();
$stmtCard->bind_result($count2);
$stmtCard->fetch();
if ($count2 == 0) {
    echo true;
} else {
    echo false;
}

?>