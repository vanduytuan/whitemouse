<?php
/******************************************************************************************************
* Displays list of stations related to datatype
******************************************************************************************************/
// According to data type selected, get stations of interest (stored in session)
// Get data type selected
$datatype=$_REQUEST['dataType'];
$vdid=$_REQUEST['vd_id'];
if ($datatype=="") exit();

// Open list of stations
echo <<<STRING
					<table><tr>
					<td style="width:50px;">Station:</td>
					<td><select id="station_id2" style="font-size:9px;width:120px; margin-top:1px">
STRING;


// According to data type
$s=substr($datatype, 0, 1).'s';
$stipe=substr($datatype, 3, 3);

session_start();
$_SESSION[$s.'_'.$stipe.'_obj']=array();
$sta_obj=&$_SESSION[$s.'_'.$stipe.'_obj'];

// List stations related to data type; $stacode: s, d, g, h ..+'s' --> ss, ds, gs, hs... etc (see:getvd_info_pur.php)
// 'dataType' used to derive the station-list from station session.
// Display only stations that have data on it

include 'php/include/db_connect_view.php';

if($datatype=="dd_edm"){
	$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.cn_id FROM cn a,".$s." b, ".$datatype." c, vd_inf d    WHERE a.cn_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=d.vd_id  and c.".$s."_id1 = b.".$s."_id and (sqrt(power(d.vd_inf_slat - b.ds_nlat, 2) + power(d.vd_inf_slon - b.ds_nlon, 2))*100)<20			ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, ".$datatype." c, vd_inf d    WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=d.vd_id  and c.".$s."_id1 = b.".$s."_id and (sqrt(power(d.vd_inf_slat - b.ds_nlat, 2) + power(d.vd_inf_slon - b.ds_nlon, 2))*100)<20		ORDER BY b.".$s."_name") or die(mysql_error());

}elseif($datatype=="dd_gpv"){			
	$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.cn_id 	FROM cn a,".$s." b, ".$datatype." c, vd_inf d  	WHERE a.cn_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=d.vd_id and c.".$s."_id = b.".$s."_id and (sqrt(power(d.vd_inf_slat - b.ds_nlat, 2) + power(d.vd_inf_slon - b.ds_nlon, 2))*100)<20			ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id 	FROM jj_volnet a, ".$s." b, ".$datatype." c, vd_inf d  		WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=d.vd_id and c.".$s."_id = b.".$s."_id  and (sqrt(power(d.vd_inf_slat - b.ds_nlat, 2) + power(d.vd_inf_slon - b.ds_nlon, 2))*100)<20		ORDER BY b.".$s."_name") or die(mysql_error());
	
}elseif($datatype=="sd_sam"){	
	$getStats = mysql_query("select  b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.sn_id		FROM sn a, ".$s." b, sd_sam c,  sd_rsm d, vd_inf e       WHERE a.sn_id=b.sn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id = b.".$s."_id and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30 	ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, sd_sam c  WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id1 = b.".$s."_id	and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30  	ORDER BY b.".$s."_name") or die(mysql_error());
		
}elseif($datatype=="sd_rsm"){	
	$getStats = mysql_query("select  b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.sn_id		FROM sn a, ".$s." b, sd_sam c,  sd_rsm d, vd_inf e     WHERE a.sn_id=b.sn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30 	ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, sd_sam c, vd_inf e  	WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id1 = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30		 ORDER BY b.".$s."_name") or die(mysql_error());

}elseif($datatype=="sd_ivl"){	
	$getStats = mysql_query("select  b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.sn_id		FROM sn a, ".$s." b, sd_ivl c,  vd_inf e     WHERE a.sn_id=b.sn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30 	ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, sd_ivl c, vd_inf e  	WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id1 = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.ss_lat, 2) + power(e.vd_inf_slon - b.ss_lon, 2))*100)<30		 ORDER BY b.".$s."_name") or die(mysql_error());

}elseif($datatype=="gd_plu"){	
	$getStats = mysql_query("select  b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.cn_id		FROM cn a, ".$s." b, gd_plu c,  vd_inf e     WHERE a.cn_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.gs_lat, 2) + power(e.vd_inf_slon - b.gs_lon, 2))*100)<30 	ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, gd_plu c, vd_inf e  	WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and a.vd_id=e.vd_id and c.".$s."_id1 = b.".$s."_id  and (sqrt(power(e.vd_inf_slat - b.gs_lat, 2) + power(e.vd_inf_slon - b.gs_lon, 2))*100)<30		 ORDER BY b.".$s."_name") or die(mysql_error());

}else{	
	$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.cn_id FROM cn a,".$s." b, ".$datatype." c, vd_inf d	  WHERE a.cn_id=b.cn_id and a.vd_id='$vdid' and c.".$s."_id = b.".$s."_id ORDER BY b.".$s."_name") or die(mysql_error());
	if (! mysql_num_rows($getStats))
		$getStats = mysql_query("select b.".$s."_id, b.".$s."_name, b.".$s."_stime, b.".$s."_etime, a.jj_net_id FROM jj_volnet a, ".$s." b, ".$datatype." c  WHERE a.jj_net_id=b.cn_id and a.vd_id='$vdid' and c.".$s."_id = b.".$s."_id ORDER BY b.".$s."_name") or die(mysql_error());
}

//----------fill object, $_SESSION[$s.'_'.$stipe.'_obj'], with data from $getStats.
$ck=0;
while ($getStation_obj = mysql_fetch_object($getStats)){
	$ck++;
	// Store in session
	$statname=$getStation_obj->{$s.'_name'};
	if($ck==1) {
		array_push($sta_obj, $getStation_obj);
		$stats=$getStation_obj->{$s.'_name'};
	}else{	
		if($statname!=$stats){
			array_push($sta_obj, $getStation_obj);
			$stats=$getStation_obj->{$s.'_name'};
		}
	}
}

foreach ($_SESSION[$s.'_'.$stipe.'_obj'] as $station) {
	echo "<option value='".$station->{$s.'_id'}."'>".$station->{$s.'_name'};
	
	if (!empty($station->{$s.'_stime'}) || !empty($station->{$s.'_stime'})) {
		echo " [";
		if (empty($station->{$s.'_stime'})) {
			echo "1970";
		}else {
			echo $station->{$s.'_stime'};
		}
		echo " - ";
		if (empty($station->{$s.'_etime'})) {
			echo "Present";
		}else {
			echo $station->{$s.'_etime'};
		}
		echo "]";
	}
	echo "</option>";
}

// Close list of stations
echo <<<STRING
					</select></td>
STRING;
?>
	<!-- Graph button -->
	<td>
	<div id="gbutton2" align=center>
		<button id="graphBtnCreate2" style="font-size:9px;">Plot</button>
	</div>
	</td>
	</tr></table>


	<script type="text/javascript">
	$("#graphBtnCreate2").button();
	$("#graphBtnCreate2").click(
		function() {
			$("#viewcontent2").show();
			$("#viewcontent2").html("");
			
			// Here will be the script for flot
			$.ajax({
				method: "get", url: "/php/plot_data.php", 
				data: "station_id=" + $("#station_id2 :selected").val() + 
						"&datatype=" + $("#dataType2 :selected").val() + 
						"&startdate=" + $("#startdate2").val() + 
						"&enddate=" + $("#enddate2").val() +
						'&nvolc=' + 2, 
				beforeSend: function(){$("#filLoading").show("fast");},
				complete: function(){$("#filLoading").hide("fast");},
				success: function(html){
					$("#viewcontent2").html(html);
				}
			});
		}
	);
	</script>