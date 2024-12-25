<?php
require("../server/connection.php");

header('Content-Type: application/json'); // Set the content type

if (isset($_POST['transactionId'])) {
    $transactionId = $_POST['transactionId'];
    $sql = "DELETE FROM trans WHERE transid = $transactionId";

    if ($connection->query($sql) === TRUE) {
        echo json_encode(['success' => 'Transaction deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting transaction: ' . $connection->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();
?>
