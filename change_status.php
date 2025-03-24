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
if (isset($_POST["status"])) {
    $status = $_POST["status"];

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

    $stmt2 = $conn->prepare("SELECT Status FROM Game WHERE GameId = ?;");
    $stmt2->bind_param("i", $gameId);
    $stmt2->execute();
    $stmt2->bind_result($currentstatus);
    $stmt2->fetch();
    $stmt2->free_result();
    if ($currentstatus === $status) {
        echo "Success: already changed";
    }else{
        $stmt3 = $conn->prepare("UPDATE Game SET Status = ? WHERE GameId = ?");
        $stmt3->bind_param("si", $status, $gameId);
        if($stmt3->execute()){
            echo"Success: changed";
        } else{
            echo "couldn't change";
        }
    }
    
}