<?php 
include_once("db_connecter.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = intval($_POST['post_id']);

    $query = "UPDATE Post SET Likes = Likes + 1 WHERE Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $postId);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
