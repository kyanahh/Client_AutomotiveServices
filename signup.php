<?php

require("server/connection.php");

$usertype = 3;

$firstname = $lastname = $phone = $gender = $bday = $email = $password = $confirmpassword = $errorMessage = $successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname =  ucwords($_POST["firstname"]);
    $lastname =  ucwords($_POST["lastname"]);
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];
    $bday = $_POST["bday"];

    if (empty($firstname) || empty($lastname) || empty($bday) || empty($phone) || empty($gender) || 
    empty($email) || empty($password) || empty($confirmpassword)) {
        $errorMessage = "All fields are required";
    } elseif ($password !== $confirmpassword) {
        $errorMessage = "Passwords do not match";
    } else {
        // Check if the email already exists in the database
        $emailExistsQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailExistsResult = $connection->query($emailExistsQuery);

        if ($emailExistsResult->num_rows > 0) {
            $errorMessage = "User already exists";
        } else {
            // Insert the user data into the database
            $insertQuery = "INSERT INTO users (firstname, lastname, phone, gender, email, bday, 
            password, usertypeid) VALUES ('$firstname', '$lastname', '$phone', '$gender', '$email', 
            '$bday', '$password', '$usertype')";
            $result = $connection->query($insertQuery);

            if (!$result) {
                $errorMessage = "Invalid query " . $connection->error;
            } else {
                header("Location: success.html");
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
                <a class="nav-link text-white fw-bold" href="about.php">ABOUT</a>
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

      <div class="container-fluid">
        <div class="card mt-5 col-md-6 mx-auto">
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
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $firstname; ?>" placeholder="e.g. Juan" required>
                        </div>
                        <div class="col">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname; ?>" placeholder="e.g. Dela Cruz" required>
                        </div>
                        <div class="col">
                            <label for="bday" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" id="bday" name="bday" value="<?php echo $bday; ?>" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="1" <?php echo ($gender === 1) ? "selected" : ""; ?>>Male</option>
                                <option value="2" <?php echo ($gender === 2) ? "selected" : ""; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="09XXXXXXX" required>
                        </div>
                        <div class="col">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="name@example.com" required>
                        </div>
                        
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                        </div>
                        <div class="col">
                            <label for="confirmpassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" value="<?php echo $confirmpassword; ?>" required>
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Passwords
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
                            <button type="submit" class="btn btn-dark mt-3 fw-bold">Sign Up</button>
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
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirmpassword");
            if (password.type === "password" && confirmPassword.type === "password") {
                password.type = "text";
                confirmPassword.type = "text";
            } else {
                password.type = "password";
                confirmPassword.type = "password";
            }
        }
    </script>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>