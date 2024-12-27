<?php

session_start();

require("../server/connection.php");   

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"]) || isset($_SESSION["email"])){
        $textaccount = $_SESSION["firstname"];
        $useremail = $_SESSION["email"];
        $staffid = $_SESSION["userid"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

// Validate and fetch transaction details
if (isset($_GET['detailid']) && is_numeric($_GET['detailid'])) {
    $detailid = intval($_GET['detailid']);

    // Fetch the appointment details
    $stmt = $connection->prepare(
        "SELECT trans_details.*, services.servicetype 
        FROM trans_details INNER JOIN services
        ON trans_details.serviceid = services.serviceid
        WHERE detailid = ?"
    );
    $stmt->bind_param("i", $detailid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transdetails = $result->fetch_assoc();
        $detailid = $transdetails['detailid'];
        $transid = $transdetails['transid'];
        $serviceid = $transdetails['servicetype'];
        $qty = $transdetails['qty'];
        $amount = $transdetails['amount'];
        $descrip = $transdetails['descrip'];

    } else {
        echo "No transaction details found!";
        exit;
    }
} else {
    echo "Invalid transaction details ID!";
    exit;
}

// Display the service type
$servicesOptions = "";

$stmtServices = $connection->prepare("SELECT serviceid, servicetype FROM services");
$stmtServices->execute();
$resultServices = $stmtServices->get_result();

if ($resultServices->num_rows > 0) {
    while ($row = $resultServices->fetch_assoc()) {
        $selected = ($row['serviceid'] == $transdetails['serviceid']) ? "selected" : ""; // Check if this service is the selected one
        $servicesOptions .= "<option value='" . $row['serviceid'] . "' $selected>" . htmlspecialchars($row['servicetype']) . "</option>";
    }
} else {
    $servicesOptions = "<option value='' disabled>No services available</option>";
}
$stmtServices->close();

// Code for saving
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $serviceid = intval($_POST["serviceid"]); // Get the selected service ID
    $amount = $_POST['amount'];
    $descrip = $_POST['descrip'];
    $qty = $_POST['qty'];
    $detailid = intval($_GET['detailid']); // Ensure we have the correct detailid

    // Prepare the update query for the transaction detail
    $stmtUpdateDetail = $connection->prepare("
        UPDATE trans_details 
        SET serviceid = ?, amount = ?, descrip = ?, qty = ? 
        WHERE detailid = ?
    ");
    $stmtUpdateDetail->bind_param("iisii", $serviceid, $amount, $descrip, $qty, $detailid);

    if ($stmtUpdateDetail->execute()) {
        // Recalculate the total amount for the associated transaction
        $stmtTotal = $connection->prepare("
            SELECT transid, SUM(amount) AS total 
            FROM trans_details 
            WHERE transid = (SELECT transid FROM trans_details WHERE detailid = ?)
        ");
        $stmtTotal->bind_param("i", $detailid);
        $stmtTotal->execute();
        $stmtTotal->bind_result($transid, $totalAmount);
        $stmtTotal->fetch();
        $stmtTotal->close();

        // Update the total amount in the trans table
        $stmtUpdateTrans = $connection->prepare("
            UPDATE trans 
            SET total_amount = ? 
            WHERE transid = ?
        ");
        $stmtUpdateTrans->bind_param("ii", $totalAmount, $transid);

        if ($stmtUpdateTrans->execute()) {
            $successMessage = "Transaction detail and total amount updated successfully!";
        } else {
            $errorMessage = "Error updating total amount: " . $stmtUpdateTrans->error;
        }

        $stmtUpdateTrans->close();
    } else {
        $errorMessage = "Error updating transaction detail: " . $stmtUpdateDetail->error;
    }

    $stmtUpdateDetail->close();

    // Redirect or display a success message
    header("Location: stafftransactiondetails.php");
    exit();
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

            <!-- Edit Transaction Details -->
            <div class="px-3 pt-4">
                        
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-2 mt-1">
                        <h2 class="fs-5">Edit Transaction Detail</h2>
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
                            <label class="form-label mt-2 px-3">Detail ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="detailid" id="detailid" value="<?php echo $detailid; ?>" disabled>
                        </div>
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
                            <label class="form-label mt-2 px-3">Service Type</label>
                        </div>
                        <div class="col-sm-6">
                            <select id="servicetype" name="serviceid" class="form-select" required>
                            <option value="" disabled selected>Select Service Type</option>
                                <?php echo $servicesOptions; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Quantity</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="qty" class="form-control" name="qty" value="<?php echo $qty ?>" placeholder="Enter quantity" required>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Amount</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="amount" class="form-control" name="amount" value="<?php echo $amount ?>" required>
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
                            <label class="form-label mt-2 px-3">Description</label>
                        </div>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="descrip" name="descrip" placeholder="Enter Description" value="<?php echo $descrip ?>" rows="3"><?php echo htmlspecialchars($descrip); ?></textarea>
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
            <!-- End of Edit Transaction Details -->

        </div>
    
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>