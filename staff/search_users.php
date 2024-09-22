<?php

require("../server/connection.php");

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    if (!empty($query)) {
        $sql = "SELECT users.userid, users.firstname, 
                       users.lastname, users.email, DATE_FORMAT(users.bday, '%M %d, %Y') AS bday,
                       gender.gendertype as gender, users.phone, 
                       usertype.usertypename as usertype 
                FROM users 
                INNER JOIN gender ON users.gender = gender.genderid
                INNER JOIN usertype ON users.usertypeid = usertype.usertypeid
                WHERE (users.userid LIKE '%$query%' 
                OR users.firstname LIKE '%$query%' 
                OR users.lastname LIKE '%$query%' 
                OR users.email LIKE '%$query%' 
                OR DATE_FORMAT(users.bday, '%M %d, %Y') LIKE '%$query%' 
                OR DATE_FORMAT(users.bday, '%m/%d/%Y') LIKE '%$query%'
                OR gender.gendertype LIKE '%$query%' 
                OR users.phone LIKE '%$query%' 
                OR usertype.usertypename LIKE '%$query%')";
    } else {
        // If the query is empty, retrieve all records where usertypeid = 1
        $sql = "SELECT users.userid, users.firstname, 
                       users.lastname, users.email, DATE_FORMAT(users.bday, '%M %d, %Y') AS bday,
                       gender.gendertype as gender, users.phone, 
                       usertype.usertypename as usertype
                FROM users 
                INNER JOIN gender ON users.gender = gender.genderid 
                INNER JOIN usertype ON users.usertypeid = usertype.usertypeid";
    }

    $result = mysqli_query($connection, $sql);

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $row['userid'] . '</td>';
            echo '<td>' . $row['firstname'] . '</td>';
            echo '<td>' . $row['lastname'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['bday'] . '</td>';
            echo '<td>' . $row['gender'] . '</td>';
            echo '<td>' . $row['phone'] . '</td>';
            echo '<td>' . $row['usertype'] . '</td>';
            echo '<td>';
            echo '<div class="d-flex justify-content-center">';
            echo '<button class="btn btn-primary me-2" onclick="editUser(' . $row['userid'] . ')">Edit</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
            $count++;
        }
    } else {
        echo '<tr><td colspan="10">No users found.</td></tr>';
    }
}

?>