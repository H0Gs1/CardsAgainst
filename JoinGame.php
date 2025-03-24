<?php 
include_once("db_connecter.php");

session_start();

if (isset($_SESSION["user_id"])) {
     
    $mysqli = require __DIR__ . "/db_connecter.php";

    $sql = "SELECT * FROM Account
            WHERE Id = {$_SESSION["user_id"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    // Query to get user details
    $sql = "SELECT Username, Email, ProfilePicture FROM Account WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId); // Bind the user ID parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user data
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $username = $userData['UserName'];
        $email = $userData['Email'];
        $ProfilePictures = $userData['ProfilePicture'];
        $stmt->bind_param(':user_id', $userId);
        $stmt->execute();
    } else {
        // Handle case where no user data is found
        $username = 'Guest';
        $email = 'Not available';
        $profilePic = 'default-avatar.png';
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join A Game</title>
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
                width: 50%;
            }
        }

        .status {
            font-weight: bold;
        }

        .or-separator {
            font-size: 24px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            color: #888;
            position: relative;
        }

        .or-separator::before, .or-separator::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background-color: #888;
        }

        .or-separator::before {
            left: 0;
        }

        .or-separator::after {
            right: 0;
        }

        .section {
            display: none;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .join-game-button {
            margin-top: 10px; /* Reduced margin to bring button closer */
        }

        /* Reducing space between input and button */
        .card-body .form-control, .card-body .btn {
            margin-bottom: 10px; /* Reduced margin between input and button */
        }

        .join-game-button button {
            margin-top: 0; /* Remove extra margin for button */
        }

        /* Footer style inside card */
        .card-footer {
            background-color: #f8f9fa; /* Light gray background for footer */
            padding: 10px 0; /* Padding inside the footer */
            text-align: center; /* Center the footer content */
        }
        .card-body{
            background-color: #e9ecef;
        }

        /* Make the breaks visible */
        .break {
            display: block;
            height: 5px;
            background-color: #888;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    
    <div class="container-fluid bg-secondary  d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-sm-12 d-flex justify-content-center">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary">
                        <h3 class="text-center" style="color: whitesmoke">Join A Game</h3>
                    </div>
                    <div class="card-body">
                        <!-- Public Section -->
                        <div id="publicSection" class="section mb-3">
                            <div class="section-title">Public Game</div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary" id="joinPublicButton">Join Now</button>
                            </div>
                        </div>

                        <div class="or-separator">
                            <br class="break">
                            OR 
                            <br class="break">
                        </div> <!-- OR separator -->

                        <!-- Private Section -->
                        <div id="privateSection" class="section mb-3">
                            <div class="section-title">Private Game</div>
                            <label for="passcodeInput" class="form-label">Enter the code</label>
                            <input type="text" class="form-control" id="passcodeInput" placeholder="Enter Code">
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary" id="joinButton">Join Game</button>
                            </div>
                        </div>   
                    </div>
                    <!-- Move the "Join Game" button inside the card footer -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Elements
        const joinButton = document.getElementById('joinButton');
        const joinPublicButton = document.getElementById('joinPublicButton');
        const passcodeInput = document.getElementById('passcodeInput');
        const publicSection = document.getElementById('publicSection');
        const privateSection = document.getElementById('privateSection');
       
        


        // Default behavior: Show both sections
        publicSection.style.display = 'block';
        privateSection.style.display = 'block';

        // Handle the action for joining the game based on Public/Private
        joinButton.addEventListener('click', function() {
            if (publicSection.style.display === 'ro') {
                // Join Public game
                console.log("HI");
                
            } else {
                // Join Private game with the code
                const code = passcodeInput.value.trim();
                if (code === '') {
                    alert("Please enter a valid code.");
                } else {
                    const isPrivate = "Private"; 
                    const passcode = document.getElementById('passcodeInput').value; // the entered passcode
                    var userId =  <?php echo json_encode($_SESSION['user_id']); ?>;
                    const playerType = "player";

                    const start = new FormData();
                    start.append('isPrivate', isPrivate);
                    start.append('passcode', passcode);
                    start.append('playerType', playerType);

                    fetch('join_room.php',{
                            method: 'POST',
                            body: start
                        })
                        .then((response) => response.text())
                        .then((data) => {
                            console.log(data);
                            if (data.includes("You are in the game")) {
                                window.location.href = "WaitingRoom.php";

                            } else {
                                alert(data);
                            }
                        }).catch(error => {
                            console.error('Error:', error);  // Handle any errors
                        });
                }
            }
        });

        // Handle the action for the Public join button (Join Now)
        joinPublicButton.addEventListener('click', function() {

            const isPrivate = "Public"; // true if private, false if public
            const passcode = document.getElementById('passcodeInput').value; // the entered passcode
            var userId =  <?php echo json_encode($_SESSION['user_id']); ?>;
            const playerType = "player";

            const start = new FormData();
            start.append('isPrivate', isPrivate);
            start.append('playerType', playerType);

            fetch('join_room.php',{
                    method: 'POST',
                    body: start
                })
                .then((response) => response.text())
                .then((data) => {
                    console.log(data);
                    if (data.includes("You are in the game")) {
                        window.location.href = "WaitingRoom.php";

                    } else {
                        alert(data);
                    }
                }).catch(error => {
                    console.error('Error:', error);  // Handle any errors
                });

                });


                
    </script>
</body>
</html>
