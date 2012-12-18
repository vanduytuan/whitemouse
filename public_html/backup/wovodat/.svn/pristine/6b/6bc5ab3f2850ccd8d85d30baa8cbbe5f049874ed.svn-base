<?php
function getNextDay($today) 
{
	$nxtDate = $today;
	$mkDate = explode("/", $today);
	if (checkdate($mkDate[0]+0, $mkDate[1]+1, $mkDate[2])) {
		$nxtDate = $mkDate[0] . "/" . ($mkDate[1]+1 < 10 ? "0" . ($mkDate[1]+1) : ($mkDate[1]+1) ) . "/" . $mkDate[2];
	}
	else if (checkdate($mkDate[0]+1, 1, $mkDate[2]))
	{
		$nxtDate = ($mkDate[0]+1 < 10 ? "0" . ($mkDate[0]+1) : ($mkDate[0]+1) ) . "/01/" . $mkDate[2];
	}
	else if (checkdate(1, 1, $mkDate[2]+1))
	{
		$nxtDate = "01/01/" . ($mkDate[2]+1);
	}
	return $nxtDate;
}

$dataType = $_REQUEST['dataType'];
$field = $_REQUEST['field'];
$station = $_REQUEST['station'];
$station_id = $_REQUEST['station_id'];
if (isset($_REQUEST['startDate'])) if ($_REQUEST['startDate'] != "undefined") $startDate = explode("/", $_REQUEST['startDate']);
if (isset($_REQUEST['endDate'])) if ($_REQUEST['endDate'] != "undefined") $endDate = explode("/", $_REQUEST['endDate']);
$timeField = $_REQUEST['timeField'];

echo $_REQUEST['startDate'];
echo $_REQUEST['endDate'];
if ($dataType == "Interval")
	$fromTable = 'sd_ivl';
else if ($dataType == "RSAM")
	$fromTable = 'sd_rsm';
else if ($dataType == "SSAM")
	$fromTable = 'sd_ssm';
else if ($dataType == "GPS")
	$fromTable = 'dd_gpv';
else if ($dataType == "Tiltmeter")
	$fromTable = 'dd_tlt';
else if ($dataType == "EDM")
	$fromTable = 'dd_edm';
else if ($dataType == "Strainmeter")
	$fromTable = 'dd_str';
else if ($dataType == "SO2")
	$fromTable = 'gd_plu';
else if ($dataType == "Thermal")
	$fromTable = '';

$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
mysql_select_db("wovodat") or die(mysql_error());
$count = 0;
$sets = "";
$where = "where " . $station . "_id = '$station_id'";
$dates = " and $timeField BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";

if ($startDate[0] && $endDate[0]) $where .= $dates;
$where .= " LIMIT 200";
echo "select `$field`, date_format($timeField,'%m') as mon, date_format($timeField,'%d') as day, date_format($timeField,'%Y') as year from $fromTable $where";
$result = mysql_query("select `$field`, date_format($timeField,'%m') as mon, date_format($timeField,'%d') as day, date_format($timeField,'%Y') as year from $fromTable $where") or die(mysql_error());

while ($fieldList = mysql_fetch_array($result))
{
	$tok = $fieldList['mon']."/".$fieldList['day']."/".$fieldList['year'];
	if (!$prevDate) {
		$disp = $fieldList['mon'] . "/" . $fieldList['year'];
		$prevDate = $fieldList['mon'] . "/01/" . $fieldList['year'];
		if ($tok != $prevDate) {
			$sets .= "<set label='$disp' />\n";
		}
	}
	while ($prevDate != $tok && $prevDate && $tok)
	{
		$prevDate = getNextDay($prevDate);
		$mkDate = explode("/", $prevDate);
		if ($prevDate == $tok) break;
		$sets .= "<set label='$mkDate[0]/$mkDate[2]' showLabel='". ($mkDate[1] == 1? "1" : "0") ."' />\n";
	}
	$mkDate = explode("/", $prevDate);
	$prevDate = $tok;
	$tok = $fieldList[$field];
	$sets .= "<set label='$mkDate[0]/$mkDate[2]' value='$tok' tooltext='$prevDate, $tok' decimalprecision='2' anchorRadius='5' showLabel='". ($fieldList['day'] == 1? "1" : "0") ."'/>\n";
	
	if (!isset($PHighVal)) $PHighVal = $tok;
	else if ($PHighVal < $tok) $PHighVal = $tok;
	if (!isset($PLowVal)) $PLowVal = $tok;
	else if ($PLowVal > $tok) $PLowVal = $tok;
}
if ($PHighVal == $PLowVal) {
	$PHighVal++;
	$PLowVal--;
}
else {
	$Mid = $PHighVal + $PLowVal;
	$Mid /= 2;
	$PLowVal -= ($Mid-$PLowVal) * 0.2;
	$PHighVal += ($PHighVal-$Mid) * 0.2;
}
$PHighVal = ceil($PHighVal * 100)/100;
$PLowVal = floor($PLowVal * 100)/100;
if ($count == 0) $div = "east";
else if ($count == 1) $div = "north";
else if ($count == 2) $div = "elevation";
$count++;
echo "<chart caption='$field' defaultAnimation='0' connectNullData='1' labelDisplay='ROTATE' slantLabels='1' formatnumberscale='0' YAxisMinValue='$PLowVal' YAxisMaxValue='$PHighVal'  xaxisname='Time' YAxisName='$field' showvalues='0' palette='3'>\n$sets</chart>";
mysql_close($link);
?>