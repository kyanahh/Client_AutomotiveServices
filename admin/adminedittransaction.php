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

// Validate and fetch appointment details
if (isset($_GET['transid']) && is_numeric($_GET['transid'])) {
    $transid = intval($_GET['transid']);

    // Fetch the appointment details
    $stmt = $connection->prepare(
        "SELECT * FROM trans
        WHERE transid = ?"
    );
    $stmt->bind_param("i", $transid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        $transid = $transaction['transid'];
        $appid = $transaction['appid'];
        $platenum = $transaction['platenum'];
        $staffid = $transaction['staffid'];
        $total_amount = $transaction['total_amount'];
        $remarks = $transaction['remarks'];
        $transdate = $transaction['transdate'];

    } else {
        echo "No transaction found!";
        exit;
    }
} else {
    echo "Invalid transaction ID!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newplatenum = $_POST['platenum'];
    $newtotalamount = $_POST['total_amount'];
    $newremarks = $_POST['remarks'];

    // Update the appointment in the database
    $updateStmt = $connection->prepare(
        "UPDATE trans SET platenum = ?, total_amount = ?, remarks = ? WHERE transid = ?"
    );
    $updateStmt->bind_param("sisi", $newplatenum, $newtotalamount, $newremarks, $transid);

    if ($updateStmt->execute()) {
        // Redirect with a success message
        header("Location: admintransactions.php?message=Transaction updated successfully!");
        exit;
    } else {
        echo "Error updating appointment: " . $connection->error;
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

            <!-- Edit Transactions -->
            <div class="px-3 pt-4">
                        
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-2 mt-1">
                        <h2 class="fs-5">Edit Transaction</h2>
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
                            <label class="form-label mt-2 px-3">Transaction ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="transid" id="transid" value="<?php echo $transid; ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Application ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="appid" id="appid" value="<?php echo $appid; ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Plate Number</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="platenum" class="form-control" name="platenum" value="<?php echo $platenum ?>" placeholder="Enter Plate Number" required>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Employee ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="staffid" class="form-control" name="staffid" value="<?php echo $staffid ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Total Amount</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="total_amount" class="form-control" name="pltotal_amountatenum" value="<?php echo $total_amount ?>">
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Remarks</label>
                        </div>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" value="<?php echo $remarks ?>"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Transaction Date</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="transdate" class="form-control" name="transdate" value="<?php echo $transdate ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3 text-light">Action</label>
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-dark col-sm-12">Save</button>
                            <a href="admintransactions.php" class="btn btn-danger col-sm-12 mt-1">Cancel</a>
                        </div>
                    </div>
                </form>

            </div>
            <!-- End of Edit Transactions -->

        </div>
    
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>