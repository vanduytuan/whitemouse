<?php

// Include PEAR DB package
require_once("DB.php");

function db_connect() {
    
    $dbUser = "root";
    $dbPass = "1234root";
    $dbHost = "localhost";
    $dbName = "monitoringdb";
    $dbType = "mysqli";
    
    $dsn = "$dbType://$dbUser:$dbPass@$dbHost/$dbName";
    //for example:  $dsn = "mysqli://testuser:testpass@localhost/test";
    $conn = @DB::connect($dsn);
    if (@DB::isError($conn)) {
        die($conn->getMessage());
    }
    return $conn;
}

?>
