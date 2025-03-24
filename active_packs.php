<?php
include_once('db_connecter.php');

// Query to fetch active sessions and their associated pack names and colors
$query = "
    SELECT gs.PackId, p.PackName, p.PackColour, COUNT(*) AS session_count
    FROM GameSession gs
    INNER JOIN Pack p ON gs.PackId = p.Id
    WHERE gs.Status = 'Active'
    GROUP BY gs.PackId
";
$result = mysqli_query($conn, $query);

// Initialize arrays for the chart data
$packNames = [];
$sessionCounts = [];
$packColours = [];

while ($row = mysqli_fetch_assoc($result)) {
    $packNames[] = $row['PackName'];
    $sessionCounts[] = $row['session_count'];
    $packColours[] = $row['PackColour'];
}

// Convert the PHP arrays into JSON format for JavaScript
$packNamesJson = json_encode($packNames);
$sessionCountsJson = json_encode($sessionCounts);
$packColoursJson = json_encode($packColours);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Packs Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Parent container */
        .chart-container {
            max-width: 1000px;  /* Limit the width of the chart container */
            width: 100%;        /* Make the container responsive */
            margin: 0 auto;     /* Center the container horizontally */
            height: 500px;      /* Set a larger height */
        }

        /* Canvas styling */
        #activePacksChart {
            width: 100% !important;   /* Ensure the canvas fills the container width */
            height: 100% !important;  /* Ensure the canvas takes full height of the container */
            display: block;           /* Block-level element for better layout */
        }
    </style>
</head>
<body>

<div class="col-md-6 text-center my-4 chart-container">
    <canvas id="activePacksChart"></canvas>
</div>

<script>
// Only define the chart code here to avoid redeclaring variables like `ctx`
document.addEventListener("DOMContentLoaded", function() {
    // Data for the chart
    const packNames = <?php echo $packNamesJson; ?>;
    const sessionCounts = <?php echo $sessionCountsJson; ?>;
    const packColours = <?php echo $packColoursJson; ?>;


    // Get the context of the canvas element
    const ctx = document.getElementById('activePacksChart').getContext('2d');
    
    // Create the chart instance
    const activePacksChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: packNames, // PackNames on the X-axis
            datasets: [{
                label: 'Active Sessions',  // Label for the Y-axis
                data: sessionCounts,       // Session counts on the Y-axis
                backgroundColor: packColours,  // Bar color
                borderColor: 'rgba(54, 162, 235, 1)',        // Border color for bars
                borderWidth: 1             // Border width for bars
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                        title: {
                            display: true,
                            text: 'Active Packs'
                        }
                    },
            scales: {
                y: {
                    beginAtZero: true,   // Ensure the Y-axis starts from 0
                    title: {
                        display: true,
                        text: 'Session Count'  // Y-axis label
                        
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Pack Name'  // X-axis label
                    }
                }
            }
        }
    });
});
</script>

</body>
</html>
