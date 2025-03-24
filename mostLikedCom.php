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
                            WHERE Status = 'new' OR Status = 'rejected'
                            GROUP BY Content 
                            ORDER BY TotalLikes DESC 
                            LIMIT 5";

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


    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        $(document).ready(function () {
            getLiked();

            function fetchCards() {
                $.ajax({
                    url: 'most_liked.php',
                    method: 'GET',
                    data: filters,
                    success: function (response) {
                        $('#card-table tbody').html(response);
                        addRowColors();
                    },
                    error: function () {
                        Swal.fire('Error', 'Unable to load cards.', 'error');
                    }
                });
            }
        });
    </script> -->


</body>

</html>