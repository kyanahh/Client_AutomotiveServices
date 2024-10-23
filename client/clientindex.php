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

      <div id="homepagecarousel" class="carousel slide align-items-center" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homepagecarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#homepagecarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#homepagecarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active c-item">
            <img src="../img/fixing0.png" class="d-block c-img" alt="NMA" style="height: 85vh; background-size: cover;
            width: 100%;">
            <div class="carousel-caption top-0 mt-5">
                <p class="fs-4 text-uppercase pt-5">Need reliable automotive services?</p>
                <h1 class="display-5 fw-bolder text-capitalize mt-5">N.M.A. AUTOMOTIVE SUSPENSION SERVICES CENTER</h1>
                <a href="signup.php" class="btn btn-light fw-bold mx-auto fs-5 px-4 py-2 mt-5">Book Now</a>
            </div>
          </div>
          <div class="carousel-item c-item">
            <img src="../img/things.png" class="d-block c-img" alt="NMA" style="height: 85vh; background-size: cover;
            width: 100%;">
            <div class="carousel-caption top-0 mt-5">
                <p class="fs-4 text-uppercase pt-5">Want your car expertly fixed?</p>
                <h1 class="fw-bold text-capitalize mt-4">Drive with confidence—get your car serviced by the experts today!</h1>
                <a href="signup.php" class="btn btn-light fw-bold mx-auto fs-5 px-4 py-2 mt-5">Book Now</a>
            </div>
          </div>
          <div class="carousel-item c-item">
            <img src="../img/shop.png" class="d-block c-img" alt="NMA" style="height: 85vh; background-size: cover;
            width: 100%;">
            <div class="carousel-caption top-0 mt-5 pt-5">
                <p class="fs-4 text-uppercase pt-5">8am to 5pm</p>
                <h1 class="display-4 fw-bold text-capitalize mt-4">Monday to Saturday</h1>
                <a href="signup.php" class="btn btn-light fw-bold mx-auto fs-5 px-4 py-2 mt-5">Book Now</a>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homepagecarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#homepagecarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>