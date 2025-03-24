<?php
include_once('db_connecter.php');

// Retrieve filter parameters
$isAnswer = isset($_GET['isAnswer']) ? $_GET['isAnswer'] : '';
$packId = isset($_GET['packId']) ? $_GET['packId'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Base SQL query
$sql = "SELECT Card.*, Pack.PackName, Account.Username FROM Card 
        LEFT JOIN Pack ON Card.PackId = Pack.Id
        LEFT JOIN Account ON Card.UserId = Account.Id
        WHERE (Card.Status = 'new' OR Card.Status = 'rejected')";

// Apply filters
if ($isAnswer !== '') {
    $sql .= " AND Card.IsAnswer = '" . mysqli_real_escape_string($conn, $isAnswer) . "'";
}
if ($packId !== '') {
    $sql .= " AND Card.PackId = '" . mysqli_real_escape_string($conn, $packId) . "'";
}
if ($status !== '') {
    $sql .= " AND Card.Status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

// Order results
$sql .= " ORDER BY CreatedAt DESC";

$result = mysqli_query($conn, $sql);

// Generate table content
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr data-id='" . $row['Id'] . "' 
                  data-content='" . htmlspecialchars($row['Content']) . "' 
                  data-status='" . $row['Status'] . "'>
                <td>" . htmlspecialchars($row['Content']) . "</td>
                <td>" . $row['Likes'] . "</td>
                <td>" . ($row['IsAnswer'] ? 'Yes' : 'No') . "</td>
                <td>" . htmlspecialchars($row['PackName']) . "</td>
                <td>" . $row['CreatedAt'] . "</td>
                <td>" . $row['Status'] . "</td>
                <td>" . htmlspecialchars($row['Username']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No cards found.</td></tr>";
}
?>
