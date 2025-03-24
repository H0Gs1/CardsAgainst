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

$stmtCount = $conn->prepare("SELECT COUNT(*) AS Count FROM PlayerTable WHERE SessionId = ?");
$stmtCount->bind_param("i", $gameId);
$stmtCount->execute();
$stmtCount->bind_result($count);
$stmtCount->fetch();
$stmtCount->free_result();

$stmtScore = $conn->prepare("UPDATE PlayerTable SET FinalScore = Score + FinalScore WHERE SessionId = ? AND UserId = ?");
$stmtScore->bind_param("ii", $gameId, $userId);
$stmtScore->execute();
$stmtScore->free_result();

$stmtScore1 = $conn->prepare("UPDATE PlayerTable SET Score = 0 WHERE SessionId = ? AND UserId = ?");
$stmtScore1->bind_param("ii", $gameId, $userId);
$stmtScore1->execute();
$stmtScore1->free_result();

$stmtOver = $conn->prepare("SELECT MaxScore, FinalScore
                                    FROM GameSession
                                    INNER JOIN PlayerTable ON GameSession.Id = PlayerTable.SessionId 
                                    WHERE GameSession.Id =  ? AND PlayerTable.UserId = ? ;");
$stmtOver->bind_param("ii", $gameId,$userId);
$stmtOver->execute();
$stmtOver->bind_result($max,$score);
$stmtOver->fetch();
if ($max === $score) {
    $stmtOver->free_result();
    $stmtScore2 = $conn->prepare("UPDATE Game SET Status = 'over' WHERE GameId = ?;");
    $stmtScore2->bind_param("i", $gameId);
    $stmtScore2->execute();
    $stmtScore2->free_result();
}

echo"ight";

?>