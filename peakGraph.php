<?php
    include_once('db_connecter.php');

    // Query to get the peak hour for each day
    $avgPeak = "SELECT 
    DAYNAME(StartDateTime) AS DayOfWeek,
    ROUND(AVG(HOUR(StartDateTime))) AS AvgHourOfDay,
    COUNT(*) AS SessionCount
FROM 
    elitewmzsu_db7.GameSession
WHERE 
    StartDateTime IS NOT NULL
GROUP BY 
    DayOfWeek
ORDER BY 
    FIELD(DAYNAME(StartDateTime), 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');";

    $avgResult = $conn->query($avgPeak);

    // Prepare data for the chart
    $day_arr = []; // Days of the week
    $hour_arr = []; // Peak hours
    if ($avgResult->num_rows > 0) {
        while ($row = $avgResult->fetch_assoc()) {
            $day_arr[] = $row["DayOfWeek"];
            $hour_arr[] = $row["AvgHourOfDay"];
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Peak Hours Chart</title>

    <style>
    /* Container for the chart */
    .diagram_div {
        max-width: 800px;  /* Limit the width of the chart container */
        width: 100%;       /* Make the container responsive */
        margin: 0 auto;    /* Center the container horizontally */
        height: 400px;     /* Set the height to make the chart taller */
    }

    /* Canvas styling */
    #barChart {
        width: 100%;        /* Ensure the canvas fills the container width */
        height: 100%        /* Ensure the canvas takes full height of the container */
        display: block;     /* Block-level element for better layout */
    }
</style>

</head>
<body>

    <div class="diagram_div">
        <canvas id="barChart"></canvas>
    </div>

    <script>
        window.onload = function () {
            const dayLabels = <?= json_encode($day_arr) ?>; // Days of the week
            const hourData = <?= json_encode($hour_arr) ?>; // Peak hours

            const data = {
                labels: dayLabels, // X-axis: Days of the week
                datasets: [{
                    label: 'Peak Hour (Hour of the Day)',
                    backgroundColor: '#20e0307a',
                    borderColor: '#04aa1a',
                    data: hourData, // Y-axis: Peak hours
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'x', // Days on X-axis
                    plugins: {
                        title: {
                            display: true,
                            text: 'Peak Hours per Day'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Days of the Week'
                            }
                        },
                        y: {
                            min: 0,
                            max: 23,
                            ticks: {
                                stepSize: 1,
                                callback: function (value) {
                                    return value + ":00";
                                }
                            },
                            title: {
                                display: true,
                                text: 'Peak Hour (24-hour format)'
                            }
                        }
                    }
                }
            };

            const barChart = new Chart(
                document.getElementById('barChart'),
                config
            );
        };
    </script>

</body>
</html>
