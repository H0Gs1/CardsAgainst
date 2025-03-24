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

$stmt = $conn->prepare("SELECT GameId, Round FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($gameId, $round);
$stmt->fetch();

if ($gameId === null) {
    echo "data: Error: No Game ID found for the player.\n\n";
    flush();
    exit;
}

$stmt->free_result();

$stmt1 = $conn->prepare("SELECT COUNT(*) AS PlayersWithValue FROM VotedCard WHERE GameId = ? AND Round = ?");
$stmt1->bind_param("ii", $gameId, $round);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = $result1->fetch_assoc();
$playersWithValue = $row1['PlayersWithValue'];
$stmt1->close();

// Count total players
$stmt2 = $conn->prepare("SELECT COUNT(*) AS TotalPlayers FROM PlayerTable WHERE SessionId = ?");
$stmt2->bind_param("i", $gameId);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();
$totalPlayers = $row2['TotalPlayers'];
$stmt2->close();

if ($playersWithValue === $totalPlayers) {
    echo true;
} else {
    echo false;
}


?>