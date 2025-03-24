<?php
ob_start();
include_once('db_connecter.php');
session_start();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Remove execution time limit
set_time_limit(0);

// Helper function to send SSE messages

function sendMessage($event, $data) {
    echo "\n";
    $JSONData = json_encode(["event" => $event, "data"=> $data]);
    echo "data: {$JSONData}\n\n";
    flush();
}



// Check if user is logged in
if (!isset($_SESSION["user_username"])) {
    sendMessage("error", ["message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION["user_id"];

$stmt1 = $conn->prepare("SELECT SessionId FROM PlayerTable WHERE UserId = ? ORDER BY SessionId DESC LIMIT 1;");
$stmt1->bind_param("i", $userId);
$stmt1->execute();
$stmt1->bind_result($gameId);
$stmt1->fetch();

if (!$gameId) {
    sendMessage("error", ["message" => "No active game session"]);
    exit;
}
$stmt1->free_result();

// Fetch current game state from the database
$stmt = $conn->prepare("SELECT Status FROM Game WHERE GameId = ?");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

// Ensure $status is not empty or null
if (!$status) {
    sendMessage("error", ["message" => "Game state is unknown or not properly set"]);
    exit;
}

// Send messages based on game state
switch ($status) {
    case "waiting":
        // Fetch player count
        $stmt = $conn->prepare("SELECT COUNT(*) FROM PlayerTable WHERE SessionId = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $stmt->bind_result($playerCount);
        $stmt->fetch();
        $stmt->close();

        sendMessage("waiting", ["message" => "Waiting for players to join", "playerCount" => $playerCount]);
        break;

    case "Ready":
        sendMessage("Ready", [ "message" => "The game has started"]);
        break;

    case "choosing":
        sendMessage("choosing", ["message" => "Players are choosing"]);
        break;

    case "chosen":
        sendMessage("chosen", ["message" => "All players have chosen"]);
        break;

    case "voting":
        sendMessage("voting", ["message" => "Players are voting"]);
        break;

    case "voted":
        sendMessage("voted", ["message" => "Errbody done voted"]); // Slight delay to ensure the client gets the event properly
        break;   

    case "updating":
        sendMessage("updating", ["message" => "Updating round, please stand by "]); // Slight delay to ensure the client gets the event properly
        break;  

    case "over":
        sendMessage("over", ["message" => "Game over yall"]);
        break;

    default:
        sendMessage("error", ["message" => "Unknown game state"]);
        break;
}

// Wait before sending the next update
sleep(1);

?>
