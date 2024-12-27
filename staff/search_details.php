<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT trans_details.*, services.servicetype 
        FROM trans_details INNER JOIN services
        ON trans_details.serviceid = services.serviceid
        WHERE trans_details.transid LIKE '%$query%' OR 
              services.servicetype LIKE '%$query%'
              ORDER BY detailid DESC";
    } else {
        $sql = "SELECT trans_details.*, services.servicetype 
                FROM trans_details INNER JOIN services 
                ON trans_details.serviceid = services.serviceid  
                ORDER BY detailid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['detailid'] . '</td>';
            echo '<td>' . $row['transid'] . '</td>';
            echo '<td>' . $row['servicetype'] . '</td>';
            echo '<td>' . $row['amount'] . '</td>';
            echo '<td>' . $row['qty'] . '</td>';
            echo '<td>' . $row['descrip'] . '</td>';
            echo '<td>' . $row['ditsdate'] . '</td>';
            echo '<td>' . $row['staffid'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center">';
            echo '<button class="btn btn-primary me-2" onclick="editDetails(' . $row['detailid'] . ')">Edit</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="5">No transaction details found.</td></tr>';
    }
}

?>
