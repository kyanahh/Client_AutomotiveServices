<?php
require("../server/connection.php");

if (isset($_POST['serviceid']) && isset($_POST['servicetype'])) {
    $serviceid = $_POST['serviceid'];
    $servicetype = $_POST['servicetype'];

    $query = $connection->prepare("UPDATE services SET servicetype = ? WHERE serviceid = ?");
    $query->bind_param("si", $servicetype, $serviceid);

    if ($query->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update service.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
