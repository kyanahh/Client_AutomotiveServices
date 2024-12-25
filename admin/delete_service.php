<?php

require("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['serviceid'])) {
    $serviceid = $_POST['serviceid'];

    $deleteQuery = "DELETE FROM services WHERE serviceid = '$serviceid'";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        echo json_encode(array('success' => 'Service deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting service: ' . $connection->error));
    }

} else {
    echo json_encode(array('error' => 'Invalid request'));
}

$connection->close();

?>
