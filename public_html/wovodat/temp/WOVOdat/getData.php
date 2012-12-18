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
$rawData = $_REQUEST['data'];
$data = strtok($rawData, "."); //"dd_gps_lat";
$ds_id = strtok(".");
$ds_id_ref1 = strtok(".");
$useDates = strtok(".");
$startDate = explode("/", strtok("."));
$endDate = explode("/", strtok("."));
$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
mysql_select_db("wovodat") or die(mysql_error());
$cats = "<categories>";
$firstField = "<dataset renderAs='line' linethickness='2' seriesName=\"\" >";
$sets = "";
$where = "where `ds_id` = '$ds_id' and ds_id_ref1 = '$ds_id_ref1'";
$dates = " and dd_gps_time BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";
if ($useDates=='1' && checkdate($startDate[0]+0, $startDate[1]+0, $startDate[2]+0) && checkdate($endDate[0]+0, $endDate[1]+0, $endDate[2]+0)) $where .= $dates;
$where .= " LIMIT 200";
$result = mysql_query("select ds_id, dd_gps_id, `$data`, date_format(dd_gps_time,'%m') as mon, date_format(dd_gps_time,'%d') as day, date_format(dd_gps_time,'%Y') as year from dd_gps $where") or die(mysql_error());
while ($lat = mysql_fetch_array($result))
{
	$tok = $lat['mon']."/".$lat['day']."/".$lat['year'];
	if (!$prevDate) {
		$disp = $lat['mon'] . "/" . $lat['year'];
		$prevDate = $lat['mon'] . "/01/" . $lat['year'];
		if ($tok != $prevDate) {
			$cats .= "<category label=\"$prevDate\"/>\n";
			$firstField .= "<set />\n";
			$sets .= "<set label=\"$disp\" />\n";
		}
	}
	while ($prevDate != $tok && $prevDate && $tok)
	{
		$prevDate = getNextDay($prevDate);
		$mkDate = explode("/", $prevDate);
		if ($prevDate == $tok) break;
		$cats .= "<category label=\"$prevDate\" showLabel=\"". ($mkDate[1] == 1? "1" : "0") ."\" />";
		$firstField .= "<set />\n";
		$sets .= "<set label=\"$mkDate[0]/$mkDate[2]\" showLabel=\"". ($mkDate[1] == 1? "1" : "0") ."\" />\n";
	}
	//if ($lat['day'] == 1) $cats.="<category label=\"$tok\" />";
	//else $cats .= "<category label=\"$tok\" showLabel='0' />\n";
	$mkDate = explode("/", $prevDate);
	$prevDate = $tok;
	$tok = $lat[$data];
	//$firstField .= "<set value=\"$tok\" anchorRadius='5' />\n";
	$sets .= "<set label=\"$mkDate[0]/$mkDate[2]\" value=\"$tok\" tooltext='$prevDate, $tok' decimalprecision=\"2\" anchorRadius='5' showLabel=\"". ($lat['day'] == 1? "1" : "0") ."\"/>\n";
	
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
$cats .= "</categories>";
$firstField .= "</dataset>";
?><chart caption="<?php echo $data; ?>" defaultAnimation='0' connectNullData='1' labelDisplay='ROTATE' slantLabels='1' formatnumberscale="0" YAxisMinValue="<?php echo $PLowVal; ?>" YAxisMaxValue="<?php echo $PHighVal; ?>"  xaxisname="Time" YAxisName="<?php if ($data == "dd_gps_lat") echo "East"; else if ($data == "dd_gps_lon") echo "North"; else if ($data == "dd_gps_elev") echo "Elevation"; ?>" showvalues="0" palette="3">
<?php
echo "$sets";
mysql_close($link);
?>
</chart>