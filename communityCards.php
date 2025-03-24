<?php
// Database connection
include_once("db_connecter.php");

session_start(); // Start the session to access session variables

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Access the user ID from the session
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: loginPage.php");
    exit();
}

// Get the selected PackId from the query parameter
$packId = isset($_GET['packId']) ? intval($_GET['packId']) : 0;

if ($packId === 0) {
    die("Invalid Pack ID.");
}

// Fetch Pack details
$packQuery = "SELECT PackColour, PackName FROM Pack WHERE Id = ?";
$packStmt = $conn->prepare($packQuery);
$packStmt->bind_param("i", $packId);
$packStmt->execute();
$packResult = $packStmt->get_result();
$packData = $packResult->fetch_assoc();

if (!$packData) {
    die("Invalid Pack ID.");
}

$packColour = $packData['PackColour'];
$packName = htmlspecialchars($packData['PackName']);

$packStmt->close();

// Check if a like is being added
if (isset($_GET['likeId'])) {
    $likeId = intval($_GET['likeId']);
    $userId = $_SESSION['user_id']; // Assuming you have the user ID stored in the session

    // Check if the user has already liked the card
    $checkLikeQuery = "SELECT 1 FROM UserCardLikes WHERE CardId = ? AND UserId = ?";
    $checkStmt = $conn->prepare($checkLikeQuery);
    $checkStmt->bind_param("ii", $likeId, $userId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows == 0) {
        // User has not liked this card yet, so insert a new like
        $insertLikeQuery = "INSERT INTO UserCardLikes (CardId, UserId) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertLikeQuery);
        $insertStmt->bind_param("ii", $likeId, $userId);
        $insertStmt->execute();
        $insertStmt->close();

        // Update the like count for the card
        $updateLikeQuery = "UPDATE Card SET Likes = Likes + 1 WHERE Id = ?";
        $updateStmt = $conn->prepare($updateLikeQuery);
        $updateStmt->bind_param("i", $likeId);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $checkStmt->close();
}


// Fetch approved cards data from the database for the selected PackId
$query = "SELECT Id, Content, IsAnswer, IsCommunity, CreatedAt, Likes, UserId, Status 
          FROM Card 
          WHERE PackId = ? AND Status = 'approved' 
          ORDER BY IsCommunity ASC, IsAnswer ASC, CreatedAt DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $packId);
$stmt->execute();
$result = $stmt->get_result();

// Separate cards into categories
$nonAnswerCards = [];
$answerCards = [];
$nonAnswerCommunityCards = [];
$answerCommunityCards = [];

while ($row = $result->fetch_assoc()) {
    if ($row['IsCommunity'] == 0) {
        if ($row['IsAnswer'] == 0) {
            $nonAnswerCards[] = $row;
        } else {
            $answerCards[] = $row;
        }
    } else {
        if ($row['IsAnswer'] == 0) {
            $nonAnswerCommunityCards[] = $row;
        } else {
            $answerCommunityCards[] = $row;
        }
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $packName ?></title>
    <!-- Custom Bootstrap CSS -->
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#addCard").click(function() {
                console.log("hello ");
            var myModal2 = new bootstrap.Modal(document.getElementById('manualImport'));
            myModal2.show();
            });
        });

    </script>
    <style>


        .section-header {
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: var(--bs-primary);
            color: #fff;
            border-radius: 0.25rem;
            text-align: center;
        }

        .section-header.community {
            background-color: var(--bs-success); /* Use Bootstrap's success color */
        }

        .section-header.answer {
            background-color: var(--bs-warning); /* Use Bootstrap's warning color */
        }

        .card {
    display: flex;
    flex-direction: column;
    min-height: 100%; /* Ensure all cards take at least full height of their container */
}

        .card-header {
            font-weight: bold;
            background-color: <?= $packColour ?>; /* Use Bootstrap's light color */
            border-bottom: 1px solid #ddd;
        }

        .card-body {
    flex-grow: 1; /* Dynamically fill available space */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: white;
}

        .non-answer {
            background-color: <?= $packColour ?> !important; /* Still use the PackColour from the database */
        }


        .like-btn:hover {
            background-color: var(--bs-dark); /* Use Bootstrap's dark color for hover */
        }

        .section {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            background-color: #fff;
        }

        .section-header {
            cursor: pointer;
        }
        .section-header:hover {
            background-color: <?= $packColour ?>;
        }

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

    /* Styling for the profile button */
    .profile-btn {
        background-color: var(--bs-info);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 30px;
        font-size: 1rem;
        font-weight: 500;
        transition: background-color 0.3s ease, transform 0.2s ease;
        cursor: pointer;
    }

    .profile-btn:hover {
        background-color: #0276b3;
        transform: translateY(-2px); /* Adds a subtle lift effect */
    }

    .profile-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(38, 143, 255, 0.5);
    }

        button {
            background-color:var(--bs-secondary);
            color: var(--bs-primary);
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center; /* Optional: For centering text */
        }


    </style>


