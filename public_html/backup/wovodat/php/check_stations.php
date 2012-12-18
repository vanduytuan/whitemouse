<?php
/******************************************************************************************************
* Displays list of stations in the selected volcano
*******************************************************************************************************/
// Connect to database
include 'php/include/db_connect_view.php';

// Get data type selected
	$vd_id=$_REQUEST['vd_id'];
	$vd_name=$_REQUEST['vd_name'];
	
	if ($vd_id=="") {
	// No volcano selected
	exit();
	}

// Count stations
$sta_sei_cnt=0;
$sta_def_cnt=0;
$sta_hyd_cnt=0;
$sta_gas_cnt=0;


// Seismic stations
	$getStations = mysql_query("select c.sn_id, c.ss_id, c.ss_lat, c.ss_lon, c.ss_name FROM sn a, ss c where a.vd_id = '$vd_id'") or die(mysql_error());
	if (! mysql_num_rows($getStations)) 
		$getStations = mysql_query("select a.jj_net_id, b.sn_id, c.sn_id, c.ss_id, c.ss_lat, c.ss_lon, c.ss_name FROM jj_volnet a, sn b, ss c where a.vd_id = '$vd_id' and a.jj_net_flag = 'S' and a.jj_net_id = b.sn_id and b.sn_id = c.sn_id") or die(mysql_error());
	$output .= "<br>Seismic Stations : " . mysql_num_rows($getStations);
	while ($getStation_arr = mysql_fetch_array($getStations)){
			foreach($getStation_arr as $k1 => $v1){
				$sta_sei_cnt++;
			}
		}

// If no data
if ($sta_sei_cnt==0) {
	echo "<div style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>No data available</center></div>";
	exit;
}else{

// Open select stations
echo <<<STRING
		<p>Station Available:</p>
		<select id="stat_id" style="width:100%; margin-top:2px">
STRING;

// According to volcano
session_start();
// List stations related to volcano

if($sta_sei_cnt !=0){
	echo "<option value='"'seista'"'>seismic";
	echo "</option>";
}

// Close list of stations
echo <<<STRING
	</select>
STRING;
}
?>
