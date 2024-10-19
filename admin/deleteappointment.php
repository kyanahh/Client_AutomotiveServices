<?php
require("../server/connection.php");

header('Content-Type: application/json'); // Set the content type

if (isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];
    $sql = "DELETE FROM appointments WHERE appid = $appointmentId";

    if ($connection->query($sql) === TRUE) {
        echo json_encode(['success' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting appointment: ' . $connection->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();
?>
