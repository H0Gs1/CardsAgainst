<?php
include_once('db_connecter.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST["id"];
}

// Prepare and execute the query
$stmt = $conn->prepare('SELECT 
    COUNT(CASE WHEN IsAnswer = 1  THEN 1 END) AS Answer_Cards,
    COUNT(CASE WHEN IsAnswer = 0 THEN 1 END) AS Question_Cards,
    COUNT(CASE WHEN IsAnswer = 1 AND IsCommunity = 1 THEN 1 END) AS Community_Answer_Cards,
    COUNT(CASE WHEN IsAnswer = 0 AND IsCommunity = 0 THEN 1 END) AS Community_Question_Cards
FROM Card
WHERE PackId = ?;');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($white, $black, $cWhite, $cBlack);

// Fetch the result (in case there is only one row)
$stmt->fetch();

// Create an associative array with the results
$data = [
    'Answer_Cards' => $white,
    'Question_Cards' => $black,
    'Community_Answer_Cards' => $cWhite,
    'Community_Question_Cards' => $cBlack
];

// Encode the data as JSON and output it
echo json_encode($data);
?>
