<?php
include_once('db_connecter.php');
header('Content-Type: application/json');  // Ensure the response is in JSON format

if (isset($_POST['id']) && isset($_POST['content'])) {
    $id = $_POST['id'];
    $content = $_POST['content'];

    // Update the card content in the database
    $sql = "UPDATE Card SET Content = ? WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $content, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
