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
            <nav class="navbar navbar-expand-md navbar-dark bg-light">
                <div class="container-fluid">
                </div>
            </nav>

            <!-- List of Transactions -->
            <div class="px-3 pt-4">
                <div class="row">
                    <div class="col-sm-1">
                        <h2 class="fs-5 mt-1 ms-2">Transactions</h2>
                    </div>
                    <div class="col input-group mb-3 ms-4">
                        <input type="text" class="form-control" id="searchTransactionInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchTransaction()">
                    </div>
                </div>
                
                <div class="card" style="height: 500px;">
                    <div class="card-body">
                        <div class="table-responsive" style="height: 460px;">
                            <table id="transaction-table" class="table table-bordered table-hover">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Trans ID</th>
                                        <th scope="col">App ID</th>
                                        <th scope="col">Plate Number</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Employee ID</th>
                                        <th scope="col">Transaction Date</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-table-body" class="table-group-divider">
                                <?php
                                    // Query the database to fetch user data
                                    $result = $connection->query("SELECT * FROM trans ORDER BY transid DESC");

                                    if ($result->num_rows > 0) {
                                        $count = 1; 

                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . $count . '</td>';
                                            echo '<td>' . $row['transid'] . '</td>';
                                            echo '<td>' . $row['appid'] . '</td>';
                                            echo '<td>' . $row['platenum'] . '</td>';
                                            echo '<td>' . $row['total_amount'] . '</td>';
                                            echo '<td>' . $row['remarks'] . '</td>';
                                            echo '<td>' . $row['staffid'] . '</td>';
                                            echo '<td>' . $row['transdate'] . '</td>';
                                            echo '<td>';
                                            echo '<div class="d-flex justify-content-center">';
                                            echo '<button class="btn btn-success me-2" onclick="addDetails(' . $row['transid'] . ')">Add Services</button>';
                                            echo '<button class="btn btn-primary me-2" onclick="editTransaction(' . $row['transid'] . ')">Edit</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            $count++; 
                                        }
                                    } else {
                                        echo '<tr><td colspan="5">No transactions found.</td></tr>';
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

            <!-- Toast Notification -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
                <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            Transaction confirmed successfully!
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
        //---------------------------Search Transactions---------------------------//
        document.addEventListener("DOMContentLoaded", function () {
            function searchAppointment() {
                const query = document.getElementById("searchTransactionInput").value;

                $.ajax({
                    url: 'search_transactions.php',
                    method: 'POST',
                    data: { query: query },
                    success: function (data) {
                        console.log(data); // Debugging log
                        $('#transaction-table-body').html(data);
                    },
                    error: function (xhr, status, error) {
                        console.error('Search request failed:', error);
                    }
                });
            }

            // Attach the function to the input field
            document.getElementById("searchTransactionInput").oninput = searchTransaction;
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

        //---------------------------Add Transactions Details ---------------------------//
        function addDetails(transid) {
            window.location = "staffaddtransdetails.php?transid=" + transid;
        }

        //---------------------------Edit Transaction---------------------------//
        function editTransaction(transid) {
            window.location = "staffedittransaction.php?transid=" + transid;
        }
        
    </script>

</body>
</html>