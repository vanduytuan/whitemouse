<?php
session_start();
/******************************************************************************************************
* Displays list of station-types in the selected volcano
*******************************************************************************************************/
// Connect to database
include 'php/include/db_connect_view.php';
include '../populate/convertie/f2genfunc/funcgen_datetime.php';

// Get data type selected
	$nvol=$_REQUEST['nvolc'];
	$vd_id=$_REQUEST['vd_id'];
	$vd_name=$_REQUEST['vd_name'];
	$vd_name=substr($vd_name,0,-9);
	$divnum=$_REQUEST['divnum'];
	
// if No volcano selected, exit;
	if ($vd_id=="") exit();	

// Counting stations available on the volcano; reference: $vd_id
$sta_sei_cnt=0;
$sta_def_cnt=0;
$sta_hyd_cnt=0;
$sta_the_cnt=0;
$sta_gas_cnt=0;
$sta_fie_cnt=0;
$sta_oth_cnt=0;

// Seismic sta
	$getStations = mysql_query("(select c.ss_code FROM sn a, ss c  where a.vd_id = '$vd_id'  and a.sn_id = c.sn_id) UNION (select c.ss_code FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20)") or die(mysql_error());
		
	while ($getStations_arr = mysql_fetch_array($getStations)){
				$getarr_arr[$sta_sei_cnt]=$getStations_arr[0];
				$sta_sei_cnt++;
	}
	$outputs .= "Seismic: " . count(array_unique($getarr_arr));
	unset($getarr_arr);

// Deformation sta
	$getStations = mysql_query("(select c.ds_code from cn a, ds c  where a.vd_id = '$vd_id' and a.cn_id = c.cn_id  order by c.ds_code) UNION (select c.ds_code FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20	ORDER BY c.ds_code)") or die(mysql_error());
	while ($getStations_arr = mysql_fetch_array($getStations)){
			$getarr_arr[$sta_def_cnt]=$getStations_arr[0];
			$sta_def_cnt++;
	}
	$outputd = "Deformation: " . count(array_unique($getarr_arr));
	unset($getarr_arr);
	
// Field sta
	$getStations = mysql_query("(select  c.fs_code from cn a, fs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id  order by c.fs_code) UNION (select c.fs_code FROM jj_volnet a, fs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.fs_lat, 2) + power(d.vd_inf_slon - c.fs_lon, 2))*100)<20 ORDER BY c.fs_code)") or die(mysql_error());
	while ($getStations_arr = mysql_fetch_array($getStations)){
			$getarr_arr[$sta_fie_cnt]=$getStations_arr[0];
			$sta_fie_cnt++;
	}
	$outputf = "Fields: " . count(array_unique($getarr_arr));
	unset($getarr_arr);

		
// Hydrologic sta
	$getStations = mysql_query("(select  c.hs_code from cn a, hs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id) UNION (select c.hs_code FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 ORDER BY c.hs_code)") or die(mysql_error());
	while ($getStations_arr = mysql_fetch_array($getStations)){
		$getarr_arr[$sta_hyd_cnt]=$getStations_arr[0];
		$sta_hyd_cnt++;
	}
	$outputh .= "Hydrologic:  " . count(array_unique($getarr_arr));
	unset($getarr_arr);

// Thermal sta
	$getStations = mysql_query("(select  c.ts_code FROM cn a, ts c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id) UNION (select c.ts_code FROM jj_volnet a, ts c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ts_lat, 2) + power(d.vd_inf_slon - c.ts_lon, 2))*100)<20 ORDER BY c.ts_code)") or die(mysql_error());
	while ($getStations_arr = mysql_fetch_array($getStations)){
		$getarr_arr[$sta_the_cnt]=$getStations_arr[0];
		$sta_the_cnt++;
	}
	$outputt .= "Thermal:  " . count(array_unique($getarr_arr));
	unset($getarr_arr);
			
// Gas sta
	$getStations = mysql_query("(select c.gs_code FROM cn a, gs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id) UNION (select c.gs_code FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 ORDER BY c.gs_code)") or die(mysql_error());
	$outputgnum .= "stations : ".mysql_num_rows($getStations);
	while ($getStations_arr = mysql_fetch_array($getStations)){
		$getarr_arr[$sta_gas_cnt]=$getStations_arr[0];
		$sta_gas_cnt++;
	}
	$outputg .= "Gas: " . count(array_unique($getarr_arr));
	unset($getarr_arr);
$sta_cnt=$sta_sei_cnt+$sta_def_cnt+$sta_gas_cnt+$sta_the_cnt;			
// If no data ----------------------------
if ($sta_cnt==0){
?>
	<script type="text/javascript">	$("#reloadSta<?php echo $divnum;?>").hide("fast");	$("#removeSta<?php echo $divnum;?>").hide("fast"); $("#reloadEqk<?php echo $divnum;?>").hide("fast");</script>
<?php
//	echo "<div style='width:100%; align:left;'><center>".$vd_name." still in populating</center></div>";
}else{// if station available------------
	

	echo "<div style='width:100%;font-size:11px;'> Stations:</div>";
	echo "<div>Check list below to view</div> ";


	
?>
	<script type="text/javascript">	$("#reloadSta<?php echo $divnum;?>").show();	$("#removeSta<?php echo $divnum;?>").show(); </script>

<?php
	echo "<div style='width:100%; align:left;'>";

	if($sta_sei_cnt>=1){//to display seismic station and create button on it
		echo "<button id='reloadSei".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent; border:#f3ffed;'>".$outputs."</button>";
		echo "<input type='checkbox' onclick='sei()' id='sei' name='sei' value='sei'/>"."<br/>";
		
?>
<script type="text/javascript">
	function sei()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(1,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("sei").checked=true;},100);
			
	}
	
