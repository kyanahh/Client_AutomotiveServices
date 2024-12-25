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

            <!-- List of Services -->
            <div class="px-3">
                <div class="row">
                    <div class="col-sm-1">
                        <h2 class="fs-5 mt-1 ms-2">Sevices</h2>
                    </div>
                    <div class="col input-group mb-3">
                        <input type="text" class="form-control" id="searchServiceInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchService()">
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="bi bi-plus-lg text-white"></i></button>
                    </div>
                </div>
                
                <div class="card" style="height: 520px;">
                    <div class="card-body">
                        <div class="table-responsive" style="height: 480px;">
                            <table id="service-table" class="table table-bordered table-hover">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Service ID</th>
                                        <th scope="col">Service Type</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                <?php
                                    // Query the database to fetch user data
                                    $result = $connection->query("SELECT * FROM services ORDER BY serviceid DESC");

                                    if ($result->num_rows > 0) {
                                        $count = 1; 

                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . $count . '</td>';
                                            echo '<td>' . $row['serviceid'] . '</td>';
                                            echo '<td>' . $row['servicetype'] . '</td>';
                                            echo '<td>';
                                            echo '<div class="d-flex justify-content-center">';
                                            echo '<button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editServiceModal" onclick="loadServiceData(' . $row['serviceid'] . ')">Edit</button>';
                                            echo '<button class="btn btn-danger" onclick="deleteService(' . $row['serviceid'] . ')">Delete</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            $count++; 
                                        }
                                    } else {
                                        echo '<tr><td colspan="5">No services found.</td></tr>';
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
            <!-- End of List of Services -->

            <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container">
                <div id="deleteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Notification</strong>
                        <small>Just now</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        User deleted successfully.
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this service?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Add Service Modal -->
            <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addServiceForm">
                                <div class="mb-3">
                                    <label for="serviceTypeInput" class="form-label">Service Type</label>
                                    <input type="text" class="form-control" id="serviceTypeInput" name="servicetype" placeholder="Enter service type" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveServiceButton">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="dynamicToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div id="toastBody" class="toast-body">
                            <!-- Message will be injected here -->
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Edit Service Modal -->
            <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editServiceForm">
                                <input type="hidden" id="editServiceId" name="serviceid">
                                <div class="mb-3">
                                    <label for="editServiceType" class="form-label">Service Type</label>
                                    <input type="text" class="form-control" id="editServiceType" name="servicetype" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
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
        //--------------------------- Dynamic Toast Notification ---------------------------//
        function showDynamicToast(message, type) {
                const toastElement = document.getElementById('dynamicToast');
                const toastBody = document.getElementById('toastBody');

                // Set the message
                toastBody.textContent = message;

                // Set the type (e.g., success, error)
                toastElement.className = `toast align-items-center border-0 text-bg-${type}`;

                // Show the toast
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            //--------------------------- Edit Service ---------------------------//
            // Load service data into the modal
            function loadServiceData(serviceid) {
                $.ajax({
                    url: 'get_service.php',
                    type: 'POST',
                    data: { serviceid: serviceid },
                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            $('#editServiceId').val(result.data.serviceid);
                            $('#editServiceType').val(result.data.servicetype);
                        } else {
                            showDynamicToast('Error fetching service data: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while fetching the service data.', 'danger');
                    },
                });
            }

            // Handle the form submission for editing a service
            $('#editServiceForm').on('submit', function (e) {
                e.preventDefault();

                const serviceId = $('#editServiceId').val();
                const serviceType = $('#editServiceType').val();

                $.ajax({
                    url: 'update_service.php',
                    type: 'POST',
                    data: { serviceid: serviceId, servicetype: serviceType },
                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            $('#editServiceModal').modal('hide');
                            showDynamicToast('Service updated successfully!', 'success');

                            // Optionally reload the page after a short delay
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showDynamicToast('Error updating service: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while updating the service.', 'danger');
                    },
                });
            });

            //--------------------------- Delete Service ---------------------------//
            let serviceIdToDelete = null;

            function deleteService(serviceid) {
                serviceIdToDelete = serviceid; // Store the service ID to delete
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (serviceIdToDelete) {
                    $.ajax({
                        url: 'delete_service.php',
                        method: 'POST',
                        data: { serviceid: serviceIdToDelete },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                showDynamicToast('Service deleted successfully!', 'success');
                                setTimeout(() => location.reload(), 3000); // Wait 3 seconds before refreshing
                            } else {
                                showDynamicToast('Error deleting service: ' + response.error, 'danger');
                            }
                        },
                        error: function () {
                            showDynamicToast('An error occurred while deleting the service.', 'danger');
                        },
                    });
                }
            });

        //---------------------------Search Services Results---------------------------//
        function searchService() {
            const query = document.getElementById("searchServiceInput").value;

            // Make an AJAX request to fetch search results
            $.ajax({
                url: 'search_services.php', // Replace with the actual URL to your search script
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    // Update the user-table with the search results
                    $('#service-table tbody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error during search request:", error);
                }
            });
        }

        //--------------------------- Add Service ---------------------------//
        $(document).ready(function () {
            $('#saveServiceButton').on('click', function () {
                const serviceType = $('#serviceTypeInput').val();

                if (serviceType.trim() === '') {
                    showDynamicToast('Please enter a service type.', 'warning');
                    return;
                }

                // Send data to the server
                $.ajax({
                    url: 'add_service.php',
                    type: 'POST',
                    data: { servicetype: serviceType },
                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            showDynamicToast('Service added successfully!', 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showDynamicToast('Error adding service: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while adding the service.', 'danger');
                    },
                });
            });
        });


    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check if the session has the update success flag set
            <?php if (isset($_SESSION['update_success'])): ?>
                var updateToast = new bootstrap.Toast(document.getElementById('updateToast'));
                updateToast.show();
                <?php unset($_SESSION['update_success']); // Clear the session variable after showing the toast ?>
            <?php endif; ?>
        });
    </script>

</body>
</html>