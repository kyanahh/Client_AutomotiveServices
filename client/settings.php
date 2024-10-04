<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"])){
        $textaccount = $_SESSION["firstname"];
        $useremail = $_SESSION["email"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

$errorMessage = "";

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
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="appointments.php">Appointments</a></li>
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
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

        <div class="d-flex justify-content-center">
            <div class="card p-3 mt-5">
                      <!-- Settings -->
                        <div class="px-3 pt-4">                
                            <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">

                            <div class="rows">
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
                                        
                            <div class="row mb-3 mt-2">
                                <div class="col-sm-4">
                                    <label class="form-label mt-2 px-3">Old Password</label>
                                </div>
                                <div class="col">
                                    <input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Enter old password" required>
                                </div>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-4">
                                    <label class="form-label mt-2 px-3">New Password</label>
                                </div>
                                <div class="col">
                                    <input type="password" class="form-control" name="newpass" id="newpass" placeholder="Enter new password" required>
                                </div>
                            </div>

                            <!-- Show Password Checkbox -->
                            <div class="row mb-3 ms-5 ps-5">
                                <div class="col-sm-5 ms-5 ps-5">
                                    <input class="ms-3" type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
                                </div>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-sm-8">
                                    <button type="submit" class="btn btn-dark px-5" style="margin-left: 455px;">Save</button>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>