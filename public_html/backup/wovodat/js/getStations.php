<?php
session_start();
if (!isset($_SESSION['dev_login']))
die('Please login first!');
else if (!$_SESSION['dev_login'])
die('Please login first!');

$dataType = $_REQUEST['dataType'];
$vd_id = $_REQUEST['vd_id'];

$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
mysql_select_db("wovodat") or die(mysql_error());

function generateSelect($vd_id, $networkId, $stationId, $stationName, $networkTable, $stationTable, $netFlag, $timeField, $dataType, $fromTable, $fields)
{
	$getStations = mysql_query("select c.$networkId, c.$stationId, c.$stationName from $networkTable a, $stationTable c where a.vd_id = '$vd_id' and a.$networkId = c.$networkId") or die(mysql_error());

	if (! mysql_num_rows($getStations)) $getStations = mysql_query("select a.jj_net_id, c.$stationId, c.$networkId, c.$stationName from jj_volnet a, $stationTable c where a.vd_id = '$vd_id' and a.jj_net_flag = '$netFlag' and a.jj_net_id = c.$networkId") or die(mysql_error());
	
	if (! mysql_num_rows($getStations)) echo "<div class='ui-state-highlight' style='width:100%; align:center'><center>No Station Found</center></div>";
	else echo "<select style='width:100%; margin-top:2px' id='station_id'>";
	
	while ($getStation_obj = mysql_fetch_array($getStations))
	{
		echo "<option value='$getStation_obj[$stationId]'>$getStation_obj[$stationName]</option>";
	}
	if (mysql_num_rows($getStations)) echo "</select>";
	?> 

	<div id="gbutton" align=center><button id="graphBtnCreate">Graph!</button></div>
	<script type="text/javascript">
	$("#graphBtnCreate").button();
	$("#graphBtnCreate").click(
		function() {
			<?php 
			$field = strtok($fields, "."); 
			$count = 0;
			while ($field) {?>
				alert("getChartData.php?fromTable=<?php echo $fromTable; ?>&field=<?php echo $field; ?>&dataType=<?php echo $dataType; ?>&station=<?php echo $stationTable; ?>&station_id=" + $("#station_id :selected").val() + "&startDate=" + $("#startDate").val() + "&endDate=" + $("#endDate").val() + "&timeField=<?php echo $timeField; ?>");
			   var chart<?php echo $count; ?>Var = new FusionCharts("../FusionCharts/Line.swf", "ChartId<?php echo $count; ?>", $("#viewcontent").width()-35, "300", "0", "0");
			   chart<?php echo $count; ?>Var.setDataURL("getChartData.php?fromTable=<?php echo $fromTable; ?>&field=<?php echo $field; ?>&station=<?php echo $stationTable; ?>&station_id=" + $("#station_id :selected").val() + "&startDate=" + $("#startDate").val() + "&endDate=" + $("#endDate").val() + "&timeField=<?php echo $timeField; ?>");
			   chart<?php echo $count; ?>Var.render("<?php if ($count == 0) echo "east"; else if ($count == 1) echo "north"; else if ($count == 2) echo "elevation"; ?>");
			   $.ajax({
				  method: "post", url: "getChartData.php",
				  data: "getChartData.php?fromTable=<?php echo $fromTable; ?>&field=<?php echo $field; ?>&dataType=<?php echo $dataType; ?>&station=<?php echo $stationTable; ?>&station_id=" + $("#station_id :selected").val() + "&startDate=" + $("#startdate").val() + "&endDate=" + $("#enddate").val() + "&timeField=<?php echo $timeField; ?>",
				  success: function(html) {
					$("#chartXML").html(html);
				  }
			   });
			<?php 
			$count++; 
			$field = strtok("."); 
			} ?>
		}
	);
	</script>
	<?php
}

if ($dataType == "Interval")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'dd_gpv_stime', $dataType, 'sd_ivl', '');
else if ($dataType == "RSAM")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'dd_gpv_stime', $dataType, 'sd_rsm', '');
else if ($dataType == "SSAM")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'dd_gpv_stime', $dataType, 'sd_ssm', '');
else if ($dataType == "GPS")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_gpv_stime', $dataType, 'dd_gpv', 'dd_gpv_N.dd_gpv_E.dd_gpv_vert');
else if ($dataType == "Tiltmeter")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_gpv_stime', $dataType, 'dd_tlt', '');
else if ($dataType == "EDM")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_gpv_stime', $dataType, 'dd_edm', '');
else if ($dataType == "Strainmeter")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_gpv_stime', $dataType, 'dd_str', '');
else if ($dataType == "SO2")
	generateSelect($vd_id, 'cn_id', 'gs_id', 'gs_name', 'cn', 'gs', 'C', 'gd_plu_time', $dataType, 'gd_plu', 'gd_plu_emit');
else if ($dataType == "Thermal")
	generateSelect($vd_id, 'cn_id', 'gs_id', 'gs_name', 'cn', 'gs', 'C', 'dd_gpv_stime', $dataType, '', '');
else 
	echo "<div class='ui-state-error' style='width:100%; align:center'><center>Please select a data type</center></div>";

mysql_close($link);
?>