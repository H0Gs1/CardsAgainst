<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room</title>
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <script src="assets\bootstrap-5.3.3\dist\js\bootstrap.bundle.min.js"></script>
    <style>
        .toggle-container {
            display: flex;
            align-items: center;
            font-size: 20px;
            gap: 10px;
        }

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
        }

        .createJoin {
            text-align: center;
        }

        .waiting-room-btn{
            color: whitesmoke;
        }

        .waiting-room-btn {
            text-align: center;
        }

        .waiting-room-status {
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        .card-body {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-secondary d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-sm-12 d-flex justify-content-center">
                <div class="card shadow-lg bg-grey">
                    <div class="card-header bg-primary ">
                        <h3 class="waiting-room-btn">Waiting Room</h3>
                    </div>
                    <div class="card-body">
                        <div class="waiting-room-status">
                            <p>You're waiting for the game to start...</p>
                            <div id="gameStatu" class="alert alert-warning" role="alert">
                                <p id="gameStatus"></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-primary d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" id="leaveButton">Leave Game</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var test = false;
        setInterval(getGame,2000);
        document.getElementById('leaveButton').addEventListener('click', function() {
            leaveGame();
        });

        const waitingMessages = [
            "Please take a seat... or stand awkwardly. Your choice.",
            "We're might be busy helping people into the room. Thanks for being on time!",
            "Be patient, good things come to those who wait. But you’re not the only one waiting, so, you know, don't get too excited.",
            "We’re sorry you’re waiting. We’re also sorry for all the people who waited before you. It's a long process.",
            "We’ll be with you as soon as we finish pretending to care about the last person.",
            "Don’t worry, you’re next… we promise! Unless you’re not. Then, sorry.",
            "If you're looking for a fast solution, you’re in the wrong place. But hey, thanks for being patient!",
            "You’ll be seen soon… probably. But we’re not making any promises.",
            "Stressed? Take a seat and relax! Or… don’t. We’re not your therapist.",
            "Feeling ignored? Don’t worry, it’s not personal. We ignore everyone equally."
        ];

        const randomMessage = waitingMessages[Math.floor(Math.random() * waitingMessages.length)];
        document.getElementById('gameStatus').innerHTML = randomMessage;
        function getGame(){
            fetch('get_game.php')
            .then((response) => response.text())
            .then((data) => {
                if (data.includes("Ready")) {
                    console.log(data);
                    window.location.href = "GameBoard.php";
                }else{
                    console.log(data);
                    while(!test){
                    const randomMessage = waitingMessages[Math.floor(Math.random() * waitingMessages.length)];
                    document.getElementById('gameStatus').innerHTML = randomMessage;
                    test =true;
                }
                }
            })
            .catch((error) => {
                console.error("Error joining game:", error); // Log errors to the console
                alert("An error occurred while joining game!");
            });
        }

        function leaveGame(){
            fetch('leave_game.php')
            .then((response) => response.text())
            .then((data) => {
                console.log(data);
                window.location.href = "LoginSuccess.php";
            })
            .catch((error) => {
                console.error("Error leaving:", error); // Log errors to the console
                alert("An error occurred while leaving!");

            });
        }
    </script>
</body>
</html>
