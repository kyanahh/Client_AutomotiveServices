<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM trans 
        WHERE transid LIKE '%$query%' OR 
              appid LIKE '%$query%' OR 
              platenum LIKE '%$query%' OR 
              staffid LIKE '%$query%' OR 
              total_amount LIKE '%$query%' OR 
              remarks LIKE '%$query%' OR 
              transdate LIKE '%$query%' OR 
              DATE_FORMAT(transdate, '%M %e, %Y') LIKE '%$query%' ORDER BY transdate DESC";
    } else {
        $sql = "SELECT * FROM trans ORDER BY transid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['transid'] . '</td>';
            echo '<td>' . $row['appid'] . '</td>';
            echo '<td>' . $row['platenum'] . '</td>';
            echo '<td>' . $row['total_amount'] . '</td>';
            echo '<td>' . $row['remarks'] . '</td>';
            echo '<td>' . $row['staffid'] . '</td>';
            echo '<td>' . $row['transdate'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center">';
            echo '<button class="btn btn-primary me-2" onclick="addDetails(' . $row['transid'] . ')">Edit</button>';
            echo '<button class="btn btn-primary me-2" onclick="editAppointment(' . $row['transid'] . ')">Edit</button>';
            echo '<button class="btn btn-danger" onclick="openDeleteModal(' . $row['transid'] .')">Delete</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="5">No transactions found.</td></tr>';
    }
}

?>
