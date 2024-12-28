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

$transid = isset($_GET['transid']) ? intval($_GET['transid']) : 0;


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
            <!-- List of Transactions Details -->
                <div class="row">
                    <div class="col-sm-8">
                        <h5 class="mb-4">Transactions Details</h5>
                    </div>
                    <div class="col d-flex align-items-center">
                        <input type="text" class="form-control" id="searchDetailInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchDetail()">
                        <a href="transactions.php" class="btn btn-dark text-white ms-3"><i class="bi bi-arrow-left"></i></a>
                    </div>
                </div>
                
                <div class="table-responsive" style="height: 450px;">
                    <table class="table">
                        <thead class="table-light" style="position: sticky; top: 0;">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Service Type</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Description</th>
                                <th scope="col">Date Added</th>
                            </tr>
                        </thead>
                        <tbody id="detail-table-body" class="table-group-divider">
                        <?php
                            // Query the database to fetch user data
                            $result = $connection->query("SELECT trans_details.*, services.servicetype 
                              FROM trans_details 
                              LEFT JOIN services ON trans_details.serviceid = services.serviceid
                              WHERE trans_details.transid = $transid");

                            if ($result->num_rows > 0) {
                                $count = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $count . '</td>';
                                    echo '<td>' . $row['servicetype'] . '</td>';
                                    echo '<td>' . $row['amount'] . '</td>';
                                    echo '<td>' . $row['qty'] . '</td>';
                                    echo '<td>' . $row['descrip'] . '</td>';
                                    echo '<td>' . $row['ditsdate'] . '</td>';
                                    echo '</tr>';
                                    $count++;
                                }
                            } else {
                                echo '<tr><td colspan="7">No service details found for this transaction.</td></tr>';
                            }
                        ?>
                        </tbody>
                            
                    </table>
                </div>
                    <!-- Search results will be displayed here -->
                <div id="search-results"></div>
            <!-- End of List of Transaction Details -->
        </div>
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        
        function searchDetail() {
            const query = document.getElementById("searchDetailInput").value;
            const transid = <?php echo $transid; ?>; // Pass transid from PHP to JavaScript

            $.ajax({
                url: 'search_details.php',
                method: 'POST',
                data: { query: query, transid: transid }, // Include transid in POST data
                success: function (data) {
                    console.log(data); // Debugging log
                    $('#detail-table-body').html(data);
                },
                error: function (xhr, status, error) {
                    console.error('Search request failed:', error);
                }
            });
        }

    </script>

</body>
</html>