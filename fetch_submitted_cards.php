<?php
    ob_start(); // Start output buffering
    include_once('db_connecter.php');
    header('Content-Type: application/json');

    session_start();
    
    // Check if user is logged in
    if (isset($_SESSION["user_username"])) {
        $player = $_SESSION["user_username"];
        $userId = $_SESSION["user_id"];
    } else {
        echo "Error: User not logged in.";
        exit;
    }

    $stmt1 = $conn->prepare("SELECT GameId FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
    $stmt1->bind_param("i", $userId);
    $stmt1->execute();
    $stmt1->bind_result($gameId);
    $stmt1->fetch();
    if ($gameId === null) {
        echo "No Game ID found for the player.";
        exit;
    }
    $stmt1->free_result();
    $cards = [];
    $stmt2 = $conn->prepare("SELECT CardContent FROM Action WHERE GameId = ?");
    $stmt2->bind_param("i", $gameId);
    $stmt2->execute();
    $result = $stmt2->get_result();
    
    if ($result) { // Ensure result is valid
        while ($row = $result->fetch_assoc()) {
            $cards[] = $row; // Add each row to the array
        }
        $result->free(); // Free the result set
        echo json_encode($cards);
    } else {
        echo "Error fetching data: " . $conn->error;
    }
    

?>