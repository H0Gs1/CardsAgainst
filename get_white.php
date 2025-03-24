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

 $stmtPack = $conn->prepare("SELECT PackId FROM GameSession WHERE Id = ? ORDER BY Id DESC LIMIT 1;");
 $stmtPack->bind_param("i", $gameId);
 $stmtPack->execute();
 $stmtPack->bind_result($packId);
 $stmtPack->fetch();
 $stmtPack->free_result();

 $stmtBlack = $conn->prepare("SELECT Id, Content FROM Card WHERE IsAnswer = 1 AND PackId = ? ORDER BY RAND() LIMIT 1");
 $stmtBlack->bind_param("i", $packId);
 $stmtBlack->execute();
 $stmtBlack->bind_result($cardId,$cardContent);
 $stmtBlack->fetch();
 $message = $cardContent;
 $stmtBlack->free_result();

 echo $message;
