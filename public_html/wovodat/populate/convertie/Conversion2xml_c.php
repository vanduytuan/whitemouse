<?php
require_once "php/include/get_root.php";

//Conversion2xml_c.php
//echo "checked_02"."<br>";
ini_set('auto_detect_line_endings',true); //for a correct reading linefeed (LF) and carriage-return (CR) \n \r of csv files
ini_set("memory_limit","32M"); // used for a very huge data file; memory alloc

$servpath=".";
require_once($servpath."/f2genfunc/funcgen_printarray.php"); 	// function ==> "print_ary($array,n)"
require_once($servpath."/f2genfunc/funcgen_array2xml.php"); 	// func => "array_to_xml ($array,'header') generic
require_once($servpath."/f2genfunc/funcgen_CsvImportertag.php"); 	// class CsvImportertag
require_once($servpath."/f2genfunc/funcgen_datetime.php"); 	// function for date time formatting
require_once($servpath."/gen2xml.php"); 	// func gen2xml

$TIME = date("Y-m-d H:i:s");
print_r ("Time : $TIME <BR>\n");

//echo "checked_03"."<br>";//check

//-------------------------------------------------------
if(empty($_POST["Submit"])){
		echo "<br><b>Defining File 0:</b></br>";
		echo "Name of file: Empty"."<br>";
		
	}

else{ 
//--read data from submitted form--------------------------
		$mondata=$_GET['monidata'];
		$institution=$_POST['observ'];
		$obsername=$_POST['observ'];
		$volcn=ucfirst(strtolower($_POST['vol2']));
		$statype=$_POST['sttype'];
		$netname=$_POST['netw'];
		$staname=$_POST['stat2'];
		$instname=$_POST['instrumform'];
//-----------------------------------------
		
		echo " datafile type: ".$mondata."<br>";
		echo " file-type: ".$statype."<br>";

		if($statype=="SeismicNetwork") $datatype="sn";
		if($statype=="SeismicStation") $datatype="ss";
		if($statype=="SeismicInstrument") $datatype="si";
		if($statype=="SeismicComponent") $datatype="si_cmp";
		if($statype=="CommonNetwork") $datatype="cn";
		if($statype=="DeformationNetwork") $datatype="cn";
		if($statype=="DeformationStation") $datatype="ds";
		if($statype=="DeformationInstrument") $datatype="di_gen";
		if($statype=="DeformationInstrumentG") $datatype="di_gen";
		if($statype=="DeformationInstrumentT") $datatype="di_tlt";
		if($statype=="HydrologicNetwork") $datatype="cn";
		if($statype=="HydrologicStation") $datatype="hs";
		if($statype=="HydrologicInstrument") $datatype="hi";
		if($statype=="GasNetwork") $datatype="cn";
		if($statype=="GasStation") $datatype="gs";
		if($statype=="GasInstrument") $datatype="gi";
		if($statype=="ThermalNetwork") $datatype="cn";
		if($statype=="ThermalStation") $datatype="ts";
		if($statype=="ThermalInstrument") $datatype="ti";
		if($statype=="FieldsNetwork") $datatype="cn";
		if($statype=="FieldsStation") $datatype="fs";
		if($statype=="FieldsIstrument") $datatype="fi";
		if($statype=="EventDataFromNetwork") $datatype="sd_evn";
		if($statype=="EventDataFromSingleStation") $datatype="sd_evs";
		if($statype=="IntensityData") $datatype="sd_int";
		if($statype=="SeismicTremor") $datatype="sd_trm";
		if($statype=="IntervalSwarmData") $datatype="sd_ivl";
		if($statype=="RSAMSSAMData") $datatype="sd_sam";
		if($statype=="RSAMData") $datatype="sd_rsm";
		if($statype=="SSAMData") $datatype="sd_ssm";
		if($statype=="RepresentativeWaveform") $datatype="sd_wav";
		if($statype=="EarthquakeTranslation") $datatype="st_eqt";
		if($statype=="ElectronicTiltData") $datatype="dd_tlt";
		if($statype=="TiltVectorData") $datatype="dd_tlv";
		if($statype=="StrainMeterData") $datatype="dd_str";
		if($statype=="EDMData") $datatype="di_edm";
		if($statype=="AngleData") $datatype="dd_ang";
		if($statype=="GPSData") $datatype="dd_gps";
		if($statype=="GPSVectors") $datatype="dd_gpv";
		if($statype=="LevelingData") $datatype="dd_lev";
		if($statype=="InSARImage") $datatype="dd_sar";
		if($statype=="InSARSateliteJunction") $datatype="";
		if($statype=="InSARData") $datatype="dd_srd";
		if($statype=="DirectlySampledGas") $datatype="gd";
		if($statype=="SoilEffluxData") $datatype="gd_sol";
		if($statype=="PlumeData") $datatype="gd_plu";
		if($statype=="HydrologicData") $datatype="hd";
		if($statype=="MagneticFieldsData") $datatype="fd_mag";
		if($statype=="MagnetorVectorData") $datatype="fd_mgv";
		if($statype=="ElectricFieldsData") $datatype="fd_ele";
		if($statype=="GravityData") $datatype="fd_gra";
		if($statype=="GroundBasedThermalData") $datatype="td";
		if($statype=="ThermalImage") $datatype="td_img";
		if($statype=="ThermalImageData") $datatype="td_pix";
		if($statype=="MagmaMovement") $datatype="ip_mag";
		if($statype=="VolatileSaturation") $datatype="ip_sat";
		if($statype=="BuildUpMagmaPressure") $datatype="ip_pres";
		if($statype=="HydrothermalSystemInteraction") $datatype="ip_hyp";
		if($statype=="RegionalTectonicInteraction") $datatype="ip_tec";
		if($statype=="BibliographicData") $datatype="cb";
		if($statype=="Images") $datatype="cm";
		if($statype=="ImageJunction") $datatype="";
		if($statype=="Satellite") $datatype="cs";
		if($statype=="VolcanoNetworkJunction") $datatype="";
		if($statype=="Maps") $datatype="md";
		if($statype=="Observation") $datatype="co";
		$targsta="";

//check the cavw number
		include 'php/include/db_connect_view.php';
		$result = mysql_query("select vd_cavw, vd_name from vd where vd_name='$volcn'");
		while ($v_arr = mysql_fetch_array($result)) {
			$volcanocavw=$v_arr[0];
		} 
//--------------------------------------------------------		
		$filename=$_FILES['fname']['name'];
		$filesize=$_FILES['fname']['size'];
		$file2read="../../../../incoming/to_be_translated/"."$filename";//prepare the name of inputfile
		$locsave="../../../../incoming/translated/";										//prepare the directory of outputfile
    	$outputfile=substr($filename,0,-4).".xml";

		echo "input: ".$filename."<br>";
		echo "xml: ".$outputfile."<br>";
		echo "filesize: ".$filesize."<br>";

//copy "temp" to inputfile name	
		if($filesize<=1000000){
			if (!move_uploaded_file($_FILES['fname']['tmp_name'],$file2read)){
	    		echo "file submission fails<br>";
			}    		  
   		}else{
		  	echo "File is too big:".$filesize."<br>";
    		echo "File submission fails<br>";
    	}
	}
