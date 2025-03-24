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

$stmtFind = $conn->prepare("SELECT CardContent FROM Action WHERE CardState = 'Chosen' AND GameId = ? AND PlayerId = ?");
$stmtFind->bind_param("ii", $gameId, $userId);
$stmtFind->execute();
$stmtFind->bind_result($card);
$stmtFind->fetch();
if ($card !== null) {
    $stmtFind->free_result();
    echo $card;
} else {
    $stmtFind->free_result();
    echo "No card";
}
?>