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



$cardContent = isset($_POST['cardContent']) ? $_POST['cardContent'] : null;
if ($cardContent) {
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



    $stmtContent = $conn->prepare("SELECT Id FROM Card WHERE Content = ?");
    $stmtContent->bind_param("s", $cardContent);
    $stmtContent->execute();
    $stmtContent->bind_result($cardId);
    $stmtContent->fetch();
    $stmtContent->free_result(); // Free the result after fetching

    $rUserId = (int)$userId;
    $scardContent = (string)$cardContent;

    $stmtInsert = $conn->prepare("INSERT INTO VotedCard (Round, PlayerId, CardId, GameId, CardContent) VALUES (?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("iiiis", $round, $rUserId, $cardId, $gameId, $cardContent);

    if ($stmtInsert->execute()) {
        echo "Voted";
    } else{
        echo "Cannot vote";
        exit;
    }

}else{
    echo "No contenet on card";
    exit;
}
?>