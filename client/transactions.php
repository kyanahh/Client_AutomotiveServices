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
            <!-- List of Transactions -->
                <div class="row">
                    <div class="col-sm-8">
                        <h5 class="mb-4">Transactions History</h5>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="searchTransactionInput" placeholder="Search" aria-describedby="button-addon2" oninput="searchTransaction()">
                    </div>
                </div>
                
                <div class="card" style="height: 450px;">
                    <div class="card-body">
                        <div class="table-responsive" style="height: 400px;">
                            <table id="transaction-table" class="table table-bordered table-hover">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Trans ID</th>
                                        <th scope="col">App ID</th>
                                        <th scope="col">Plate Number</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Transaction Date</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-table-body" class="table-group-divider">
                                <?php
                                    // Query the database to fetch user data
                                    $result = $connection->query("SELECT trans.* 
                                                                    FROM trans 
                                                                    INNER JOIN appointments ON trans.appid = appointments.appid 
                                                                    WHERE appointments.userid = $userid
                                                                    ORDER BY trans.transid DESC");

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
                                            echo '<td>' . $row['transdate'] . '</td>';
                                            echo '<td>';
                                            echo '<div class="d-flex justify-content-center">';
                                            echo '<button class="btn btn-primary me-2" onclick="viewDetails(' . $row['transid'] . ')">View Services</button>';
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
            <!-- End of List of Appointments -->
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
            function searchTransaction() {
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

    </script>

</body>
</html>