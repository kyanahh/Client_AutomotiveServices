<?php

require("../server/connection.php");

if (isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];

    // Perform any necessary validation on $appointmentId

    $appointmentId = $connection->real_escape_string($appointmentId);

    $updateQuery = "UPDATE appointments SET statsid = 2 WHERE appid = '$appointmentId'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Appointment confirmed successfully']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error confirming the appointment']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>