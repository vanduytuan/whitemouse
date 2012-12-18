<?php
session_start();
if (!isset($_SESSION['dev_login']))
die('Please login first!');
else if (!$_SESSION['dev_login'])
die('Please login first!');

$dataType = $_REQUEST['dataType'];
$vd_id = $_REQUEST['vd_id'];

//$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
$link = mysql_connect("localhost", "wovodat", "123_nousironsauxbois") or die(mysql_error());
mysql_select_db("wovodat") or die(mysql_error());

function generateSelect($vd_id, $networkId, $stationId, $stationName, $networkTable, $stationTable, $netFlag, $timeField, $dataType, $fromTable, $fields)
{
	$getStations = mysql_query("select c.$networkId, c.$stationId, c.$stationName from $networkTable a, $stationTable c where a.vd_id = '$vd_id' and a.$networkId = c.$networkId") or die(mysql_error());
	$getStations2 = mysql_query("select a.jj_net_id, c.$stationId, c.$networkId, c.$stationName from jj_volnet a, $stationTable c where a.vd_id = '$vd_id' and a.jj_net_flag = '$netFlag' and a.jj_net_id = c.$networkId") or die(mysql_error());
	$bool = false;
	if (! mysql_num_rows($getStations) && ! mysql_num_rows($getStations2)) echo "<div class='ui-state-highlight' style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>No Station Found</center></div>";
	else echo "<select style='width:100%; margin-top:2px' id='station_id'>";
	
	while ($getStation_obj = mysql_fetch_array($getStations))
	{
		$bool = true;
		echo "<option value='$getStation_obj[$stationId]'>$getStation_obj[$stationName]</option>";
	}
	
	while ($getStation_obj2 = mysql_fetch_array($getStations2))
	{
		$bool = true;
		echo "<option value='$getStation_obj2[$stationId]'>$getStation_obj2[$stationName]</option>";
	}
	if ($bool) {
		echo "</select>";
	?> 
	<div id="gbutton" align=center><button id="graphBtnCreate">Graph!</button></div>
	<script type="text/javascript">
	$("#graphBtnCreate").button();
	$("#graphBtnCreate").click(
		function() {
			$("#viewcontent").show();
			$("#east").html("");
			$("#north").html("");
			$("#elevation").html("");
			$("#fourth").html("");
			<?php 
			$field = strtok($fields, "."); 
			$count = 0;
			while ($field) {
			if ($count == 0) $div = "east"; else if ($count == 1) $div = "north"; else if ($count == 2) $div = "elevation"; else if ($count == 4) $div = "fourth";
			?>
			dataVars<?php echo $count; ?> = "fromTable=<?php echo $fromTable; ?>&field=<?php echo $field; ?>&dataType=<?php echo $dataType; ?>&station=<?php echo $stationTable; ?>&station_id=" + $("#station_id :selected").val() + "&startDate=" + $("#startdate").val() + "&endDate=" + $("#enddate").val() + "&timeField=<?php echo $timeField; ?>";
			$.ajax({
				  method: "get", url: "getChartData.php",
				  data: dataVars<?php echo $count; ?>,
				  success: function(html) {
					   $("#chartXML").html(html);
					   var chart<?php echo $count; ?>Var = new FusionCharts("../FusionCharts/<?php if ($dataType == "SO2") echo "Bar2D"; else echo "Line"; ?>.swf", "ChartId<?php echo $count; ?>", $("#viewcontent").width()-35, "400", "0", "0");
					   chart<?php echo $count; ?>Var.setDataXML(html);
					   chart<?php echo $count; ?>Var.render("<?php echo $div; ?>");
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
}

if ($vd_id == "")
	echo "<div class='ui-state-error' style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>Please select a Volcano</center></div>";
else if ($dataType == "Interval")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'sd_ivl_stime', $dataType, 'sd_ivl', 'sd_ivl_nrec');
else if ($dataType == "RSAM")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'sd_rsm_stime', $dataType, 'sd_sam', 'sd_rsm_count');
else if ($dataType == "SSAM")
	generateSelect($vd_id, 'sn_id', 'ss_id', 'ss_name', 'sn', 'ss', 'S', 'sd_ssm_stime', $dataType, 'sd_sam', 'sd_ssm_count');
else if ($dataType == "GPS")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_gpv_stime', $dataType, 'dd_gpv', 'dd_gpv_N.dd_gpv_E.dd_gpv_vert');
else if ($dataType == "Tiltmeter")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_tlt_time', $dataType, 'dd_tlt', 'dd_tlt1.dd_tlt2');
else if ($dataType == "EDM")
	generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_edm_time', $dataType, 'dd_edm', 'dd_edm_line');
else if ($dataType == "Strainmeter")
	echo "<div class='ui-state-highlight' style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>Still under evaluation</center></div>";
	//generateSelect($vd_id, 'cn_id', 'ds_id', 'ds_name', 'cn', 'ds', 'C', 'dd_str_time', $dataType, 'dd_str', 'dd_str_vdstr.dd_str_comp1.dd_str_comp2.dd_str_comp3.dd_str_comp4');
else if ($dataType == "SO2")
	generateSelect($vd_id, 'cn_id', 'gs_id', 'gs_name', 'cn', 'gs', 'C', 'gd_plu_time', $dataType, 'gd_plu', 'gd_plu_emit');
else if ($dataType == "Thermal")
	generateSelect($vd_id, 'cn_id', 'gs_id', 'gs_name', 'cn', 'gs', 'C', 'td_time', $dataType, 'td', 'td_temp');
else 
	echo "<div class='ui-state-error' style='width:100%; align:center; margin-top: 5px; margin-bottom: 5px'><center>Please select a data type</center></div>";

mysql_close($link);
?>