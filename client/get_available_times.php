<?php

require("../server/connection.php");

// Get the selected date from the POST request
$date = isset($_POST['date']) ? $_POST['date'] : null;

if (!empty($date)) {
    $availableTimes = getAvailableTimes($date);
    echo json_encode($availableTimes);
} else {
    echo json_encode(['error' => 'Invalid date']);
}

$connection->close();

function getAvailableTimes($date) {
    global $connection;

    // Query to fetch only non-cancelled bookings (statsid != 5) for the selected date
    $query = "SELECT oras FROM appointments WHERE araw = ? AND statsid != 5";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookedTimes = [];
    while ($row = $result->fetch_assoc()) {
        // Store booked times in the format "h:i A - h:i A"
        $bookedTimes[] = trim($row['oras']); // Match this with the generated format
    }

    // Generate available time slots from 8:00 AM to 5:00 PM (in 1-hour intervals)
    $allTimes = [];
    for ($i = 8; $i < 17; $i++) {
        $startTime = DateTime::createFromFormat('H', $i)->format('h:i A');
        $endTime = DateTime::createFromFormat('H', $i + 1)->format('h:i A');
        $timeSlot = "$startTime - $endTime";

        // Add the time slot if it's not already booked
        if (!in_array($timeSlot, $bookedTimes)) {
            $allTimes[] = $timeSlot;
        }
    }

    return $allTimes;
}

?>