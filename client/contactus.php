<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"])){
        $textaccount = $_SESSION["firstname"];
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

      <div class="container-fluid mt-5 pt-3 d-flex justify-content-center">
        <div class="card col-sm-8 p-4">
            <h5>Contact Information</h5>
            <p class="mt-3">Telephone Number:</p>
            <p>(+02) 836-0541</p>
            <p class="mt-3">Phone Number:</p>
            <p>(+63) 946 394 7546</p>
            <p class="mt-3">Email Address:</p>
            <a class="text-decoration-none" href="mailto:NMAAutomotiveSuspension@gmail.com">NMAAutomotiveSuspension@gmail.com</a>
            <p class="mt-3">Address:</p>
            <p>113 B National Road Putatan, Muntinlupa City, Metro Manila</p>
            <div class="container my-3 col-sm-10">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!3m2!1sen!2sph!4v1727194483296!5m2!1sen!2sph!6m8!1m7!1sA6C1bcDMN18QFnczbMy0qQ!2m2!1d14.40415322322151!2d121.0469817882124!3f57.9661931139978!4f-1.3176981782065837!5f0.7820865974627469" 
                    width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
      </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>