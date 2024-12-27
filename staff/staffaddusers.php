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

$firstname = $lastname = $phone = $gender = $email = $bday =  $usertype = $password = $errorMessage = $successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname =  ucwords($_POST["firstname"]);
    $lastname =  ucwords($_POST["lastname"]);
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $bday = $_POST["bday"];
    $usertype = $_POST["usertype"];

    if (empty($firstname) || empty($lastname) || empty($phone) || empty($gender) || empty($email) || 
    empty($password)) {
        $errorMessage = "All fields are required";
    } else {
        // Check if the email already exists in the database
        $emailExistsQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailExistsResult = $connection->query($emailExistsQuery);

        if ($emailExistsResult->num_rows > 0) {
            $errorMessage = "User already exists";
            $firstname = $lastname = $phone = $gender = $email = $password = "";
        } else {
            // Insert the user data into the database
            $insertQuery = "INSERT INTO users (firstname, lastname, phone, gender, bday, email, 
            password, usertypeid) VALUES ('$firstname', '$lastname', '$phone', '$gender', '$bday', 
            '$email', '$password', '$usertype')";
            $result = $connection->query($insertQuery);

            if (!$result) {
                $errorMessage = "Invalid query " . $connection->error;
            } else {
                $firstname = $lastname = $phone = $gender = $email = $password = "";
                $errorMessage = "Account successfully created";
            }
        }
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

            <!-- Add Users -->
            <div class="px-3 pt-4">
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-1 mt-1">
                        <h2 class="fs-5">Add New User</h2>
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
                            <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $lastname; ?>" placeholder="Enter last name" required>
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
                                <option value="1" <?php echo ($gender === 1) ? "selected" : ""; ?>>Male</option>
                                <option value="2" <?php echo ($gender === 2) ? "selected" : ""; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">Password<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" name="password" id="password" value="<?php echo $password; ?>" placeholder="Enter password" required>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">User Type<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <select id="usertype" name="usertype" class="form-select" required>
                                <option value="" disabled selected>Select User Type</option>
                                <option value="1" <?php echo ($usertype === 1) ? "selected" : ""; ?>>Admin</option>
                                <option value="2" <?php echo ($usertype === 2) ? "selected" : ""; ?>>Staff</option>
                                <option value="3" <?php echo ($usertype === 3) ? "selected" : ""; ?>>Client</option>
                                <option value="4" <?php echo ($usertype === 4) ? "selected" : ""; ?>>Guest</option>
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
            <!-- End of Add Users -->

        </div>
    
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>