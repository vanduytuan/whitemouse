<?php
/******************************************************************************************************
* Reloads Google Maps API - centered on selected volcano, with monitoring stations, seismic events and neighbouring volcanoes
******************************************************************************************************/
include 'php/include/db_connect_view.php';
$vd_id = $_REQUEST['vd_id'];
$vd_name = $_REQUEST['vd_name'];
$staplot = $_REQUEST['staplot'];
$qty = 100;

if ($_REQUEST['filter']) {
	$qty = $_REQUEST['qty'];
	$date_start = $_REQUEST['date_start'];
	$date_end = $_REQUEST['date_end'];
	$dr_start = $_REQUEST['dr_start'];
	$dr_end = $_REQUEST['dr_end'];
	$eqtype = $_REQUEST['eqtype'];
}
if ($date_start && $date_end) {
	$startDate = explode("/", $date_start);
	$endDate = explode("/", $date_end);
	$dates = " and c.sd_evn_time BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";
}
if ($dr_start && $dr_end){
	$depth = " and c.sd_evn_edep BETWEEN '$dr_start' AND '$dr_end' ";
}
if ($eqtype){
	$quaketype = " and sd_evn_eqtype = '$eqtype' ";
}
$result = mysql_query("select vd_inf_slat, vd_inf_slon from vd_inf where vd_id = '$vd_id'") or die(mysql_error());

