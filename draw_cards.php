<?php
include_once('db_connecter.php');

session_start();

header('Content-Type: application/json'); // Ensure JSON response
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
    echo "No Game ID found for the player.";
    exit;
}
 $stmt1->free_result();

 $stmtPack = $conn->prepare("SELECT PackId FROM GameSession WHERE Id = ? ORDER BY Id DESC LIMIT 1;");
 $stmtPack->bind_param("i", $gameId);
 $stmtPack->execute();
 $stmtPack->bind_result($packId);
 $stmtPack->fetch();
  $stmtPack->free_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card = $_POST['cardType'] ?? null;
    $message = null;

    if (!$card) {
        echo json_encode(["error" => "No card type provided."]);
        exit;
    }

    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        exit;
    }

    if ($card === "Black") {
        $stmt = $conn->prepare("SELECT COUNT(*) AS Count FROM Black WHERE GameId = ? AND CardContent IS NULL");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $stmt->bind_result($empty);
        $stmt->fetch();

        if ($empty === 1) {
            $stmt->free_result();
            $stmtBlack = $conn->prepare("SELECT Id, Content FROM Card WHERE IsAnswer = 0 AND PackId = ? ORDER BY RAND() LIMIT 1");
            $stmtBlack->bind_param("i", $packId);
            $stmtBlack->execute();
            $stmtBlack->bind_result($cardId,$cardContent);
            $stmtBlack->fetch();
            $message = $cardContent;
            $stmtBlack->free_result();

            $stmtInsert = $conn->prepare("UPDATE Black SET CardId = ?, CardContent = ? WHERE GameId = ?");
            $stmtInsert->bind_param("isi",$cardId,$cardContent, $gameId);
            $stmtInsert->execute();
        } else {
            $stmt->free_result();
            $stmtCard = $conn->prepare("SELECT CardContent FROM Black WHERE GameId = ?");
            $stmtCard->bind_param("i",$gameId);
            $stmtCard->execute();
            $stmtCard->bind_result($blackCard);
            $stmtCard->fetch();
            $message = $blackCard;
            $stmtCard->free_result();
        }
        
    } elseif ($card === "White") {
        $sql = "SELECT Id, Content FROM Card WHERE IsAnswer = 1  AND PackId = '$packId' ORDER BY RAND() LIMIT 10";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $cards = [];
            while ($row = $result->fetch_assoc()) {
                $cards[] = $row; 
            }
            $message = $cards;
        } else {
            $message = ["error" => "No White cards found."];
        }
    } else {
        $message = ["error" => "Invalid card type provided."];
    }

    echo json_encode($message);
    exit;
}
