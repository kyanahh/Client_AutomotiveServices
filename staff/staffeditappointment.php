<?php
session_start();
require("../server/connection.php");

if (isset($_SESSION["logged_in"])) {
    $textaccount = $_SESSION["firstname"] ?? "Account";
    $useremail = $_SESSION["email"] ?? "";
} else {
    $textaccount = "Account";
}

// Initialize variables
$selectedDate = "";
$currentTime = "";  // Ensure currentTime is initialized

// Validate and fetch appointment details
if (isset($_GET['appid']) && is_numeric($_GET['appid'])) {
    $appid = intval($_GET['appid']);

    // Fetch the appointment details
    $stmt = $connection->prepare(
        "SELECT appointments.userid, appointments.araw, appointments.oras, 
        appointments.statsid, stats.statsname 
        FROM appointments 
        INNER JOIN stats ON appointments.statsid = stats.statsid 
        WHERE appointments.appid = ?"
    );
    $stmt->bind_param("i", $appid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $userid = $appointment['userid'];
        $date = $appointment['araw'];  // Date of the appointment
        $currentTime = trim($appointment['oras']);  // Current time slot
        $status = $appointment['statsid'];
        $selectedDate = $date;  // Use the appointment date as the initial selected date
    } else {
        echo "No appointment found!";
        exit;
    }
} else {
    echo "Invalid appointment ID!";
    exit;
}

// Fetch booked times from the database (excluding the current appointment)
$query = "SELECT oras, araw FROM appointments WHERE statsid != 5 AND appid != ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $appid);
$stmt->execute();
$result = $stmt->get_result();

$bookedTimesByDate = [];
while ($row = $result->fetch_assoc()) {
    $bookedTimesByDate[$row['araw']][] = trim($row['oras']);
}

// Generate available time slots (8:00 AM to 5:00 PM)
$allTimes = [];
for ($i = 8; $i < 17; $i++) {
    $startTime = DateTime::createFromFormat('H', $i)->format('h:i A');
    $endTime = DateTime::createFromFormat('H', $i + 1)->format('h:i A');
    $timeSlot = "$startTime - $endTime";

    // Ensure the current appointment time is always available
    if (
        $timeSlot == $currentTime || 
        (empty($bookedTimesByDate[$selectedDate]) || 
        !in_array($timeSlot, $bookedTimesByDate[$selectedDate]))
    ) {
        $allTimes[] = $timeSlot;
    }
}

// Handle form submission for updating the appointment
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedDate = $_POST["date"];
    $selectedDateTime = new DateTime($selectedDate);
    $currentDateTime = new DateTime();

    if ($selectedDateTime < $currentDateTime) {
        $errorMessage = "You cannot book appointments for past dates.";
    } else {
        $newDate = $_POST['date'];
        $newTime = $_POST['time'];
        $newStatus = $_POST['status'];

        // Update the appointment in the database
        $updateStmt = $connection->prepare(
            "UPDATE appointments SET araw = ?, oras = ?, statsid = ? WHERE appid = ?"
        );
        $updateStmt->bind_param("ssii", $newDate, $newTime, $newStatus, $appid);

        if ($updateStmt->execute()) {
            // Redirect with a success message
            header("Location: staffappointments.php?message=Appointment updated successfully!");
            exit;
        } else {
            echo "Error updating appointment: " . $connection->error;
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
    <link rel="stylesheet" type="text/css" href="staff.css">
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
                    <a href="staffindex.php" class="text-decoration-none px-3 py-2 d-block">
                        <i class="fal fa-home me-2"></i>Dashboard
                    </a>
                </li>

                <li>
                    <a href="staffusers.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-person-square me-2"></i>Users
                    </a>
                </li>

                <li>
                    <a href="staffchats.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-chat-text me-2"></i>Chats
                    </a>
                </li>

                <li>
                    <a href="staffservices.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-gear me-2"></i>Services
                    </a>
                </li>

                <li>
                    <a href="staffappointments.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-calendar-check me-2"></i>Appointments
                    </a>
                </li>

                <li>
                    <a href="stafftransactions.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-file-earmark-text me-2"></i>Transactions
                    </a>
                </li>

                <li>
                    <a href="stafftransactiondetails.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-card-list me-2"></i>Transactions Details
                    </a>
                </li>

            </ul>

            <hr class="h-color mx-2">

            <ul class="list-unstyled px-2">
                <li class=""><a href="staffsettings.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="fal fa-bars me-2"></i>Settings</a></li>
                <li class=""><a href="../logout.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
            </ul>

            <hr class="h-color mx-2 mt-5">
            
            <div class="d-flex align-items-end">
                <p class="text-white ms-3 fs-6 px-2">Logged in as: <?php echo $useremail ?><br>(Staff)</p>
            </div>
        </div>

        <div class="content bg-light">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container-fluid">
                </div>
            </nav>

            <!-- Edit Appointments -->
            <div class="px-3 pt-4">
                
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-2 mt-1">
                        <h2 class="fs-5">Edit Appointment</h2>
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
                                <option value="" disabled>Select Time</option>
                                <?php foreach ($allTimes as $availableTime) : ?>
                                    <option value="<?php echo $availableTime; ?>"
                                        <?php echo ($availableTime === $currentTime) ? 'selected' : ''; ?>>
                                        <?php echo $availableTime; ?>
                                    </option>
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
                                <option value="1" <?php echo ($status === 1) ? "selected" : ""; ?>>Pending</option>
                                <option value="2" <?php echo ($status === 2) ? "selected" : ""; ?>>Confirmed</option>
                                <option value="3" <?php echo ($status === 3) ? "selected" : ""; ?>>Ongoing</option>
                                <option value="4" <?php echo ($status === 4) ? "selected" : ""; ?>>Done</option>
                                <option value="5" <?php echo ($status === 5) ? "selected" : ""; ?>>Cancelled</option>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script> 
    /---------------------------Date Time Appointment---------------------------//
    $(document).ready(function() {
        $("#date-input").change(function() {
            var selectedDate = $(this).val();

            $.ajax({
                url: "get_available_times.php", 
                method: "POST",
                data: { date: selectedDate },
                success: function(response) {
                    const times = JSON.parse(response);
                    $("#time").empty(); // Clear previous options
                    $("#time").append('<option value="" disabled selected>Select Time</option>'); // Add default option
                    
                    // Check for error in the response
                    if (times.error) {
                        console.error("Error fetching available times: " + times.error);
                        return; // Exit if there's an error
                    }
                    
                    times.forEach(function(time) {
                        $("#time").append('<option value="' + time + '">' + time + '</option>'); // Populate available times
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching available times: " + error);
                }
            });
        });
    });
    
    </script>

</body>
</html>