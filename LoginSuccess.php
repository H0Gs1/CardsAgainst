<?php 
include_once("db_connecter.php");

session_start();

if (isset($_SESSION["user_id"])) {
     
    $mysqli = require __DIR__ . "/db_connecter.php";

    $sql = "SELECT * FROM Account
            WHERE Id = {$_SESSION["user_id"]}";

// if ($_SESSION['UserRole'] == 0) {
//     echo "Welcome, Admin!";
// } else {
//     echo "Welcome, User!";
// }

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
        $stmt->bind_Param(':user_id', $userId);
        $stmt->execute();
    } else {
        // Handle case where no user data is found
        $username = 'Guest';
        $email = 'Not available';
        $profilePic = 'default_pfp.png';
    }

/*     // Close the statement and the connection
    $stmt->close();
    $conn->close(); */
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cards Against</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="generator" content="Hugo 0.72.0" />
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">





    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"
        integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        /* styles from loginPage for modal  */

        /* Full-width input fields */
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Set a style for all buttons */
        button {
            background-color: #04AA6D;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        /* Extra styles for the cancel button */
        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
        }

        /* Center the image and position the close button */
        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
            position: relative;
        }

        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        .container {
            padding: 16px;
        }

        span.password {
            float: right;
            padding-top: 16px;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            padding-top: 60px;
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto;
            /* 5% from the top, 15% from the bottom and centered */
            border: 2px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
        }

        /* The Close Button (x) */
        .close {
            position: absolute;
            right: 25px;
            top: 0;
            color: #000;
            font-size: 35px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: var(--bs-danger);
            cursor: pointer;
        }

        /* Add Zoom Animation */
        .animate {
            -webkit-animation: animatezoom 0.6s;
            animation: animatezoom 0.6s
        }

        @-webkit-keyframes animatezoom {
            from {
                -webkit-transform: scale(0)
            }

            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes animatezoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.password {
                display: block;
                float: none;
            }

            .cancelbtn {
                width: 100%;
            }
        }
        
    </style>

    <!-- Custom styles for this template -->
    <!-- <link href="product.css" rel="stylesheet"> -->
    <style>
        .container {
            max-width: 960px;
        }

        /*
 * Custom translucent site header
 */

        .site-header {
            background-color: rgba(0, 0, 0, 0.85);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            backdrop-filter: saturate(180%) blur(20px);
        }

        .site-header a {
            color: #727272;
            transition: color 0.15s ease-in-out;
        }

        .site-header a:hover {
            color: #fff;
            text-decoration: none;
        }

        /*
 * Dummy devices (replace them with your own or something else entirely!)
 */

        .product-device {
            position: absolute;
            right: 10%;
            bottom: -30%;
            width: 300px;
            height: 540px;
            background-color: #333;
            border-radius: 21px;
            transform: rotate(30deg);
        }

        .product-device::before {
            position: absolute;
            top: 10%;
            right: 10px;
            bottom: 10%;
            left: 10px;
            color: var(--bs-secondary);
            content: "This is the prime of my life. I'm young, hot and full of ______";
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .product-device-2 {
            top: -25%;
            right: auto;
            bottom: 0;
            left: 5%;
            background-color: #e5e5e5;
        }

        /*
 * Extra utilities
 */

        .flex-equal>* {
            flex: 1;
        }

        @media (min-width: 768px) {
            .flex-md-equal>* {
                flex: 1;
            }
        }
    </style>
</head>

<body>

<!-- Navigation Bar -->
<nav class="site-header sticky-top py-1">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <img src="assets/bootstrap-5.3.3/Images/Cards_Against_Humanity_29.webp" height="40" width="40" />

        <!-- Buttons -->
        <div class="d-flex align-items-center gap-3">
            <button id="btnRules" class="btn btn-info">Rules</button>
            <button onclick="window.location.href='update_profile.php';" class="btn btn-info">Profile</button>
        </div>
    </div>
</nav>

<!-- Modal Structure -->
<div class="modal modal-lg fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rulesModalLabel">Game Rules</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <h1>Cards Against Humanity - Game Rules</h1>
    <p>Welcome to <strong>Cards Against Humanity</strong> — where hilarity reigns and the absurdity knows no bounds! Here’s how to play:</p>

    <h2>1. The Cards</h2>
    <ul>
        <li><strong>Black Cards</strong> are your question cards, setting the stage for some ridiculous answers.</li>
        <li><strong>White Cards</strong> are your answer cards, where you’ll unleash your most outlandish (and often inappropriate) responses.</li>
    </ul>

    <h2>2. Set-Up</h2>
    <ul>
        <li>Each player draws <strong>X number</strong> of white cards to kick things off (equal to the number of players, so everyone’s got a fair shot at the fun!).</li>
        <li>Each player receives one black card for the round.</li>
    </ul>

    <h2>3. Gameplay</h2>
    <ul>
        <li>The player who draws the black card reads the question aloud for all to hear.</li>
        <li>Submit your funniest white card anonymously—no peeking or changing your mind!</li>
    </ul>

    <h2>4. Voting</h2>
    <ul>
        <li>Once all answers are submitted, the group votes on which card made them laugh the hardest. The card with the most votes wins.</li>
    </ul>

    <h2>5. Scoring</h2>
    <ul>
        <li>The player who submitted the winning card gets <strong>1 point</strong>.</li>
    </ul>

    <h2>6. Endgame</h2>
    <ul>
        <li>The game ends when a player reaches the point goal and becomes the ultimate master of terrible humor!</li>
    </ul>

    <p class="text-center"><em>Let the laughs and chaos begin! Ready to see who’s got the wildest sense of humor?</em></p>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


        <!-- <form action="upload_profile_pic.php"method="POST"enctype="multipart/form-data">
            <label for="image">File here</label>
            <input type="file" name="image" id="image"required>
            <button type="submit">upload</button>
        </form> -->

    <div 
        class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
        <div class="col-md-5 p-lg-5 mx-auto my-5">
            <h1 class="display-4 font-weight-normal">Cards <br> Against <br> _ _ _ _ _ _ _ </h1>
            <p class="lead font-weight-normal">
                THE MOST UNSERIOUS GAME
            </p>

            <!-- play buttons -->
            <div class="row">
                <div class="col-sm-6">
                    <button class="btn btn-secondary w-100" name="submit" type="submit" id="btnCreate">Create
                        game</button>
                    <p>Create your own chaos, Get ready to offend</p>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-secondary w-100" name="submit" type="submit" id="btnJoin">Join
                        game</button>
                    <p>Join the madness, where nothing's off-limits</p>
                </div>
            </div>


            <!-- <a class="btn btn-outline-secondary" href="#">Coming soon</a> -->
        </div>

        <!-- <div class="product-device shadow-sm d-none d-md-block"></div>
        <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div> -->

    </div>




<!-- Packs start here  -->    
<?php 
    include('communityPacks.php');
?> 
<!-- Packs end here -->


<!-- Feature cards start here -->
<!-- Feature cards end here -->


    <script>

document.getElementById("btnRules").onclick = function () {
        var myModal = new bootstrap.Modal(document.getElementById('rulesModal'));
        myModal.show();
    };

        document.getElementById("btnJoin").onclick = function () {
                location.href = "JoinGame.php";
            };

        document.getElementById("btnCreate").onclick = function () {
                location.href = "CreateGame.php";
            };

        var modal = document.getElementById('id01');

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }



    </script>

</body>

</html>
