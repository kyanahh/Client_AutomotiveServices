<?php

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"])){
        $userid = $_SESSION["userid"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
}

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
              DATE_FORMAT(appointments.appcreated, '%M %e, %Y') LIKE '%$query%' 
              WHERE users.userid = '$userid' ORDER BY appcreated DESC";
    } else {
        $sql = "SELECT appointments.appid, appointments.userid, users.firstname, users.lastname, 
                appointments.araw, appointments.oras, stats.statsname, appointments.appcreated FROM ((appointments 
                INNER JOIN users on appointments.userid = users.userid) 
                INNER JOIN stats on appointments.statsid = stats.statsid) 
                WHERE users.userid = '$userid' ORDER BY appcreated DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
        echo '<td>' . $count . '</td>';
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
            echo '<button class="btn btn-danger me-2" onclick="cancelAppointment(' . $row['appid'] . ')">Cancel</button>';

        }
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
