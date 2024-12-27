<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM services 
        WHERE serviceid LIKE '%$query%' OR 
              servicetype LIKE '%$query%'
              ORDER BY serviceid DESC";
    } else {
        $sql = "SELECT * FROM services ORDER BY serviceid DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1; 

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['serviceid'] . '</td>';
            echo '<td>' . $row['servicetype'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center">';
            echo '<button class="btn btn-primary me-2" onclick="editService(' . $row['serviceid'] . ')">Edit</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++; 
        }
    } else {
        echo '<tr><td colspan="5">No services found.</td></tr>';
    }
}

?>
