<?php
ob_start(); // Start output buffering

include_once('db_connecter.php');
session_start();

if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
    $playerId = $_SESSION["user_id"];
    $sessionId = session_id();
}

$message = "Error: Unknown issue occurred."; // Default message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
    $isPrivate = isset($_POST['isPrivate']) ? $_POST['isPrivate'] : 'Public';
    $passcode = isset($_POST['passcode']) ? $_POST['passcode'] : '';
    $playerType = isset($_POST['playerType']) ? $_POST['playerType'] : '';
    $userName = isset($_POST['username']) ? $_POST['username'] : '';
    $room = 0;
    $host = null;

    // Query for game sessions
    $sql = "SELECT Id, GameType, Status, Host, PassCode FROM GameSession ORDER BY Id DESC";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $message = "Error: " . mysqli_error($conn);
        echo $message;
        exit();
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($playerType === "Host") {
                $message = "Success";
                $room = $row['Id'];
                $host = $row['Host'];
                break;
            }

            if ($row['Status'] === "Waiting") {
                if ($isPrivate === "Private" && $row['GameType'] === "Private" && $row['PassCode'] === $passcode) {
                    $message = "Success";
                    $room = $row['Id'];
                    $host = $row['Host'];
                    break;
                } elseif ($isPrivate === "Public") {
                    $message = "Success";
                    $room = $row['Id'];
                    $host = $row['Host'];
                    break;
                }
            } else {
                $message = "Error: Room not available";
            }
        }
    } else {
        $message = "Error: No records found.";
    }

    if ($message === "Success") {
        $sql2 = "INSERT INTO PlayerTable (SessionId, Host, Player, UserId, SSEId) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql2);
        if ($stmt) {
            $stmt->bind_param("iisis", $room, $host, $player, $playerId, $sessionId);
            if ($stmt->execute()) {
                $sql3 = "INSERT INTO Action (Round, PlayerId, UserName, GameId) VALUES (?, ?, ?, ?)";
                $round = "1";
                $stmt = $conn->prepare($sql3);
                if ($stmt) {
                    $stmt->bind_param("iisi", $round, $playerId, $player, $room);
                    if ($stmt->execute()) {
                        $message = "You are in the game";
                    } else {
                        $message = "Error inserting action: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $message = "Error preparing Action statement: " . $conn->error;
                }
            } else {
                $message = "Error inserting player: " . $stmt->error;
            }
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}

// Ensure no extra output
ob_end_clean();
echo $message;

// Close the database connection
$conn->close();
?>