if ($vd_info_obj = mysql_fetch_object($result)) {
?>  

<script type="text/javascript">
    //<![CDATA[
    
	if (GBrowserIsCompatible()) { 
		// A function to create the marker and set up the event window
		// Dont try to unroll this function. It has to be here for the function closure
		// Each instance of the function preserves the contends of a different instance
		// of the "marker" and "html" variables which will be needed later when the event triggers.    

		function createMarker(point,html) {
			var marker = new GMarker(point);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}

		function createMap(divId, showStations) {
			// Display the map, with some controls and set the initial location 
			var map = new GMap2(document.getElementById(divId));
			map.addControl(new GLargeMapControl());
			map.setCenter(new GLatLng(<?php echo $vd_info_obj->vd_inf_slat . "," . $vd_info_obj->vd_inf_slon; ?>),8);
			map.enableRotation();
			var point = new GLatLng(<?php echo $vd_info_obj->vd_inf_slat . "," . $vd_info_obj->vd_inf_slon; ?>);
			var marker = createMarker(point,'<div><?php echo "Current Volcano: " . addslashes($vd_name) . "<br>Lat: $vd_info_obj->vd_inf_slat<br>Lon: 	$vd_info_obj->vd_inf_slon"; ?><\/div>');
			var newIcon = MapIconMaker.createMarkerIcon({width:26, height:26,primaryColor: "#ff00ff"});
		
	
			if (showStations>=1) {
				map.setZoom(10);
<?php				
				$ctr = 0;
				$ct = 0;
				session_start();
				$_SESSION['ss_obj']=array();
				$ss_obj=&$_SESSION['ss_obj'];
				$_SESSION['ds_obj']=array();
				$ds_obj=&$_SESSION['ds_obj'];
				$_SESSION['gs_obj']=array();
				$gs_obj=&$_SESSION['gs_obj'];
				$_SESSION['hs_obj']=array();
				$hs_obj=&$_SESSION['hs_obj'];	
				$_SESSION['fs_obj']=array();
				$fs_obj=&$_SESSION['fs_obj'];
				$_SESSION['ts_obj']=array();
				$ts_obj=&$_SESSION['ts_obj'];

				$_SESSION['ss9_obj']=array();
				$ss9_obj=&$_SESSION['ss9_obj'];
				$_SESSION['ds9_obj']=array();
				$ds9_obj=&$_SESSION['ds9_obj'];
				$_SESSION['gs9_obj']=array();
				$gs9_obj=&$_SESSION['gs9_obj'];
				$_SESSION['hs9_obj']=array();
				$hs9_obj=&$_SESSION['hs9_obj'];	
				$_SESSION['fs9_obj']=array();
				$fs9_obj=&$_SESSION['fs9_obj'];
				$_SESSION['ts9_obj']=array();
				$ts9_obj=&$_SESSION['ts9_obj'];
				
				$nss_sta=&$_SESSION['nss_sta'];
				$nds_sta=&$_SESSION['nds_sta'];			
				$ngs_sta=&$_SESSION['ngs_sta'];			
				$nhs_sta=&$_SESSION['nhs_sta'];			
				$nts_sta=&$_SESSION['nts_sta'];			
?>				 
				if (showStations==9) {
<?php
				// Seismic stations: GREEN markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ff00"});';
				$getStations = mysql_query("select c.sn_id, c.ss_code, c.ss_lat, c.ss_lon, c.ss_stime, c.ss_etime from sn a, ss c where a.vd_id = '$vd_id' and a.sn_id = c.sn_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.sn_id, c.ss_code, c.ss_lat, c.ss_lon, c.ss_stime, c.ss_etime from jj_volnet a, ss c, cc d, vd_inf e  where a.vd_id = '$vd_id' and a.vd_id =e.vd_id and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and c.cc_id=d.cc_id and (sqrt(power(e.vd_inf_slat - c.ss_lat, 2) + power(e.vd_inf_slon - c.ss_lon, 2))*100)<=30 order by c.ss_code" ) or die(mysql_error());
				
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ss_lat,$getStation_obj->ss_lon);";
					echo "var marker$ct = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ct);";
					$ct++;
					// Store in session
					array_push($ss9_obj, $getStation_obj);
				}
				// Deformation stations: PURPLE markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ff00ff"});';
				$getStations = mysql_query("select c.cn_id, c.ds_code, c.ds_nlat, c.ds_nlon, c.ds_stime, c.ds_etime FROM cn a, ds c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id order by c.ds_code") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.ds_code, c.ds_nlat, c.ds_nlon, c.ds_stime, c.ds_etime FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20 ORDER BY c.ds_code") or die(mysql_error());	
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ds_nlat,$getStation_obj->ds_nlon);";
					echo "var marker$ct = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ct);";
					$ct++;
					// Store in session
					array_push($ds9_obj, $getStation_obj);
				}
				// Hydrologic stations: BLUE markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#0000ff"});';
				$getStations = mysql_query("select c.cn_id, c.hs_code, c.hs_lat, c.hs_lon, c.hs_stime, c.hs_etime from cn a, hs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.hs_code, c.hs_lat, c.hs_lon, c.hs_stime, c.hs_etime f FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 ORDER BY c.hs_code") or die(mysql_error());	
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->hs_lat,$getStation_obj->hs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($hs_obj, $getStation_obj);
				}
				// Thermal stations: ORANGE markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ff7f00"});';
				$getStations = mysql_query("select c.cn_id, c.ts_code, c.ts_lat, c.ts_lon, c.ts_stime, c.ts_etime from cn a, ts c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.ts_code, c.ts_lat, c.ts_lon, c.ts_stime, c.ts_etime FROM jj_volnet a, ts c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ts_lat, 2) + power(d.vd_inf_slon - c.ts_lon, 2))*100)<20 ORDER BY c.ts_code") or die(mysql_error());	
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ts_lat,$getStation_obj->ts_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($ts_obj, $getStation_obj);
				}
				// Gas stations: YELLOW markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ffff00"});';
				$getStations = mysql_query("select c.cn_id, c.gs_code, c.gs_lat, c.gs_lon, c.gs_stime, c.gs_etime FROM cn a, gs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.gs_code, c.gs_lat, c.gs_lon, c.gs_stime, c.gs_etime FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 ORDER BY c.gs_code") or die(mysql_error());	
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->gs_lat,$getStation_obj->gs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($gs_obj, $getStation_obj);
				}
		
				// Fields stations: CYAN markers
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ffff"});';
				$getStations = mysql_query("select c.cn_id, c.fs_id, c.fs_lat, c.fs_lon, c.fs_code, c.fs_stime, c.fs_etime from cn a, fs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.fs_id, c.fs_lat, c.fs_lon, c.fs_code, c.fs_stime, c.fs_etime FROM jj_volnet a, fs c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.fs_lat, 2) + power(d.vd_inf_slon - c.fs_lon, 2))*100)<20 ORDER BY c.fs_code") or die(mysql_error());	
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->fs_lat,$getStation_obj->fs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($fs_obj, $getStation_obj);
				}
