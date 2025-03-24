<?php
// Database connection
include_once("db_connecter.php");

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: loginPage.php");
    exit();
}

// Get the selected PackId
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
    $userId = $_SESSION['user_id'];

    // Check if the user has already liked the card
    $checkLikeQuery = "SELECT 1 FROM UserCardLikes WHERE CardId = ? AND UserId = ?";
    $checkStmt = $conn->prepare($checkLikeQuery);
    $checkStmt->bind_param("ii", $likeId, $userId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows == 0) {
        $insertLikeQuery = "INSERT INTO UserCardLikes (CardId, UserId) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertLikeQuery);
        $insertStmt->bind_param("ii", $likeId, $userId);
        $insertStmt->execute();
        $insertStmt->close();

        $updateLikeQuery = "UPDATE Card SET Likes = Likes + 1 WHERE Id = ?";
        $updateStmt = $conn->prepare($updateLikeQuery);
        $updateStmt->bind_param("i", $likeId);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $checkStmt->close();
}

// Fetch new and rejected cards
$query = "SELECT Id, Content, IsAnswer, IsCommunity, CreatedAt, Likes, UserId, Status 
          FROM Card 
          WHERE PackId = ? AND (Status = 'new' OR Status = 'rejected')
          ORDER BY IsCommunity ASC, IsAnswer ASC, CreatedAt DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $packId);
$stmt->execute();
$result = $stmt->get_result();

// Separate cards into categories and fetch UserName for each UserId
$nonAnswerCards = [];
$answerCards = [];
$nonAnswerCommunityCards = [];
$answerCommunityCards = [];
$usernames = [];

while ($row = $result->fetch_assoc()) {
    $cardUserId = $row['UserId'];

    // Fetch UserName if not already retrieved
    if (!isset($usernames[$cardUserId])) {
        $userQuery = "SELECT UserName FROM Account WHERE Id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $cardUserId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userRow = $userResult->fetch_assoc();
        $usernames[$cardUserId] = $userRow ? htmlspecialchars($userRow['UserName']) : "Unknown User";
        $userStmt->close();
    }

    $row['UserName'] = $usernames[$cardUserId];

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
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div >
        <div class="center">
            <div>
                <h2>Potential cards for the <strong style="color:<?= $packColour ?>"><?= $packName ?></strong> pack. Like ‘em if you think they’re worthy!</h2>
            </div>
        </div>

    <?php
    function displayPotCard($row, $packColour, $extraClass = '', $packId) {
        ?>
        <div class="col-md-3">
            <div class="card <?= $extraClass ?>">
                <div class="card-header">
                    <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" width="50" height="auto">
                    Cards against ___________
                </div>    
                <div class="card-body">
                    <p><?= htmlspecialchars($row['Content']); ?></p>
                    <p><small class="text-muted">Created At: <?= $row['CreatedAt']; ?></small></p>
                    <p>Suggested by : <strong><?= htmlspecialchars($row['UserName']); ?></strong></p>
                </div>
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Likes:</strong> <?= $row['Likes']; ?></p>
                        </div>

                        <?php if ($row['Status'] == 'new') { ?>
                            <p>Status: <strong style="color: var(--bs-warning)"><?= htmlspecialchars($row['Status']); ?></strong></p>
                        <?php } else { ?>
                            <p>Status: <strong style="color: var(--bs-danger)"><?= htmlspecialchars($row['Status']); ?></strong></p>
                        <?php } ?>                     
                                                
                        <div class="col-md-6 text-end">
                            <button class="btn btn-primary btn-sm" 
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
        ["Black Cards", $nonAnswerCards, 'non-answer'],
        ["White Cards", $answerCards, ''],
        ["Community Black Cards", $nonAnswerCommunityCards, 'non-answer'],
        ["Community White Cards", $answerCommunityCards, '']
    ];

    foreach ($sections as [$title, $cards, $extraClass]) {
        if (!empty($cards)) {
            echo "<div class='section'>";
            echo "<div class='section-header'>{$title}</div>";
            echo "<div class='row'>";
            foreach ($cards as $row) {
                displayPotCard($row, $packColour, $extraClass, $packId);
            }
            echo "</div>";
            echo "</div>";
        }
    }

    if (empty($nonAnswerCards) && empty($answerCards) && empty($nonAnswerCommunityCards) && empty($answerCommunityCards)) {
        echo "<p class='text-center'>No potential cards available for this pack.</p>";
    }
    ?>
</div>
</body>
</html>