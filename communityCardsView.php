<?php
// Database connection
include_once("db_connecter.php");

// Check if a like is being added
if (isset($_GET['like'])) {
    $pack_id = $_GET['like'];
    $update_like_query = "UPDATE packs SET Likes = Likes + 1 WHERE id = ?";
    $stmt = $conn->prepare($update_like_query);
    $stmt->bind_param("i", $pack_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch packs data from the database, ordered by creation date
$query = "SELECT id, PackColour, PackName, PackPrice, PackDescription, Likes, CreatedAt FROM Pack ORDER BY CreatedAt DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Packs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pack-color {
            height: 150px;
            background-color: var(--bs-info); /* Default Bootstrap info color */
            border-radius: 0.5rem;
        }
        .pack {
            border: 1px solid var(--bs-info);
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .pack .like-btn {
            background-color: var(--bs-info);
            color: #fff;
            border: none;
            text-align: center;
        }
        .pack .like-btn:hover {
            background-color: var(--bs-info-hover);
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4 text-info">Packs</h1>
        <div class="row justify-content-center">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="pack">
                            <div class="pack-color" style="background-color: <?= $row['PackColour']; ?>;"></div>
                            <div class="p-3">
                                <h5 class="text-info"><?= htmlspecialchars($row['PackName']); ?></h5>
                                <p><strong>Price:</strong> $<?= number_format($row['PackPrice'], 2); ?></p>
                                <p><?= htmlspecialchars($row['PackDescription']); ?></p>
                                <p><strong>Likes:</strong> <?= $row['Likes']; ?></p>
                                <p><strong>Created At:</strong> <?= date('Y-m-d H:i', strtotime($row['CreatedAt'])); ?></p>
                                <a href="community.php?like=<?= $row['id']; ?>" class="btn like-btn w-100">Like</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>No packs available.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
