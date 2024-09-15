<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT * FROM userlogs WHERE (logtime LIKE '%$query%' OR userid LIKE '%$query%' 
        OR DATE_FORMAT(logtime, '%M %e, %Y') LIKE '%$query%')";
    } else {
        // If the query is empty, retrieve all records where usertypeid = 1
        $sql = "SELECT * FROM userlogs";
    }
    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['userid'] . '</td>';
            echo '<td>' . $row['logtime'] . '</td>';
            echo '</tr>';
            $count++;
        }
    } else {
        echo '<tr><td colspan="5">No user logs found.</td></tr>';
    }
}

?>
