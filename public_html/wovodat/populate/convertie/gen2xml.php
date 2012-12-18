<?php
//gen2xml.php
function gen2xml($ary,$mondata,$dtype,$institution, $volcan,$netname,$staname,$instname,$targsta,$gen=0){

//check the cavw number
		include 'php/include/db_connect_view.php';
		$res= mysql_query("select vd_id, vd_cavw from vd where vd_name='$volcan'");
			while ($v_arr = mysql_fetch_array($res)) {
				$volcan_id=$v_arr[0];
				$cav=$v_arr[1];
			} 
			$volcanocavw=$cav;

//echo "gen2xml.php:".$dtype." cav: ".$volcanocavw." ".$netname."<br>";
//-------------------------------------------------------- for "data file"
if($mondata=='data'){
	$monitor="Monitor";
	$dttype=strtolower($dtype);
	$fscript="gen2xml_4data.php";
	include $fscript;

	if ($dttype=="sd_evn" || $dttype=="sd_evs" || $dttype=="sd_int" || $dttype=="sd_trm" || $dttype=="sd_ivl" || $dttype=="sd_sam" || $dttype=="sd_rsm" || $dttype=="sd_ssm" || $dttype=="sd_wav") {
		$monitor="Seismic";
	}elseif ($dttype=="dd_tlt" || $dttype=="dd_tlv"  || $dttype=="dd_str" || $dttype=="dd_edm" || $dttype=="dd_ang" || $dttype=="dd_gps" || $dttype=="dd_gpv" || $dttype=="dd_lev" || $dttype=="dd_sar") {
		$monitor="Deformation";
	}elseif ($dttype=="gd" || $dttype=="gd_sol"  || $dttype=="gd_plu") {
		$monitor="Gas";
	}elseif ($dttype=="hd") {
		$monitor="Hydrologic";
	}elseif ($dttype=="fd_mag" || $dttype=="fd_mgv"  || $dttype=="fd_ele" || $dttype=="fd_gra") {
		$monitor="Fields";
	}elseif ($dttype=="td" || $dttype=="td_img"  || $dttype=="td_pix") {
		$monitor="Thermal";
	}
	$ary2["Data"][$monitor]=$array;

//-------------------------------------------------------- for "station file"
}else{
	$monitor="MonitoringSystems";
	$dttype=strtolower($dtype);
	$dtype=ucfirst($dttype);

//	$fscript="./gen2xml_".$dttype.".php";
	$fscript="gen2xml_4station.php";
	
//	echo "check step of gen_xml:"." ".$fscript."<br>";
	include $fscript;
	$ary2[$monitor]=$array;
}
	return $ary2;

}
?>
