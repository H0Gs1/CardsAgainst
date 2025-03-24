<?php
    include_once('db_connecter.php');
    //PHP errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //Obtain number of games played
    $query_totalGamesPlayed = "SELECT COUNT(*) as TotalGamesPlayed
        FROM GameSession 
        LIMIT 1";

    $result_totalGamesPlayed = mysqli_query($conn, $query_totalGamesPlayed);

    if ($result_totalGamesPlayed) {
        if ($row = mysqli_fetch_assoc($result_totalGamesPlayed)) {
            $totalGamesPlayed = $row["TotalGamesPlayed"];
        } else {
            $totalGamesPlayed = 0; // Default value if no row is returned
        }
        mysqli_free_result($result_totalGamesPlayed);
    }

    //Obtain number of users - Active and Total
    $activeUsers = 0;
    $totalUsers = 0;
    $query_userBreakdown = "SELECT 
        COUNT(DISTINCT CASE WHEN gs.Status = 'Active' THEN p.Player END) AS ActiveUsers,
        (SELECT COUNT(*) FROM Account WHERE UserRole = 1) AS TotalUniqueUsers
        FROM GameSession gs 
        JOIN PlayerTable p
        ON gs.Id = p.SessionId;";
            

    $result_userBreakdown = mysqli_query($conn, $query_userBreakdown);

    // Check if query succeeded
    if ($result_userBreakdown) {
        $row = mysqli_fetch_assoc($result_userBreakdown);

        // Fetch results safely
        $activeUsers = $row['ActiveUsers'] ?? 0; // Default to 0 if null
        $totalUsers = $row['TotalUniqueUsers'] ?? 0; // Default to 0 if null

        // Free result set
        mysqli_free_result($result_userBreakdown);
    }

    //Obtain games - Active and abondoned
    $months = [];
    $abandonedGamesData = [];
    $completedGamesData = [];
    $query_gamesBreakdown = "SELECT 
            YEAR(StartDateTime) AS Year,
            MONTH(StartDateTime) AS Month,
            COUNT(CASE WHEN Status = 'Active' AND EndDateTime IS NULL THEN 1 END) AS AbandonedGames,
            COUNT(CASE WHEN Status = 'Completed' AND EndDateTime IS NOT NULL THEN 1 END) AS CompletedGames
        FROM GameSession
        GROUP BY YEAR(StartDateTime), MONTH(StartDateTime)
        ORDER BY YEAR(StartDateTime), MONTH(StartDateTime);";

    $result_gamesBreakdown = mysqli_query($conn, $query_gamesBreakdown);

    if ($result_gamesBreakdown) {
        while ($row = mysqli_fetch_assoc($result_gamesBreakdown)) {
            $months[] = date('F', mktime(0, 0, 0, $row['Month'], 10)); // Get the month name
            $abandonedGamesData[] = $row['AbandonedGames'];
            $completedGamesData[] = $row['CompletedGames'];
        }
        mysqli_free_result($result_gamesBreakdown);
    }





    //Obtain number packs sold

    $query_packsSold = "SELECT Pack.PackColour, Pack.PackName, COUNT(*) AS TotalBought
    FROM Purchase
    LEFT JOIN Pack ON Purchase.PackId = Pack.Id
    GROUP BY PackId;";

    
    $result_packsSold = mysqli_query($conn, $query_packsSold);

    // Check if query succeeded
    if ($result_packsSold) {
        // Prepare arrays to hold the labels (Pack Names) and data (Total Bought)
        $dougnutLabels = [];
        $dougnutData = [];
        $dougnutBackgroundColor = []; // Optional: For custom colors in doughnut chart

        // Fetch all rows and prepare data for Chart.js
        while ($row = mysqli_fetch_assoc($result_packsSold)) {
            $dougnutLabels[] = $row['PackName'];        // Add Pack Name to labels
            $dougnutData[] = $row['TotalBought'];       // Add Total Bought count to data
            $dougnutBackgroundColor[] = $row['PackColour']; // Assuming PackColour is a hex code or color name
        }
        mysqli_free_result($result_packsSold);  // Free result set after use
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <link rel="stylesheet" href="admin-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.min.css">
    <script src="assets\bootstrap-5.3.3\dist\js\bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get Bootstrap's primary and secondary colours from CSS variable
        const primaryColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim();
        const secondaryColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-secondary').trim();
        const infoColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-info').trim();
        const dangerColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-danger').trim();
        const successColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-success').trim();
    </script>
</head>
<body>
<div class="container-fluid">   

    <!-- Cards -->
    <div class="row text-center">

    <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Most liked Cards</h5>
                </div>
                <div class="card-body bg-secondary text-white">
                    <?php
                        include_once('mostLikedAct.php');
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Nr of games played</h5>
                </div>
                <div class="card-body bg-secondary text-white">
                    <h5><?php echo $totalGamesPlayed; ?></h5>
                </div>
            </div>
        </div>

        <!-- Active  Total players  -->
        <div class="col-md-4 mb-3">           
            <div class="card">              
                <div class="card-header bg-primary text-white">
                    <h5>Active / Total Players</h5>
                </div>            
                <div class="card-body bg-secondary text-white">
                    <h5><?php echo $activeUsers; ?> / <?php echo $totalUsers; ?></h5>
                </div>
            </div>
        </div>

        <!-- Most Liked Community Cards  -->
        <!-- <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Most Liked Community Card</h5>
                </div>
                <div class="card-body bg-secondary text-white">
                    <?php
                        // include_once('mostLikedCom.php');
                    ?>
                </div>
            </div>
        </div> -->
            <!-- Most liked cards  -->
   
    </div>




    <!-- Graphs Section -->
    <div class="row">
        <div class="col-md-4 text-center my-4">
            <!-- Horizontal Bar chart -->
                <!-- Public VS Private Games -->
               
                <canvas id="horizontalBarChart"></canvas>
                <script>
                    // TODO:: retrieve the data from the DB and update this section to display the data dynically
                    // Sample Data Configuration
                    const horizontalLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
                    const horizontalData = {
                        labels: horizontalLabels,
                        datasets: [
                            {
                                label: 'Public Games',
                                data: [10, 20, 30, 40, 50, 60, 70], // Example data
                                backgroundColor: primaryColour,
                            },
                            {
                                label: 'Private Games',
                                data: [20, 30, 40, 10, 50, 60, 70], // Example data
                                backgroundColor: secondaryColour,
                            },
                        ]
                    };

                    // Chart Configuration
                    const horizontalConfig = {
                        type: 'bar',
                        data: horizontalData, // Use the horizontalData variable
                        options: {
                            indexAxis: 'y', // This makes the chart horizontal
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Public VS Private Games'
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true,
                                },
                                y: {
                                    stacked: true,
                                }
                            }
                        }
                    };

                    // Render Chart
                    const horizontalctx = document.getElementById('horizontalBarChart').getContext('2d');
                    new Chart(horizontalctx, horizontalConfig);
                </script>
        </div> 
        <div class="col-md-4 text-center my-4">
            <!-- Vertical Bar chart -->
                <!-- Completed VS Abandoned Games -->
                <canvas id="verticalBarChart"></canvas>
                <script>
                    // Pass PHP data to JavaScript
                    const months = <?php echo json_encode($months); ?>;
                    const abandonedGamesData = <?php echo json_encode($abandonedGamesData); ?>;
                    const completedGamesData = <?php echo json_encode($completedGamesData); ?>;

                    const data = {
                        labels: months,  // Months from PHP
                        datasets: [
                            {
                                label: 'Abandoned Games',
                                data: abandonedGamesData,  // Abandoned games data from PHP
                                backgroundColor: dangerColour,
                            },
                            {
                                label: 'Completed Games',
                                data: completedGamesData,  // Completed games data from PHP
                                backgroundColor: successColour,
                            }
                        ]
                    };

                    const config = {
                        type: 'bar',
                        data: data,
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Completed vs Abandoned Games'
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true,
                                },
                                y: {
                                    stacked: false,
                                }
                            }
                        }
                    };

                    // Render the chart
                    const ctx = document.getElementById('verticalBarChart').getContext('2d');
                    new Chart(ctx, config);
                </script>
        </div>


        <!-- Doughnut Chart -->
        <div class="col-md-4 text-center my-4">
                <!-- How Many Packs Were Sold -->
                <canvas id="doughnutChart" style="width: 100%; height: auto;"></canvas>
                
                
                
                <script>
                    // Prepare PHP data to be passed to JavaScript
                    const dougnutLabels = <?php echo json_encode($dougnutLabels); ?>;
                    const dougnutData = <?php echo json_encode($dougnutData); ?>;
                    const dougnutBackgroundColor = <?php echo json_encode($dougnutBackgroundColor); ?>;

                    // Doughnut Chart Data
                    const doughnutData = {
                        labels: dougnutLabels,  // Labels from PHP
                        datasets: [
                            {
                                label: 'Packs Sold',
                                data: dougnutData,  // Data from PHP
                                backgroundColor: dougnutBackgroundColor,  // Background colors from PHP
                                hoverOffset: 40
                            }
                        ]
                    };

                    // Doughnut Chart Configuration
                    const doughnutConfig = {
                        type: 'doughnut',
                        data: doughnutData,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Packs Sold'
                                }
                            }
                        }
                    };

                    // Render Doughnut Chart
                    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
                    new Chart(doughnutCtx, doughnutConfig);
                </script>
        </div>




    </div>

    <div class="row">
    <!-- Active Packs Chart -->
    <div class="col-md-6 col-sm-12">
        <?php include_once 'active_packs.php'; ?>
    </div>

    <!-- Peak Graph Chart -->
    <div class="col-md-6 col-sm-12">
        <?php include_once('peakGraph.php'); ?>
    </div>
</div>


</div>
</body>