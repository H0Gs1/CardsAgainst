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

 $stmtUpdate = $conn->prepare("UPDATE PlayerTable SET IsReady = 1 WHERE UserId = ? AND SessionId = ?");
 $stmtUpdate->bind_param("ii", $userId,$gameId);
 $stmtUpdate->execute();
 $stmtUpdate->free_result();

 $stmt2 = $conn->prepare("SELECT COUNT(*) as Count FROM PlayerTable WHERE IsReady = 0 AND SessionId = ?;");
 $stmt2->bind_param("i", $gameId);
 $stmt2->execute();
 $stmt2->bind_result($num);

 if ($num == 0) {
    echo true;
 } else {
    echo false;
 }
 
?>