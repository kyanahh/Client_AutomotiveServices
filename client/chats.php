<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"])){
        $textaccount = $_SESSION["firstname"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

$clientid = $_SESSION["userid"];

// Check if the client already has an open conversation
$query = "SELECT conversationid FROM conversations WHERE clientid = $clientid AND status = 'open' LIMIT 1";
$result = mysqli_query($connection, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $conversationid = $row["conversationid"];
} else {
    // Start a new conversation if none exists
    $query = "INSERT INTO conversations (clientid) VALUES ($clientid)";
    if (mysqli_query($connection, $query)) {
        $conversationid = mysqli_insert_id($connection);
    } else {
        die("Error creating conversation: " . mysqli_error($connection)); // Debugging
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N.M.A.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg py-4 bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand ps-5 text-white fw-bold" href="clientindex.php">N.M.A. AUTOMOTIVE SUSPENSION SERVICES CENTER</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="clientindex.php">HOME</a>
              </li>
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="about.php">ABOUT</a>
              </li>
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="contactus.php">CONTACT US</a>
              </li>
              <li class="nav-item me-5">
                <div class="dropdown-center">
                    <button class="btn btn-light dropdown-toggle px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Account
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="chats.php">Chats</a></li>
                        <li><a class="dropdown-item" href="appointments.php">Appointments</a></li>
                        <li><a class="dropdown-item" href="transactions.php">Transactions</a></li>
                        <li><a class="dropdown-item" href="settings.php">Account <br> Management</a></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item">Hello, <?php echo $textaccount?></a></li>
                    </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- MAIN -->
      <div class="container my-4">
            <div class="card">
                <div class="card-header bg-dark text-white">Chat with N.M.A.</div>
                <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto;">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="card-footer">
                    <div class="input-group">
                        <input type="text" id="message" class="form-control" placeholder="Type your message...">
                        <button class="btn btn-dark" id="send">Send</button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Script -->  
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        var conversationId = <?php echo json_encode($conversationid); ?>;
        var clientId = <?php echo json_encode($clientid); ?>;

        function loadMessages() {
            $.ajax({
                url: "chatload_messages.php",
                type: "POST",
                data: { conversationid: conversationId },
                success: function (data) {
                    $("#chat-box").html(data);
                    $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                },
                error: function (xhr, status, error) {
                    console.error("Error loading messages:", xhr.responseText);
                }
            });
        }

        $("#send").click(function () {
            var message = $("#message").val();
            if (message.trim() !== "") {
                $.ajax({
                    url: "chatsend_message.php",
                    type: "POST",
                    data: {
                        conversationid: conversationId,
                        senderid: clientId,
                        message: message
                    },
                    success: function () {
                        $("#message").val("");
                        loadMessages();
                    },
                    error: function (xhr, status, error) {
                        console.error("Error sending message:", xhr.responseText);
                    }
                });
            }
        });

        setInterval(loadMessages, 2000);
        loadMessages();
    </script>

</body>
</html>