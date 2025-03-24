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

        // All players have voted, count votes
        $stmtCheck = $conn->prepare("
            WITH Counts AS (
                SELECT CardId, COUNT(*) AS occurrence_count
                FROM VotedCard
                WHERE GameId = ? AND Round = ?
                GROUP BY CardId
            ),
            MaxCount AS (
                SELECT MAX(occurrence_count) AS max_occurrence
                FROM Counts
            )
            SELECT c.CardId, c.occurrence_count
            FROM Counts c
            JOIN MaxCount m ON c.occurrence_count = m.max_occurrence;
        ");
        $stmtCheck->bind_param("ii", $gameId, $round);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows == 1) {
            // Single winner
            $stmtCheck->bind_result($winnerCardId, $count);
            $stmtCheck->fetch();

            // Get winner details
            $stmtWinner = $conn->prepare("SELECT PlayerId, CardContent FROM Action WHERE GameId = ? AND CardId = ?");
            $stmtWinner->bind_param("ii", $gameId, $winnerCardId);
            $stmtWinner->execute();
            $stmtWinner->bind_result($winnerId, $winnerCard);
            $stmtWinner->fetch();
            $stmtWinner->free_result();

            // Update winner's score
            $stmtPoints = $conn->prepare("UPDATE PlayerTable SET Score = 1 WHERE UserId = ? AND SessionId = ?");
            $stmtPoints->bind_param("ii", $winnerId, $gameId);
            $stmtPoints->execute();

            // $stmtCard = $conn->prepare("SELECT COUNT(*) as Count FROM Action WHERE CardId IS NULL AND GameId = ?");
            // $stmtCard->bind_param("i", $gameId);
            // $stmtCard->execute();
            // $stmtCard->bind_result($count2);
            // $stmtCard->fetch();
            // if ($count2 == 0) {
            //     $stmtCard->free_result();
            //     // Update round
            //     $stmtRoundUpdate = $conn->prepare("
            //         UPDATE Action 
            //         SET Round = Round + 1, CardState = NULL, CardId = NULL, CardContent = NULL 
            //         WHERE GameId = ?  
            //     ");
            //     $stmtRoundUpdate->bind_param("i", $gameId);
            //     $stmtRoundUpdate->execute();
            //     $stmtRoundUpdate->free_result();
            // }

            // Update round
            // $stmtRoundUpdate = $conn->prepare("
            //     UPDATE Action 
            //     SET Round = Round + 1, CardState = NULL, CardId = NULL, CardContent = NULL 
            //     WHERE GameId = ?  
            // ");
            // $stmtRoundUpdate->bind_param("i", $gameId);
            // $stmtRoundUpdate->execute();

            // Send winner info
            echo $winnerCard;
        } else {
            // $stmtCard = $conn->prepare("SELECT COUNT(*) as Count FROM Action WHERE CardId IS NULL AND GameId = ?");
            // $stmtCard->bind_param("i", $gameId);
            // $stmtCard->execute();
            // $stmtCard->bind_result($count2);
            // $stmtCard->fetch();
            // if ($count2 == 0) {
            //     $stmtCard->free_result();
            //     // Update round
            //     $stmtRoundUpdate = $conn->prepare("
            //         UPDATE Action 
            //         SET Round = Round + 1, CardState = NULL, CardId = NULL, CardContent = NULL 
            //         WHERE GameId = ?  
            //     ");
            //     $stmtRoundUpdate->bind_param("i", $gameId);
            //     $stmtRoundUpdate->execute();
            //     $stmtRoundUpdate->free_result();
            // }

            echo "IT IS A TIE" . $stmtCheck->num_rows;
        }
?>