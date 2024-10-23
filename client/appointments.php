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
                <div class="col-sm-4">
                    <h5 class="mb-4">Appointment History</h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-dark" href="addappointment.php"><i class="bi bi-plus-lg"></i></a>
                    </div>
                </div>
            </div>
            <input type="text" class="form-control" id="searchAppointmentInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchAppointment()">
            
            <div class="table-responsive" style="height: 320px;">
                <table class="table">
                    <thead class="bg-light" style="position: sticky; top: 0;">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Status</th>
                            <th scope="col">Appointment Created</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                        <tbody id="appointment-table-body" class="table-group-divider">
                            <?php
                                // Query the database to fetch user data
                                $result = $connection->query("SELECT appointments.appid, appointments.userid,  
                                appointments.araw, appointments.oras, stats.statsname, appointments.appcreated FROM ((appointments 
                                INNER JOIN users on appointments.userid = users.userid) 
                                INNER JOIN stats on appointments.statsid = stats.statsid) 
                                WHERE users.userid = '$userid' 
                                ORDER BY appcreated DESC");

                                if ($result->num_rows > 0) {
                                    $count = 1; 

                                    while ($row = $result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . $count . '</td>';
                                        // Format the date
                                        $dateObject = new DateTime($row['araw']);
                                        $formattedDate = $dateObject->format('F j, Y');
                                        echo '<td>' . $formattedDate . '</td>';
                                        echo '<td>' . $row['oras'] . '</td>';
                                        echo '<td>' . $row['statsname'] . '</td>';
                                        echo '<td>' . $row['appcreated'] . '</td>';
                                        echo '<td>';
                                        echo '<div class="d-flex justify-content-center">';
                                        if ($row['statsname'] == 'Pending') {
                                            echo '<button class="btn btn-danger me-2" onclick="cancelAppointment(' . $row['appid'] . ')">Cancel</button>';

                                        }
                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                        $count++; 
                                    }
                                } else {
                                    echo '<tr><td colspan="5">No appointments found.</td></tr>';
                                }
                            ?>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cancel Appointment Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this appointment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="cancelButton">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>

    /---------------------------Search Appointment---------------------------//
    document.addEventListener("DOMContentLoaded", function () {
        function searchAppointment() {
            const query = document.getElementById("searchAppointmentInput").value;

            $.ajax({
                url: 'search_appointments.php',
                method: 'POST',
                data: { query: query },
                success: function (data) {
                    console.log(data); // Debugging log
                    $('#appointment-table-body').html(data);
                },
                error: function (xhr, status, error) {
                    console.error('Search request failed:', error);
                }
            });
        }

        // Attach the function to the input field
        document.getElementById("searchAppointmentInput").oninput = searchAppointment;
    });

    //---------------------------Cancel Appointment---------------------------//
    let appointmentIdToCancel = null;

    // Function to open the cancel modal
    function cancelAppointment(appid) {
        console.log("Opening cancel modal for appointment ID:", appid); // Debugging log
        appointmentIdToCancel = appid;
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        cancelModal.show();
    }

    // Event listener for the cancel button in the modal
    document.getElementById('cancelButton').addEventListener('click', function () {
        if (appointmentIdToCancel) {
            $.ajax({
                url: "cancel_appointment.php",
                method: "POST",
                data: { appointmentId: appointmentIdToCancel },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Display a success toast
                        showToast(response.success, "bg-success");
                        setTimeout(() => location.reload(), 2000); // Optional delay before reload
                    } else {
                        // Display an error toast
                        showToast(response.error, "bg-danger");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors from the AJAX request
                    showToast('Error cancelling the appointment', 'bg-danger');
                }
            });
        }
    });

    </script>

</body>
</html>