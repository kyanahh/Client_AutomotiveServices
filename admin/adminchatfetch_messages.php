<?php

require("../server/connection.php");

if (isset($_POST["conversationid"])) {
    $conversationid = $_POST["conversationid"];
    
    $query = "SELECT m.message, m.created_at, u.firstname, u.usertypeid
              FROM messages m
              JOIN users u ON m.senderid = u.userid
              WHERE m.conversationid = ? 
              ORDER BY m.created_at ASC";
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $conversationid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $sender = $row["usertypeid"] == 3 ? "Client" : "Admin";
        echo "<p><strong>{$sender} ({$row['firstname']}):</strong> {$row['message']} <small>{$row['created_at']}</small></p>";
    }
}

?>
