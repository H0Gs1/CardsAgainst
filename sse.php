<?php
ob_start();
include_once('db_connecter.php');
session_start();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Remove execution time limit
set_time_limit(0);

// Check if user is logged in
if (!isset($_SESSION["user_username"])) {
    echo "data: Error: User not logged in\n\n";
    flush();
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
    flush();
    exit;
}

function checkGameStatus($gameId) {
    global $conn;

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

    return ($playersWithValue == $totalPlayers) ? "Ready" : "Not Yet";
}

// Variable to track the previous game state
$previousState = "";

//while (true) {
    $currentState = checkGameStatus($gameId);

    // Only send the update if the state has changed
    if ($currentState !== $previousState) {
        $message =  $currentState;
        echo "data: $message\n\n";
        ob_flush();
        flush();

        // Update the previous state
        $previousState = $currentState;
    }

    // Throttle the loop
    sleep(1);
//}
