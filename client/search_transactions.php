<?php

require("../server/connection.php");
session_start();

if (!isset($_SESSION['userid'])) {
    echo '<tr><td colspan="8">User not logged in.</td></tr>';
    exit;
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);

    if (!empty($query)) {
        // Query to search transactions for the logged-in user
        $sql = "SELECT trans.* 
                FROM trans 
                INNER JOIN appointments ON trans.appid = appointments.appid 
                WHERE appointments.userid = $userid AND 
                      (trans.transid LIKE '%$query%' OR 
                       trans.appid LIKE '%$query%' OR 
                       trans.platenum LIKE '%$query%' OR 
                       trans.staffid LIKE '%$query%' OR 
                       trans.total_amount LIKE '%$query%' OR 
                       trans.remarks LIKE '%$query%' OR 
                       trans.transdate LIKE '%$query%' OR 
                       DATE_FORMAT(trans.transdate, '%M %e, %Y') LIKE '%$query%')
                ORDER BY trans.transid DESC";
    } else {
        // Default query for transactions of the logged-in user
        $sql = "SELECT trans.* 
                FROM trans 
                INNER JOIN appointments ON trans.appid = appointments.appid 
                WHERE appointments.userid = $userid
                ORDER BY trans.transid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result && $result->num_rows > 0) {
        $count = 1;

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['transid'] . '</td>';
            echo '<td>' . $row['appid'] . '</td>';
            echo '<td>' . $row['platenum'] . '</td>';
            echo '<td>' . $row['total_amount'] . '</td>';
            echo '<td>' . $row['remarks'] . '</td>';
            echo '<td>' . $row['transdate'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center">';
            echo '<button class="btn btn-primary me-2" onclick="viewDetails(' . $row['transid'] . ')">View Services</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++;
        }
    } else {
        echo '<tr><td colspan="8">No transactions found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="8">Invalid request.</td></tr>';
}

?>
