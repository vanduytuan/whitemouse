<?php
//search for  tag-dictionary at wovodat2wovoml11.xml
ini_set('auto_detect_line_endings',true); //for a correct reading linefeed (LF) and carriage-return (CR) \n \r of csv files

$servpath=".";
require_once($servpath."/f2genfunc/func_xmlparse.php"); 	// class xml parser to read "wovodat-wovoml" dictionary
require_once($servpath."/f2genfunc/funcgen_printarray.php"); // function ==> "print_ary($array,n)"

$filetag="wovodat2wovoml11.xml";
$params=xml2ary_1(file_get_contents($filetag)); // from myscr.php
$tag=$params;
//print_ary($tag,1);

//-------------------------------------------------------------------------------------
$extension=$dttype; // read wovodat wovoml element names, creater array "$oml[wovodat]=wovoml"
$oml=array();
$array1=array();
//echo "gen2xml_4data.php: cav=".$volcanocavw." network=".$netname." \$extension=".$extension."<br>";

foreach($tag as $k => $v){
	if(is_array($v)){
		foreach($v as $k1 => $v1){
			if(is_array($v1)){
				if($k1==$extension){
					foreach($v1 as $k2 => $v2){
						$oml[$k2]=$v2;
}	}	}	}	}	}

//echo "check step at gen2xml_4data.php, filetag=".$filetag."<br>";
//-----------------------------------------------------------
//here is the basic info, volcano name = $volcan;
$caw=$volcanocavw;
$owner=$institution;

$snname=$netname; // if network code is needed in the xml file
$ssname=$staname;

$extensioncode=$dttype."_code";// adjust according to the wovodat table_code:sd_evn_code, ds_gps_code etc
$table_id=$dttype."_id";//column not used to overpass
$table_code=$dttype."_code";//column not used to overpass
//echo $extensioncode."<br>";

include 'php/include/db_connect_view.php';

