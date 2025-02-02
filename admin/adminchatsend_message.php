<?php
require("../server/connection.php");

if (isset($_POST["conversationid"], $_POST["senderid"], $_POST["message"])) {
    $conversationid = $_POST["conversationid"];
    $senderid = $_POST["senderid"];
    $message = $_POST["message"];

    $query = "INSERT INTO messages (conversationid, senderid, message) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iis", $conversationid, $senderid, $message);
        if ($stmt->execute()) {
            echo "Message sent successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $connection->error;
    }
}

?>
