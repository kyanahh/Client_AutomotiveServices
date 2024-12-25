<?php
require("../server/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['servicetype'])) {
    $servicetype = $connection->real_escape_string($_POST['servicetype']);

    $query = "INSERT INTO services (servicetype) VALUES ('$servicetype')";

    if ($connection->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add service.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