//===========================================================
if($dttype=="sd_evn"){
	$qparent="NetworkEventDataset";
	$qdata="NetworkEvent";
	if($snname=="new" || $snname==""){
		foreach($ary as $k => $v){
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					if($k1=="sn_id" && $v1!="")$network_code=$v1;
				}
				foreach($v as $k1 => $v1){
					if($k1==$extensioncode && $v1!=""){
						$evncod=$qdata." code=\"".$v1."\""." network=\"".$network_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$gparent=$qparent." network=\"".$network_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
				}	}
				foreach($oml as $j => $u){
					foreach($v as $k1 => $v1){
						if($k1!=$table_id && $k1!="sn_id" && $k1!="sd_evn_code"){
							if($j==$k1) $array1[$gparent][$evncod][$u]=$v1;
				}	}	}
				foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
					foreach($v as $k1 => $v1){
						foreach($v1 as $k2 => $v2){
							if($v2!="") $array[$k][$k1][$k2]=$v2;
		}	}	}	}	}
	}else{
		$result = mysql_query("SELECT sn_code FROM sn WHERE sn_name='$netname'") or die(mysql_error());
		while ($v_r = mysql_fetch_array($result)) {
			$network_code=htmlentities($v_r[0], ENT_COMPAT, "cp1252");
		}
		foreach($ary as $k => $v){
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					if($k1==$extensioncode && $v1!=""){
						$gparent=$qparent." network=\"".$network_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$parent=$qdata." code=\"".$v1."\""." network=\"".$network_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
				}	}
				foreach($oml as $j => $u){
					foreach($v as $k1 => $v1){
						if($k1!=$table_id && $k1!="sn_id" && $k1!="sd_evn_code"){
							if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
				}	}	}
				foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
					foreach($v as $k1 => $v1){
						foreach($v1 as $k2 => $v2){
							if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}	}

//===========================================================
if($dttype=="sd_evs"){
	$qparent="SingleStationEventDataset";
	$qdata="SingleStationEvent";
	if($ssname=="new" || $ssname==""){
		foreach($ary as $k => $v){
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					if($k1=="ss_id"){
						if($v1!="")$station_code=$v1;
				}	}
				foreach($v as $k1 => $v1){
					if($k1==$extensioncode && $v1!=""){
						$gparent=$qparent." station=\"".$station_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$parent=$qdata." code=\"".$v1."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
				}	}
				foreach($oml as $j => $u){
					foreach($v as $k1 => $v1){
						if($k1!=$table_id && $k1!="ss_id" && $k1!="sd_evs_code"){
							if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
				}	}	}
				foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
					foreach($v as $k1 => $v1){
						foreach($v1 as $k2 => $v2){
							if($v2!="") $array[$k][$k1][$k2]=$v2;
		}	}	}	}	}
	}else{
		$result = mysql_query("SELECT ss_code FROM ss WHERE ss_name='$staname'") or die(mysql_error());
		while ($v_r = mysql_fetch_array($result)) {
			$station_code=htmlentities($v_r[0], ENT_COMPAT, "cp1252");
		}
		foreach($ary as $k => $v){
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					if($k1==$extensioncode && $v1!=""){
						$gparent=$qparent." station=\"".$station_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$evscod=$qdata." code=\"".$v1."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; //parent tag for the xml file
				}	}
				foreach($oml as $j => $u){
					foreach($v as $k1 => $v1){
						if($k1!=$table_id && $k1!="ss_id" && $k1!="sd_evs_code"){
							if($j==$k1) $array1[$gparent][$evscod][$u]=$v1;
				}	}	}
				foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
					foreach($v as $k1 => $v1){
						foreach($v1 as $k2 => $v2){
							if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}	}

//===========================================================
if($dttype=="sd_int"){
	$qparent="IntensityDataset";
	$qdata="Intensity";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($k1=="sd_evn_id"){
					if($v1!="")$evn_code=$v1;
				}elseif($k1=="sd_evs_id"){
					if($v1!="")$evs_code=$v1;
			}	}
			foreach($v as $k1 => $v1){
				if($k1==$extensioncode && $v1!=""){
					if($evn_code!=""){
						$gparent=$qparent." networkEvent=\"".$evn_code."\""." volcano=\"".$caw."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$sdintcod=$qdata." code=\"".$v1."\""." networkEvent=\"".$evn_code."\""." volcano=\"".$caw."\""." owner1=\"".$owner."\""; //parent tag for the xml file
					}
					if($evs_code!=""){
						$gparent=$qparent." singleStationEvent=\"".$evs_code."\""." volcano=\"".$caw."\""." owner1=\"".$owner."\""; //parent tag for the xml file
						$sdintcod=$qdata." code=\"".$v1."\""." singleStationEvent=\"".$evs_code." volcano=\"".$caw."\""."\""." owner1=\"".$owner."\""; //parent tag for the xml file
			}	}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!="sd_evn_id" && $k1!="sd_evs_id" && $k1!="vd_id" && $k1!="sd_int_code"){
						if($j==$k1) $array1[$gparent][$sdintcod][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

//===========================================================
if($dttype=="sd_ivl" || $dttype=="sd_trm"){
	if($dttype=="sd_ivl"){$qdata="Interval";$qparent="IntervalDataset";}
	if($dttype=="sd_trm"){$qdata="Tremor";$qparent="TremorDataset";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($k1=="sn_id" && $v1!="")$network_code=$v1;
				if($k1=="ss_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){ //-------- this create element tag for the table array
				if($k1==$extensioncode && $v1!=""){
					$sdivlcod=$qdata." code=\"".$v1."\""." network=\"".$network_code."\""." owner1=\"".$owner."\""." station=\"".$station_code."\""; //parent tag for the xml file
					$gparent=$qparent." network=\"".$network_code."\""." owner1=\"".$owner."\""." station=\"".$station_code."\""; //parent tag for the xml file
				}
			}	
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!='sn_id' && $k1!='ss_id' && $k1!=$table_code){
						if($k1==$j){
							$array1[$gparent][$sdivlcod][$u]=$v1;
			}	}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

//===========================================================
if($dttype=="sd_rsm" || $dttype=="sd_ssm"){
	$g3parent="RSAM-SSAMDATAset";
	if ($dttype=="sd_rsm") {$qdata="RSAMData"; $qparent="RSAM";}
	if ($dttype=="sd_ssm") {$qdata="SSAMData"; $qparent="SSAM";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($k1=="ss_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){
				if($k1=="sam_id" && $v1!=""){
					$gggparent=$g3parent." station=\"".$station_code."\""." owner1=\"".$owner."\""; //gggparent tag for the xml file
					$ggparent="RSAM-SSAM code=\"".$v1."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; //gparent tag for the xml file
			}	}
			foreach($v as $k1 => $v1){ //-------- this create element tag for the table array
				if($k1==$extensioncode && $v1!=""){
					$sdrsmcod=$qdata." code=\"".$v1."\""; //parent tag for the xml file
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1=="sd_sam_int" || $k1=="sd_sam_int_unc" || $k1=="sd_sam_stime" || $k1=="sd_sam_stime_unc" || $k1=="sd_sam_etime" || $k1=="sd_sam_etime_unc"){
						$array1[$gggparent][$ggparent][$u]=$v1;
					}else{
						if($k1!=$table_id && $k1!='sam_id'){
							if($j==$k1) $array1[$gggparent][$ggparent][$qparent][$sdrsmcod][$u]=$v1;
			}	}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					if(!is_array($v1)){
						if($v1!="") $array[$k][$k1]=$v1;
					}else{
						foreach($v1 as $k2 => $v2){
							foreach($v2 as $k3 => $v3){
								foreach($v3 as $k4 => $v4){
									if($v4!="") $array[$k][$k1][$k2][$k3][$k4]=$v4;
	}	}	}	}	}	}	}	}
}	

//===========================================================
if($dttype=="dd_tlt" || $dttype=="dd_tlv" || $dttype=="dd_str"){
	if ($dttype=="dd_tlt") {$qdata="ElectronicTilt";$qparent="ElectronicTiltDataset";}
	if ($dttype=="dd_tlv") {$qdata="TiltVector";$qparent="TiltVectorDataset";}
	if ($dttype=="dd_str") {$qdata="Strain";$qparent="StrainDataset";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="ds_id" && $v1!="")$station_code=$v1; 
				if($k1=="di_tlt_id" && $v1!="")$instrument_code=$v1; 
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
						$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
						$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!='ds_id' && $k1!='di_tlt_id' && $k1!=$table_code){
						if($j==$k1)$array1[$gparent][$parent][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

//===========================================================
if($dttype=="dd_edm"){
	$qdata="EDM";	$qparent="EDMDataset";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="di_gen_id" && $v1!="")$instrument_code=$v1; 
				if($k1=="ds_id1" && $v1!="")$station_code=$v1; 
				if($k1=="ds_id2" && $v1!="")$target_code=$v1; 
			}	
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." station=\"".$target_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code= \"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." station=\"".$target_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!='di_gen_id' && $k1!='ds_id1' && $k1!='ds_id2' && $k1!=$table_code){
						if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	} }	
}

