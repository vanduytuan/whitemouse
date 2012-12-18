<?php
/******************************************************************************************************
* Plots data from a certain station
******************************************************************************************************/

// Get data type and station selected
$datatype=$_REQUEST['datatype'];
$station_id=$_REQUEST['station_id'];
$nvol=$_REQUEST['nvolc'];

if($datatype=="dd_gpv" || $datatype=="dd_gps") {$tabel="ds";}
if($datatype=="sd_rsm" || $datatype=="sd_ssm") {$tabel="ss";}

// Get data
include 'php/include/db_connect_view.php';
$result = mysql_query("select b.cc_code, b.cc_country FROM $tabel a, cc b WHERE a.".$tabel."_id='$station_id' and a.cc_id=b.cc_id");

while ($ownname=mysql_fetch_array($result)){
	$ownername=$ownname[0];
	$country=$ownname[1];
}
echo "<div><br><span style='font-size:9px;'>Please contact: $ownername $country,<br> for more information about the data</span></div>";

// Disconnect from DB
mysql_close($link);
?>