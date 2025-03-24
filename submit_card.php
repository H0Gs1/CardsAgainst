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

// Check if card content is set
$cardContent = isset($_POST['cardContent']) ? $_POST['cardContent'] : null;
if ($cardContent) {
    // Prepare query to fetch CardId from the database
    $stmt1 = $conn->prepare("SELECT Id FROM Card WHERE Content = ?");
    $stmt1->bind_param("s", $cardContent);
    $stmt1->execute();
    $stmt1->bind_result($cardId);
    $stmt1->fetch();
    $stmt1->free_result(); // Free the result after fetching

    // Set card state
    $cardState = "Chosen";

    // Update Action table with the chosen card details
    $stmt2 = $conn->prepare("UPDATE Action SET CardState = ?, CardId = ?, CardContent = ? WHERE PlayerId = ? ORDER BY Id DESC LIMIT 1;");
    $stmt2->bind_param("sisi", $cardState, $cardId, $cardContent, $userId);

    if ($stmt2->execute()) {
        // Query to get the GameId associated with the user
        $stmt3 = $conn->prepare("SELECT GameId FROM Action WHERE PlayerId = ? ORDER BY GameId DESC LIMIT 1;");
        $stmt3->bind_param("i", $userId);
        $stmt3->execute();
        $stmt3->bind_result($gameId);
        $stmt3->fetch();
        if ($gameId === null) {
            // Handle the case where gameId is null
            echo "No Game ID found for the player.";
            exit;
        }
        $stmt3->free_result(); // Free result after fetching

        $message = "Ready";

    } else {
        echo "Error: " . $stmt2->error;
        exit;
    }
} else {
    echo "Error: Card content is not set.";
    exit;
}

// Display the message
echo $message;
?>
