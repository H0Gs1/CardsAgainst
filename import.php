<?php
header('Content-Type: text/plain'); // or 'application/json' if returning JSON;
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('db_connecter.php');

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $filename = $_FILES['file']['tmp_name'];
    $originalFilename = $_FILES['file']['name'];

    // Check if the file is a valid CSV file
    $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
    if (($fileExtension === 'csv') && (mime_content_type($filename) == 'text/plain' || mime_content_type($filename) == 'application/vnd.ms-excel')) {
        if ($_FILES['file']['size'] > 0) {
            // Open the file and start reading
            $file = fopen($filename, 'r');
            $importSuccess = true;

            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                // Prepare SQL query
                $stmt = $conn->prepare("INSERT INTO Card (Content, IsAnswer, IsCommunity, PackId) 
                                       VALUES (?, ?, ?, ?)");
                $stmt->bind_param("siis", $getData[0], $getData[1], $getData[2], $getData[3]);

                $result = $stmt->execute();

                if (!$result) {
                    $importSuccess = false;
                    break; // Stop if any query fails
                }
            }

            fclose($file);

            if ($importSuccess) {
                echo "success! File has uploaded"; // Send success response to AJAX
            } else {
                echo "Error occurred while inserting data into the database.";
            }
        } else {
            echo "File is empty.";
        }
    } else {
        echo "Invalid file format. Only CSV files are allowed.";
    }
} else {
    echo "No file uploaded or there was an error during the file upload.";
}
?>

