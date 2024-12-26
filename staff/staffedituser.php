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

$firstname = $lastname = $phone = $gender = $email = $bday = $ut = $newpassword 
= $errorMessage = $successMessage = "";

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $query = "SELECT * FROM users WHERE userid = '$id'";

    $res = $connection->query($query);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        $userid1 = $row["userid"];
        $firstname = $row["firstname"];
        $lastname = $row["lastname"];
        $phone = $row["phone"];
        $gender = $row["gender"];
        $email = $row["email"];
        $bday = $row["bday"];
        $ut = $row["usertypeid"];

        $gender = $row["gender"] == 1 ? "Male" : "Female";
    } else {
        $errorMessage = "User not found.";
    }
} else {
    $errorMessage = "User ID is missing.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($id)) {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $bday = $_POST["bday"];
    $gender = $_POST["gender"];
    $ut = $_POST["ut"];
    $newpassword = $_POST["newpassword"];

    // Base update query
    $query1 = "UPDATE users 
               SET 
                   firstname = '$firstname', 
                   lastname = '$lastname', 
                   phone = '$phone', 
                   email = '$email', 
                   bday = '$bday', 
                   gender = '$gender', 
                   usertypeid = '$ut'";

    // Append password to query only if it's provided
    if (!empty($newpassword)) {
        $query1 .= ", password = '$newpassword'";
    }

    $query1 .= " WHERE userid = '$id'";

    $result = $connection->query($query1);

    if ($result) {
        // Set a session variable for success
        $_SESSION['update_success'] = true;
        header("Location: staffusers.php"); // Redirect to adminusers.php
        exit;
    } else {
        $errorMessage1 = "Error updating details";
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

            <!-- Edit Users -->
            <div class="px-3 pt-4">
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-1 mt-1">
                        <h2 class="fs-5">Edit User</h2>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                                if (!empty($errorMessage)) {
                                    echo "
                                    <div class='alert alert-warning alert-dismissible fade show mt-2 ms-3' role='alert'>
                                        <strong>$errorMessage</strong>
                                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                    </div>
                                    ";
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 ps-3">First Name<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $firstname; ?>" placeholder="Enter first name" required>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Last Name<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $lastname; ?>" placeholder="Enter first name" required>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Phone<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" placeholder="Enter phone number" required>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Email<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" placeholder="Enter email address" required>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Birthday<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="bday" name="bday" value="<?php echo $bday; ?>" required>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Gender<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="1" <?php echo ($gender === "Male") ? "selected" : ""; ?>>Male</option>
                                <option value="2" <?php echo ($gender === "Female") ? "selected" : ""; ?>>Female</option>
                                </select>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">New Password</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" name="newpassword" id="newpassword" value="<?php echo $newpassword; ?>" placeholder="Enter new password">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">User Type<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <select id="ut" name="ut" class="form-select" required>
                                <option value="" disabled selected>Select User Type</option>
                                <option value="1" <?php echo ($ut == 1) ? "selected" : ""; ?>>Admin</option>
                                <option value="2" <?php echo ($ut == 2) ? "selected" : ""; ?>>Staff</option>
                                <option value="3" <?php echo ($ut == 3) ? "selected" : ""; ?>>Client</option>
                                <option value="4" <?php echo ($ut == 4) ? "selected" : ""; ?>>Guest</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2 float-end">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-dark px-5">Save</button>
                        </div>
                    </div>
                </form>

            </div>
            <!-- End of Edit Users -->

        </div>
    
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>