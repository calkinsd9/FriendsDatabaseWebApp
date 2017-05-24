<?php
function connect()
{
    // Create a mysqli object connected to the database.
    $connection = new mysqli("cis.gvsu.edu", "calkinda", "calkinda");
    // Complain if the the connection fails.  (This would have to be more graceful
    // in a production environment)
    if (!$connection || $connection->connect_error) {
        die('Unable to connect to database [' . $connection->connect_error . ']');
    }
    if (!$connection->select_db("calkinda")) {
        die ("Unable to select database:  [" . $connection->error . "]");
    }
    return $connection;
}

function tableExists($c){
    $sql = "SHOW TABLES LIKE 'friendForm'";
    $result = $c->query($sql);
    return mysqli_num_rows($result) > 0;
}

function getAllFriends($c) {
    $sql = "select * from friendForm";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->errno . "; " . $c->error . "]");
    }
    return $result;
}

?>

<html>
<head>
    <title>All Your Friends</title>
</head>
<body>
<h1>Your Friends:</h1>
<br/>

<?php
$c = connect();

if (!(tableExists($c))) {
    echo "<p>No database exists yet. Please visit <a href='./Create.php'>this page</a> to create one.</p>";
}
else {
    echo <<<html
<table>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone Number</th>
        <th>Age</th>
    </tr>
html;

    $result = getAllFriends($c);
    // iterate over each record in the result.
    // (Each record will be one row in the table, beginning with <tr> and ending with </tr>
    foreach ($result as $row) {
        echo "<tr>";
        $keys = array("firstName", "lastName", "phoneNumber", "age");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    $c->close();

    echo "</table>";
}
?>

</form>
</body>
</html>