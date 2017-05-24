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
function makeTable($c, $option) {
    $result = $c->query("DROP TABLE friendForm");
    if (!$result) {
        die ("Unable to create table: [" . $c->errno . "; ". $c->error . "]");
    }
    $make_table = "CREATE TABLE friendForm (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, firstName VARCHAR(20), lastName VARCHAR(20), phoneNumber VARCHAR(20), age INT);";
    $result = $c->query($make_table);
    if (!$result) {
        die ("Unable to create table: [" . $c->errno . "; ". $c->error . "]");
    }
    if ($option == "seeded"){
        populateTable($c);
    }
    return $result;
}

function populateTable($c){
    $insertStatement = "INSERT INTO friendForm (firstName, lastName, phoneNumber, age) VALUES";
    $myFile = fopen("friendData.txt", "r");
    while (($line = fgets($myFile)) != false){
        $dataArray = explode(",", $line);
        $firstName = $dataArray[0];
        $lastName = $dataArray[1];
        $phoneNumber = $dataArray[2];
        $age = rtrim($dataArray[3]);
        $insertStatement .= "('$firstName', '$lastName', '$phoneNumber', $age), ";
    }
    fclose($myFile);
    $insertStatement = rtrim($insertStatement, ', ');
    $insertStatement .= ";";
    $result = $c->query($insertStatement);
    if (!$result) {
        die ("Unable to insert values: [" . $c->errno . "; ". $c->error . "]");
    }
    return $result;

}

function getAllFriends($c) {
    $sql = "select * from friendForm";
    $result = $c->query($sql);
    return $result;
}

function tableExists($c){
    $sql = "SHOW TABLES LIKE 'friendForm'";
    $result = $c->query($sql);
    return mysqli_num_rows($result) > 0;
}
?>

<html>
<head>
    <title>Create Database</title>
</head>
<body>
<h1>Create Database</h1>
<br/>

<?php
$c = connect();

if (key_exists("create", $_GET)){
    $option = $_GET['create'];
    makeTable($c, $option);
    echo "<p>Database successfully created!</p>";
}
else {
    if (tableExists($c)) {
        $result = getAllFriends($c);
        $numberOfRows = mysqli_num_rows($result);

        echo "<p>A database already exists and contains $numberOfRows entries.</p>";
        echo "<p>(Click <a href='./Display.php'>here</a> to view it.)";
        echo "<p><strong>Creating a new database will overwrite the current one!</strong></p>";
    } else {
        echo "<p>No database currently exists.</p>";
    }
}
?>

<br/>
<br/>
<form action="Create.php" method="get">
    <button name="create" type="submit" value="empty">Create Empty Database</button>
    <button name="create" type="submit" value="seeded">Create Seeded Database</button>
</form>
</body>
</html>