</head>
<body>

<nav class="site-header sticky-top py-1">

    <div class="container d-flex flex-column flex-md-row justify-content-between">
    <a class="nav-link" href="LoginSuccess.php"><img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" height="40" width="40" /></a>
    <button onclick="window.location.href='update_profile.php';" style="width:auto; margin: auto; background-color:var(--bs-info)" class="profile-btn">Profile</button>
    </div>
</nav>

<div >
    <div class="row">

        <div class="center">
            <h2>Browsing the <strong style="color:<?= $packColour ?>"><?= $packName ?></strong> pack</h2>
            <p>Did you know? On this page, you can <strong>Like</strong> cards and <strong>suggest your own</strong> for your favorite game! Get ready to make the game even more fun!</p>         
        </div>
        
        <div class="center">
        <p>Don't just play – <em>contribute</em> and <strong>level up</strong> your Cards Against ________ experience!</p>
            <h5>Oh, you think you're funny? Let's see if you can actually make us laugh.</h5>
            <p><button style="color: var(--bs-primary)" onclick="window.location.href='communityCardGet.php';" class="btn-primary ">Add a card</button></p>
        </div>
    </div>

    <?php
function displayCard($row, $packColour, $extraClass = '', $packId, $conn) {
    ?>
    <div class="flex-wrap col-lg-4 col-md-6 mb-4">
        <div class="card <?= $extraClass ?> d-flex flex-column flex-grow-1">
            <div class="card-header">
                <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" width="50" height="auto">
                Cards against ___________
            </div>
            <div class="card-body">
                <p><?= htmlspecialchars($row['Content']); ?></p>
                <p><small class="text-muted">Created At: <?= $row['CreatedAt']; ?></small></p>

                <?php if ($row['IsCommunity'] == 1): ?>
                    <?php 
                    // Fetch the username based on UserId
                    $userQuery = "SELECT Username FROM Account WHERE Id = ?";
                    $userStmt = $conn->prepare($userQuery);
                    $userStmt->bind_param("i", $row['UserId']);
                    $userStmt->execute();
                    $userResult = $userStmt->get_result();
                    $userData = $userResult->fetch_assoc();
                    $username = $userData['Username'] ?? 'Unknown';
                    ?>
                    <p>Suggested By : <strong><?= htmlspecialchars($username); ?></strong></p>
                    <p>Status : <strong style="color: var(--bs-success)"><?= htmlspecialchars($row['Status']); ?></strong> </p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Likes:</strong> <?= $row['Likes']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary" 
                                onclick="window.location.href='communityCards.php?packId=<?= htmlspecialchars($packId); ?>&likeId=<?= htmlspecialchars($row['Id']); ?>';">
                            Like
                        </button>
                    </div>
                </div>                    
            </div>  
        </div>
    </div>
    <?php
}


    $sections = [
        ["Black Cards", $nonAnswerCards, 'non-answer', 'collapseNonAnswerCards'],
        ["White Cards", $answerCards, '', 'collapseAnswerCards'],
        ["Community Black Cards", $nonAnswerCommunityCards, 'non-answer', 'collapseCommunityNonAnswerCards'],
        ["Community White Cards", $answerCommunityCards, '', 'collapseCommunityAnswerCards']

    ];

    foreach ($sections as [$title, $cards, $extraClass, $collapseId]) {
        if (!empty($cards)) {
            echo "<div class='section'>";
            echo "<div class='section-header' data-bs-toggle='collapse' href='#{$collapseId}' role='button' aria-expanded='false' aria-controls='{$collapseId}'>";
            echo "{$title}";
            echo "</div>";
            echo "<div class='collapse' id='{$collapseId}'>";
            echo "<div class='row'>";
            foreach ($cards as $row) {
                displayCard($row, $packColour, $extraClass, $packId, $conn);
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }
    
    if (empty($nonAnswerCards) && empty($answerCards) && empty($nonAnswerCommunityCards) && empty($answerCommunityCards)) {
        echo "<p class='text-center'>No cards available for this pack.</p>";
    }
    

    include_once("communityCardsReject.php");

    ?>
    
</div>
</body>
</html>

