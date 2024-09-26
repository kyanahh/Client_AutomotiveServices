<?php

require("../server/connection.php");

if (isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];

    $appointmentId = $connection->real_escape_string($appointmentId);

    $updateQuery = "UPDATE appointments SET statsid = 5 WHERE appid = '$appointmentId'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(array('success' => 'Appointment cancelled successfully'));
    } else {
        // Log the error for debugging
        error_log("Error: " . $connection->error);
        echo json_encode(array('error' => 'Error cancelling the appointment'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
