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
    echo json_encode(["error" => "No Game ID found for the player."]);
    exit;
}
$stmt1->free_result();

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT Player, FinalScore AS Points FROM PlayerTable WHERE SessionId = ?");

// Bind the gameId to the prepared statement
$stmt->bind_param("i", $gameId); // "i" for integer (SessionId is an integer)

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Create an array to store players' data
$players = [];

// If there are rows in the result, loop through them and add to the array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add player data to the array
        $players[] = [
            'name' => $row['Player'],
            'points' => $row['Points']
        ];
    }

    // Output the players as a JSON response
    echo json_encode($players);
} else {
    // If no players, output an empty array or a message
    echo json_encode(["message" => "No Players"]);
}
?>
