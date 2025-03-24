<?php
ob_start();
include_once('db_connecter.php');
session_start();


// Check if user is logged in
if (!isset($_SESSION["user_username"])) {
    echo "data: Error: User not logged in\n\n";

    exit;
}

// Get session data
$userId = $_SESSION["user_id"];
$sessionId = session_id();

// Query to get Game ID
$stmt = $conn->prepare("SELECT GameId FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($gameId);
$stmt->fetch();
$stmt->close();

if (!$gameId) {
    echo "data: Error: No game found\n\n";
    exit;
}
    // Query to count players who have acted
    $stmt1 = $conn->prepare("SELECT COUNT(*) AS PlayersWithValue FROM Action WHERE GameId = ? AND CardState IS NOT NULL");
    $stmt1->bind_param("i", $gameId);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();
    $playersWithValue = $row1['PlayersWithValue'];
    $stmt1->close();

    // Query to count total players in the game
    $stmt2 = $conn->prepare("SELECT COUNT(*) AS TotalPlayers FROM PlayerTable WHERE SessionId = ?");
    $stmt2->bind_param("i", $gameId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();
    $totalPlayers = $row2['TotalPlayers'];
    $stmt2->close();

    echo ($playersWithValue == $totalPlayers) ? "Ready" : "Not Yet";