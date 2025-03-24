<?php

include_once('db_connecter.php');
session_start();

if (isset($_SESSION["user_id"])) {}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Over - Results</title>
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <script src="assets\bootstrap-5.3.3\dist\js\bootstrap.bundle.min.js"></script>
    <style>
        .card {
            width: 100%;
        }

        @media (min-width: 767px) {
            .card {
                width: 30%;
            }
        }

        @media (min-width: 768px) {
            .card {
                width: 50%; /* 50% width for medium devices (tablets) */
            }
        }

        .status {
            font-weight: bold;
            font-size: 18px;
        }

        .game-result {
            font-size: 24px;
            font-weight: bold;
            color: green;
            margin-top: 20px;
        }

        .player-list {
            margin-top: 20px;
        }

        .player {
            margin: 5px 0;
        }

        .text-center{
            color: whitesmoke;
        }

        .restart-btn, .menu-btn {
            width: 45%;
        }

        .result-container {
            text-align: center;
            padding: 20px;
        }

        .card-body{
            background-color: #e9ecef;
        }

        /* Confetti CSS */
        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(1000px) rotate(360deg);
                opacity: 0;
            }
        }

        .confetti {
            position: fixed;
            top: 0;
            left: 50%;
            width: 5px;
            height: 5px;
            background-color: #FF0;
            animation: confetti 5s linear infinite;
            opacity: 0;
        }

        .confetti:nth-child(odd) {
            background-color: #FF5733;
        }

        .confetti:nth-child(even) {
            background-color: #33FF57;
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-secondary d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-sm-12 d-flex justify-content-center">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary">
                        <h3 class="text-center">Game Over - Results</h3>
                    </div>
                    <div class="card-body result-container">
                        <!-- Winner Section -->
                        <div class="game-result">
                            <?php include 'congrat.php' ;?>
                        </div>

                        <!-- Player Scores List -->
                        <div class="player-list">
                            <h4>Final Scores</h4>
                            <ul class="list-group">
                                <?php include 'final_scores.php' ;?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer bg-primary d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary menu-btn" id="mainMenuButton">Back to Home Page</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Celebration animation on page load
        window.onload = function() {
            // Create confetti elements on page load
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement("div");
                confetti.classList.add("confetti");

                // Randomize position and animation delay
                confetti.style.left = `${Math.random() * 100}%`;
                confetti.style.animationDelay = `${Math.random() * 2}s`;
                
                // Add the confetti to the body
                document.body.appendChild(confetti);
            }

            // Remove confetti after 5 seconds
            setTimeout(function() {
                const confettiElements = document.querySelectorAll('.confetti');
                confettiElements.forEach(confetti => confetti.remove());
            }, 5000); // 5000ms = 5 seconds
        };

        // Back to Home page button functionality
        document.getElementById('mainMenuButton').addEventListener('click', function() {
            window.location.href = 'LoginSuccess.php'; // Redirect to the main menu or home page
        });
    </script>
</body>
</html>
