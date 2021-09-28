<?php
$dbServerName = "108.62.122.47";
$dbUsername = "root";
$dbPassword = "voipinfotech@2019";
$dbName = "bhawani_xbank";

// create connection
$conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";





 $query="select * from accounts Where `AccountNumber`='UDRRD3488'";
    $result=mysqli_query($conn,$query);
  
    while ($row=mysqli_fetch_array($result,MYSQLI_BOTH)) {
     print_r($row);
    }
?>