//===========================================================
if($dttype=="dd_ang"){
	$qdata="Angle";$qparent="AngleDataset";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="di_gen_id" && $v1!="")$instrument_code=$v1;
				if($k1=="ds_id" && $v1!="")$station_code=$v1;
				if($k1=="ds_id1" && $v1!="")$target1_code=$v1;
				if($k1=="ds_id2" && $v1!="")$target2_code=$v1; 
			}	
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." station=\"".$target1_code."\""." station=\"".$target2_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code= \"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." station=\"".$target1_code."\""." station=\"".$target2_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='di_gen_id' && $k1!='ds_id' && $k1!='ds_id1' && $k1!='ds_id2'){
						if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}
}

//===========================================================
if($dttype=="dd_gps" || $dttype=="dd_gpv"){
	if($dttype=="dd_gps"){$qdata="GPS";$qparent="GPSDataset";}
	if($dttype=="dd_gpv"){$qdata="GPSVector";$qparent="GPSVectorDataset";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="di_gen_id" && $v1!="")$instrument_code=$v1;
				if($k1=="ds_id" && $v1!="")$station_code=$v1;
				if($k1=="ds_id_ref1" && $v1!="")$ref1_code=$v1;
				if($k1=="ds_id_ref2" && $v1!="")$ref2_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." code= \"".$v1."\""." instrument=\"".$instrument_code."\" station=\"".$station_code."\" station=\"".$ref1_code."\" station=\"".$ref2_code."\" owner1=\"".$owner."\""; 
					$parent=$gdata." code= \"".$v1."\""." instrument=\"".$instrument_code."\" station=\"".$station_code."\" station=\"".$ref1_code."\" station=\"".$ref2_code."\" owner1=\"".$owner."\""; 
			} }
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='di_gen_id' && $k1!='ds_id' && $k1!='ds_id_ref1' && $k1!='ds_id_ref2' ){
						if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="dd_lev"){
	$qdata="Leveling";$qparent="LevelingDataset";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="di_gen_id" && $v1!="")$instrument_code=$v1;
				if($k1=="ds_id_ref" && $v1!="")$station_code=$v1;
				if($k1=="ds_id1" && $v1!="")$ref1_code=$v1;
				if($k1=="ds_id2" && $v1!="")$ref2_code=$v1;
			}	
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\" refStation=\"".$station_code."\" firstBMStation=\"".$ref1_code."\" secondBMStation=\"".$ref2_code."\" owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\" instrument=\"".$instrument_code."\" refStation=\"".$station_code."\" firstBMStation=\"".$ref1_code."\" secondBMStation=\"".$ref2_code."\" owner1=\"".$owner."\""; 
				}
			}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='di_gen_id' && $k1!='ds_id_ref' && $k1!='ds_id1' && $k1!='ds_id2' ){
						if($j==$k1)	$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="dd_sar"){
	$qdata="InSARImage";$qparent="InSARImageDataset";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="di_gen_id" && $v1!="")$instrument_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." volcano=\"".$volcan."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code= \"".$v1."\""." instrument=\"".$instrument_code."\""." volcano=\"".$volcan."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='di_gen_id' && $k1!='vd_id'){
						if($j==$k1)$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="gd" || $dttype=="gd_sol" || $dttype=="gd_plu"){
	if($dttype=="gd"){$qdata="GasSample";$qparent="GasSampleDataset";}
	if($dttype=="gd_sol"){$qdata="SoilEfflux";$qparent="SoilEffluxDataset";}
	if($dttype=="gd_plu"){$qdata="Plume";$qparent="PlumeDataset";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="gi_id" && $v1!="")$instrument_code=$v1;
				if($k1=="gs_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='gs_id' && $k1!='gi_id' ){
						if($j==$k1)$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="hd"){
	$qdata="HydrologicSample";$qparent="HydrologicSampleDataset";$qchild="HydrologicSpecies";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="hi_id" && $v1!="")$instrument_code=$v1;
				if($k1=="hs_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($v as $k1 => $v1){
				if($k1=="hd_species"){
					$child=$qchild." type=\"".$v1."\"";
				}
			} 
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($j==$k1 && $k1!="hd_species"){
						if($k1=="hd_content" ||$k1=="hd_content_err" ||$k1=="hd_units"){
							$array1[$gparent][$parent][$child][$u]=$v1;
						}else{
							if($k1!=$table_id && $k1!=$table_code && $k1!='hs_id' && $k1!='hi_id' )$array1[$gparent][$parent][$u]=$v1;
			}	}	}	}	
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if(!is_array($v2)){
							if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	}
}

