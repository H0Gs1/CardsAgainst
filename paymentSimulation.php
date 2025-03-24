<?php
require('db_connecter.php');
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['packId'])) {
    $_SESSION['pack_id'] = $_GET['packId'];
    $packId = $_SESSION['pack_id'];

    // Fetch data for the specific packId
    $query = "SELECT * FROM Pack WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $packId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pack = $result->fetch_assoc();
        $_SESSION['pack_name'] = $pack['PackName'];
        $_SESSION['pack_price'] = $pack['PackPrice'];
    } else {
        echo "Pack not found!";
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo "User is not logged in!";
        exit;
    }
} else {
    echo "No pack selected!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Simulating Online Payment</title>
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: Arial, sans-serif;
        }
        .header-logo h2 {
            margin: 20px 0;
            color: #007bff;
        }
        .btn-primary {
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        .btn-secondary {
            font-size: 1rem;
            margin-top: 20px;
        }
        .fullscreen-loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: opacity 0.3s ease;
        }
        .fullscreen-loader.show {
            display: flex;
        }
        .cards {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
        }
        .cards .item {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            animation: bounce 1.8s infinite cubic-bezier(0.6, 0.01, 0.4, 1);
        }
        .item-1 { background: #007bff; animation-delay: 0s; }
        .item-2 { background: #6c757d; animation-delay: 0.2s; }
        .item-3 { background: #17a2b8; animation-delay: 0.4s; }
        .item-4 { background: #dc3545; animation-delay: 0.6s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="container text-center mt-5">
        <div class="header-logo">
            <h2>Simulating an Online Credit/Debit Card Payment</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h4>
                    Click the button below to proceed with the simulation of buying a 
                    <strong><u><?php echo htmlspecialchars($pack['PackName']); ?></u></strong> pack
                </h4>
                <p class="mt-3">
                    <button class="btn btn-primary" onclick="startPaymentSimulation()">Proceed</button>
                </p>
                <p>
                    <a href="LoginSuccess.php" class="btn btn-secondary">Change Your Mind</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Fullscreen Loader -->
    <div class="fullscreen-loader" id="fullscreen-loader">
        <div class="cards">
            <div class="item item-1"></div>
            <div class="item item-2"></div>
            <div class="item item-3"></div>
            <div class="item item-4"></div>
        </div>
        <h4>Redirecting...</h4>
    </div>

    <script>
        function startPaymentSimulation() {
            const loader = document.getElementById('fullscreen-loader');
            loader.classList.add('show');
            setTimeout(() => {
                window.location.href = "paymentSimulationPaying.php";
            }, 5000);
        }
    </script>
</body>
</html>
