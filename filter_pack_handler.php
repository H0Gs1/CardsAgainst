<?php
include 'db_connecter.php'; // Replace with your database connection file

// Fetch filter values from the POST request
$priceRange = isset($_POST['priceRange']) ? $_POST['priceRange'] : '';
$packName = isset($_POST['packName']) ? $_POST['packName'] : '';

// Construct the SQL query with filters
$sql = "SELECT * FROM Pack WHERE 1=1"; // `1=1` ensures a valid query even with no filters

// Apply price range filter
if (!empty($priceRange)) {
    $range = explode('-', $priceRange);
    $minPrice = $range[0];
    $maxPrice = $range[1];
    $sql .= " AND PackPrice BETWEEN $minPrice AND $maxPrice";
}

// Apply pack name search filter
if (!empty($packName)) {
    $sql .= " AND PackName LIKE '%" . mysqli_real_escape_string($conn, $packName) . "%'";
}

// Execute the query
$result = mysqli_query($conn, $sql);

// Generate the HTML for filtered results
if (mysqli_num_rows($result) > 0) {
    echo "<table class='table'>";
    echo "<thead class='thead-dark'>";
    echo "<tr>";
    echo "<th>Name of Pack</th>";
    echo "<th>Price of Pack (Rand)</th>";
    echo "<th>Colour of Pack</th>";
    echo "<th>Pack Description</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr class='clickable-row' 
            data-id='" . htmlspecialchars($row['Id']) . "' 
            data-packname='" . htmlspecialchars($row['PackName']) . "' 
            data-packprice='" . htmlspecialchars($row['PackPrice']) . "' 
            data-packcolour='" . htmlspecialchars($row['PackColour']) . "' 
            data-packdescription='" . htmlspecialchars($row['PackDescription']) . "'>";
        echo "<td>" . htmlspecialchars($row['PackName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PackPrice']) . "</td>";
        echo '<td style="background-color:' . htmlspecialchars($row['PackColour']) . ';"></td>';
        echo "<td>" . htmlspecialchars($row['PackDescription']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No packs found matching the filters.</p>";
}
?>