//
//echo "checked_04"."<br>";//check

//---------------READ observatory file------------
$impor = new CsvImportertag($file2read,true,",");
while($data = $impor->get()){$ary=$data;}
$nrow=count($ary);
echo "n-row: ".$nrow."<br>";

//----------------------------------------------format-converter
//echo "check step: conversion2xml_c- ".$datatype."<br>";
//echo "<br>";

$ary2=gen2xml($ary,$mondata,$datatype,$institution,$volcn,$netname,$staname,$instname,$targsta,0);

//echo "check step: netname ->".$netname."<br>";
//echo "<br>";

print_ary($ary2,1);

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$filexml = $locsave.$outputfile;
$handle=fopen($filexml,"w");
//-------------------------
	$head='';
	$volcode=$volcanocavw;
	$obscode=$obsername;
	$pubdate='2012-07-01 00:00:00';

$xml=array_to_xml($ary2,"wovoml version=\"1.1.0\" xmlns=\"http://www.wovodat.org\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.wovodat.org/WOVOdatV1.xsd\"",$head,1,0);

fwrite($handle,$xml);
fclose($handle);
//----------------------

//print_r ("<BR>\n<BR>\n");
print_r ("<BR>\n");
print_r ("$nrow data-set are executed.\n");
print_r ("<BR>\n<BR>\n");

?>




