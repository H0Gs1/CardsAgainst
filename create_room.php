<?php
    include_once('db_connecter.php');
    session_start(); // Ensure session is started

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data from POST
        $isPrivate = isset($_POST['isPrivate']) ? $_POST['isPrivate'] : 'Private';
        $playerAmount = isset($_POST['playerAmount']) ? $_POST['playerAmount'] : '';
        $pointsToWin = isset($_POST['pointsToWin']) ? $_POST['pointsToWin'] : '';
        $selectedPack = isset($_POST['selectedPack']) ? $_POST['selectedPack'] : '';
        $passcode = isset($_POST['passcode']) ? $_POST['passcode'] : '';
        $userId = isset($_POST['user_id']) ? $_POST['user_id'] : ''; // Get user_id from POST
        $Status = "Waiting";

        // Ensure userId exists
        if ($userId === '') {
            echo "Error: User not found.";
            exit;
        }

        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO GameSession (GameType, Status, Host, PassCode, PlayerAmount, MaxScore, PackId) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisiii", $isPrivate, $Status, $userId, $passcode, $playerAmount, $pointsToWin, $selectedPack);

        if ($stmt->execute()) {

            $sql = "SELECT Id FROM GameSession WHERE Host = ? ORDER BY Id DESC LIMIT 1;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();  
            $stmt->bind_result($gameId);  
    
            if ($stmt->fetch()) { 
                $stmt->free_result();
                $gStatus = "Ready";
                $stmt2 = $conn->prepare("INSERT INTO Game (GameId, Status) VALUES (?, ?)");
                $stmt2->bind_param("is", $gameId, $gStatus);
                if ($stmt2->execute()) {
                    $stmtBlack = $conn->prepare("INSERT INTO Black(GameId) VALUES (?)");
                    $stmtBlack->bind_param("i", $gameId);
                    $stmtBlack->execute();
                    echo "Success: GameId=" . $gameId;
                }else{
                    echo "Couldn't update game table: GameId=" . $gameId;
                }
            } else {
                echo "Error: Unable to retrieve Game ID.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
?>
