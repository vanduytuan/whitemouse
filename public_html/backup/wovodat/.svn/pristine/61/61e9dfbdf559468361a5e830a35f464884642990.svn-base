<?php
require_once "php/include/login_check.php";  // Check login
require_once "php/include/get_root.php";    // Get root url
include 'php/include/db_connect_view.php';

	
$volca=trim($_GET['volcan']);  			    	   // Get valcano name
$stationdisplay=trim($_GET['stationdisplay']);    //get SeismicStation or GasStation etc
$networkdisplay=trim($_GET['networkdisplay']);    // get network type what the user chooses



if($stationdisplay == "SeismicNetwork" || $stationdisplay == "SeismicStation" || $stationdisplay == "SeismicInstrument" || $stationdisplay == "SeismicComponent"){
	$stationvalue = "Seismic";

}
else if($stationdisplay == "DeformationNetwork" || $stationdisplay == "DeformationStation" || $stationdisplay == "DeformationInstrument_General" || $stationdisplay == "DeformationInstrument_Tilt/Strain"){
	$stationvalue = "Deformation";

}
else if($stationdisplay == "GasNetwork" || $stationdisplay == "GasStation" || $stationdisplay == "GasInstrument"){
	$stationvalue = "GAS";
}
else if($stationdisplay == "HydrologicNetwork" || $stationdisplay == "HydrologicStation" || $stationdisplay == "HydrologicInstrument"){
	$stationvalue = "Hydrologic";
}
else if($stationdisplay == "ThermalNetwork" || $stationdisplay == "ThermalStation" || $stationdisplay == "ThermalInstrument" ){
	$stationvalue = "Thermal";
}
else if($stationdisplay == "FieldsNetwork" || $stationdisplay == "FieldsStation" || $stationdisplay == "FieldsInstrument"){

	$stationvalue = "Fields";
}


if($stationdisplay == "DeformationInstrument_General") {	
	$nettype="ds";
	$inst_type="di_gen";
}elseif($stationdisplay == "DeformationInstrument_Tilt/Strain"){
	$nettype="ds";
	$inst_type="di_tlt";
}elseif($stationdisplay=="GasInstrument" ) {	
	$nettype="gs";
}elseif($stationdisplay=="HydrologicInstrument" ) {	
	$nettype="hs";
}elseif($stationdisplay=="ThermalInstrument") {	
	$nettype="ts";
}elseif($stationdisplay=="FieldsInstrument" ) {	
	$nettype="fs";
}

if($stationdisplay == "SeismicInstrument" || $stationdisplay == "SeismicComponent"){  

	$result = mysql_query("select s.ss_name from ss as s,sn as n,vd where s.sn_id = n.sn_id and n.vd_id = vd.vd_id and vd.vd_name='$volca' and n.sn_name='$networkdisplay'") or die(mysql_error());

}else{	
	$sql="select s.".$nettype."_name from $nettype as s, cn, vd where s.cn_id = cn.cn_id and cn.vd_id = vd.vd_id and vd.vd_name = '$volca' and cn.cn_type='$stationvalue' and cn.cn_name='$networkdisplay'";
	$result = mysql_query($sql);
	
}	 

	$row=mysql_fetch_array($result);
	
	if(!$row){   // false means no data result
		echo "false";
	}else{
		echo "true";
	}
?>