</script>
<script type="text/javascript">
	$("#reloadSei<?php echo $divnum;?>").button();
	$("#reloadSei<?php echo $divnum;?>").click(function() {
		$("#stationSelect<?php echo $divnum;?>").hide();			
		$("#spatial<?php echo $divnum;?>").show();			
		$("#displayeq<?php echo $divnum;?>").show();
		$("#reloadEqk<?php echo $divnum;?>").show();
		setupMaps(1,<?php echo $divnum;?>);
	});
</script>	
<?php
	}
	if($sta_def_cnt>=1) {//to display deformation station and create button on it
		echo "<button id='reloadDef".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent;; border:#f3ffed;'>".$outputd."</button>";
		echo "<input type='checkbox' onclick='def()' id='def' name='def' value='def'/>"."<br/>";
?>
<script type="text/javascript">
	function def()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(2,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("def").checked=true;},100);
			
	}
	
</script>
<script type="text/javascript">
	$("#reloadDef<?php echo $divnum;?>").button();
	$("#reloadDef<?php echo $divnum;?>").click(function() {
		$("#stationSelect<?php echo $divnum;?>").show();			
		$("#displayeq<?php echo $divnum;?>").hide();
		$("#displayEquakeInformation<?php echo $divnum;?>").hide();			
		setupMaps(2,<?php echo $divnum;?>);
	});
</script>		
<?php
	}
	
	if($sta_hyd_cnt>=1) {
		echo "<button id='reloadHyd".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent;; border:#f3ffed;'>".$outputh."</button>";
		echo "<input type='checkbox' onclick='hyd()' id='hyd' name='hyd' value='hyd'/>"."<br/>";
?>
<script type="text/javascript">
	function hyd()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(3,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("hyd").checked=true;},100);
			
	}
	
</script>

<script type="text/javascript">
	$("#reloadHyd<?php echo $divnum;?>").button();
	$("#reloadHyd<?php echo $divnum;?>").click(function() {				
		$("#stationSelect<?php echo $divnum;?>").hide();			
		$("#displayeq<?php echo $divnum;?>").hide();
		$("#displayEquakeInformation<?php echo $divnum;?>").hide();			
		$("#temporal<?php echo $divnum;?>").hide("fast");	
			setupMaps(3,<?php echo $divnum;?>);
	});
</script>		
<?php
	}
	if($sta_the_cnt>=1) {
		echo "<button id='reloadThe".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent;; border:#f3ffed;'>".$outputt."</button>";
		echo "<input type='checkbox' onclick='the()' id='the' name='the' value='the'/>"."<br/>";
?>
<script type="text/javascript">
	function the()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(4,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("the").checked=true;},100);
			
	}
	
</script>

<script type="text/javascript">
	$("#reloadThe<?php echo $divnum;?>").button();
	$("#reloadThe<?php echo $divnum;?>").click(function() {
		$("#stationSelect<?php echo $divnum;?>").hide();			
		$("#displayeq<?php echo $divnum;?>").hide();
		$("#displayEquakeInformation<?php echo $divnum;?>").hide();			
		$("#temporal<?php echo $divnum;?>").hide("fast");	
			setupMaps(4,<?php echo $divnum;?>);
	});
</script>		
<?php
	}
	if($sta_gas_cnt>=1) {
		echo "<button id='reloadGas".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent;; border:#f3ffed;'>".$outputg."</button>";
		echo "<input type='checkbox' onclick='gas()' id='gas' name='gas' value='gas'/>"."<br/>";
?>
<script type="text/javascript">
	function gas()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(5,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("gas").checked=true;},100);
			
	}
	
</script>
<script type="text/javascript">
	$("#reloadGas<?php echo $divnum;?>").button();
	$("#reloadGas<?php echo $divnum;?>").click(function() {
		$("#stationSelect<?php echo $divnum;?>").hide();			
		$("#displayeq<?php echo $divnum;?>").hide();
		$("#displayEquakeInformation<?php echo $divnum;?>").hide();			
		$("#temporal<?php echo $divnum;?>").hide("fast");	
			setupMaps(5,<?php echo $divnum;?>);
	});
</script>		
<?php
	}

	if($sta_fie_cnt>=1) {
		echo "<button id='reloadFie".$divnum."' style='font-size:11px; font-family:arial; font-weight:normal; color:#000080; background:transparent;; border:#f3ffed;'>".$outputf."</button>";
		echo "<input type='checkbox' onclick='fie()' id='fie' name='fie' value='fie'/>"."<br/>";
?>
<script type="text/javascript">
	function fie()
	{
		
			$("#stationSelect<?php echo $divnum;?>").hide();			
			$("#spatial<?php echo $divnum;?>").show();		
			$("#displayeq<?php echo $divnum;?>").show();
			$("#reloadEqk<?php echo $divnum;?>").show();			
			setupMaps(6,<?php echo $divnum;?>);
			setTimeout(function() {document.getElementById("fie").checked=true;},100);
			
	}
	
</script>

<script type="text/javascript">
	$("#reloadFie<?php echo $divnum;?>").button();
	$("#reloadFie<?php echo $divnum;?>").click(function() {
		$("#stationSelect<?php echo $divnum;?>").hide();			
		$("#displayeq<?php echo $divnum;?>").hide();
		$("#displayEquakeInformation<?php echo $divnum;?>").hide();			
		$("#temporal<?php echo $divnum;?>").hide("fast");	
 		setupMaps(6,<?php echo $divnum;?>);
	});
</script>		
<?php
	}
	echo "</div>";
}

?>
