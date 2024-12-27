<?php
require("../server/connection.php");

if (isset($_POST['serviceid'])) {
    $serviceid = $_POST['serviceid'];
    $query = $connection->prepare("SELECT * FROM services WHERE serviceid = ?");
    $query->bind_param("i", $serviceid);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Service not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
