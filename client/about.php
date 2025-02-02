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

// Fetch ratings from the database
$sql = "SELECT AVG(rating_value) AS average_rating, COUNT(*) AS total_reviews FROM rating";
$result = $connection->query($sql);
$average_rating = 0;
$total_reviews = 0;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $average_rating = round($row['average_rating'], 1);
    $total_reviews = $row['total_reviews'];
}

// Handle form submission for new ratings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $connection->real_escape_string($_POST['customer_name']);
    $rating_value = (int) $_POST['rating_value'];
    $review_text = $connection->real_escape_string($_POST['review_text']);

    if ($rating_value >= 1 && $rating_value <= 5) {
        $insert_sql = "INSERT INTO rating (customer_name, rating_value, review_text) VALUES ('$customer_name', $rating_value, '$review_text')";
        if ($connection->query($insert_sql) === TRUE) {
            header("Location: about.php"); // Refresh to display the new rating
            exit;
        } else {
            $error_message = "Error adding rating: " . $connection->error;
        }
    } else {
        $error_message = "Please provide a valid rating (1 to 5).";
    }
}

// Fetch individual reviews for display
$reviews_sql = "SELECT customer_name, rating_value, review_text, created_at FROM rating ORDER BY created_at DESC";
$reviews_result = $connection->query($reviews_sql);

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
                        <li><a class="dropdown-item" href="chats.php">Chats</a></li>
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

      <div class="container-fluid my-5 pt-3 d-flex justify-content-center">
        <div class="card col-sm-8 p-4">
            <h5>About NMA Automotive Suspension Service Center</h5>
            <div class="d-flex justify-content-center mt-3">
                <img class="w-25" src="../img/owner.jpg" alt="Owner">
            </div>
            <p class="mt-3">NMA Automotive Suspension Service Center was founded by Nelson M. Alimurong and has been a trusted name in automotive care since March 1, 1995—the same day the city of Muntinlupa celebrated its transition to municipal status. With nearly three decades of experience, we take pride in being specialists in automotive suspension systems, focusing on the repair and replacement of underchassis parts.</p>
            <p class="mt-3">Our expertise covers a wide range of suspension-related services, ensuring that each vehicle we service performs optimally and safely on the road. At NMA Automotive Suspension Service Center, we are dedicated to providing top-tier solutions that improve ride comfort, vehicle stability, and overall driving performance.</p>
            <p class="mt-3">Whether you're in need of routine maintenance or extensive repairs, our team of skilled technicians is committed to delivering high-quality service with integrity and precision. We believe that attention to detail and customer satisfaction are the keys to long-term trust, and we continue to uphold these values in every job we undertake.</p>

            <!-- Customer Satisfaction Rating -->
            <div class="mt-4">
                <h5>Customer Satisfaction Rating</h5>
                <div>
                    <p class="fs-4">
                        <?php echo str_repeat('<i class="bi bi-star-fill text-warning"></i>', floor($average_rating)); ?>
                        <?php echo str_repeat('<i class="bi bi-star text-muted"></i>', 5 - floor($average_rating)); ?>
                    </p>
                    <p>Average Rating: <?php echo $average_rating; ?> / 5 (<?php echo $total_reviews; ?> reviews)</p>
                </div>
            </div>

            <!-- Form to Add a Rating -->
            <div class="mt-5">
                <h5>Submit Your Rating</h5>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="rating_value" class="form-label">Your Rating (1 to 5)</label>
                        <input type="number" class="form-control" id="rating_value" name="rating_value" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="review_text" class="form-label">Your Review (Optional)</label>
                        <textarea class="form-control" id="review_text" name="review_text" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                </form>
            </div>

            <!-- Display Reviews -->
            <div class="mt-5">
                <h5>Recent Reviews</h5>
                <div style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 15px; border-radius: 5px;">
                    <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                        <?php while ($review = $reviews_result->fetch_assoc()): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <p><strong><?php echo htmlspecialchars($review['customer_name']); ?></strong> 
                                    <span><?php echo str_repeat('<i class="bi bi-star-fill text-warning"></i>', $review['rating_value']); ?></span>
                                </p>
                                <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                                <p class="text-muted small"><?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to rate!</p>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
      </div>

    <!-- Script -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>