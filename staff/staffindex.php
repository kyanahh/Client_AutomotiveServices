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

// Number of customers
$customer_query = "SELECT COUNT(*) AS total_customers FROM users WHERE usertypeid IN (3, 4)";
$customer_result = $connection->query($customer_query);
$total_customers = $customer_result->fetch_assoc()['total_customers'];

// Appointment statistics
$appointment_query = "SELECT 
    SUM(statsid = 1) AS pending,
    SUM(statsid = 2) AS ongoing,
    SUM(statsid = 3) AS cancelled,
    SUM(statsid = 4) AS done,
    SUM(statsid = 5) AS confirmed,
    COUNT(*) AS total_appointments
FROM appointments";
$appointment_result = $connection->query($appointment_query);
$appointments = $appointment_result->fetch_assoc();

// Transaction statistics
$transaction_query = "SELECT 
    COUNT(*) AS total_transactions, 
    SUM(total_amount) AS total_sales 
FROM trans";
$transaction_result = $connection->query($transaction_query);
$transactions = $transaction_result->fetch_assoc();

// Sales per month
$sales_month_query = "SELECT DATE_FORMAT(transdate, '%Y-%m') AS month, SUM(total_amount) AS sales 
FROM trans GROUP BY month ORDER BY month";
$sales_month_result = $connection->query($sales_month_query);

$sales_per_month = [];
while ($row = $sales_month_result->fetch_assoc()) {
    $sales_per_month[] = $row;
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

            <div class="container px-5 pb-5 pt-3">
                <h3>Dashboard Statistics</h3>
                <div class="row">
                    
                    <!-- General Statistics -->
                    <div class="col-md-3">
                        <div class="card p-3">
                            <h5>Total Customers</h5>
                            <p><?php echo $total_customers; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-3">
                            <h5>Total Appointments</h5>
                            <p><?php echo $appointments['total_appointments']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-3">
                            <h5>Total Transactions</h5>
                            <p><?php echo $transactions['total_transactions']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-3">
                            <h5>Total Sales</h5>
                            <p>₱<?php echo number_format($transactions['total_sales'], 2); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-5">
                    <div class="col-md-6">
                        <canvas id="appointmentsChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="salesChart"></canvas>
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
        // Pass PHP data to JavaScript
        const appointmentsData = <?php echo json_encode($appointments); ?>;
        const salesData = <?php echo json_encode($sales_per_month); ?>;

        // Appointments Chart
        const ctx1 = document.getElementById('appointmentsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Ongoing', 'Cancelled', 'Done', 'Confirmed'],
                datasets: [{
                    label: 'Appointments',
                    data: [
                        appointmentsData.pending,
                        appointmentsData.ongoing,
                        appointmentsData.cancelled,
                        appointmentsData.done,
                        appointmentsData.confirmed
                    ],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF5722'],
                }]
            }
        });

        // Sales Chart
        const ctx2 = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.month),
                datasets: [{
                    label: 'Sales per Month',
                    data: salesData.map(item => item.sales),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            }
        });
    </script>

</body>
</html>