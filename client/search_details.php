<?php

require("../server/connection.php");

// Ensure both `query` and `transid` are present in the POST request
if (isset($_POST['query']) && isset($_POST['transid'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    $transid = intval($_POST['transid']); // Ensure transid is an integer

    if (!empty($query)) {
        $sql = "SELECT trans_details.*, services.servicetype 
                FROM trans_details 
                LEFT JOIN services ON trans_details.serviceid = services.serviceid
                WHERE trans_details.transid = $transid 
                AND (services.servicetype LIKE '%$query%' 
                     OR trans_details.amount LIKE '%$query%') 
                ORDER BY trans_details.detailid DESC";
    } else {
        $sql = "SELECT trans_details.*, services.servicetype 
                FROM trans_details 
                LEFT JOIN services ON trans_details.serviceid = services.serviceid
                WHERE trans_details.transid = $transid";
    }

    $result = mysqli_query($connection, $sql);

    if ($result && $result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['servicetype'] . '</td>';
            echo '<td>' . $row['amount'] . '</td>';
            echo '<td>' . $row['qty'] . '</td>';
            echo '<td>' . $row['descrip'] . '</td>';
            echo '<td>' . $row['ditsdate'] . '</td>';
            echo '</tr>';
            $count++;
        }
    } else {
        echo '<tr><td colspan="7">No service details found for this transaction.</td></tr>';
    }
} else {
    echo '<tr><td colspan="6">Invalid request.</td></tr>';
}
?>