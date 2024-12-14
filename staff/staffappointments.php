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
                    <a href="staffappointments.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-calendar-check me-2"></i>Appointments
                    </a>
                </li>

                <li>
                    <a href="stafftransactions.php" class="text-decoration-none px-3 py-2 d-block">
                    <i class="bi bi-file-earmark-text me-2"></i>Transactions
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

            <!-- List of Appointments -->
            <div class="px-3 pt-4">
                <div class="row">
                    <div class="col-sm-1">
                        <h2 class="fs-5 mt-1 ms-2">Appointments</h2>
                    </div>
                    <div class="col input-group mb-3 ms-5 ps-4">
                        <input type="text" class="form-control" id="searchAppointmentInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchAppointment()">
                    </div>
                    <div class="col-sm-1">
                        <a class="btn btn-dark ms-2 px-3" href="staffaddappointment.php"><i class="bi bi-plus-lg"></i></a>
                    </div>
                </div>
                
                <div class="card" style="height: 500px;">
                    <div class="card-body">
                        <div class="table-responsive" style="height: 420px;">
                            <table id="appointment-table" class="table table-bordered table-hover">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">User ID</th>
                                        <th scope="col">Full Name</th>
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
                                    $result = $connection->query("SELECT appointments.appid, appointments.userid, users.firstname, users.lastname, 
                                    appointments.araw, appointments.oras, stats.statsname, appointments.appcreated FROM ((appointments 
                                    INNER JOIN users on appointments.userid = users.userid) 
                                    INNER JOIN stats on appointments.statsid = stats.statsid) 
                                    ORDER BY appcreated DESC");

                                    if ($result->num_rows > 0) {
                                        $count = 1; 

                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . $count . '</td>';
                                            echo '<td>' . $row['userid'] . '</td>';
                                            echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
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
                                                echo '<button class="btn btn-success me-2" onclick="openConfirmModal(' . $row['appid'] . ')">Confirm</button>';
                                                echo '<button class="btn btn-danger me-2" onclick="cancelAppointment(' . $row['appid'] . ')">Cancel</button>';

                                            }

                                            if ($row['statsname'] == 'Confirmed') {
                                                echo '<button class="btn btn-warning me-2" onclick="openOngoingModal(' . $row['appid'] . ')">Ongoing</button>';
                                            }

                                            if ($row['statsname'] == 'Ongoing') {
                                                echo '<button class="btn btn-warning me-2" onclick="transAppointment(' . $row['appid'] . ')">Transact</button>';
                                                echo '<button class="btn btn-success me-2" onclick="openDoneModal(' . $row['appid'] . ')">Done</button>';
                                            }

                                            echo '<button class="btn btn-primary me-2" onclick="editAppointment(' . $row['appid'] . ')">Edit</button>';
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
                    <!-- Search results will be displayed here -->
                <div id="search-results"></div>
            </div>
            <!-- End of List of Appointments -->

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Confirm Appointment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to confirm this appointment?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="confirmButton">Confirm</button>
                        </div>
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

            <!-- Ongoing Modal -->
            <div class="modal fade" id="ongoingModal" tabindex="-1" aria-labelledby="ongoingModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ongoingModalLabel">Ongoing Appointment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to update this appointment to Ongoing?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="ongoingButton">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Done Modal -->
            <div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="doneModalLabel">Done Appointment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to update this appointment to Done?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="doneButton">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
                <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            Appointment confirmed successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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

    //---------------------------Confirm Appointment---------------------------//
        // JavaScript code for modal and toast handling
        let appointmentIdToConfirm = null;

        // Function to open the confirmation modal
        function openConfirmModal(appid) {
            console.log("Opening modal for appointment ID:", appid); // Debugging log
            appointmentIdToConfirm = appid;
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            confirmModal.show();
        }

        // Event listener for the confirmation button
        document.getElementById('confirmButton').addEventListener('click', function () {
            if (appointmentIdToConfirm) {
                $.ajax({
                    url: "confirm_appointment.php",
                    method: "POST",
                    data: { appointmentId: appointmentIdToConfirm },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display the success toast
                            showToast(response.success, "bg-success");
                            setTimeout(() => location.reload(), 2000); // Optional delay before reload
                        } else {
                            // Display an error toast
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error confirming the appointment', 'bg-danger');
                    }
                });
            }
        });

        // Function to display the toast
        function showToast(message, className) {
            // Get the toast elements
            const toastMessage = document.getElementById('toastMessage');
            const toastElement = document.getElementById('liveToast');

            // Update the toast message and class
            toastMessage.textContent = message;
            toastElement.className = `toast align-items-center text-white ${className} border-0`;

            // Initialize and show the toast
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }


        //---------------------------Edit Appointment---------------------------//
        function editAppointment(appid) {
            window.location = "staffeditappointment.php?appid=" + appid;
        }

        //---------------------------Transact Appointment---------------------------//
        function transAppointment(appid) {
            window.location = "stafftransappointment.php?appid=" + appid;
        }

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

        //---------------------------Ongoing Appointment---------------------------//
        // JavaScript code for modal and toast handling
        let appointmentIdToOngoing = null;

        // Function to open the confirmation modal
        function openOngoingModal(appid) {
            console.log("Opening modal for appointment ID:", appid); // Debugging log
            appointmentIdToOngoing = appid;
            const ongoingModal = new bootstrap.Modal(document.getElementById('ongoingModal'));
            ongoingModal.show();
        }

        // Event listener for the confirmation button
        document.getElementById('ongoingButton').addEventListener('click', function () {
            if (appointmentIdToOngoing) {
                $.ajax({
                    url: "ongoing_appointment.php",
                    method: "POST",
                    data: { appointmentId: appointmentIdToOngoing },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display the success toast
                            showToast(response.success, "bg-success");
                            setTimeout(() => location.reload(), 2000); // Optional delay before reload
                        } else {
                            // Display an error toast
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error updating the appointment to Ongoing', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Done Appointment---------------------------//
        // JavaScript code for modal and toast handling
        let appointmentIdToDone = null;

        // Function to open the confirmation modal
        function openDoneModal(appid) {
            console.log("Opening modal for appointment ID:", appid); // Debugging log
            appointmentIdToDone = appid;
            const doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
            doneModal.show();
        }

        // Event listener for the confirmation button
        document.getElementById('doneButton').addEventListener('click', function () {
            if (appointmentIdToDone) {
                $.ajax({
                    url: "done_appointment.php",
                    method: "POST",
                    data: { appointmentId: appointmentIdToDone },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display the success toast
                            showToast(response.success, "bg-success");
                            setTimeout(() => location.reload(), 2000); // Optional delay before reload
                        } else {
                            // Display an error toast
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error updating the appointment to Done', 'bg-danger');
                    }
                });
            }
        });

    </script>

</body>
</html>