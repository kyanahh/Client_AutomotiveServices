<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT appointments.*, users.firstname, users.lastname, stats.statsname 
        FROM ((appointments 
        INNER JOIN users ON appointments.userid = users.userid) 
        INNER JOIN stats ON appointments.statsid = stats.statsid) 
        WHERE appointments.araw LIKE '%$query%' OR 
              appointments.oras LIKE '%$query%' OR 
              stats.statsname LIKE '%$query%' OR 
              users.firstname LIKE '%$query%' OR 
              users.lastname LIKE '%$query%' OR 
              appointments.appid LIKE '%$query%' OR 
              appointments.userid LIKE '%$query%' OR 
              appointments.appcreated LIKE '%$query%' OR 
              DATE_FORMAT(appointments.araw, '%M %e, %Y') LIKE '%$query%' OR 
              DATE_FORMAT(appointments.appcreated, '%M %e, %Y') LIKE '%$query%' ORDER BY appcreated DESC";
    } else {
        $sql = "SELECT appointments.appid, appointments.userid, users.firstname, users.lastname, 
                appointments.araw, appointments.oras, stats.statsname, appointments.appcreated FROM ((appointments 
                INNER JOIN users on appointments.userid = users.userid) 
                INNER JOIN stats on appointments.statsid = stats.statsid) 
                ORDER BY appcreated DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
        echo '<td>' . $count . '</td>';
        echo '<td>' . $row['userid'] . '</td>';
        echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
        // Format the date
        $dateObject = new DateTime($row['araw']);
        $formattedDate = $dateObject->format('F j, Y');
        echo '<td>' . $formattedDate . '</td>';
        echo '<td>' . $row['oras'] . '</td>';
        echo '<td>' . $row['statsname'] . '</td>';
        echo '<td>' . $row['appcreated'] . '</td>';
        echo '<td>';
        echo '<div class="d-flex justify-content-center">';
        if ($row['statsname'] == 'Pending') {
            echo '<button class="btn btn-success me-2" onclick="openConfirmModal(' . $row['appid'] . ')">Confirm</button>';
            echo '<button class="btn btn-danger me-2" onclick="cancelAppointment(' . $row['appid'] . ')">Cancel</button>';

        }
        echo '<button class="btn btn-primary me-2" onclick="editAppointment(' . $row['appid'] . ')">Edit</button>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        $count++;
        }
    } else {
        echo '<tr><td colspan="5">No appointments found.</td></tr>';
    }
}

?>