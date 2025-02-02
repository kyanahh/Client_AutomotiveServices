<?php

session_start();

require("../server/connection.php");   

// Start setting the admin ID here
if (isset($_SESSION['userid'])) {
    $admin_id = $_SESSION['userid'];
}

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"]) || isset($_SESSION["email"])){
        $textaccount = $_SESSION["firstname"];
        $useremail = $_SESSION["email"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

// Fetch conversations
$query = "SELECT c.conversationid, u.firstname, u.lastname 
          FROM conversations c
          JOIN users u ON c.clientid = u.userid
          ORDER BY c.created_at DESC";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection)); // Debugging line
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
    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>
<style>
    .chat-container {
        display: flex;
        height: 80vh;
    }
    .chat-list {
        width: 30%;
        border-right: 1px solid #ddd;
        overflow-y: auto;
    }
    .chat-messages {
        width: 70%;
        display: flex;
        flex-direction: column;
        padding: 10px;
    }
    .message-box {
        flex-grow: 1;
        overflow-y: auto;
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }
    .chat-input {
        display: flex;
        gap: 10px;
        padding: 10px;
    }

    .chat-item {
        cursor: pointer;
    }

</style>
<body>

    <div class="main-container d-flex">
        <div class="sidebar" id="side_nav">
            <div class="header-box px-2 pt-3 pb-4 d-flex justify-content-between">
                <h1 class="fs-4 ps-3 pt-3">
                <span class="text-white fw-bold">N.M.A</span></h1>
                <button class="btn d-md-none d-block close-btn px-1 py-0 text-white"><i class="fal fa-stream"></i></button>
            </div>

            <ul class="list-unstyled px-2">

                <li>
                    <a href="adminindex.php" class="text-decoration-none px-3 py-2 d-block">
                        <i class="fal fa-home me-2"></i>Dashboard
                    </a>
                </li>

                <li>
                    <a href="adminusers.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-person-square me-2"></i>Users
                    </a>
                </li>

                <li>
                    <a href="adminchats.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-chat-text me-2"></i>Chats
                    </a>
                </li>

                <li>
                    <a href="adminservices.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-gear me-2"></i>Services
                    </a>
                </li>

                <li>
                    <a href="adminappointments.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-calendar-check me-2"></i>Appointments
                    </a>
                </li>

                <li>
                    <a href="admintransactions.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-file-earmark-text me-2"></i>Transactions
                    </a>
                </li>

                <li>
                    <a href="admintransactiondetails.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-card-list me-2"></i>Transactions Details
                    </a>
                </li>

                <li>
                    <a href="adminuserlogs.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-journal me-2"></i>User Logs
                    </a>
                </li>

            </ul>

            <hr class="h-color mx-2">

            <ul class="list-unstyled px-2">
                <li class=""><a href="adminsettings.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="fal fa-bars me-2"></i>Settings</a></li>
                <li class=""><a href="../logout.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
            </ul>

            <hr class="h-color mx-2 mt-5">
            
            <div class="d-flex align-items-end">
                <p class="text-white ms-3 fs-6">Logged in as: <?php echo $useremail ?><br>(Admin)</p>
            </div>
        </div>

        <div class="content bg-light">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container-fluid">
                </div>
            </nav>

            <!-- MAIN -->

            <div class="container mt-4">
                <h2 class="text-center">Chat</h2>
                <div class="chat-container">
                    <!-- Chat List -->
                    <div class="chat-list p-3">
                        <h5>Clients</h5>
                        <ul class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <li class="list-group-item chat-item" data-conversationid="<?= $row['conversationid'] ?>">
                                    <?= $row['firstname'] . " " . $row['lastname'] ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Chat Messages -->
                    <div class="chat-messages">
                        <div class="message-box" id="message-box"></div>
                        <div class="chat-input">
                            <input type="text" id="message-input" class="form-control" placeholder="Type a message...">
                            <button class="btn btn-primary" id="send-message">Send</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Ensure admin_id is properly set
        <?php if (isset($admin_id)): ?>
            let adminId = <?= json_encode($admin_id) ?>;
        <?php else: ?>
            let adminId = null;
        <?php endif; ?>

        // Your existing JavaScript for chat functionality here
        let currentConversation = null;

        $(".chat-item").click(function () {
            currentConversation = $(this).data("conversationid");
            loadMessages(currentConversation);
        });

        function loadMessages(conversationId) {
            $.ajax({
                url: "adminchatfetch_messages.php",
                type: "POST",
                data: { conversationid: conversationId },
                success: function (data) {
                    $("#message-box").html(data);
                }
            });
        }

        $("#send-message").click(function () {
            let message = $("#message-input").val();
            if (message.trim() !== "" && currentConversation) {
                $.ajax({
                    url: "adminchatsend_message.php",
                    type: "POST",
                    data: { conversationid: currentConversation, senderid: adminId, message: message },
                    success: function () {
                        $("#message-input").val("");
                        loadMessages(currentConversation);
                    }
                });
            }
        });

        setInterval(function () {
            if (currentConversation) {
                loadMessages(currentConversation);
            }
        }, 3000);
    </script>

</body>
</html>