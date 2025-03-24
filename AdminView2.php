<?php
include_once('db_connecter.php');

session_start();

if (isset($_SESSION["user_id"])) {

    $mysqli = require __DIR__ . "/db_connecter.php";

    // Query to get user details
    $sql = "SELECT Username, Email, ProfilePicture FROM Account WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]); // Bind the session user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user data
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $username = $userData['Username'];
        $email = $userData['Email'];
        $profilePic = $userData['ProfilePicture'];
    } else {
        // Handle case where no user data is found
        $username = 'Guest';
        $email = 'Not available';
        $profilePic = 'default_pfp.png';
    }

/*     // Close the statement and the connection
    $stmt->close();
    $conn->close(); */
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <link rel="stylesheet" href="admin-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.min.css">
    <script src="assets\bootstrap-5.3.3\dist\js\bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>
        <h2>CARDS AGAINST_______ Admin</h2>
    </header>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <aside class="sidebar">
                    <nav class="navigation">
                        <div class="info" id="answers-type1">
                            <ul class="content">
                                <li class="nav-item"><a href="?dashboard=1">Dashboard</a></li>
                                <li class="nav-item" id="userNav"><a href="?user_management=1">User Management</a></li>
                                <li class="nav-item"><a href="?pack_management=1">Pack Management</a></li>
                                <li class="nav-item"><a href="?card_management=1">Card Management</a></li>
                                <li class="nav-item"><a href="?com_card_management=1">Community Management</a></li>
                            </ul>
                        </div>
                        
                        <div class="profile-dropdown">
                            <hr>
                            <a href="?profile_management=1">Profile</a>
                            <a href="LoginPage.php">Sign Out</a>
                        </div>
                    </nav>
                </aside>
            </div>

            <div class="col-md-10">

            <?php
            if ($_SESSION['UserRole'] == 0) {
                echo "<h5>Welcome, Admin! to</h5>";
            } else {
                echo "Welcome, User!";
            }
            if (isset($_GET['dashboard']) && $_GET['dashboard'] == 1) {
                echo "<h4>CARDS AGAINST___ Admin Dashboard</h4>";
                include('AdminDashboard.php');
                }
            ?>
            
            <?php
            if (isset($_GET['com_card_management']) && $_GET['com_card_management'] == 1) {
                echo "<h4>CARDS AGAINST___ Community Cards</h4>";
                include('communityCardsAdm.php');
                }
            ?>
            
            <?php
if (isset($_GET['pack_management']) && $_GET['pack_management'] == 1) {
    echo "<h4>Cards against____ Pack Management</h4>";
    echo '<title>Pack Management</title>';
    $Pack = array('PackName', 'PackPrice', 'PackColour', 'PackDescription');
    $packCount = count($Pack);

    // Set the number of records per page
    $resultsPerPage = 10;

    // Get the current page from the URL (default to 1 if not set)
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $startLimit = ($currentPage - 1) * $resultsPerPage;

    // Initialize filter variables
    $priceRange = isset($_POST['priceRange']) ? $_POST['priceRange'] : '';
    $packName = isset($_POST['packName']) ? $_POST['packName'] : '';

    // Construct the SQL query with filters
    $sqlCount = "SELECT COUNT(*) AS totalRecords FROM Pack WHERE 1=1"; // Base query for counting records
    $sql = "SELECT * FROM Pack WHERE 1=1"; // Base query for fetching records

    // Apply price range filter
    if (!empty($priceRange)) {
        $range = explode('-', $priceRange);
        $minPrice = $range[0];
        $maxPrice = $range[1];
        $sql .= " AND PackPrice BETWEEN $minPrice AND $maxPrice";
        $sqlCount .= " AND PackPrice BETWEEN $minPrice AND $maxPrice";
    }

    // Apply pack name search filter
    if (!empty($packName)) {
        $sql .= " AND PackName LIKE '%" . mysqli_real_escape_string($conn, $packName) . "%'";
        $sqlCount .= " AND PackName LIKE '%" . mysqli_real_escape_string($conn, $packName) . "%'";
    }

    // Get the total number of records to calculate the number of pages
    $countResult = mysqli_query($conn, $sqlCount);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['totalRecords'];
    $totalPages = ceil($totalRecords / $resultsPerPage);

    // SQL query to fetch pack records with LIMIT for pagination
    $sql .= " LIMIT $startLimit, $resultsPerPage"; // Add LIMIT to the filtered query
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    // Include the filter form
    include_once("filter_pack.php");

    // Add an empty div to load filtered results
    echo '<div id="filtered-results"><strong></strong></div>';

    if ($resultCheck > 0) {
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
            echo "<tr class='clickable-row' data-id='" . $row['Id'] . 
            "' data-packname='" . $row['PackName'] . 
            "' data-packprice='" . $row['PackPrice'] . 
            "' data-packcolour='" . $row['PackColour'] . 
            "' data-packdescription='" . $row['PackDescription'] . "'>";
            
            // Loop through the Pack columns
            for ($i = 0; $i < $packCount; $i++) {
                if ($Pack[$i] === 'PackColour') {
                    echo '<td style="background-color:' . $row['PackColour'] . ';"></td>';
                } else {
                    echo "<td>" . $row[$Pack[$i]] . "</td>";
                }                            
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        // Pagination with Bootstrap
        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination justify-content-center'>";

        // Previous page link
        if ($currentPage > 1) {
            echo "<li class='page-item'><a class='page-link' href='?pack_management=1&page=" . ($currentPage - 1) . "'>Previous</a></li>";
        } else {
            echo "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
        }

        // Page number links
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $currentPage) ? ' active' : '';
            echo "<li class='page-item$activeClass'><a class='page-link' href='?pack_management=1&page=$i'>$i</a></li>";
        }

        // Next page link
        if ($currentPage < $totalPages) {
            echo "<li class='page-item'><a class='page-link' href='?pack_management=1&page=" . ($currentPage + 1) . "'>Next</a></li>";
        } else {
            echo "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
        }

        echo "</ul>";
        echo "</nav>";
    }
} else {
    echo " ";
}
?>

