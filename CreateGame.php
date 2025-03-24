<?php
session_start();

if (isset($_SESSION["user_id"])) {
     
    $mysqli = require __DIR__ . "/db_connecter.php";

    $sql = "SELECT * FROM Account
            WHERE Id = {$_SESSION["user_id"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    // Query to get user details
    $sql = "SELECT UserName, Email, ProfilePicture FROM Account WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId); // Bind the user ID parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $userdata = $result->fetch_assoc();

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
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Cards Against_______</title>
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
            border-radius: 10px;
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

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        #public {
            color: #888;
        }
        
        #passcodeGen {
            transition: height 0.3s ease, padding 0.3s ease, margin 0.3s ease;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .card-header, .card-footer {
            background-color: #f8f9fa;
        }

        .createJoin {
            color: #f8f9fa;
        }

        .card-body {
            background-color: #e9ecef;
        }

        .form-label {
            color: black;
        }

        .form-control {
            border-color: #ced4da;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>
<body class="bg-light">
    
    <div class="container-fluid bg-secondary d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-sm-12 d-flex justify-content-center">
                <div class="card bg-white shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h3 class="createJoin text-center" style = "color: bg-white">Create A Game</h3>
                    </div>
                    <div class="card-body ">
                
                        <div class="toggle-container mb-3 d-flex justify-content-center">
                            <span id="private" class="status">Private</span>
                            <label class="switch">
                                <input type="checkbox" id="toggle">
                                <span class="slider"></span>
                            </label>
                            <span id="public" class="status">Public</span>
                        </div>

                        <form action="">
                            <div class="mb-3">
                                <label for="playerAmount" class="form-label">How many players?</label>
                                <select class="form-control" id="playerAmount">
                                    <option value="0">Select an amount</option>
                                    <option value="1">3</option>
                                    <option value="2">4</option>
                                    <option value="3">5</option>
                                    <option value="4">6</option>
                                    <option value="5">7</option>
                                    <option value="6">8</option>
                                </select>
                            </div>  

                            <div class="mb-3">
                                <label for="passcodeGen" id="lCode" class="form-label">Create a code</label>
                                <input type="text" class="form-control" id="passcodeGen">
                            </div>    
                            
                            <div class="mb-3">
                                <label for="pointAmount" class="form-label">How many points to win?</label>
                                <select class="form-control" id="pointAmount">
                                    <option value="0">Select an amount</option>
                                    <option value="1">4</option>
                                    <option value="2">5</option>
                                    <option value="3">6</option>
                                    <option value="4">7</option>
                                    <option value="5">8</option>
                                    <option value="6">9</option>
                                    <option value="7">10</option>
                                </select>
                            </div>   
                            
                            <div class="mb-3">
                                <label for="packChosen" class="form-label">Choose a Pack</label>
                                <select class="form-control" id="packChosen">
                                    <?php include 'load_start_pack.php'; ?>
                                </select>
                            </div>                        
                        </form>
                    </div>
                    <div class="card-footer bg-primary d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" id="createButtuon">CREATE GAME</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        
        const toggle = document.getElementById('toggle');
        const privateText = document.getElementById('private');
        const publicText = document.getElementById('public');
        const passcode = document.getElementById('passcodeGen');
        const lable = document.getElementById('lCode');

        // Initialize the toggle state
        toggle.addEventListener('change', function() {
    if (toggle.checked) {
        privateText.style.color = '#888';  // Grey out the 'Private' text
        publicText.style.color = 'black';  // Set 'Public' text to black
        passcode.style.display = 'none';   // Completely hide the input field and remove it from the layout
        lCode.style.display = 'none';
    } else {
        privateText.style.color = 'black'; // Set 'Private' text to black
        publicText.style.color = '#888';   // Grey out the 'Public' text
        passcode.style.display = 'block';  // Show the input field again
        lCode.style.display = 'block';
    }
});

createButtuon.addEventListener('click',playGame);

function playGame(){

    const isPrivate = document.getElementById('toggle').checked; // true if private, false if public
    const playerAmount = parseInt(document.getElementById('playerAmount').value) + 2; // the selected number of players
    const passcode = document.getElementById('passcodeGen').value; // the entered passcode
    const pointsToWin = parseInt(document.getElementById('pointAmount').value) + 3; // the selected points to win
    const selectedPack = document.getElementById('packChosen').value; // the selected pack
    var userId =  <?php echo json_encode($_SESSION['user_id']); ?>;
    const playerType = "Host";
    const userName = <?php echo json_encode($username); ?>;


    const formData = new FormData();
    formData.append('isPrivate', isPrivate ? 'Public' : 'Private');
    formData.append('playerAmount', playerAmount); // Corrected variable name
    formData.append('pointsToWin', pointsToWin); // Corrected variable name
    formData.append('selectedPack', selectedPack); // Corrected variable name
    formData.append('passcode', passcode);
    formData.append('user_id', userId);
        

    const start = new FormData();
    start.append('isPrivate', isPrivate ? 'Public' : 'Private');
    start.append('user_id', userId);
    start.append('passcode', passcode);
    start.append('playerType', playerType);
    start.append('username', userName);

// creates a game room
    fetch('create_room.php', {
        method: 'POST',
        body: formData
    })
    .then((response) => response.text())
    .then((data) => {
        console.log(data);  // Handle the server's response
        if (data.includes("Success")) {
            const gameIdMatch = data.match(/GameId=(\d+)/); // Find "clientId" value
            const gameId = gameIdMatch ? gameIdMatch[1] : null;
/// joins the game room
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
        } else {
            alert("NOOO");
        }
    })
    .catch(error => {
        console.error('Error:', error);  // Handle any errors
    });

}

// Function to capture form data
// function captureFormData() {
 
//     const isPrivate = document.getElementById('toggle').checked; // true if private, false if public
//     const playerAmount = document.getElementById('playerAmount').value; // the selected number of players
//     const passcode = document.getElementById('passcodeGen').value; // the entered passcode
//     const pointsToWin = document.getElementById('pointAmount').value; // the selected points to win
//     const selectedPack = document.getElementById('packChosen').value; // the selected pack
//     const userId ="";

//     const formData = new FormData();
//     formData.append('isPrivate', isPrivate ? 'Public' : 'Private');
//     formData.append('playerAmount', playerAmount); // Corrected variable name
//     formData.append('pointsToWin', pointsToWin); // Corrected variable name
//     formData.append('selectedPack', selectedPack); // Corrected variable name
//     formData.append('passcode', passcode);
//     formData.append('user_id', userId);

//     console.log(FormData);

//     fetch('CreateRoom.php', {
//         method: 'POST',
//         body: formData
//     })
//     .then((response) => response.text())
//     .then((data) => {
//         console.log(data);  // Handle the server's response
//         if (data.includes("Success")) {
//             const gameIdMatch = data.match(/GameId=(\d+)/); // Find "clientId" value
//             const gameId = gameIdMatch ? gameIdMatch[1] : null;
// 			let ws = new WebSocket("ws://localhost:9090");
//             let clientIdReceived = false;
//             ws.onopen = function() {

//             }

//             ws.onmessage = function(message){
//                 const messageData = JSON.parse(message.data);  // Parse the incoming message

//                 if (messageData.clientId) {

//                 const clientId = messageData.clientId;
//                 console.log("Received clientId:", clientId);
                
//                 // Mark that the clientId is received
//                 clientIdReceived = true;

//                 console.log(messageData);
//                 const payloadCreate = {
//                 "method": "create",
//                 "clientId": clientId,
//                 "HostId": clientId,
//                 "maxPoints": pointsToWin,
//                 "maxPlayers": playerAmount,
//                 "gameId": gameId
//                 }
//                 if (ws.readyState === WebSocket.OPEN) {
//         ws.send(JSON.stringify(payloadCreate));
//         console.log("Payload sent:", JSON.stringify(payloadCreate));
//     } else {
//         console.error("WebSocket is not open. ReadyState:", ws.readyState);
//     }

//                 }

//             }
//             alert("IT WORKS");
//         } else {
//             alert("NOOO");
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);  // Handle any errors
//     });

//     // Log the captured data (you can send this data to a server or handle it as needed)
//     // console.log('GameType:', isPrivate ? 'Public' : 'Private');
//     // console.log('Number of Players:', playerAmount);
//     // console.log('Passcode:', passcode);
//     // console.log('Points to Win:', pointsToWin);
//     // console.log('Pack Chosen:', selectedPack);
// }



    </script>
</body>
</html>