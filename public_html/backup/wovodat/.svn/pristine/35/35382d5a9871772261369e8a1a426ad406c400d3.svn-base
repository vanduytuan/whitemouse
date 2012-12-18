<?php
/******************************************************************************************************
* Plots data from a certain station
******************************************************************************************************/

// Get data type and station selected
$datatype=$_REQUEST['datatype'];
$station_id=$_REQUEST['station_id'];
$nvol=$_REQUEST['nvolc'];

$startDate=explode("/", $_REQUEST['startdate']);
if (!checkdate($startDate[0]+0, $startDate[1]+0, $startDate[2]+0)) {
	$startDate=array(0, 0, 0);
}
$endDate=explode("/", $_REQUEST['enddate']);
if (!checkdate($endDate[0]+0, $endDate[1]+0, $endDate[2]+0)) {
	$endDate=array(12, 31, 9999);
}

// Get data
include 'php/include/db_connect_view.php';
mysql_select_db("wovodat") or die(mysql_error());
$sql="SELECT ";
include_once "php/include/data_from_db/".$datatype.".php";
$result=mysql_query($sql) or die(mysql_error());

// If no results
if (mysql_num_rows($result)==0) {
	echo "<div class='ui-state-highlight' style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>NO data available for this station</center></div>";
	// Disconnect from DB
	mysql_close($link);
	exit;
}

// MySQL result to array for Flot
include_once "php/include/data_to_flot/".$datatype.".php";

// Disconnect from DB
mysql_close($link);
?>