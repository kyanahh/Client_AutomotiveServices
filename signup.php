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
          <a class="navbar-brand ps-5 text-white fw-bold" href="home.html">N.M.A.</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="home.html">HOME</a>
              </li>
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="about.html">ABOUT</a>
              </li>
              <li class="nav-item me-3">
                <a class="nav-link text-white fw-bold" href="contactus.html">CONTACT US</a>
              </li>
              <li class="nav-item me-5">
                <a class="nav-link px-3 fw-bold btn btn-white bg-white" href="login.php">LOGIN</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="container-fluid" style="padding-top: 5%">
        <div class="card mt-5 col-md-5 mx-auto">
            <div class="card-body">
                <?php
                    if (!empty($errorMessage)) {
                        echo "
                        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                            <strong>$errorMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                        ";
                }
                ?>
                <h4 class="card-title fw-bold text-center my-3">Sign Up</h4>
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $firstname; ?>" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname; ?>" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Phone Number" required>
                        </div>
                        <div class="col">
                        <select id="sex" name="sex" class="form-select" required>
                            <option value="" disabled selected>Select Sex</option>
                            <option value="1" <?php echo ($sex === 1) ? "selected" : ""; ?>>Male</option>
                            <option value="2" <?php echo ($sex === 2) ? "selected" : ""; ?>>Female</option>
                        </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email address" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" placeholder="Password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" value="<?php echo $confirmpassword; ?>" placeholder="Confirm Password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-grid gap-2">
                        <?php

                            if (!empty($successMessage)) {
                                echo "
                                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                    <strong>$successMessage</strong>
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>
                                ";
                            }

                        ?>
                            <button type="submit" class="btn text-white mt-3 fw-bold" style="background-color: #510400">Sign Up</button>
                        </div>
                    </div>
                    <div class="row d-grid gap-2">
                            <p class="text-center mt-2">Already have an account?<a href="login.php" class="text-decoration-none"> Login here.</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>