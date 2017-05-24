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
?>

<html>
<head>
    <title>Add a New Friend</title>
</head>
<body>
<h1>Add a New Friend</h1>
<br/>

<?php
$c = connect();

if (key_exists("firstName", $_POST)){
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $phoneNumber = $_POST["phoneNumber"];
    $age = $_POST["age"];

    $sql = "INSERT INTO friendForm (firstName, lastName, phoneNumber, age) VALUES('$firstName', '$lastName', '$phoneNumber', '$age');";
    $result = $c->query($sql);
    if (!$result) {
        echo "<p>Unable to save friend data</p>";
        die ("Unable to insert values: [" . $c->errno . "; ". $c->error . "]");
    }

    echo "<p>Your new friend information has been saved.</p>";
}
else {
    if (!(tableExists($c))) {
        echo "<p>No database currently exists! <a href='/Create.php'>Click here</a> to create one.</p>";
    } else {
        echo <<<html
<h1>Please enter the following information for your friend:</h1>
<form action="friendForm.php" method="post">
    First Name: <input type="text" name="firstName" value="" /> <br />
    Last Name: <input type="text" name="lastName" value="" /> <br />
    Phone Number: <input type="text" name="phoneNumber" value="" /> <br />
    Age: <input type="number" name="age" value="" /> <br />
    <br />
    <br />
    <input type="submit" name="submitButton" value="Submit Button" />
    <br />
    </form>
html;
    }
}
?>

</form>
</body>
</html>