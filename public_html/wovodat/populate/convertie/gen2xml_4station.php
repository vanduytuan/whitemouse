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
//echo "<br> table_pnt 1=".$table_pr;

//-------------------------------------------------------------------------------------
$table_ini=$dttype; // read wovodat wovoml element names, creater array "$oml[wovodat]=wovoml"
$oml=array();
$array1=array();

foreach($tag as $k => $v){
	if(is_array($v)){
		foreach($v as $k1 => $v1){
			if(is_array($v1)){
				if($k1==$table_ini){
					foreach($v1 as $k2 => $v2){
						$oml[$k2]=$v2;
}	}	}	}	}	}

//-----------------------------------------------------------
//here is the basic info, volcano name = $volcan;
$caw=$volcanocavw;
$owner=$institution;

$snname=$netname; // if network code is needed in the xml file
$sscode=$staname; // if station code is needed in the xml file

$table_code=$dttype."_code";// adjust according to the wovodat table_code:sd_evn_code, ds_gps_code etc
$table_id=$dttype."_id";//column not used to overpass
$table_name=$dttype."_name";//column not used to overpass

if($table_id=="si_id")$table_pr="ss_id";  //parent-table---
if($table_id=="di_gen_id")$table_pr="ds_id";
if($table_id=="di_tlt_id")$table_pr="ds_id";
if($table_id=="gi_id")$table_pr="gs_id";
if($table_id=="hi_id")$table_pr="hs_id";
if($table_id=="ti_id")$table_pr="ts_id";
if($table_id=="fi_id")$table_pr="fs_id";

if($table_id=="ss_id")$table_pr="sn_id";  //parent-table---
if($table_id=="ds_id" || $table_id=="gs_id" || $table_id=="hs_id" || $table_id=="ts_id" || $table_id=="fs_id")$table_pr="cn_id";

include 'php/include/db_connect_view.php';

//===========================================================
if($dttype=="cn" || $dttype=="sn"){
	$qdata=$_POST['sttype']; 
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($k1==$table_code && $v1!=""){
					$gdata=$qdata." code="."\"".$v1."\""." owner1="."\"".$owner."\"";
					$ext=1;
			}	}
			foreach($v as $k1 => $v1){
				if($k1==$table_name){
					if($v1=="'--'" || $v1=="-") $v1="";
					if($v1!=""){
						$array[$gdata]["Volcanoes"]["volcanoCode"]=$caw;				
						$array[$gdata]["name"]=$v1;
			}	}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code){
						if($j==$k1) $array1[$gdata][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
						if($v1!="") $array[$k][$k1]=$v1;
}	}	}	}	}

//===========================================================
if($dttype=="ds" || $dttype=="gs" || $dttype=="ss" || $dttype=="hs" || $dttype=="fs" || $dttype=="ts"){
	$qdata=$_POST['sttype']; 
	$qparent=$qdata."s";
	foreach($ary as $k => $v){
		if(is_array($v)){
			if($netname=="new" || $netname==""){ //get "network-code" from csv file at "sn_id"
				foreach($v as $k1 => $v1){
					if($k1==$table_pr){
						$snname=$v1;
			}	}	}
			foreach($v as $k1 => $v1){
				if($k1==$table_code && $v1!=""){
					$gparent=$qparent." network="."\"".$snname."\""." owner1="."\"".$owner."\"";
					$gdata=$qdata." code="."\"".$v1."\""." network="."\"".$snname."\""." owner1="."\"".$owner."\"";
					$ext=1;
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!=$table_pr){
						if($j==$k1) $array1[$gparent][$gdata][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

//===========================================================
if($dttype=="di_gen" || $dttype=="di_tlt" || $dttype=="gi" || $dttype=="si" || $dttype=="hi" || $dttype=="fi" || $dttype=="ti"){	
	$qdata=$_POST['sttype']; 
	$qparent=$qdata."s";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($staname=="new"){ 
					if($k1==$table_pr)$sscode=$v1;
			}	}
			foreach($v as $k1 => $v1){
				if($k1==$table_code && $v1!=""){
					$gparent=$qparent." station="."\"".$sscode."\""." owner1="."\"".$owner."\"";
					$gdata=$qdata." code="."\"".$v1."\""." station="."\"".$sscode."\""." owner1="."\"".$owner."\"";
					$ext=1;
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code && $k1!=$table_pr){
						if($j==$k1) $array1[$gparent][$gdata][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

//===========================================================
if($dttype=="si_cmp"){
	$qdata=$_POST['sttype']; 
	$qparent=$qdata."s";
	foreach($ary as $k => $v){
		if(is_array($v)){
			foreach($v as $k1 => $v1){
				if($k1==$table_code && $v1!=""){
					$gparent=$qparent." instrument="."\"".$instname."\""." owner1="."\"".$owner."\"";
					$gdata=$qdata." code="."\"".$v1."\""." instrument="."\"".$instname."\""." owner1="."\"".$owner."\"";
					$ext=1;
			}	}
			foreach($oml as $j => $u){
				foreach($v as $k1 => $v1){
					if($k1!=$table_id && $k1!=$table_code){
						if($j==$k1) $array1[$gparent][$gdata][$u]=$v1;
			}	}	}
			foreach($array1 as $k => $v){ 		//this block below to remove element (=line) with "null" value
				foreach($v as $k1 => $v1){
					foreach($v1 as $k2 => $v2){
						if($v2!="") $array[$k][$k1][$k2]=$v2;
}	}	}	}	}	}

?>
