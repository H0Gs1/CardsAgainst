<?php
include_once('db_connecter.php');

// Query to fetch cards along with their pack name
$sql = "SELECT Card.*, Pack.PackName FROM Card 
        LEFT JOIN Pack ON Card.PackId = Pack.Id         
        WHERE (Card.Status = 'new' OR Card.Status = 'rejected')
        ORDER BY CreatedAt DESC";
// Execute the query
$result = mysqli_query($conn, $sql);

// Generate table content
if (mysqli_num_rows($result) > 0) {
    echo "<table class='table'>";
    echo "<thead class='thead-dark'>";
    echo "<tr>";
    echo "<th>Content</th>";
    echo "<th>Likes</th>";
    echo "<th>Answer?</th>";
    echo "<th>Pack Name</th>";
    echo "<th>Created At</th>";
    echo "<th>Status</th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        // Content column (editable)
        echo "<td contenteditable='true' id='content-" . $row['Id'] . "'>" . htmlspecialchars($row['Content']) . "</td>";
        // Likes
        echo "<td>" . $row['Likes'] . "</td>";
        // Answer status
        echo "<td>" . ($row['IsAnswer'] ? 'Yes' : 'No') . "</td>";
        // Pack Name
        echo "<td>" . htmlspecialchars($row['PackName']) . "</td>";
        // Created At
        echo "<td>" . $row['CreatedAt'] . "</td>";
        // Status
        echo "<td>" . $row['Status'] . "</td>";
        // Actions
        echo "<td>
                <button class='btn btn-success status-btn' data-id='" . $row['Id'] . "' data-status='approved'>Accept</button>
                <button class='btn btn-danger status-btn' data-id='" . $row['Id'] . "' data-status='rejected'>Reject</button>
                <button class='btn btn-primary save-btn' data-id='" . $row['Id'] . "'>Save</button>
              </td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No cards found.</p>";
}
?>
