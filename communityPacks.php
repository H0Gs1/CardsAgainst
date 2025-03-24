<?php
// Database connection
include_once("db_connecter.php");

// Fetch packs data from the database, ordered by creation date
$query = "SELECT id, PackColour, PackName, PackPrice, PackDescription FROM Pack ORDER BY PackName ASC";
$result = $conn->query($query);

// Get the current user's ID from the session (make sure session is started)
$userId = $_SESSION["user_id"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packs</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<div class="row justify-content-center">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if the user has already purchased the current pack
            $packId = $row['id'];
            $ownedPackQuery = "SELECT COUNT(*) FROM Purchase WHERE UserId = $userId AND PackId = $packId";
            $ownedPackResult = $conn->query($ownedPackQuery);
            $ownedPack = $ownedPackResult->fetch_row()[0];

            ?>
                <div class="d-flex flex-wrap col-lg-4 col-md-6 mb-4" style="padding: 20px;">
                <div class="pack h-100 w-100 d-flex flex-column my-1">
                    <div class="pack-color" style="background-color: <?= $row['PackColour']; ?>;"></div>
                    <div class="p-3 d-flex flex-column justify-content-between" style="height: 100%;">
                        <div>
                            <h5 class="text-info"><?= htmlspecialchars($row['PackName']); ?></h5>
                            <p><strong>Price:</strong> R<?= number_format($row['PackPrice'], 2); ?></p>
                            <p><?= htmlspecialchars($row['PackDescription']); ?></p>
                        </div>

                        <div class="row justify-content-center">

                            <div class="row justify-content-between" style="padding: 1px;">
                                <!-- BUY -->
                                <?php if ($ownedPack == 0): // Only show the Buy button if the user hasn't purchased the pack ?>
                                    
                                    <a href="paymentSimulation.php?packId=<?= $row['id']; ?>" onclick="confirmPayment(event, this)" class="btn btn-secondary">Buy pack</a>

                                <?php endif; ?>

                            </div>

                            <div class="row justify-content-between" style="padding: 1px;">                            
                                <!-- VIEW -->
                                <button onclick="window.location.href='communityCards.php?packId=<?= $row['id']; ?>';" class="btn btn-info">View</button>
                            </div>
                            
                        </div>
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
    <script>
        // Confirmation for reset password
        function confirmPayment(event, link) {
    event.preventDefault(); // Prevent default action (page navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you wish to proceed with payment.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Manually redirect to the URL in the link after confirmation
            window.location.href = link.href;  // This will navigate to the correct page
        }
    });
}
    </script>
</body>
</html>
