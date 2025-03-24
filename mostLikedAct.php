<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
</head>

<body>
<div class="container mt-4">
    <div id="card-table-container">
        <table class="table table-bordered table-hover" id="card-table">
            <thead>
                <tr class="table-primary">
                    <th>Likes</th>
                    <th>Content</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic data will be loaded here -->

                <?php
                 // only the highest 5 liked cards 
                 $findmax = "SELECT Likes as TotalLikes, Content 
                            FROM Card 
                            
                            GROUP BY Content 
                            ORDER BY TotalLikes DESC 
                            LIMIT 1";
     

                $maxResult = $conn->query($findmax);
                if ($maxResult->num_rows > 0) {
                    while ($row = $maxResult->fetch_assoc()) {
                        ?>
                        <tr class="table-secondary">
                            <td class="table-secondary"><?= htmlspecialchars($row['TotalLikes']); ?></td>
                            <td class="table-secondary"><?= htmlspecialchars($row['Content']); ?></td>                                    
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='2'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>

</html>