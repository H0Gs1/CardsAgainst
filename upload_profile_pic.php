<?php
include_once("db_connecter.php");
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    // Check if user is logged in
    if (!isset($_SESSION["user_id"])) {
        exit("You must be logged in to upload a profile picture.");
    }
    $userId = $_SESSION["user_id"];
    $targetDir = "uploads/"; // Directory to store uploaded files
    if (!is_dir($targetDir)) {
        exit("Upload directory does not exist");
    }
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    // Validate file type
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        exit("Only JPG, JPEG, PNG, and GIF files are allowed.");
    }
    // Save the file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Update the database
        $sql = "UPDATE Account SET ProfilePicture = ? WHERE Id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $targetFile, $userId);
        if ($stmt->execute()) {
            echo "Profile picture updated successfully.";
        } else {
            echo "Failed to update profile picture in database.";
        }
        $stmt->close();
    } else {
        echo "Failed to upload the file.";
    }
} else {
    exit("Invalid request.");
}
?>