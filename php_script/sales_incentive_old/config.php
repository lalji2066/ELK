<?php
function getdb(){
$servername = "192.168.12.219";
$username = "laljiy";
$password = "L@lj!y";
$db = "test";
try {
   
    $conn = mysqli_connect($servername, $username, $password, $db);
     //echo "Connected successfully"; 
    }
catch(exception $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
    return $conn;
}
?>
