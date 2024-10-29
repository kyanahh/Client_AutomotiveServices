<?php

require("../server/connection.php");

if (isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];

    $appointmentId = $connection->real_escape_string($appointmentId);

    $updateQuery = "UPDATE appointments SET statsid = 3 WHERE appid = '$appointmentId'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Appointment set to Ongoing successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error updating the appointment to Ongoing']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>