//===========================================================
if($dttype=="fd_mag" || $dttype=="fd_gra"){
	if($dttype=="fd_mag"){$qdata="Magnetic";$qparent="MagneticDataset";}
	if($dttype=="fd_gra"){$qdata="Gravity";$qparent="GravityDataset";}
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="fi_id" && $v1!="")$instrument_code=$v1;
				if($k1=="fs_id" && $v1!="")$station_code=$v1;
				if($k1=="fs_id_ref" && $v1!="")$ref_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." refStation=\"".$ref_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." refStation=\"".$ref_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='fs_id' && $k1!='fi_id'  && $k1!='fs_id_ref' ){
						if($j==$k1)	$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 	//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}
//===========================================================
if($dttype=="fd_mgv"){
	$qparent="MagneticVectorDataset";$qdata="MagneticVector";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="fi_id" && $v1!="")$instrument_code=$v1;
				if($k1=="fs_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($v1!="" && $k1!=$table_id && $k1!=$table_code && $k1!='fs_id' && $k1!='fi_id' ){
						if($j==$k1)$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 	//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="fd_ele"){
	$qparent="ElectricDataset";$qdata="Electric";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="fi_id" && $v1!="")$instrument_code=$v1;
				if($k1=="fs_id1" && $v1!="")$station_code=$v1;
				if($k1=="fs_id2" && $v1!="")$station2_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." refStation1=\"".$station_code."\""." refStation2=\"".$station2_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." refStation1=\"".$station_code."\""." refStation2=\"".$station2_code."\""." owner1=\"".$owner."\""; 
			}	}


			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($v1!="" && $k1!=$table_id && $k1!=$table_code && $k1!='fs_id1'  && $k1!='fs_id2' && $k1!='fi_id' ){
							if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 	//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="td"){
	$qparent="Ground-basedDataset";	$qdata="Ground-based";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="ti_id" && $v1!="")$instrument_code=$v1;
				if($k1=="ts_id" && $v1!="")$station_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." owner1=\"".$owner."\""; 
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($v1!="" && $k1!=$table_id && $k1!=$table_code && $k1!='ts_id' && $k1!='ti_id' ){
						if($j==$k1)	$array1[$gparent][$parent][$u]=$v1;
			}	}	}	
			foreach($array1 as $k => $v){ 	//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

//===========================================================
if($dttype=="td_img_pix"){
	$qparent="ThermalImageDataset";	$qdata="ThermalImage";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){ 
				if($k1=="ti_id" && $v1!="")$instrument_code=$v1;
				if($k1=="ts_id" && $v1!="")$station_code=$v1;
				if($k1=="cs_id" && $v1!="")$sat_code=$v1;
				if($k1=="td_img_iplat" && $v1!="")$plat_code=$v1;
			}
			foreach($v as $k1 => $v1){ 
				if($k1==$extensioncode && $v1!=""){
					$gparent=$qparent." airplane=\"".$plat_code."\""." volcano=\"".$caw."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." satellite=\"".$sat_code."\""." owner1=\"".$owner."\""; 
					$parent=$qdata." code=\"".$v1."\""." volcano=\"".$caw."\""." instrument=\"".$instrument_code."\""." station=\"".$station_code."\""." satellite=\"".$sat_code."\""." owner1=\"".$owner."\"";  
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!='td_img_pix' ){
						foreach($oml as $j => $u){
							if($j==$k1) $array1[$gparent][$parent][$u]=$v1;
			}	}	}	}
			foreach($array1 as $k => $v){ 	//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
	}	}	}	}	}	
}

?>
