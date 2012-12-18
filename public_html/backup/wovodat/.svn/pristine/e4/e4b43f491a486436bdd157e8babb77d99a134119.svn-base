<?php

/******************************************************************************************************
*
* Reloads Google Maps API - centered on selected volcano, with monitoring stations, seismic events and neighbouring volcanoes
*
******************************************************************************************************/

include 'php/include/db_connect_view.php';
$vd_id = $_REQUEST['vd_id'];
$vd_name = $_REQUEST['vd_name'];
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
if ($dr_start && $dr_end)
{
	$depth = " and c.sd_evn_edep BETWEEN '$dr_start' AND '$dr_end' ";
}
if ($eqtype)
{
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
      var marker = createMarker(point,'<div><?php echo "Current Volcano: " . addslashes($vd_name) . "<br>Lat: $vd_info_obj->vd_inf_slat<br>Lon: $vd_info_obj->vd_inf_slon"; ?><\/div>');
	  var newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ff00ff"});
	  if (showStations) 
	  {
		map.setZoom(10); 

<?php
		$ctr = 0;
		
		session_start();
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
		$_SESSION['ss_obj']=array();
		$ss_obj=&$_SESSION['ss_obj'];
		
		// Deformation stations: PURPLE markers
		$getStations = mysql_query("select c.cn_id, c.ds_id, c.ds_nlat, c.ds_nlon, c.ds_name, c.ds_stime, c.ds_etime from cn a, ds c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id order by c.ds_name") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, c.cn_id, c.ds_id, c.ds_nlat, c.ds_nlon, c.ds_name, c.ds_stime, c.ds_etime from jj_volnet a, ds c where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id order by c.ds_name") or die(mysql_error());
		$output .= "<br>Deformation Stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->ds_nlat,$getStation_obj->ds_nlon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('GPS station: $getStation_obj->ds_name<br>Lat: $getStation_obj->ds_nlat<br>Lon: $getStation_obj->ds_nlon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($ds_obj, $getStation_obj);
		}
		
		// Seismic stations: GREEN markers
		echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ff00"});';
		$getStations = mysql_query("select c.sn_id, c.ss_id, c.ss_lat, c.ss_lon, c.ss_name, c.ss_stime, c.ss_etime from sn a, ss c where a.vd_id = '$vd_id' and a.sn_id = c.sn_id") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, b.sn_id, c.sn_id, c.ss_id, c.ss_lat, c.ss_lon, c.ss_name, c.ss_stime, c.ss_etime from jj_volnet a, sn b, ss c where a.vd_id = '$vd_id' and a.jj_net_flag = 'S' and a.jj_net_id = b.sn_id and b.sn_id = c.sn_id") or die(mysql_error());
		$output .= "<br>Seismic Stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->ss_lat,$getStation_obj->ss_lon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('Seismic station: $getStation_obj->ss_name<br>Lat: $getStation_obj->ss_lat<br>Lon: $getStation_obj->ss_lon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($ss_obj, $getStation_obj);
		}
		
		// Gas stations: YELLOW markers
		echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ffff00"});';
		$getStations = mysql_query("select c.cn_id, c.gs_id, c.gs_lat, c.gs_lon, c.gs_name, c.gs_stime, c.gs_etime from cn a, gs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, c.cn_id, c.gs_id, c.gs_lat, c.gs_lon, c.gs_name, c.gs_stime, c.gs_etime from jj_volnet a, gs c where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id") or die(mysql_error());
		$output .= "<br>Gas Stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->gs_lat,$getStation_obj->gs_lon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('Gas station: $getStation_obj->gs_name<br>Lat: $getStation_obj->gs_lat<br>Lon: $getStation_obj->gs_lon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($gs_obj, $getStation_obj);
		}
		
		// Hydrologic stations: BLUE markers
		echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#0000ff"});';
		$getStations = mysql_query("select c.cn_id, c.hs_id, c.hs_lat, c.hs_lon, c.hs_name, c.hs_stime, c.hs_etime from cn a, hs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, c.cn_id, c.hs_id, c.hs_lat, c.hs_lon, c.hs_name, c.hs_stime, c.hs_etime from jj_volnet a, hs c where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id") or die(mysql_error());
		$output .= "<br>Hydrologic stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->hs_lat,$getStation_obj->hs_lon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('Hydrologic station: $getStation_obj->hs_name<br>Lat: $getStation_obj->hs_lat<br>Lon: $getStation_obj->hs_lon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($hs_obj, $getStation_obj);
		}
		
		// Thermal stations: ORANGE markers
		echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#ff7f00"});';
		$getStations = mysql_query("select c.cn_id, c.ts_id, c.ts_lat, c.ts_lon, c.ts_name, c.ts_stime, c.ts_etime from cn a, ts c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, c.cn_id, c.ts_id, c.ts_lat, c.ts_lon, c.ts_name, c.ts_stime, c.ts_etime from jj_volnet a, ts c where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id") or die(mysql_error());
		$output .= "<br>Thermal Stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->ts_lat,$getStation_obj->ts_lon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('Thermal station: $getStation_obj->ts_name<br>Lat: $getStation_obj->ts_lat<br>Lon: $getStation_obj->ts_lon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($ts_obj, $getStation_obj);
		}
		
		// Fields stations: CYAN markers
		echo 'newIcon = MapIconMaker.createMarkerIcon({width:24, height:24,primaryColor: "#00ffff"});';
		$getStations = mysql_query("select c.cn_id, c.fs_id, c.fs_lat, c.fs_lon, c.fs_name, c.fs_stime, c.fs_etime from cn a, fs c where a.vd_id = '$vd_id' and a.cn_id = c.cn_id") or die(mysql_error());
		if (! mysql_num_rows($getStations))
		$getStations = mysql_query("select a.jj_net_id, c.cn_id, c.fs_id, c.fs_lat, c.fs_lon, c.fs_name, c.fs_stime, c.fs_etime from jj_volnet a, fs c where a.vd_id = '$vd_id' and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id") or die(mysql_error());
		$output .= "<br>Fields Stations : " . mysql_num_rows($getStations);
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			echo "point = new GLatLng($getStation_obj->fs_lat,$getStation_obj->fs_lon);";
			echo "var marker$ctr = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$ctr, 'click', function() {
			  marker$ctr.openInfoWindowHtml('Fields station: $getStation_obj->fs_name<br>Lat: $getStation_obj->fs_lat<br>Lon: $getStation_obj->fs_lon');
			}); map.addOverlay(marker$ctr);";
			$ctr++;
			// Store in session
			array_push($fs_obj, $getStation_obj);
		}
		
		// Earthquakes
		$getVd_inf = mysql_query("select vd_inf_slat, vd_inf_slon where vd_id = '$vd_id'");
		if ($getVd_inf) $vd_inf = mysql_fetch_object($getVd_inf);
		$getStations = mysql_query("select a.jj_volnet_id, a.jj_net_id, b.sn_id, 
										c.sn_id, c.sd_evn_elat, c.sd_evn_elon, 
										c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, 
										c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon
										from jj_volnet a, sn b, sd_evn c, vd_inf d
										where a.vd_id = '$vd_id'
										and a.jj_net_id = b.sn_id 
										and b.sn_id = c.sn_id and d.vd_id = '$vd_id'
										and a.jj_net_flag = 'S'
										$dates $depth $quaketype 
										order by ((abs(d.vd_inf_slat - c.sd_evn_elat) + abs(d.vd_inf_slon - c.sd_evn_elon))) asc
										limit $qty") or die(mysql_error());
		$output .= "<br>Seismic Events : " . mysql_num_rows($getStations);
		$count = 1;
		$clear = "";
		while ($getStation_obj = mysql_fetch_object($getStations))
		{
			if ($getStation_obj->sd_evn_edep <= 2.5) $color = "#FF0000"; // RED
			else if ($getStation_obj->sd_evn_edep <= 5) $color = "#FFFF00"; // YELLOW
			else if ($getStation_obj->sd_evn_edep <= 10) $color = "#00FF00"; // GREEN
			else if ($getStation_obj->sd_evn_edep <= 50) $color = "#0000FF"; // BLUE
			else $color = "#000080"; // DARK BLUE
			$mag = round($getStation_obj->sd_evn_pmag) * 5;
			if ($mag < 5) $mag = 5;
			$md5 = md5($getStation_obj->sd_evn_elon . $getStation_obj->sd_evn_elon);
			$md5 = substr($md5, 0, 25);
			echo "point = new GLatLng($getStation_obj->sd_evn_elat,$getStation_obj->sd_evn_elon);
			    newIcon = MapIconMaker.createFlatIcon({width: $mag, height: $mag, primaryColor: '$color'});
				";
			$clear .= "window.str$md5 = '';\n";
			echo "if (window.str$md5 === undefined) { str$md5 = '$count. Event Time: $getStation_obj->sd_evn_time\\nDepth: $getStation_obj->sd_evn_edep\\nMagnitude: $getStation_obj->sd_evn_pmag\\nEarthquake Type: $getStation_obj->sd_evn_eqtype'; }
			else { str$md5 += '\\n$count. Event Time: $getStation_obj->sd_evn_time\\nDepth: $getStation_obj->sd_evn_edep\\nMagnitude: $getStation_obj->sd_evn_pmag\\nEarthquake Type: $getStation_obj->sd_evn_eqtype'; }
			st2$md5 = str$md5;
			marker$md5 = new GMarker(point, {icon: newIcon});
			GEvent.addListener(marker$md5, 'click', function() {
			  marker$md5.openInfoWindowHtml('<textarea rows=\"10\" cols=\"35\" readonly>' + st2$md5 + '</textarea>');
			  alert(strcpy);
			}); map.addOverlay(marker$md5);
			";
			$count++;
		}
		echo $clear;
		?>

		map.setMapType(G_PHYSICAL_MAP);
	  }
	  else {
		map.setMapType(G_HYBRID_MAP);
	  }
		// Add neighbor volcanoes: RED markers
	  newIcon = MapIconMaker.createMarkerIcon({width:30, height:30,primaryColor: "#ff0000"});
		
	  <?php 
      $neighbors = mysql_query("select a.vd_inf_slat, a.vd_inf_slon, b.vd_name, a.vd_id, ((pow(a.vd_inf_slat - $vd_info_obj->vd_inf_slat, 2) + pow(a.vd_inf_slon - $vd_info_obj->vd_inf_slon, 2)) / 2) as `distance` from vd_inf a, vd b where a.vd_id != '$vd_id' and a.vd_id = b.vd_id order by `distance` asc limit 10") or die(mysql_error());
	  while ($getNeighbor_obj = mysql_fetch_object($neighbors))
	  {
		  echo "point = new GLatLng($getNeighbor_obj->vd_inf_slat, $getNeighbor_obj->vd_inf_slon);
		  var marker$ctr = new GMarker(point, {icon: newIcon});
          GEvent.addListener(marker$ctr, \"click\", function() {
			marker$ctr.openInfoWindowHtml('<div>Volcano: ".addslashes($getNeighbor_obj->vd_name) . "<br>Lat: $getNeighbor_obj->vd_inf_slat<br>$getNeighbor_obj->vd_inf_slon<\/div>');
          });
          GEvent.addListener(marker$ctr, \"dblclick\", function() {
			$('#vd_name').val('$getNeighbor_obj->vd_id');
			setupNeighborMap('$getNeighbor_obj->vd_id', '".addslashes($getNeighbor_obj->vd_name)."');
          });
		  map.addOverlay(marker$ctr);";
		  $ctr++;
	  }
	  ?>
	  
      map.addOverlay(marker);
		map.enableScrollWheelZoom();
	  document.getElementById(divId).title='<?php echo addslashes($vd_name); ?>';
	  }
	  if ($("#bigmap").is(":visible")) {
	  createMap('bigmap', true);
	  window.mapcreated = true;
	  }
	  else window.mapcreated = false;
	  createMap('dialog', false);
	}
    
    // display a warning if the browser was not compatible
    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }
    // This Javascript is based on code provided by the
    // Community Church Javascript Team
    // http://www.bisphamchurch.org.uk/   
    // http://econym.org.uk/gmap/
    //]]>
</script>


<?php } 
mysql_close($link);
?>