<?php
if (isset($_GET['card_management']) && $_GET['card_management'] == 1) {
    echo '<title>Card Management</title>';
    $Card = array('Content', 'IsAnswer', 'IsCommunity', 'PackName');
    $cardCount = count($Card);

    // Rows per page
    $rowsPerPage = 10; // Number of rows to display per page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page or default to 1
    $offset = ($currentPage - 1) * $rowsPerPage; // Calculate the offset for SQL

    // Initialize filter variables
    $packNameFilter = isset($_GET['pack_name']) ? $_GET['pack_name'] : '';
    $isCommunityFilter = isset($_GET['is_community']) ? (int)$_GET['is_community'] : -1; // -1 means no filter
    $isAnswerFilter = isset($_GET['is_answer']) ? (int)$_GET['is_answer'] : -1; // -1 means no filter

    // Build the WHERE clause based on filters
    $whereClauses = [];
    if ($packNameFilter !== '') {
        $whereClauses[] = "Pack.PackName LIKE '%" . $mysqli->real_escape_string($packNameFilter) . "%'";
    }
    if ($isCommunityFilter != -1) {
        $whereClauses[] = "Card.IsCommunity = " . $isCommunityFilter;
    }
    if ($isAnswerFilter != -1) {
        $whereClauses[] = "Card.IsAnswer = " . $isAnswerFilter;
    }

    // Combine WHERE clauses
    $whereSql = '';
    if (count($whereClauses) > 0) {
        $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
    }

    // Query to get the total number of rows with filters
    $totalRowsQuery = "SELECT COUNT(*) AS totalRecords FROM Card LEFT JOIN Pack ON Card.PackId = Pack.Id $whereSql";
    $totalRowsResult = $mysqli->query($totalRowsQuery);
    $totalRows = $totalRowsResult->fetch_assoc()['totalRecords'];

    // Calculate the total number of pages
    $totalPages = ceil($totalRows / $rowsPerPage);

    // Query to fetch paginated rows with filters
    $dataQuery = "SELECT Card.*, Pack.PackName 
                  FROM Card 
                  LEFT JOIN Pack ON Card.PackId = Pack.Id 
                  $whereSql 
                  LIMIT $offset, $rowsPerPage";
    $result = $mysqli->query($dataQuery);

    if ($result->num_rows > 0) {
        echo "<h4 class='mb-4'>Cards Against____ Card Management</h4>";

        // Filter form
echo '<form method="GET" action="" class="mb-4">';
echo '<input type="hidden" name="card_management" value="1" />';
echo '<div class="form-row align-items-center">'; // Align items vertically at the center

// Combined filters and button in one echo
echo '<div class="row">
    <div class="form-group col-auto">
        <label for="pack_name" class="sr-only">Pack Name</label>
        <input type="text" name="pack_name" class="form-control form-control-sm" id="pack_name" style="width: 150px;" placeholder="Pack Name" value="' . htmlspecialchars($packNameFilter) . '">
    </div>
    <div class="form-group col-auto">
        <label for="is_community" class="sr-only">Is Community</label>
        <select name="is_community" class="form-control form-control-sm" id="is_community" style="width: 150px;">
            <option value="-1"' . ($isCommunityFilter == -1 ? ' selected' : '') . '>All</option>
            <option value="1"' . ($isCommunityFilter == 1 ? ' selected' : '') . '>Yes</option>
            <option value="0"' . ($isCommunityFilter == 0 ? ' selected' : '') . '>No</option>
        </select>
    </div>
    <div class="form-group col-auto">
        <label for="is_answer" class="sr-only">Is Answer</label>
        <select name="is_answer" class="form-control form-control-sm" id="is_answer" style="width: 150px;">
            <option value="-1"' . ($isAnswerFilter == -1 ? ' selected' : '') . '>All</option>
            <option value="1"' . ($isAnswerFilter == 1 ? ' selected' : '') . '>Yes</option>
            <option value="0"' . ($isAnswerFilter == 0 ? ' selected' : '') . '>No</option>
        </select>
    </div>
    <div class="form-group col-auto">
        <input type="submit" value="Filter" class="btn btn-primary btn-sm">
    </div>
    </div>
';

echo '</div>'; // Close form-row
echo '</form>';

echo "<table class='table table-striped table-bordered'>";
echo "<thead class='thead-dark'>";
echo "<tr>";
echo "<th>Content</th>";
echo "<th>Answer?</th>";
echo "<th>Community?</th>";
echo "<th>Name of Pack</th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='clickable-row' data-id='" . $row['Id'] . 
                 "' data-content='" . $row['Content'] . 
                 "' data-isanswer='" . $row['IsAnswer'] . 
                 "' data-iscommunity='" . $row['IsCommunity'] . 
                 "' data-packname='" . $row['PackName'] . 
                 "' data-packid='" . $row['PackId'] . "'>";  

            // Loop through the Card columns
            for ($i = 0; $i < $cardCount; $i++) {
                if ($Card[$i] == 'IsAnswer' || $Card[$i] == 'IsCommunity') {
                    echo "<td>" . ($row[$Card[$i]] == 1 ? 'Yes' : 'No') . "</td>";
                } else {
                    if ($Card[$i] == 'PackName') {
                        echo "<td>" . $row['PackName'] . "</td>";
                    } else {
                        echo "<td>" . $row[$Card[$i]] . "</td>";
                    }
                }
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        // Pagination 
        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination justify-content-center'>";

        // Previous button
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            echo "<li class='page-item'><a class='page-link' href='?card_management=1&page=$prevPage&pack_name=" . urlencode($packNameFilter) . "&is_community=$isCommunityFilter&is_answer=$isAnswerFilter'>Previous</a></li>";
        }

        // First Page Button
        if ($currentPage > 1) {
            echo "<li class='page-item'><a class='page-link' href='?card_management=1&page=1&pack_name=" . urlencode($packNameFilter) . "&is_community=$isCommunityFilter&is_answer=$isAnswerFilter'>First</a></li>";
        }

        // Page number links
        $aroundPages = 5; // Number of pages to show around the current page
        $startPage = max(1, $currentPage - $aroundPages);
        $endPage = min($totalPages, $currentPage + $aroundPages);
        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i == $currentPage) ? ' active' : '';
            echo "<li class='page-item$activeClass'><a class='page-link' href='?card_management=1&page=$i&pack_name=" . urlencode($packNameFilter) . "&is_community=$isCommunityFilter&is_answer=$isAnswerFilter'>$i</a></li>";
        }

        // Last Page Button
        if ($currentPage < $totalPages) {
            echo "<li class='page-item'><a class='page-link' href='?card_management=1&page=$totalPages&pack_name=" . urlencode($packNameFilter) . "&is_community=$isCommunityFilter&is_answer=$isAnswerFilter'>Last $totalPages</a></li>";
        }

        // Next button
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            echo "<li class='page-item'><a class='page-link' href='?card_management=1&page=$nextPage&pack_name=" . urlencode($packNameFilter) . "&is_community=$isCommunityFilter&is_answer=$isAnswerFilter'>Next</a></li>";
        }

        echo "</ul>";
        echo "</nav>";
    } else {
        echo "<p>No cards found.</p>";
    }
}
?>



            <?php
            if (isset($_GET['user_management']) && $_GET['user_management'] == 1) {
                echo '<title>User Management</title>';
                $Account = array('UserName', 'Email', 'UserRole'); 
                $accountCount = count($Account); 

                // Set the number of records per page
                $recordsPerPage = 10;

                // Get the current page from the URL (default to 1 if not set)
                $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($currentPage - 1) * $recordsPerPage;

                // Get the total number of records to calculate the number of pages
                $sqlCount = "SELECT COUNT(*) FROM Account";
                $resultCount = mysqli_query($conn, $sqlCount);
                $rowCount = mysqli_fetch_array($resultCount);
                $totalRecords = $rowCount[0];
                $totalPages = ceil($totalRecords / $recordsPerPage);

                // SQL query to fetch account records with LIMIT for pagination
                $sql = "SELECT * FROM Account LIMIT $offset, $recordsPerPage"; 
                $result = mysqli_query($conn, $sql);
                $resultCheck = mysqli_num_rows($result);

                if ($resultCheck > 0) {
                    echo "<h4>Cards against____ User Management</h4>";
                    echo "<table class='table'>"; 
                    echo "<tr>";
                    echo "<th>Username</th>";
                    echo "<th>Email</th>";
                    echo "<th>UserRole</th>";
                    echo "</tr>";

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='clickable-row' data-id='" . $row['Id'] . 
                        "' data-userName='" . $row['UserName'] . 
                        "' data-password='" . $row['Password'] . 
                        "' data-email='" . $row['Email'] . 
                        "' data-userRole='" . $row['UserRole'] . "'>";
                        for ($i = 0; $i < $accountCount; $i++) {
                            if ($Account[$i] == 'Password') {
                                // Mask the password field with asterisks
                                echo "<td>" . str_repeat('*', strlen($row[$Account[$i]])) . "</td>";
                            } else if ($Account[$i] == 'UserRole') {
                                echo "<td>" . ($row[$Account[$i]] == 1 ? 'User' : 'Admin') . "</td>";
                            } else {
                                echo "<td>" . $row[$Account[$i]] . "</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    echo "</table>";

                    // Pagination Links
                    echo "<nav aria-label='Page navigation'>";
                    echo "<ul class='pagination justify-content-center'>";

                    // Previous page link
                    if ($currentPage > 1) {
                        echo "<li class='page-item'><a class='page-link' href='?user_management=1&page=" . ($currentPage - 1) . "'>Previous</a></li>";
                    } else {
                        echo "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
                    }
                    // Page number links
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i == $currentPage) ? ' active' : '';
                        echo "<li class='page-item$activeClass'><a class='page-link' href='?user_management=1&page=$i'>$i</a></li>";
                    }
                    // Next page link
                    if ($currentPage < $totalPages) {
                        echo "<li class='page-item'><a class='page-link' href='?user_management=1&page=" . ($currentPage + 1) . "'>Next</a></li>";
                    } else {
                        echo "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
                    }
                    echo "</ul>";
                    echo "</nav>";
                }
            }
            ?>

            <?php
                if (isset($_GET['profile_management']) && $_GET['profile_management'] == 1) {
                    require('update_profile.php');
                    }
                ?>
    
                <div class="container mt-3" id="add-button-container" style="display: none;">
                    <button type="button" id="addPack" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPackModal">
                        Add Pack
                    </button>
                </div>
                <div class="container mt-3" id="add-card-button-container" style="display: none;">
                <button type="button" id="addCard" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCardModal">
                    Add Card Manually
                </button>
                <button type="button" id="importCard" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCardModal">
                    Import Card
                </button>
                </div>


            </div>
        </div>
    </div>

        <!-- Modal Structure for Editing users -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <div class="mb-3">
                            <label for="modal-user-id" class="form-label" style="display: none;">Id</label>
                            <input type="text" class="form-control" id="modal-user-id" disabled style="display: none;">
                        </div>
                        <div class="mb-3">
                            <label for="modal-user-name" class="form-label">Username</label>
                            <input class="form-control" id="modal-user-name" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-user-password" class="form-label">Password</label>
                            <input type ="password" class="form-control" id="modal-user-password" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-user-email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="modal-user-email" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-user-role" class="form-label">User Role</label>
                            <select class="form-control" id="modal-user-role" disabled>

                            </select>
                        </div>
                    </form>
                    <button type="button" class="btn btn-success" id="saveUserChangesButton" style="display:none;">Save Changes</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="editUserButton" style="margin-right: 10px;">Edit</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for Editing Card -->
    <div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="cardModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardModalLabel">Card Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCardForm">
                        <div class="mb-3">
                            <label for="modal-card-id" class="form-label" style="display: none;">Id</label>
                            <input type="text" class="form-control" id="modal-card-id" disabled style="display: none;">
                        </div>
                        <div class="mb-3">
                            <label for="modal-card-content" class="form-label">Content</label>
                            <textarea class="form-control" id="modal-card-content" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modal-card-isanswer" class="form-label">Is Answer</label>
                            <select class="form-control" id="modal-card-isanswer" disabled>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal-card-iscommunity" class="form-label">Is Community</label>
                            <select class="form-control" id="modal-card-iscommunity" disabled>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal-card-packname" class="form-label">Pack Name</label>
                            <select class="form-control" id="modal-card-packname" disabled>
                            <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </form>
                    <button type="button" class="btn btn-success" id="saveCardChangesButton" style="display:none;">Save Changes</button>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" id="editCardButton"style="margin-right: 10px;" >Edit</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for Editing Pack -->
    <div class="modal fade" id="packModal" tabindex="-1" aria-labelledby="packModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packModalLabel">Pack Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPackForm">
                        <div class="mb-3">
                            <label for="modal-pack-id" class="form-label">Id</label>
                            <input type="text" class="form-control" id="modal-pack-id" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-packname" class="form-label">Pack Name</label>
                            <input type="text" class="form-control" id="modal-packname" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-packprice" class="form-label">Pack Price</label>
                            <input type="text" class="form-control" id="modal-packprice" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-packcolour" class="form-label">Pack Colour</label>
                            <input type="color" class="form-control" id="modal-packcolour" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="modal-packdescription" class="form-label">Pack Description</label>
                            <textarea class="form-control" id="modal-packdescription" maxlength="250" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <ul class="no-bullets" id="cardList">

                            </ul>
                        </div>
                    </form>
                    <button type="button" class="btn btn-success" id="savePackChangesButton" style="display:none;">Save Changes</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="editPackButton" >Edit</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Structure for Manual Import(pack)-->
    <div class="modal fade" id="manualPackImport" tabindex="-1" aria-labelledby="manualPackImportLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <h5 class="modal-title" id="manualPackImportLabel">Add Pack</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="manualPackImportForm">
                        <div class="mb-3">
                            <label for="manualPackImport-content" class="form-label">Name</label>
                            <input type="text" class="form-control" id="manualPackImport-content">
                        </div>
                        <div class="mb-3">
                            <label for="manualPackImport-isAnswer" class="form-label">Price</label>
                            <input type="text" class="form-control" id="manualPackImport-isAnswer">
                        </div>
                        <div class="mb-3">
                            <label for="manualPackImport-isCommunity" class="form-label">Colour</label>
                            <input type="color" class="form-control" id="manualPackImport-isCommunity">
                        </div>
                        <div class="mb-3">
                            <label for="manualPackImport-packName" class="form-label">Description</label>
                            <input type="text" class="form-control" id="manualPackImport-packName" maxlength="250">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="manualPackImport-save">Add Pack</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Structure for Manual Import(card)-->
    <div class="modal fade" id="manualImport" tabindex="-1" aria-labelledby="manualImportLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <h5 class="modal-title" id="manualImportLabel">Add Card</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="manualImportForm">
                        <div class="mb-3">
                            <label for="manualImport-content" class="form-label">Content</label>
                            <textarea class="form-control" id="manualImport-content" ></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="manualImport-isAnswer" class="form-label">Is Answer?</label>
                            <select class="form-control" id="manualImport-isAnswer" >
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="manualImport-isCommunity" class="form-label">Is Community?</label>
                            <select class="form-control" id="manualImport-isCommunity">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="manualImport-packName" class="form-label">Pack Name</label>
                            <select class="form-control" id="manualImport-packName">
                            <!-- Options will be placed dynamically -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="manualImport-save">Add Card</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for Import Card -->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Select File to Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form name="upload_excel" enctype="multipart/form-data" id="uploadFileForm">
                        <div class="form-group">
                            <label for="file">Select File</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv">
                        </div>
                        <button type="submit" id="submit" name="Import" class="btn btn-secondary mt-3">Import Data</button>
                        <div id="error" style="color: red; margin-top: 10px;"></div>
                    </form>
                    <br>
                    <div class="disclamer">
                        <h5> IMPORTANT INFO:</h5>
                        <br>
                        <p>Only CSV files can be uploaded</p>
                        <p>We <strong>ONLY</strong> use Google sheets</p>
                        <p>Data in each column should be <strong> in this order</strong>:</p>
                        <ul>
                            <li>Card Content</li>
                            <li>Black/White card (0 for Black/1 for White)</li>
                            <li>Community card yes or no (ALWAYS 1 since 1 = no)</li>
                            <li>Pack Id (Refer to Pack Management tab)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
        // Fetch all packs from the Pack table
        $sql = "SELECT Id, PackName FROM Pack";
        $result = mysqli_query($conn, $sql);
        $packs = [];

        if (mysqli_num_rows($result) > 0) {            
            while ($row = mysqli_fetch_assoc($result)) {
                $packs[] = $row; // Store pack data
            }
        }
    ?>
    <?php
        // Fetch all UserRoles from the Account table
        $sql = "SELECT Id, roleCharacter FROM UserRole";
        $result = mysqli_query($conn, $sql);
        $roles = [];


        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $roles[] = $row; // Store pack data
            }
        }
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script>

        
$(document).ready(function() {
    /////////////////////////////
    //User management code starts
    /////////////////////////////


    $('#uploadFileForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create a FormData object with the form data

        $.ajax({
            url: 'import.php', // The PHP script to handle the file upload
            type: 'POST',
            data: formData,
            contentType: false, // Not to set content type (multipart)
            processData: false, // Not to process the data
            success: function (response) {
                console.log(response);
                // Handle the success response (response is the message from PHP)
                if (response.includes("success!")) {
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'File Uploaded Successfully!',
                        text: 'The file has been processed and uploaded.',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        }
                    }).then(function() {
                         // Redirect after success
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: response,
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        }
                    });
                }
            },
            error: function () {
                console.log(response)
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'There was an issue with the file upload.',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                });
            }
        });
    });







    var dynamicTitle = "Line 1 <br /> Line 2 \n Line 3";
    
    // Set the title dynamically before initializing the popover
    $('#popoverButton').attr('content', dynamicTitle);
  $('[data-toggle="popover"]').popover();
   

    if (window.location.search.includes("card_management=1")) {
    $("#add-card-button-container").show();
    }
    //checks if user tab is active
    if (window.location.search.includes("user_management=1")) {

        //$('#userNav').css('background-color', '#151F3A');
       // $('#userNav').css('color', 'white !important');

        var roles = <?php echo json_encode($roles); ?>;
        var $roleSelect = $('#modal-user-role');// Populate the role dropdown
         
        // Loop through rols and append them to the dropdown
        $.each(roles, function(index, role) {
            $roleSelect.append('<option value="' + role.Id + '">' + role.roleCharacter + '</option>');
        });
        
        //fetches the row data when the row is clicked
        $(".clickable-row").click(function() {
            var id = $(this).data('id');
            var name = $(this).data('username');
            var password = $(this).data('password');
            var email = $(this).data('email');
            var userRole = $(this).attr('data-userRole');

            $.ajax({
                url: 'card_amount.php',  // Replace with the actual path to your PHP script
                type: 'POST',  // POST request method
                data: id,  // Data to send (optional)
                dataType: 'json',  // Expected response type (JSON)
                success: function(response) {
                    // Handle the JSON response
                    console.log(response);  // Log the response for debugging
                    
                    // Access the data from the response
                    console.log('Answer Cards:', response.Answer_Cards);
                    console.log('Question Cards:', response.Question_Cards);
                    console.log('Community Answer Cards:', response.Community_Answer_Cards);
                    console.log('Community Question Cards:', response.Community_Question_Cards);
                    
                    // You can use the response data to update the UI or handle it further
                    const items = [
                        'Total Black Cards: ' + response.Question_Cards,
                        'Total White Cards: ' + response.Answer_Cards,
                        'Total Community Black Cards: ' + response.Community_Question_Cards,
                        'Total Community White Cards: ' +response.Community_Question_Cards
                    ];

                    // Get the <ul> element by its ID
                    const ul = document.getElementById('cardList');
                    
                    // Loop through the items array
                    items.forEach(function(item) {
                        // Create a new <li> element
                        const li = document.createElement('li');
                        
                        // Set the text content of the <li> element
                        li.textContent = item;
                        
                        // Append the <li> element to the <ul>
                        ul.appendChild(li);
                    });
                },
                error: function(xhr, status, error) {
                    // Handle any errors
                    console.error('Request failed:', error);
                }
            });



            // Set values in modal for users
            $("#modal-user-id").val(id);
            $("#modal-user-name").val(name);
            $("#modal-user-password").val(password);
            $("#modal-user-email").val(email);
            $("#modal-user-role").val(userRole);

            console.log(userRole);

            // Shows the user modal
            var myModal = new bootstrap.Modal(document.getElementById('userModal'));
            myModal.show();
            
            // Enable fields for editing on click of "Edit" button
            $("#editUserButton").click(function() {
                $("#modal-user-name").prop('disabled', false);
                $("#modal-user-password").prop('disabled', false);
                $("#modal-user-email").prop('disabled', false);
                $("#modal-user-role").prop('disabled', false);
                $("#saveUserChangesButton").show();
                $("#editUserButton").hide();
            });

            // Handle Save Changes for users
            $("#saveUserChangesButton").off('click').on('click', function() {
                var updatedData = {
                    id: $("#modal-user-id").val(),
                    username: $("#modal-user-name").val(),
                    password: $("#modal-user-password").val(),
                    email: $("#modal-user-email").val(),
                    userrole: $("#modal-user-role").val()
                };

                console.log(updatedData);
                // Ajax for posting to the database
                $.ajax({
    url: 'update_user.php',
    type: 'POST',
    data: updatedData,
    success: function(response) {
        var data = JSON.parse(response);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'User updated successfully!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Reload the page to reflect changes
                location.reload();
            });
        } else {
            // Display the error message if validation failed
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error,
                confirmButtonText: 'OK'
            });
        }
    },
    error: function(xhr, status, error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error updating the user. Please try again.',
            confirmButtonText: 'OK'
        });
    }
});

            });


            // Close modal and reset form when "Close" or "X" is clicked
            $("#userModal .btn-close, #userModal .btn-danger").click(function() {
                // Reset the fields to disabled on close
                $("#modal-user-name").prop('disabled', true);
                $("modal-user-password").prop('disabled', true);
                $("#modal-user-email").prop('disabled', true);
                $("#modal-user-role").prop('disabled', true);
                $("#saveUserChangesButton").hide();
                $("#editUserButton").show();
                myModal.hide();
            });
        });
    }

    //user management code ends

    //pack management code starts 
    
    if (window.location.search.includes("pack_management=1")) {
        $("#add-button-container").show();
    }

    // Handle Pack Management (clickable rows for packs)
    if (window.location.search.includes("pack_management=1")) {
            //show manual add modal
            $("#addPack").click(function() {
            var myModal2 = new bootstrap.Modal(document.getElementById('manualPackImport'));
            myModal2.show();
        });

        ////////////////////////
        //Handle Pack Addition////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////

        $("#manualPackImport-save").click(function(){
            if ($("#manualPackImport-content").val() === "" || $("#manualPackImport-isAnswer").val() === "" || $("#manualPackImport-content").val() === "" || $("#manualPackImport-isAnswer").val() === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Fill in ALL the options please',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                    })
            } else {
                var updatedData = {
                packname: $("#manualPackImport-content").val(),
                packprice: $("#manualPackImport-isAnswer").val(),
                packcolour: $("#manualPackImport-isCommunity").val(),
                packdescription: $("#manualPackImport-packName").val()
            }; 

            $.ajax({
                url: 'add_pack.php',
                type: 'POST',
                data: updatedData,
                success: function(response) {
                    Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Pack has been added',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                    })
                    // Reset the fields to disabled on close
                    $("#manualPackImport-content").prop('disabled', false);
                    $("#manualPackImport-isAnswer").prop('disabled', false);
                    $("#manualPackImport-isCommunity").prop('disabled', false);
                    $("#manualPackImport-packName").prop('disabled', false);
                    
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                    icon: 'danger',
                    title: 'Failed!',
                    text: 'Pack has NOT been added',
                    })
                }
            });
                
            }

        });

        $(".clickable-row").click(function() {
            var id = $(this).data('id');
            var packName = $(this).data('packname');
            var packPrice = $(this).data('packprice');
            var packColour = $(this).data('packcolour');
            var packDescription = $(this).data('packdescription');

            // Set values in modal for pack
            $("#modal-pack-id").val(id);
            $("#modal-packname").val(packName);
            $("#modal-packprice").val(packPrice);
            $("#modal-packcolour").val(packColour);
            $("#modal-packdescription").val(packDescription);

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('packModal'));
            myModal.show();

            // Enable fields for editing on click of "Edit" button
            $("#editPackButton").click(function() {
                $("#modal-packname").prop('disabled', false);
                $("#modal-packprice").prop('disabled', false);
                $("#modal-packcolour").prop('disabled', false);
                $("#modal-packdescription").prop('disabled', false);
                $("#savePackChangesButton").show();
                $("#editPackButton").hide();
            });

            // Handle Save Changes for Pack
            $("#savePackChangesButton").off('click').on('click', function() {

                if ($("#modal-packname").val() === "" || $("#modal-packprice").val() === "" || $("#modal-packcolour").val() === "" || $("#modal-packdescription").val() === "") {
                    Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Fill in ALL the options please',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                    })
                } else {
                    var updatedData = {
                    id: $("#modal-pack-id").val(),
                    packname: $("#modal-packname").val(),
                    packprice: $("#modal-packprice").val(),
                    packcolour: $("#modal-packcolour").val(),
                    packdescription: $("#modal-packdescription").val()
                }; 
                
                $.ajax({
    url: 'update_packs.php',
    type: 'POST',
    data: updatedData,
    success: function(response) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Pack has been modified',
            customClass: {
                confirmButton: 'btn btn-secondary' // Use Bootstrap 5 'btn' class for the button
            }
        }).then(() => {
            // Reload the page to reflect changes
            location.reload();
        });

        // Optional: Perform UI updates before the reload if needed
        $("#modal-packname").prop('disabled', true);
        $("#modal-packprice").prop('disabled', true);
        $("#modal-packcolour").prop('disabled', true);
        $("#modal-packdescription").prop('disabled', true);
        $("#savePackChangesButton").hide();
        $("#editPackButton").show();
        myModal.hide();
    },
    error: function(xhr, status, error) {
        Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Pack has NOT been modified',
        });
    }
});

                    
                }

            });

            // Close modal and reset form when "Close" or "X" is clicked
            $("#packModal .btn-close, #packModal .btn-danger").click(function() {
                // Reset the fields to disabled on close
                $("#modal-packname").prop('disabled', true);
                $("#modal-packprice").prop('disabled', true);
                $("#modal-packcolour").prop('disabled', true);
                $("#modal-packdescription").prop('disabled', true);
                $("#savePackChangesButton").hide();
                $("#editPackButton").show();
                myModal.hide();
            });
        });
    }
    ///////////////////////////
    // Handle Card Management////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////
    
    if (window.location.search.includes("card_management=1")) {
        $("#add-card-button-container").show();
    }

    if (window.location.search.includes("card_management=1")) {
        //show manual add modal
        $("#addCard").click(function() {
            var myModal2 = new bootstrap.Modal(document.getElementById('manualImport'));
            myModal2.show();
        });

        var packs = <?php echo json_encode($packs); ?>;
        var $packSelect = $('#modal-card-packname');// Populate the Pack Name dropdown

        // Add default "Select a Pack" option
        $packSelect.append('<option value="">Select a Pack</option>');
        
        // Loop through packs and append them to the dropdown
        $.each(packs, function(index, pack) {
            $packSelect.append('<option value="' + pack.Id + '">' + pack.PackName + '</option>');
        });

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var packs = <?php echo json_encode($packs); ?>;
    
        // Populate the Pack Name dropdown
        var $packSelect = $('#manualImport-packName');

        
        // Add default "Select a Pack" option
        $packSelect.append('<option value="">Select a Pack</option>');
        
        // Loop through packs and append them to the dropdown
        $.each(packs, function(index, pack) {
            $packSelect.append('<option value="' + pack.Id + '">' + pack.PackName + '</option>');
        });
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $("#manualImport-save").click(function(){

            if ($("#manualImport-content").val() === "" || $("#manualImport-isAnswer").val() === "" || $("#manualImport-isCommunity").val() ==="" || $("#manualImport-packName") ==="") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Fill in ALL the options please',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                    })
            } else {
                var updatedData = {
                content: $("#manualImport-content").val(),
                isAnswer: $("#manualImport-isAnswer").val(),
                isCommunity: $("#manualImport-isCommunity").val(),
                packId: $("#manualImport-packName").val() // Send PackId instead of PackName
                }; 

                console.log(updatedData);

                $.ajax({
                    url: 'add_card.php',
                    type: 'POST',
                    data: updatedData,
                    success: function(response) {
                        Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Card has been added',
                        customClass: {
                        confirmButton: 'btn btn-secondary'  // Use Bootstrap 5 'btn' class for the button
                    }
                        })
                         // Reset the fields to disabled on close
                        $("#manualImport-content").prop('disabled', false);
                        $("#manualImport-isAnswer").prop('disabled', false);
                        $("#manualImport-isCommunity").prop('disabled', false);
                        $("#manualImport-packName").prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Card has NOT been added',
                    })
                    }
                });
            }
        });

        $(".clickable-row").click(function() {
    var id = $(this).data('id');
    var content = $(this).data('content');
    var isAnswer = $(this).data('isanswer');
    var isCommunity = $(this).data('iscommunity');
    var packName = $(this).data('packname');  
    var packId = $(this).data('packid');     

    // Set values in modal for card
    $("#modal-card-id").val(id);
    $("#modal-card-content").val(content);
    $("#modal-card-isanswer").val(isAnswer);
    $("#modal-card-iscommunity").val(isCommunity);

    // Set the correct PackName in the dropdown
    $("#modal-card-packname").val(packId); // Set the PackId, which will select the right PackName in the dropdown

    // Show the modal
    var myModal = new bootstrap.Modal(document.getElementById('cardModal'));
    myModal.show();

    // Enable fields for editing on click of "Edit" button
    $("#editCardButton").click(function() {
        $("#modal-card-content").prop('disabled', false);
        $("#modal-card-isanswer").prop('disabled', false);
        $("#modal-card-iscommunity").prop('disabled', false);
        $("#modal-card-packname").prop('disabled', false);
        $("#saveCardChangesButton").show();
        $("#editCardButton").hide();
    });

    // Handle Save Changes for Card
    $("#saveCardChangesButton").off('click').on('click', function() {

        if ($("#modal-card-content").val() === "" || $("#modal-card-isanswer").val() ==="" || $("#modal-card-iscommunity").val() == "") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Fill in ALL the options please',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                }
            })
        } else {
            var updatedData = {
            id: $("#modal-card-id").val(),
            content: $("#modal-card-content").val(),
            isanswer: $("#modal-card-isanswer").val(),
            iscommunity: $("#modal-card-iscommunity").val(),
            packid: $("#modal-card-packname").val() // Send the PackId (not PackName)
        };

        $.ajax({
    url: 'update_cards.php',
    type: 'POST',
    data: updatedData,
    success: function(response) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Card has been modified',
            customClass: {
                confirmButton: 'btn btn-secondary' // Use Bootstrap 5 'btn' class for the button
            }
        }).then(() => {
            // Reload the page to reflect changes
            location.reload();
        });

        // Optional: Perform additional UI actions before reload if necessary
        myModal.hide();
    },
    error: function(xhr, status, error) {
        Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Card has NOT been modified',
        });
    }
});


        $("#modal-card-content").prop('disabled', true);
        $("#modal-card-isanswer").prop('disabled', true);
        $("#modal-card-iscommunity").prop('disabled', true);
        $("#modal-card-packname").prop('disabled', true);
        $("#saveCardChangesButton").hide();
        $("#editCardButton").show();  
        }
        

    });

        });
    }
});

document.getElementById("importCard").onclick = function() {
            var fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
            fileModal.show();
        }

        $("#answers-type1 li").click(function() {
            $(this).css("background-color", "var(--bs-secondary)");
        });
    </script>
</body>
</html>
