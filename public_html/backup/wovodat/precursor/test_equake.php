<?php
//measure time 
function getTime()
    {
    $a = explode (' ',microtime());
    return(double) $a[0] + $a[1];
    }
$Start = getTime();

include 'php/include/db_connect_view.php';
$volcanoId = '283';
$numberOfEvents = 100;
$volcanoNameAndCavw = 'Ruapehu_0401-10=';
$volcanoName = 'Ruapehu';
$divNum = 1;

    $startDate = '1/1/2012';
	$endDate=date('d/m/y');
    $startDepth = 0;
    $endDepth = 50;
$equakeType = '';
if($equakeType != '') $equakeType = " and c.sd_evn_eqtype = '$equakeType' ";
$start = split('/', $startDate);
$end = split('/', $endDate);
$dates = " and c.sd_evn_time BETWEEN '$start[2]-$start[0]-$start[1]' AND '$end[2]-$end[0]-$end[1]' ";
//$dates = " and c.sd_evn_time BETWEEN $startDate AND $endDate ";
$depth = " and c.sd_evn_edep BETWEEN $startDepth AND $endDepth ";
$mapWidth = 20;// map width is not set by user, hard coded value

//get all network that has data about volcano
$sqlNetwork1 = "select sn_id from sn where vd_id = '$volcanoId'";
$sqlNetwork2 = "select jj_net_id from jj_volnet where vd_id = '$volcanoId' and jj_net_flag = 'S'";
$network_query1 = mysql_query($sqlNetwork1) or die('<b>Can\'t connect to server now!<br/>');
$network_query2 = mysql_query($sqlNetwork2) or die('<b>Can\'t connect to server now!<br/>');
$network_ids = array();

while ($network_ids_fetch = mysql_fetch_array($network_query1, MYSQL_NUM)){
	array_push($network_ids,$network_ids_fetch[0]);
}
while ($network_ids_fetch = mysql_fetch_array($network_query2, MYSQL_NUM)){
	array_push($network_ids,$network_ids_fetch[0]);
}

//query for the event that happens within the network found above
$sqlEvent = "select sd_evn_elat, sd_evn_elon, sd_evn_edep, sd_evn_pmag,
                UNIX_TIMESTAMP(sd_evn_time), sd_evn_eqtype from sd_evn where sn_id = $network_ids[0]";
for ($i=1;$i<count($network_ids);$i++){
	$sqlEvent = $sqlEvent . " or sn_id = $network_ids[$i]";
}
$sqlEvent = $sqlEvent . " limit $numberOfEvents";
echo $sqlEvent;
$getQuakes = mysql_query($sqlEvent) or die('<b>Can\'t connect to server now!</b>');
$count = 0;
while ($row = mysql_fetch_array($getQuakes,MYSQL_NUM)){
	$count++;
	echo $row[0] . "," . $row[5] . "</br>";
}
echo "Time taken2 = ".number_format((getTime() - $Start),2)." secs";
echo "Number of rows:" . $count
?>

