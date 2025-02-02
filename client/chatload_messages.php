<?php
require("../server/connection.php");

if (!isset($_POST["conversationid"])) {
    exit();
}

$conversationid = $_POST["conversationid"];
$query = "SELECT m.*, u.firstname FROM messages m 
          JOIN users u ON m.senderid = u.userid 
          WHERE m.conversationid = $conversationid 
          ORDER BY m.created_at ASC";

$result = mysqli_query($connection, $query);

if (!$result) {
    echo "Error loading messages: " . mysqli_error($connection);
    exit();
}

while ($row = mysqli_fetch_assoc($result)) {
    $sender = $row["firstname"];
    $message = $row["message"];
    $timestamp = date("h:i A", strtotime($row["created_at"]));

    echo "<div><strong>$sender:</strong> $message <small class='text-muted'>$timestamp</small></div>";
}
?>
