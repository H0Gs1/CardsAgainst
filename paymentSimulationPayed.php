<?php
    include("db_connecter.php");
    session_start();

    if (isset($_SESSION['user_id']) && isset($_SESSION['pack_id']) && isset($_SESSION['pack_price'])) {
        $date = date('Y-m-d H:i:s');  // Get the current date and time
        $userId = $_SESSION['user_id'];  // User's ID from session
        $packPrice = $_SESSION['pack_price'];  // Pack price from session
        $packId = $_SESSION['pack_id'];  // Pack ID from session
        
        $query = "
            INSERT INTO Purchase (Date, UserId, Price, PackId)
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($query);

        // Bind parameters
        $stmt->bind_param("sidi", $date, $userId, $packPrice, $packId);

        if ($stmt->execute()) {
            echo 'Purchase recorded successfully!';
                // Unset the variables inside the session
            unset($_SESSION['pack_price']);
            unset($_SESSION['pack_id']);
            
        } else {
            echo 'Failed to record purchase.';
            
        }

    } else {
        echo 'Missing required data.';
    }
?>