?>
			}
				
			if (showStations<9) {
<?php
				
				// Seismic stations: GREEN markers
				if (($staplot==1)) {
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ff00"});';
				
				$getStations = mysql_query("select c.ss_code, c.ss_lat, c.ss_lon, c.ss_stime, c.ss_etime, d.cc_code from sn a, ss c, cc d where a.vd_id = '$vd_id' and a.sn_id = c.sn_id and c.cc_id=d.cc_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.ss_code, c.ss_lat, c.ss_lon, c.ss_stime, c.ss_etime, d.cc_code from jj_volnet a, ss c, cc d, vd_inf e  where a.vd_id = '$vd_id' and a.vd_id =e.vd_id and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and c.cc_id=d.cc_id and (sqrt(power(e.vd_inf_slat - c.ss_lat, 2) + power(e.vd_inf_slon - c.ss_lon, 2))*100)<=20 order by c.ss_code" ) or die(mysql_error());
								
				$output .= "<br>Seismic Stations : " . mysql_num_rows($getStations);
				$nss_sta=mysql_num_rows($getStations);
				
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ss_lat,$getStation_obj->ss_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
						 marker$ctr.openInfoWindowHtml('Seismic station: $getStation_obj->ss_code<br>Lat: $getStation_obj->ss_lat<br>Lon: $getStation_obj->ss_lon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($ss_obj, $getStation_obj);
				}
				}
		
				// Deformation stations: PURPLE markers
				if (($staplot==2)) {
				$getStations = mysql_query("select c.cn_id, c.ds_code, c.ds_nlat, c.ds_nlon, c.ds_stime, c.ds_etime, d.cc_code from cn a, ds c, cc d where a.vd_id = '$vd_id' and a.cn_id = c.cn_id and c.cc_id=d.cc_id order by c.ds_code") or die(mysql_error());

				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.ds_code, c.ds_nlat, c.ds_nlon, c.ds_stime, c.ds_etime, d.cc_code 		FROM jj_volnet a, ds c, cc d, vd_inf e	WHERE a.vd_id = '$vd_id' and a.vd_id=e.vd_id and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id  and c.cc_id=d.cc_id  and (sqrt(power(e.vd_inf_slat - c.ds_nlat, 2) + power(e.vd_inf_slon - c.ds_nlon, 2))*100)<20		ORDER BY c.ds_code") or die(mysql_error());
				
				$output .= "<br>Deformation Stations : " . mysql_num_rows($getStations);
				$nds_sta=mysql_num_rows($getStations);
				
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ds_nlat,$getStation_obj->ds_nlon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
						marker$ctr.openInfoWindowHtml('GPS station: $getStation_obj->ds_code<br>Lat: $getStation_obj->ds_nlat<br>Lon: $getStation_obj->ds_nlon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($ds_obj, $getStation_obj);
				}
				}
			
				// Hydrologic stations: BLUE markers
				if (($staplot==3) || ($staplot==9)){
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#0000ff"});';
				$getStations = mysql_query("select c.cn_id, c.hs_code, c.hs_lat, c.hs_lon, c.hs_stime, c.hs_etime, d.cc_code from cn a, hs c, cc d where a.vd_id = '$vd_id' and a.cn_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.hs_code, c.hs_lat, c.hs_lon, c.hs_stime, c.hs_etime, d.cc_code from jj_volnet a, hs c, cc d where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				$output .= "<br>Hydrologic stations : " . mysql_num_rows($getStations);
				$nhs_sta=mysql_num_rows($getStations);
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->hs_lat,$getStation_obj->hs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
					 	marker$ctr.openInfoWindowHtml('Hydrologic station: $getStation_obj->hs_code<br>Lat: $getStation_obj->hs_lat<br>Lon: $getStation_obj->hs_lon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($hs_obj, $getStation_obj);
				}
				}
			
				// Thermal stations: ORANGE markers
				if (($staplot==4)) {
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ff7f00"});';
				$getStations = mysql_query("select c.cn_id, c.ts_code, c.ts_lat, c.ts_lon, c.ts_code, c.ts_stime, c.ts_etime, d.cc_code from cn a, ts c, cc d where a.vd_id = '$vd_id' and a.cn_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.ts_code, c.ts_lat, c.ts_lon, c.ts_stime, c.ts_etime, d.cc_code from jj_volnet a, ts c, cc d where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				$output .= "<br>Thermal Stations : " . mysql_num_rows($getStations);
				$nts_sta=mysql_num_rows($getStations);
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->ts_lat,$getStation_obj->ts_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
						marker$ctr.openInfoWindowHtml('Thermal station: $getStation_obj->ts_code<br>Lat: $getStation_obj->ts_lat<br>Lon: $getStation_obj->ts_lon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($ts_obj, $getStation_obj);
				}
				}
		
				// Gas stations: YELLOW markers
				if (($staplot==5)){
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ffff00"});';
				$getStations = mysql_query("select c.cn_id, c.gs_code, c.gs_lat, c.gs_lon, c.gs_stime, c.gs_etime, d.cc_code from cn a, gs c, cc d where a.vd_id = '$vd_id' and a.cn_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.gs_code, c.gs_lat, c.gs_lon, c.gs_stime, c.gs_etime, d.cc_code from jj_volnet a, gs c, cc d where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and c.cc_id=d.cc_id") or die(mysql_error());
				$output .= "<br>Gas Stations : " . mysql_num_rows($getStations);
				$ngs_sta=mysql_num_rows($getStations);
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->gs_lat,$getStation_obj->gs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
					  marker$ctr.openInfoWindowHtml('Gas station: $getStation_obj->gs_code<br>Lat: $getStation_obj->gs_lat<br>Lon: $getStation_obj->gs_lon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($gs_obj, $getStation_obj);
				}
				}
		
				// Fields stations: CYAN markers
				if (($staplot==6)) {
				echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ffff"});';
				$getStations = mysql_query("select c.cn_id, c.fs_code, c.fs_lat, c.fs_lon, c.fs_stime, c.fs_etime, d.cc_code from cn a, fs c, cc d where a.vd_id = '$vd_id' and a.cn_id = c.cn_id and c.cc_id=d.cc_id ") or die(mysql_error());
				if (! mysql_num_rows($getStations))
				$getStations = mysql_query("select c.cn_id, c.fs_code, c.fs_lat, c.fs_lon, c.fs_stime, c.fs_etime, d.cc_code 		from jj_volnet a, fs c, cc d 		where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and c.cc_id=d.cc_id ") or die(mysql_error());
				$output .= "<br>Fields Stations : " . mysql_num_rows($getStations);
				while ($getStation_obj = mysql_fetch_object($getStations)){
					echo "point = new GLatLng($getStation_obj->fs_lat,$getStation_obj->fs_lon);";
					echo "var marker$ctr = new GMarker(point, {icon: newIcon});
					GEvent.addListener(marker$ctr, 'click', function() {
						  marker$ctr.openInfoWindowHtml('Fields station: $getStation_obj->fs_code<br>Lat: $getStation_obj->fs_lat<br>Lon: $getStation_obj->fs_lon<br>owner/source: $getStation_obj->cc_code');
					}); 
					map.addOverlay(marker$ctr);";
					$ctr++;
					// Store in session
					array_push($fs_obj, $getStation_obj);
				}
				}
				
				// Earthquakes
				if ($staplot==7) {
				$getVd_inf = mysql_query("select vd_inf_slat, vd_inf_slon where vd_id = '$vd_id'");
				if ($getVd_inf) $vd_inf = mysql_fetch_object($getVd_inf);
				$getQuakes = mysql_query("select c.sn_id, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon from sn b, sd_evn c, vd_inf d where b.sn_id = c.sn_id and b.vd_id=d.vd_id and d.vd_id = '$vd_id' $dates $depth $quaketype order by ((abs(d.vd_inf_slat - c.sd_evn_elat) + abs(d.vd_inf_slon - c.sd_evn_elon))) asc limit $qty") or die(mysql_error());
				if (! mysql_num_rows($getQuakes))
				$getQuakes = mysql_query("select c.sn_id, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, 											c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon	from jj_volnet a, sn b, sd_evn c, vd_inf d where a.vd_id = '$vd_id' and a.jj_net_id = b.sn_id 											and b.sn_id = c.sn_id and d.vd_id = '$vd_id' and a.jj_net_flag = 'S' $dates $depth $quaketype order by ((abs(d.vd_inf_slat - c.sd_evn_elat) + abs(d.vd_inf_slon - c.sd_evn_elon))) asc limit $qty") or die(mysql_error());
				$output .= "<br>Seismic Events : " . mysql_num_rows($getQuakes);
				$count = 1;
				$clear = "";
				while ($getQuake_obj = mysql_fetch_object($getQuakes)){
					if ($getQuake_obj->sd_evn_edep <= 2.5) $color = "#00FF00"; // Green
					else if ($getQuake_obj->sd_evn_edep <= 5) $color = "#FFFF00"; // YELLOW
					else if ($getQuake_obj->sd_evn_edep <= 10) $color = "#FF0000"; // Red
					else if ($getQuake_obj->sd_evn_edep <= 50) $color = "#0000FF"; // BLUE
					else $color = "#000080"; // DARK BLUE
					$mag = round($getQuake_obj->sd_evn_pmag) * 4;
					if ($mag < 4) $mag = 4;
					$md5 = md5($getQuake_obj->sd_evn_elon . $getQuake_obj->sd_evn_elon);
					$md5 = substr($md5, 0, 25);
				
					echo "point = new GLatLng($getQuake_obj->sd_evn_elat,$getQuake_obj->sd_evn_elon);
					    newIcon = MapIconMaker.createFlatIcon({width: $mag, height: $mag, primaryColor: '$color'});
				    ";
					$clear .= "window.str$md5 = '';\n";
					echo "if (window.str$md5 === undefined) { str$md5 = '$count. Time: $getQuake_obj->sd_evn_time\\nDepth:$getQuake_obj->sd_evn_edep n, Mag:$getQuake_obj->sd_evn_pmag\\nType:$getQuake_obj->sd_evn_eqtype'; }
						else { str$md5 += '\\n$count. Time:$getQuake_obj->sd_evn_time\\nDepth: $getQuake_obj->sd_evn_edep , Mag:$getQuake_obj->sd_evn_pmag\\nType: $getQuake_obj->sd_evn_eqtype'; }
						st2$md5 = str$md5;
						marker$md5 = new GMarker(point, {icon: newIcon});
						GEvent.addListener(marker$md5, 'click', function() {
						  	marker$md5.openInfoWindowHtml('<textarea rows=\"3\" cols=\"30\" readonly>' + st2$md5 + '</textarea>');
						  	alert(strcpy);
						}); 
						map.addOverlay(marker$md5);
					";
					$count++;
				}
				}
?>
		}
<?php 	echo $clear; ?> 		
		map.setMapType(G_PHYSICAL_MAP);
		}else {
				map.setZoom(7);
				map.setMapType(G_SATELLITE_MAP);
			}
</script>
?>