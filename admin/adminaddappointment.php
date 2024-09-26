<?php

session_start();

require("../server/connection.php");   

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

$query = "SELECT oras FROM appointments";

$result = $connection->query($query);

$bookedTimes = array();

while ($row = $result->fetch_assoc()) {
    $bookedTimes[] = $row['oras'];
}

$userid = $date = $time = $status = $errorMessage = "";

$allTimes = [];
for ($i = 8; $i <= 16; $i++) {
    $startHour = $i;
    $endHour = $i + 1;
    
    $timeSlot = sprintf("%02d:00-%02d:00", $startHour, $endHour);

    if (!in_array($timeSlot, $bookedTimes)) {
        $allTimes[] = $timeSlot;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDate = $_POST["date"];
    $selectedDateTime = new DateTime($selectedDate);

    $currentDateTime = new DateTime();
    if ($selectedDateTime < $currentDateTime) {
        $errorMessage = "You cannot book appointments for past dates.";
    } else {
        $userid = $_POST["userid"];
        $time = $_POST["time"];
        $status = $_POST["status"];

        // Check if the user ID exists before proceeding
        $stmtCheckUser = $connection->prepare("SELECT COUNT(*) FROM users WHERE userid = ?");
        $stmtCheckUser->bind_param("i", $userid);
        $stmtCheckUser->execute();
        $stmtCheckUser->bind_result($userCount);
        $stmtCheckUser->fetch();
        $stmtCheckUser->close();

        if ($userCount === 0) {
            $errorMessage = "User does not exist.";
        } else {
            if (empty($time)) {
                $errorMessage = "Please select a time.";
            } else {
                // Convert DateTime object to string
                $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

                // Proceed with inserting the appointment
                $stmt = $connection->prepare("INSERT INTO appointments (userid, araw, oras, statsid, appcreated) 
                VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issis", $userid, $selectedDate, $time, $status, $formattedDateTime);

                if ($stmt->execute()) {
                    $errorMessage = "Appointment booked successfully!";
                } else {
                    $errorMessage = "Error: " . $stmt->error;
                }

                $stmt->close();

                header("Location: adminappointments.php");
            }
        }
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
    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>
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

            <!-- Add Appointments -->
            <div class="px-3 pt-4">
                
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-2 mt-1">
                        <h2 class="fs-5">Add Appointment</h2>
                    </div>

                    <div class="ms-2 col-sm-8">
                        <?php
                            if (!empty($errorMessage)) {
                                echo "
                                <div class='alert alert-warning alert-dismissible fade show mt-2 ms-4' role='alert'>
                                    <strong>$errorMessage</strong>
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>
                                ";
                            }
                        ?>
                    </div>
                    
                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">User ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="userid" id="userid" value="<?php echo $userid; ?>" placeholder="Enter User ID" required>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Date</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="date" id="date-input" class="form-control" name="date" value="<?php echo $date ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Time</label>
                        </div>
                        <div class="col-sm-6">
                            <select id="time" name="time" class="form-select" required>
                                <option value="" disabled selected>Select Time</option>
                                <?php foreach ($allTimes as $availableTime) : ?>
                                    <option value="<?php echo $availableTime; ?>"><?php echo $availableTime; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Status</label>
                        </div>
                        <div class="col-sm-6">
                            <select id="status" name="status" class="form-select" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="1" <?php echo ($status === "1") ? "selected" : ""; ?>>Pending</option>
                                <option value="2" <?php echo ($status === "2") ? "selected" : ""; ?>>Confirmed</option>
                                <option value="3" <?php echo ($status === "3") ? "selected" : ""; ?>>Ongoing</option>
                                <option value="4" <?php echo ($status === "4") ? "selected" : ""; ?>>Done</option>
                                <option value="4" <?php echo ($status === "5") ? "selected" : ""; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3 text-light">Action</label>
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-dark col-sm-12">Save</button>
                        </div>
                    </div>
                </form>

            </div>
            <!-- End of Add Appointments -->

        </div>
    
    </div>

    <!-- Script -->
    
    <script> 
    /---------------------------Search Appointment---------------------------//
    function searchAppointment() {
        const query = document.getElementById("searchAppointmentInput").value;
        // Make an AJAX request to fetch search results
        $.ajax({
            url: 'search_appointmentadmin.php',
            method: 'POST',
            data: { query: query },
            success: function(data) {
                // Update the appointment-table with the search results
                $('#appointment-table-body').html(data);
            }
        })};

    /---------------------------Date Time Appointment---------------------------//
    $(document).ready(function() {
        $("#date-input").change(function() {
        var selectedDate = $(this).val();

        var currentDate = new Date();
        var currentDateTime = currentDate.toISOString().slice(0, 19).replace("T", " ");

        $.ajax({
            url: "get_available_times.php", 
            method: "POST",
            data: { date: selectedDate },
            success: function(response) {
            $("#available-times-container").html("");

            const times = JSON.parse(response);
            times.forEach(function(time) {
            console.log("Available Time: " + time);
            $("#available-times-container").append('<div class="alert alert-success" role="alert">' + time + '</div>');
        });

            $("#time").empty();
            $("#time").append('<option value="" disabled selected>Select Time</option>');
            times.forEach(function(time) {
            $("#time").append('<option value="' + time + '">' + time + '</option>');
        });

                        
        if (selectedDate <= currentDateTime) 
            $("#available-times-container").html("<div class='alert alert-success text-center fw-bold' role='alert'><p class='mt-2'>Selected date has passed. Please choose a future date.</p></div>");
            },
            error: function(xhr, status, error) {
            console.error("Error fetching available times: " + error);
            }
        });
        });
            });
    
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>