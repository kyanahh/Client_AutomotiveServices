<?php

// Include your database connection file
require("../server/connection.php");

// Get the selected date from the POST request
$date = isset($_POST['date']) ? $_POST['date'] : null;

// Check if the date is valid and not empty
if (!empty($date)) {
    // Call a function to get available times based on the selected date
    $availableTimes = getAvailableTimes($date);

    // Output the HTML for the available times

    echo json_encode($availableTimes);
} else {
    // Handle the case where the date is not valid or empty
    echo '<div class="alert alert-danger" role="alert">Invalid date</div>';
}

// Close the database connection if needed
$connection->close();

// Function to get available times from the database based on the selected date
function getAvailableTimes($date) {
    // Modify this function based on your database structure and query logic
    global $connection; // Assuming $connection is your database connection object

    $query = "SELECT oras FROM appointments WHERE araw = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookedTimes = array();
    while ($row = $result->fetch_assoc()) {
        $bookedTimes[] = $row['oras'];
    }

    // Generate an array of available times (you may need to adjust this based on your logic)
    $allTimes = [];
    for ($i = 8; $i <= 16; $i++) {
        $startHour = $i;
        $endHour = $i + 1;
        $timeSlot = sprintf("%02d:00-%02d:00", $startHour, $endHour);

        if (!in_array($timeSlot, $bookedTimes)) {
            $allTimes[] = $timeSlot;
        }
    }

    return $allTimes;
}
?>