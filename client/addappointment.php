<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"])){
        $textaccount = $_SESSION["firstname"];
        $userid = $_SESSION["userid"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

// Booked times from the database
$query = "SELECT oras, araw FROM appointments WHERE statsid != 5"; // Get only non-cancelled appointments
$result = $connection->query($query);

$bookedTimesByDate = [];
while ($row = $result->fetch_assoc()) {
    $bookedTimesByDate[$row['araw']][] = trim($row['oras']);
}

$date = $time = $status = $errorMessage = "";

$selectedDate = isset($_POST['date']) ? $_POST['date'] : null;

$allTimes = [];
for ($i = 8; $i < 17; $i++) {
    $startTime = DateTime::createFromFormat('H', $i)->format('h:i A');
    $endTime = DateTime::createFromFormat('H', $i + 1)->format('h:i A');
    $timeSlot = "$startTime - $endTime";

    // Add the time slot to the list if not already booked for the selected date
    if (empty($bookedTimesByDate[$selectedDate]) || !in_array($timeSlot, $bookedTimesByDate[$selectedDate])) {
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
        $time = $_POST["time"];
        $status = 1;

        if (empty($time)) {
            $errorMessage = "Please select a time.";
        } else {
            // Proceed with inserting the appointment
            $stmt = $connection->prepare("INSERT INTO appointments (userid, araw, oras, statsid, appcreated) 
            VALUES (?, ?, ?, ?, ?)");
            
            // Use the entire time string as booked (e.g., "10:00 AM - 11:00 AM")
            $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

            $stmt->bind_param("issis", $userid, $selectedDate, $time, $status, $formattedDateTime);

            if ($stmt->execute()) {
                $errorMessage = "Appointment booked successfully!";
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }

            $stmt->close();
            header("Location: appointments.php");
            exit();
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

      <div class="container-fluid mt-4 d-flex justify-content-center">
        <div class="card col-sm-10 p-4">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="fw-bold pt-3">Available Time Slots</h4>
                    <div id="available-times-container" class="pt-3" style="height: 60vh; overflow-y: auto;">
                        <div class="alert alert-info">Please select a date to view available time slots.</div>
                    </div>
                </div>
                <div class="col-md-6 px-4 pt-3">
                    <h4 class="fw-bold">Book Appointment</h4>
                    <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" id="date-input" class="form-control" name="date" value="<?php echo $date ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">Select Time</label>
                            <select id="time" name="time" class="form-select" required>
                                <option value="" disabled selected>Select Time</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark px-5">Book</button>
                    </form>
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
                    
                    // Clear the available times display
                    $("#available-times-container").empty();

                    // If no available times, display a message
                    if (times.length === 0) {
                        $("#available-times-container").append('<div class="alert alert-warning">No available time slots for the selected date.</div>');
                    } else {
                        // Populate the dropdown and display available times
                        times.forEach(function(time) {
                            $("#time").append('<option value="' + time + '">' + time + '</option>');
                            $("#available-times-container").append('<div class="alert alert-success">' + time + '</div>');
                        });
                    }
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