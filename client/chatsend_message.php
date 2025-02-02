<?php
require("../server/connection.php");

if (!isset($_POST["conversationid"]) || !isset($_POST["senderid"]) || !isset($_POST["message"])) {
    exit();
}

$conversationid = $_POST["conversationid"];
$senderid = $_POST["senderid"];
$message = mysqli_real_escape_string($connection, $_POST["message"]); // Use $connection instead of $conn

$query = "INSERT INTO messages (conversationid, senderid, message) 
          VALUES ($conversationid, $senderid, '$message')";

mysqli_query($connection, $query); // Ensure $connection is used here
?>
