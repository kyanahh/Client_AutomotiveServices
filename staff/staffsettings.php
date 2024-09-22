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

$errorMessage = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $useremail = $_SESSION["email"];
    $oldpass = $_POST["oldpass"];
    $newpass = $_POST["newpass"];
    $result = $connection->query("SELECT password FROM users WHERE email = '$useremail'");
    $record = $result->fetch_assoc();
    $stored_password = $record["password"];
    
    if ($oldpass == $stored_password) {
        $connection->query("UPDATE users SET password = '$newpass' WHERE email = '$useremail'");
        $_SESSION['update_success'] = "Password changed successfully"; // Set success message
    } else {
        $_SESSION['error_message'] = "Old password does not match"; // Set error message
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

            <!-- Settings -->
            <div class="px-3 pt-4">                
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                    <div class="row ms-2 mt-1">
                        <h2 class="fs-5">Settings</h2>
                    </div>

                    <div class="ms-2 col-sm-7">
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
                            <label class="form-label mt-2 px-3">Old Password</label>
                        </div>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Enter old password" required>
                        </div>
                    </div>

                    <div class="row mb-3 ms-2 mt-2">
                        <div class="col-sm-2">
                            <label class="form-label mt-2 px-3">New Password</label>
                        </div>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" name="newpass" id="newpass" placeholder="Enter new password" required>
                        </div>
                    </div>

                    <!-- Show Password Checkbox -->
                    <div class="row mb-3 ms-5 ps-5">
                        <div class="col-sm-5 ms-5 ps-5">
                            <input class="ms-2" type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
                        </div>
                    </div>

                    <div class="row mb-3 mt-2 float-end">
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-dark px-5" style="margin-right: 455px;">Save</button>
                        </div>
                    </div>
                </form>

            </div>
            <!-- End of Settings -->
        </div>
    
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']); // Clear the session variable
                }
                ?>
            </div>
        </div>

        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php
                if (isset($_SESSION['update_success'])) {
                    echo $_SESSION['update_success'];
                    unset($_SESSION['update_success']); // Clear the session variable
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function togglePassword() {
            var oldPass = document.getElementById("oldpass");
            var newPass = document.getElementById("newpass");
            if (oldPass.type === "password" && newPass.type === "password") {
                oldPass.type = "text";
                newPass.type = "text";
            } else {
                oldPass.type = "password";
                newPass.type = "password";
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var errorToast = document.getElementById('errorToast');
            var successToast = document.getElementById('successToast');
            
            if (errorToast.querySelector('.toast-body').innerText.trim() !== '') {
                var toast = new bootstrap.Toast(errorToast);
                toast.show();
            }

            if (successToast.querySelector('.toast-body').innerText.trim() !== '') {
                var toast = new bootstrap.Toast(successToast);
                toast.show();
            }
        });
    </script>

</body>
</html>