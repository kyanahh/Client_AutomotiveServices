<?php

require("../server/connection.php");

if (isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];

    $appointmentId = $connection->real_escape_string($appointmentId);

    $updateQuery = "UPDATE appointments SET statsid = 4 WHERE appid = '$appointmentId'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Appointment set to Done successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error updating the appointment to Done']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>