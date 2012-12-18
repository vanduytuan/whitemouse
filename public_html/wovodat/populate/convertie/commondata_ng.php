<?php
$servpath=".";
require_once($servpath."/f2genfunc/func_xmlparse.php"); // class xml parser 
require_once($servpath."/f2genfunc/funcgen_printarray.php"); 

if($conv=="EventDataFromNetwork"){ 
	$datatype="sd_evn";
	$xmldataset="NetworkEventDataset";
	$xmlinitag="NetworkEvent";
}
else if($conv=="EventDataFromSingleStation"){ 
	$datatype="sd_evs";
	$xmldataset="SingleStationEventDataset";
	$xmlinitag="SingleStationEvent";	
}
else if($conv=="SeismicTremor_Network" || $conv == 'SeismicTremor_Station'){
	$datatype="sd_trm";
	$xmldataset="TremorDataset";
	$xmlinitag="Tremor";
}
else if($conv == 'SeismicIntervalSwarm_Network' || $conv == 'SeismicIntervalSwarm_Station'){
	$datatype="sd_ivl";
	$xmldataset="IntervalDataset";
	$xmlinitag="Interval";	
}
else if($conv=="RSAMData"){   
	$datatype="sd_rsm";
	$xmldataset="RSAM";
	$xmlinitag="RSAMData";	
}
else if($conv=="SSAMData"){  
	$datatype="sd_ssm";
	$xmldataset="SSAM";	
	$xmlinitag="SSAMData";	  	
}
else if($conv=="RepresentativeWaveform"){    
	$datatype="sd_wav";	
}
else if($conv == 'ElectronicTiltData'){
	$datatype="dd_tlt";
	$xmldataset="ElectronicTiltDataset";
	$xmlinitag="ElectronicTilt";	
}	
else if($conv == 'TiltVectorData'){
	$datatype="dd_tlv";
	$xmldataset="TiltVectorDataset";
	$xmlinitag="TiltVector";	
}
else if($conv == 'StrainMeterData'){
	$datatype="dd_str";
	$xmldataset="StrainDataset";
	$xmlinitag="Strain";	
}
else if($conv == 'EDMData'){
	$datatype="dd_edm";
	$xmldataset="EDMDataset";
	$xmlinitag="EDM";	
}
else if($conv == 'AngleData'){
	$datatype="dd_ang";
	$xmldataset="AngleDataset";
	$xmlinitag="Angle";	
}
else if($conv == 'GPSData'){
	$datatype="dd_gps";
	$xmldataset="GPSDataset";
	$xmlinitag="GPS";	
}
else if($conv == 'GPSVectors'){
	$datatype="dd_gpv";
	$xmldataset="GPSVectorDataset";
	$xmlinitag="GPSVector";	
}
else if($conv == 'DirectlySampledGas'){
	$datatype="gd";
	$xmldataset="GasSampleDataset";
	$xmlinitag="GasSample";	
}
else if($conv == 'SoilEffluxData'){
	$datatype="gd_sol";
	$xmldataset="SoilEffluxDataset";
	$xmlinitag="SoilEfflux";	
}
else if($conv == 'PlumeData' || $conv == "plume_satellite_type"){
	$datatype="gd_plu";
	$xmldataset="PlumeDataset";
	$xmlinitag="Plume";	
}
else if($conv == "HydrologicData"){
	$datatype="hd";
}
else if($conv == "MagneticFieldsData"){
	$datatype="fd_mag";
	$xmldataset="MagneticDataset";
	$xmlinitag="Magnetic";	
}
else if($conv == "MagnetorVectorData"){
	$datatype="fd_mgv";
	$xmldataset="MagneticVectorDataset";
	$xmlinitag="MagneticVector";	
}
else if($conv == "ElectricFieldsData"){
	$datatype="fd_ele";
	$xmldataset="ElectricDataset";
	$xmlinitag="Electric";	
}
else if($conv == "GravityData"){
	$datatype="fd_gra";
	$xmldataset="GravityDataset";
	$xmlinitag="Gravity";	 
}
else if($conv == "GroundBasedThermalData"){
	$datatype="td";
	$xmldataset="Ground-basedDataset";
	$xmlinitag="Ground-based";	 
}
else if($conv == "ThermalImage and ThermalImageData" || $conv == "ThermalImage_satellite_type"){
	$datatype="td_img";
}
else if($conv == "Insar_satellite_type"){  // InSARImage and InSARData
	$datatype="dd_sar";
}
else if($conv == "LevelingData"){
	$datatype="dd_lev";
}
else if($conv == "IntensityData"){
	$datatype="sd_int";
}


$filetag="wovodat2wovoml11.xml";
$datafile=file_get_contents($filetag);
$params=xml2ary_1($datafile); 
$tag=$params;

$table_ini=$datatype; 
$datacode=$datatype."_code";

$datapubdate=$datatype."_pubdate";     // Added on 23-feb-2012

$newfileheader=array();
	


foreach($tag as $k => $v){
	if(is_array($v)){
		foreach($v as $k1 => $v1){
			if(is_array($v1)){
				if($k1==$table_ini){
					foreach($v1 as $k2 => $v2){
						$newfileheader[$k2]=$v2;
}	}	}	}	}	}


for($i=1;$i<$count;$i++){ 
	for($j=0;$j<sizeof($fileheader[0]);$j++){
		$a[]=$fileheader[0][$j];
		$b[]=$fileheader[$i][$j];
	}
	$fileheaderarray[]=array_combine($a,$b);
}




if(isset($fileheader2)){   	// when two files upload, this if condition starts working

	for($i=1;$i<$count2;$i++){ 
		for($j=0;$j<sizeof($fileheader2[0]);$j++){
			$c[]=$fileheader2[0][$j];
			$d[]=$fileheader2[$i][$j];
		}
		$fileheaderarray2[]=array_combine($c,$d);
	}

	for($i=0; $i< count($fileheaderarray2);$i++){	
		
		if($conv == "ThermalImage and ThermalImageData" || $conv == "ThermalImage_satellite_type"){	
			if($fileheaderarray2[$i]['td_pix_lat'] != '' && $fileheaderarray2[$i]['td_pix_lat'] != 'NULL'){
				$lat[$i]=$fileheaderarray2[$i]['td_pix_lat'];     
			}	
			if($fileheaderarray2[$i]['td_pix_lon'] != '' && $fileheaderarray2[$i]['td_pix_lon'] != 'NULL'){
				$lon[$i]=$fileheaderarray2[$i]['td_pix_lon'];     
			}
			if($fileheaderarray2[$i]['td_pix_elev'] != '' && $fileheaderarray2[$i]['td_pix_elev'] != 'NULL'){
				$elev[$i]=$fileheaderarray2[$i]['td_pix_elev'];     
			}
			if($fileheaderarray2[$i]['td_pix_rad'] != '' && $fileheaderarray2[$i]['td_pix_rad'] != 'NULL'){
				$radiance[$i]=$fileheaderarray2[$i]['td_pix_rad'];     
			}	
			if($fileheaderarray2[$i]['td_pix_flux'] != '' && $fileheaderarray2[$i]['td_pix_flux'] != 'NULL'){
				$heatFlux[$i]=$fileheaderarray2[$i]['td_pix_flux'];     
			}
			if($fileheaderarray2[$i]['td_pix_temp'] != '' && $fileheaderarray2[$i]['td_pix_temp'] != 'NULL'){
				$temperature[$i]=$fileheaderarray2[$i]['td_pix_temp'];     
			}
		}
		else if($conv == "Insar_satellite_type"){
	
			if($fileheaderarray2[$i]['dd_srd_numb'] != '' && $fileheaderarray2[$i]['dd_srd_numb'] != 'NULL'){
				$number[$i]=$fileheaderarray2[$i]['dd_srd_numb'];     
			}else{
				$number[$i]=0;
			}
		
			if($fileheaderarray2[$i]['dd_srd_dchange'] != '' && $fileheaderarray2[$i]['dd_srd_dchange'] != 'NULL'){
				$rangeOfChange[$i]=$fileheaderarray2[$i]['dd_srd_dchange'];     
			}
		}
	}
}

	for($i=0; $i< count($fileheaderarray);$i++){

	if($conv == "RSAMData" || $conv=="SSAMData"){ //RSAM/SSAM don't have code/owner 1,etc. Thats why need to split. 
		
		if($conv == "RSAMData"){
			if($fileheaderarray[$i]['sd_rsm_stime'] != '' && $fileheaderarray[$i]['sd_rsm_stime'] != 'NULL'){
				$rsam_ssam_stime[$i]=$fileheaderarray[$i]['sd_rsm_stime'];     
			}
		}else if($conv=="SSAMData"){
			if($fileheaderarray[$i]['sd_ssm_stime'] != '' && $fileheaderarray[$i]['sd_ssm_stime'] != 'NULL'){
				$rsam_ssam_stime[$i]=$fileheaderarray[$i]['sd_ssm_stime'];     
			}
		}
	}
	else{
		$code[]= $fileheaderarray[$i][$datacode];         
		
		if($fileheaderarray[$i]['cc_id2'] != '' && $fileheaderarray[$i]['cc_id2'] != 'NULL'){
			$owner2[$i]=$fileheaderarray[$i]['cc_id2'];     
			$owner2[$i]=" owner2=\"$owner2[$i]\"";
		}else{
			$owner2[$i]="";
		}
		
		
		if($fileheaderarray[$i]['cc_id3'] != '' && $fileheaderarray[$i]['cc_id3'] != 'NULL'){
			$owner3[$i]=$fileheaderarray[$i]['cc_id3'];     
			$owner3[$i]=" owner3=\"$owner3[$i]\"";
		}else{
			$owner3[$i]="";
		}

		if($fileheaderarray[$i][$datapubdate] != '' && $fileheaderarray[$i][$datapubdate] != 'NULL'){
			$pubdate[$i]=$fileheaderarray[$i][$datapubdate]; 
			$pubdate[$i]=" pubDate=\"$pubdate[$i]\"";			
		}else{
			$pubdate[$i]="";
		}


	
		if($conv == "DirectlySampledGas"){
			if($fileheaderarray[$i]['gd_species'] != '' && $fileheaderarray[$i]['gd_species'] != 'NULL'){
				$type[$i]=$fileheaderarray[$i]['gd_species'];     
			}	
			if($fileheaderarray[$i]['gd_waterfree_flag'] != '' && $fileheaderarray[$i]['gd_waterfree_flag'] != 'NULL'){
				$waterFree[$i]=$fileheaderarray[$i]['gd_waterfree_flag'];     
			}	
			if($fileheaderarray[$i]['gd_concentration'] != '' && $fileheaderarray[$i]['gd_concentration'] != 'NULL'){
				$concentration[$i]=$fileheaderarray[$i]['gd_concentration'];  

			}
			if($fileheaderarray[$i]['gd_concentration_err'] != '' && $fileheaderarray[$i]['gd_concentration_err'] != 'NULL'){
				$concentrationUnc[$i]=$fileheaderarray[$i]['gd_concentration_err'];     
			}
			if($fileheaderarray[$i]['gd_units'] != '' && $fileheaderarray[$i]['gd_units'] != 'NULL'){
				$units[$i]=$fileheaderarray[$i]['gd_units'];     
			}		
			if($fileheaderarray[$i]['gd_recalc'] != '' && $fileheaderarray[$i]['gd_recalc'] != 'NULL'){
				$recalculated[$i]=$fileheaderarray[$i]['gd_recalc'];     
			}	
		}  
	
		else if($conv == "PlumeData" || $conv=="plume_satellite_type"){
			if($fileheaderarray[$i]['gd_plu_species'] != '' && $fileheaderarray[$i]['gd_plu_species'] != 'NULL'){
				$type[$i]=$fileheaderarray[$i]['gd_plu_species'];     
			}	
			if($fileheaderarray[$i]['gd_plu_emit'] != '' && $fileheaderarray[$i]['gd_plu_emit'] != 'NULL'){
				$emissionRate[$i]=$fileheaderarray[$i]['gd_plu_emit'];     
			}
			if($fileheaderarray[$i]['gd_plu_emit_err'] != '' && $fileheaderarray[$i]['gd_plu_emit_err'] != 'NULL'){
				$emissionRateUnc[$i]=$fileheaderarray[$i]['gd_plu_emit_err'];     
			}
			if($fileheaderarray[$i]['gd_plu_units'] != '' && $fileheaderarray[$i]['gd_plu_units'] != 'NULL'){
				$units[$i]=$fileheaderarray[$i]['gd_plu_units'];     
			}		
			if($fileheaderarray[$i]['gd_plu_recalc'] != '' && $fileheaderarray[$i]['gd_plu_recalc'] != 'NULL'){
				$recalculated[$i]=$fileheaderarray[$i]['gd_plu_recalc'];     
			}	
		}  
		else if($conv == "HydrologicData"){
			if($fileheaderarray[$i]['hd_comp_species'] != '' && $fileheaderarray[$i]['hd_comp_species'] != 'NULL'){
				$type[$i]=$fileheaderarray[$i]['hd_comp_species'];     
			}	
			if($fileheaderarray[$i]['hd_comp_content'] != '' && $fileheaderarray[$i]['hd_comp_content'] != 'NULL'){
				$content[$i]=$fileheaderarray[$i]['hd_comp_content'];     
			}
			if($fileheaderarray[$i]['hd_comp_content_err'] != '' && $fileheaderarray[$i]['hd_comp_content_err'] != 'NULL'){
				$contentUnc[$i]=$fileheaderarray[$i]['hd_comp_content_err'];     
			}
			if($fileheaderarray[$i]['hd_comp_units'] != '' && $fileheaderarray[$i]['hd_comp_units'] != 'NULL'){
				$units[$i]=$fileheaderarray[$i]['hd_comp_units'];     
			}		
		}
		else if($conv == "LevelingData"){
			if($fileheaderarray[$i]['di_gen_id'] != '' && $fileheaderarray[$i]['di_gen_id'] != 'NULL'){
				$instrument[$i]=$fileheaderarray[$i]['di_gen_id'];  
				$instrument[$i]="instrument=\"$instrument[$i]\"";				
			}else{
				$instrument[$i]="";
			}	
			if($fileheaderarray[$i]['ds_id_ref'] != '' && $fileheaderarray[$i]['ds_id_ref'] != 'NULL'){
				$refStation[$i]=$fileheaderarray[$i]['ds_id_ref'];     
				$refStation[$i]="refStation=\"$refStation[$i]\"";
			}else{
				$refStation[$i]="";
			}
			
			if($fileheaderarray[$i]['ds_id1'] != '' && $fileheaderarray[$i]['ds_id1'] != 'NULL'){
				$firstBMStation[$i]=$fileheaderarray[$i]['ds_id1'];     
				$firstBMStation[$i]="firstBMStation=\"$firstBMStation[$i]\"";
			}else{
				$firstBMStation[$i]="";
			}
			
			if($fileheaderarray[$i]['ds_id2'] != '' && $fileheaderarray[$i]['ds_id2'] != 'NULL'){
				$secondBMStation[$i]=$fileheaderarray[$i]['ds_id2'];     
				$secondBMStation[$i]="secondBMStation=\"$secondBMStation[$i]\"";
			}else{
				$secondBMStation[$i]="";
			}		
		}	
		else if($conv == "RepresentativeWaveform"){  //Waveform event code come from "FORM POST" value 

			if($wav_eventtype == "EventDataFromNetwork"){
				$networkEvent="networkEvent=\"$wav_eventcode\"";
			}else{
				$networkEvent="";  
			}		
			
			if($wav_eventtype == "EventDataFromSingleStation"){
				$singleStationEvent="singleStationEvent=\"$wav_eventcode\"";
			}else{
				$singleStationEvent="";
			}
			
			if($wav_eventtype == "SeismicTremor"){
				$tremor="tremor=\"$wav_eventcode\"";
			}else{
				$tremor="";
			}		
		}
	}	
}

//------------------------------------------------------- 


if($conv=="EventDataFromNetwork" || $conv=="SeismicTremor_Network" || $conv == 'SeismicIntervalSwarm_Network'){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Seismic>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Seismic>
</Data>
</wovoml>
FOOT;

$rowprefix = "";	

/*
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" network=\"$network\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" network=\"$network\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" network=\"$network\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" network=\"$network\" owner1=\"$observ\" $pubdate[$j]";	
		}	
		
	}
*/
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" network=\"$network\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}

	
$rowfoot = "$xmlinitag";
}
else if($conv=="EventDataFromSingleStation" || $conv=="SeismicTremor_Station" || $conv == "SeismicIntervalSwarm_Station" ){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Seismic>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Seismic>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

/*
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";	
		}	
		
	}
*/
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}


	
$rowfoot = "$xmlinitag";
}
else if($conv=="ElectronicTiltData" || $conv == "TiltVectorData" || $conv == "StrainMeterData" || $conv == "GPSVectors"  || $conv == 'EDMData' || $conv == 'AngleData' || $conv == 'GPSData'){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Deformation>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Deformation>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

if($conv=="ElectronicTiltData" || $conv == "TiltVectorData" || $conv == "StrainMeterData" || $conv == "GPSVectors"){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else if($conv == 'EDMData'){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation=\"$station2\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else if($conv == 'AngleData'){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation1=\"$station2\" targetStation2=\"$station3\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else if($conv == 'GPSData'){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation1=\"$station2\" refStation2=\"$station3\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}

/*
if($conv=="ElectronicTiltData" || $conv == "TiltVectorData" || $conv == "StrainMeterData" || $conv == "GPSVectors"){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";
		}	
		
	}
}
else if($conv == 'EDMData'){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" targetStation=\"$station2\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation=\"$station2\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation=\"$station2\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation=\"$station2\" owner1=\"$observ\" $pubdate[$j]";
		}	
		
	}
}
else if($conv == 'AngleData'){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" targetStation1=\"$station2\" targetStation2=\"$station3\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation1=\"$station2\" targetStation2=\"$station3\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation1=\"$station2\" targetStation2=\"$station3\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" targetStation1=\"$station2\" targetStation2=\"$station3\" owner1=\"$observ\" $pubdate[$j]";
		}	
	}
}
else if($conv == 'GPSData'){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" refStation1=\"$station2\" refStation2=\"$station3\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation1=\"$station2\" refStation2=\"$station3\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation1=\"$station2\" refStation2=\"$station3\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation1=\"$station2\" refStation2=\"$station3\" owner1=\"$observ\" $pubdate[$j]";	
		}	
	}
} */
	
$rowfoot = "$xmlinitag";
}
if($conv=="DirectlySampledGas" || $conv == 'SoilEffluxData' || $conv == 'PlumeData' || $conv=="plume_satellite_type"){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Gas>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Gas>
</Data>
</wovoml>
FOOT;


if( $conv =="DirectlySampledGas"){ 
	for($j=0;$j<sizeof($code);$j++){

$rowprefix[$j+1] = <<<HTMLBLOCK
	<GasSpecies type="$type[$j]" waterFree="$waterFree[$j]">
HTMLBLOCK;
		if(isset($concentration[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<concentration>".$concentration[$j]."</concentration>"; 
		}	
		if(isset($concentrationUnc[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<concentrationUnc>".$concentrationUnc[$j]."</concentrationUnc>"; 
		}	
		if(isset($units[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<units>".$units[$j]."</units>"; 
		}	
		if(isset($recalculated[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<recalculated>".$recalculated[$j]."</recalculated>"; 
		}
		$rowprefix[$j+1] .="\n\t\t</GasSpecies>";	
	}			
}else if($conv == 'PlumeData' || $conv=="plume_satellite_type"){
	for($j=0;$j<sizeof($code);$j++){

$rowprefix[$j+1] = <<<HTMLBLOCK
	<PlumeSpecies type="$type[$j]">
HTMLBLOCK;
		if(isset($emissionRate[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<emissionRate>".$emissionRate[$j]."</emissionRate>"; 
		}	
		if(isset($emissionRateUnc[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<emissionRateUnc>".$emissionRateUnc[$j]."</emissionRateUnc>"; 
		}	
		if(isset($units[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<units>".$units[$j]."</units>"; 
		}	
		if(isset($recalculated[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<recalculated>".$recalculated[$j]."</recalculated>"; 
		}
		$rowprefix[$j+1] .="\n\t\t</PlumeSpecies>";	
	}		
}else{
	$rowprefix="";
}	

// Plume header has three types of header thats why divided into 3 parts
if($conv=="DirectlySampledGas" || $conv == 'SoilEffluxData' || $conv == 'PlumeData'){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else{   //This is for $conv=="plume_satellite_type" checking
	if(isset($airplane)){
		for($j=0;$j<sizeof($code);$j++){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" airplane=\"$air_satellite\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
		}
	}
	else if(isset($satellite)){
		for($j=0;$j<sizeof($code);$j++){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" satellite=\"$air_satellite\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
		}	
	}
}

/*
if($conv=="DirectlySampledGas" || $conv == 'SoilEffluxData' || $conv == 'PlumeData'){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\"  owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";
		}	
	}
}
else{   //This is for $conv=="plume_satellite_type" checking
	if(isset($airplane)){
		for($j=0;$j<sizeof($code);$j++){
			if((isset($owner2)) && (isset($owner3))){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
			}else if(isset($owner2)){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $owner2 $pubdate[$j]";
			}else if(isset($owner3)){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" airplane=\"$air_satellite\"  owner1=\"$observ\" $owner3 $pubdate[$j]";	
			}else{
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $pubdate[$j]";	
			}	
		}

	}
	else if(isset($satellite)){
		for($j=0;$j<sizeof($code);$j++){
			if((isset($owner2)) && (isset($owner3))){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" satellite=\"$air_satellite\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
			}else if(isset($owner2)){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" satellite=\"$air_satellite\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
			}else if(isset($owner3)){
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" satellite=\"$air_satellite\"  owner1=\"$observ\" $owner3 $pubdate[$j]";	
			}else{
				$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" satellite=\"$air_satellite\" owner1=\"$observ\"$pubdate[$j]";	
			}	
		}	
	}
}
*/

	
$rowfoot = "$xmlinitag";
}
if( $conv == "HydrologicData"){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Hydrologic>
<HydrologicSampleDataset>
HTMLBLOCK;


$footer = <<<FOOT
</HydrologicSampleDataset>
</Hydrologic>
</Data>
</wovoml>
FOOT;


for($j=0;$j<sizeof($code);$j++){

$rowprefix[$j+1] = <<<HTMLBLOCK
	<HydrologicSpecies type="$type[$j]">
HTMLBLOCK;
		if(isset($content[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<content>".$content[$j]."</content>"; 
		}	
		if(isset($contentUnc[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<contentUnc>".$contentUnc[$j]."</contentUnc>"; 
		}	
		if(isset($units[$j])){
		$rowprefix[$j+1] .="\n\t\t\t<units>".$units[$j]."</units>"; 
		}	
		$rowprefix[$j+1] .="\n\t\t</HydrologicSpecies>";	
}
/*
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "HydrologicSample code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "HydrologicSample code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "HydrologicSample code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\"  owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "HydrologicSample code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";	
		}	
		
	}
*/	
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "HydrologicSample code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}

$rowfoot = "HydrologicSample";
}
else if($conv == "MagneticFieldsData" || $conv == "MagnetorVectorData" || $conv == "ElectricFieldsData" || $conv == "GravityData"){

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Fields>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Fields>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

/*
if($conv == 'MagnetorVectorData'){
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";
		}	
		
	}
}
else if($conv == "MagneticFieldsData"){
	
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" refStation=\"$station2\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation=\"$station2\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation=\"$station2\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" refStation=\"$station2\" owner1=\"$observ\" $pubdate[$j]";	
		}	
		
	}
}
else if($conv == "ElectricFieldsData" || $conv == "GravityData"){
	
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" refStation1=\"$station\" refStation2=\"$station2\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" refStation1=\"$station\" refStation2=\"$station2\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" refStation1=\"$station\" refStation2=\"$station2\" owner1=\"$observ\" $owner3 $pubdate[$j]";
		}else{
			$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" refStation1=\"$station\" refStation2=\"$station2\" owner1=\"$observ\" $pubdate[$j]";	
		}	
		
	}
}*/

if($conv == 'MagnetorVectorData'){
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else if($conv == "MagneticFieldsData"){
	
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" refStation=\"$station2\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}
else if($conv == "ElectricFieldsData" || $conv == "GravityData"){
	
	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" refStation1=\"$station\" refStation2=\"$station2\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}
}

$rowfoot = "$xmlinitag";

}
else if($conv == "GroundBasedThermalData"){

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Thermal>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</Thermal>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

/*
for($j=0;$j<sizeof($code);$j++){
	if((isset($owner2)) && (isset($owner3))){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
	}else if(isset($owner2)){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";
	}else if(isset($owner3)){
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";
	}else{
		$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\"  instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";
	}	
	
}*/

for($j=0;$j<sizeof($code);$j++){
	$rowhead[$j+1]= "$xmlinitag code=\"$code[$j]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
}

$rowfoot = "$xmlinitag";
}
else if($conv == "RSAMData" ||  $conv=="SSAMData"){

$lastrow_rsam_stime=sizeof($rsam_ssam_stime);

$firstdate=substr($rsam_ssam_stime[0],0,10);
$firsttime=substr($rsam_ssam_stime[0],10);

$seconddate=substr($rsam_ssam_stime[1],0,10);
$secondtime=substr($rsam_ssam_stime[1],10);

$cntInterval=datetime2unix($seconddate,$secondtime,$sepd="-")-datetime2unix($firstdate,$firsttime,$sepd="-");
$startTime=$rsam_ssam_stime[0];
//$endTime=$rsam_ssam_stime[$lastrow_rsam_stime-1];

$endTime=$rsam_ssam_stime[$lastrow_rsam_stime-1];
$dateinsec=strtotime($endTime);		
$newdate=$dateinsec+$cntInterval;		
$endTime=date('Y-m-d H:i:s',$newdate);

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Seismic>
<RSAM-SSAMDataset>
<RSAM-SSAM code="$rsam_ssamcode" station="$station" owner1="$observ"> 
<cntInterval>$cntInterval</cntInterval>
<startTime>$startTime</startTime>	
<endTime>$endTime</endTime>
<$xmldataset>
HTMLBLOCK;


$footer = <<<FOOT
</$xmldataset>
</RSAM-SSAM>
</RSAM-SSAMDataset>
</Seismic>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

for($j=0;$j<sizeof($rsam_ssam_stime);$j++){   
// don't need to make it array but don't want to change csv2xml function so that can get constant way.
	$rowhead[$j+1] = "$xmlinitag";
}

$rowfoot = "$xmlinitag";
}
if($conv=="ThermalImage and ThermalImageData" || $conv == "ThermalImage_satellite_type"){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Thermal>
<ThermalImageDataset>
HTMLBLOCK;


$footer ="\n\t\t</ThermalPixels>";
$footer .="\n\t</ThermalImage>\n";
$footer .= <<<FOOT
</ThermalImageDataset>
</Thermal>
</Data>
</wovoml>
FOOT;

$rowprefix = "";	

//Upload Two csv files can't have more than one "rowhead <ThermalImage code="123" owner1="CVGHM"....>". Bcoz Thermalpixels child can't distinguish more than one parent.. So ThermalImage csv file always must have one row.

	if(isset($airplane)){
		$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" airplane=\"$air_satellite\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[0]$owner3[0]$pubdate[0]";
	}else if(isset($satellite)){
		$rowhead= "ThermalImage code=\"$code[0]\" satellite=\"$air_satellite\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[0]$owner3[0]$pubdate[0]";
	}else{
		$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" station=\"$station\" volcano=\"$volcode\" owner1=\"$observ\"$owner2[0]$owner3[0]$pubdate[0]";
	}
/*
	if(isset($airplane)){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" airplane=\"$air_satellite\" owner1=\"$observ\" $pubdate[$j]";
		}	
	}else if(isset($satellite)){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead= "ThermalImage code=\"$code[0]\" satellite=\"$air_satellite\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead= "ThermalImage code=\"$code[0]\" satellite=\"$air_satellite\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead= "ThermalImage code=\"$code[0]\" satellite=\"$air_satellite\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead= "ThermalImage code=\"$code[0]\" satellite=\"$air_satellite\" owner1=\"$observ\" $pubdate[$j]";
		}	
	}else{
		if((isset($owner2)) && (isset($owner3))){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
		}else if(isset($owner3)){
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead= "ThermalImage code=\"$code[0]\" instrument=\"$instrument\" station=\"$station\" owner1=\"$observ\" $pubdate[$j]";
		}
	}
*/

	for($j=0;$j<sizeof($fileheaderarray2);$j++){

			$rowfoot[0] = "\t<ThermalPixels>";
			
			$rowfoot[$j+1] = "\n\t\t\t<ThermalPixel>";

			if(isset($lat[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<lat>".$lat[$j]."</lat>"; 
			}	
			if(isset($lon[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<lon>".$lon[$j]."</lon>"; 
			}	
			if(isset($elev[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<elev>".$elev[$j]."</elev>"; 
			}	
			if(isset($radiance[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<radiance>".$radiance[$j]."</radiance>"; 
			}	
			if(isset($heatFlux[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<heatFlux>".$heatFlux[$j]."</heatFlux>"; 
			}	
			if(isset($temperature[$j])){
			$rowfoot[$j+1] .="\n\t\t\t\t<temperature>".$temperature[$j]."</temperature>"; 
			}			
			$rowfoot[$j+1] .="\n\t\t\t</ThermalPixel>";	
	}
}
if($conv == "Insar_satellite_type"){ 

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Deformation>
<InSARImageDataset>
HTMLBLOCK;


$footer ="\n\t\t</InSARPixels>";
$footer .="\n\t</InSARImage>\n";
$footer .= <<<FOOT
</InSARImageDataset>
</Deformation>
</Data>
</wovoml>
FOOT;


$rowprefix = "";	

/*
	if((isset($owner2)) && (isset($owner3))){
		$rowhead= "InSARImage code=\"$code[0]\" satellite=\"$satellite\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
	}else if(isset($owner2)){
		$rowhead= "InSARImage code=\"$code[0]\" satellite=\"$satellite\" owner1=\"$observ\" $owner2 $pubdate[$j]";	
	}else if(isset($owner3)){
		$rowhead= "InSARImage code=\"$code[0]\" satellite=\"$satellite\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
	}else{
		$rowhead= "InSARImage code=\"$code[0]\" satellite=\"$satellite\" owner1=\"$observ\" $pubdate[$j]";	
	}	
*/

//Upload Two csv files can't have more than one "rowhead <InSARImage code="123" owner1="CVGHM"....>". Bcoz InSARpixels child can't distinguish more than one parent.. So InSARImage csv file always must have one row.

$rowhead= "InSARImage code=\"$code[0]\" satellite=\"$satellite\" owner1=\"$observ\"$owner2[0]$owner3[0]$pubdate[0]";

	
	for($j=0;$j<sizeof($fileheaderarray2);$j++){

		$rowfoot[0] = "\t<InSARPixels>";
		
		$rowfoot[$j+1] = "\n\t\t\t<InSARPixel number=\"$number[$j]\">";

		if(isset($rangeOfChange[$j]))
			$rowfoot[$j+1] .="\n\t\t\t\t<rangeOfChange>".$rangeOfChange[$j]."</rangeOfChange>"; 
			
		$rowfoot[$j+1] .="\n\t\t\t</InSARPixel>";	
	}
}
else if($conv == "LevelingData"){
$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Deformation>
<LevelingDataset>
HTMLBLOCK;


$footer = <<<FOOT
</LevelingDataset>
</Deformation>
</Data>
</wovoml>
FOOT;

$rowprefix = "";	
/*
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "Leveling code=\"$code[$j]\" $instrument[$j] $refStation[$j] $firstBMStation[$j] $secondBMStation[$j] owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";		

		}else if(isset($owner2)){
			$rowhead[$j+1]= "Leveling code=\"$code[$j]\" $instrument[$j] $refStation[$j] $firstBMStation[$j] $secondBMStation[$j] owner1=\"$observ\" $owner2 $pubdate[$j]";		

		}else if(isset($owner3)){
			$rowhead[$j+1]= "Leveling code=\"$code[$j]\" $instrument[$j] $refStation[$j] $firstBMStation[$j] $secondBMStation[$j] owner1=\"$observ\"$owner3 $pubdate[$j]";				
		
		}else{
			$rowhead[$j+1]= "Leveling code=\"$code[$j]\" $instrument[$j] $refStation[$j] $firstBMStation[$j] $secondBMStation[$j] owner1=\"$observ\" $pubdate[$j]";			
		}	
		
	}  
*/

	for($j=0;$j<sizeof($code);$j++){
		$rowhead[$j+1]= "Leveling code=\"$code[$j]\" $instrument[$j] $refStation[$j] $firstBMStation[$j] $secondBMStation[$j] owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";	
	}		

	
$rowfoot = "Leveling";
}
else if($conv == "IntensityData"){ 

// This $_GET value will come from "intensity_csvmml_view_ng.php" while uploaing intensity csv to get networkevent code or singlestation event code.

if(isset($_GET['evn_code'])){     
	$evn_code=$_GET['evn_code']; 
	
	for($i=0;$i<sizeof($evn_code);$i++){
		$checking_code=substr($evn_code[$i],-1);
		$event_code = strstr($evn_code[$i], '_type', true);
		
		if(is_numeric($checking_code)){
			$singleStationEvent[$i]="singleStationEvent=\"$event_code\"";
		}else{
			$networkEvent[$i]="networkEvent=\"$event_code\"";
		}
	} 
}	

$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Seismic>
<IntensityDataset>
HTMLBLOCK;


$footer = <<<FOOT
</IntensityDataset>
</Seismic>
</Data>
</wovoml>
FOOT;

$rowprefix = "";

/*
	for($j=0;$j<sizeof($code);$j++){
	
		if(isset($singleStationEvent[$j])){	
		
			if((isset($owner2)) && (isset($owner3))){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $singleStationEvent[$j] owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
			}else if(isset($owner2)){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $singleStationEvent[$j] owner1=\"$observ\" $owner2 $pubdate[$j]";	
			}else if(isset($owner3)){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $singleStationEvent[$j] owner1=\"$observ\" $owner3 $pubdate[$j]";	
			}else{
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $singleStationEvent[$j] owner1=\"$observ\" $pubdate[$j]";	
			}	
		}
		else{
			if((isset($owner2)) && (isset($owner3))){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $networkEvent[$j] owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
			}else if(isset($owner2)){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $networkEvent[$j] owner1=\"$observ\" $owner2 $pubdate[$j]";	
			}else if(isset($owner3)){
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $networkEvent[$j] owner1=\"$observ\" $owner3 $pubdate[$j]";	
			}else{
				$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $networkEvent[$j] owner1=\"$observ\" $pubdate[$j]";
			}	
		
		}
	} */
	
	for($j=0;$j<sizeof($code);$j++){
	
		if(isset($singleStationEvent[$j])){	
		
			$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $singleStationEvent[$j] owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
		}
		else{
			$rowhead[$j+1]= "Intensity code=\"$code[$j]\" $networkEvent[$j] owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
		}
	}	
	
$rowfoot = "Intensity";
}
else if($conv=="RepresentativeWaveform"){
    
$header = <<<HTMLBLOCK
<?xml version="1.0" encoding="UTF-8" ?> 
<wovoml xmlns="http://www.wovodat.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
version="1.1.0" xsi:schemaLocation="http://www.wovodat.org/WOVOdatV1.xsd">
<Data>
<Seismic>
<WaveformDataset>
HTMLBLOCK;


$footer = <<<FOOT
</WaveformDataset>
</Seismic>
</Data>
</wovoml>
FOOT;

$rowprefix = "";	

/*
	for($j=0;$j<sizeof($code);$j++){
		if((isset($owner2)) && (isset($owner3))){
			$rowhead[$j+1]= "Waveform code=\"$code[$j]\" $networkEvent$singleStationEvent$tremor station=\"$station\" owner1=\"$observ\" $owner2 $owner3 $pubdate[$j]";
		}else if(isset($owner2)){
			$rowhead[$j+1]= "Waveform code=\"$code[$j]\" $networkEvent $singleStationEvent $tremor station=\"$station\" owner1=\"$observ\" $owner2 $pubdate[$j]";
		}else if(isset($owner3)){
			$rowhead[$j+1]= "Waveform code=\"$code[$j]\" $networkEvent $singleStationEvent $tremor station=\"$station\" owner1=\"$observ\" $owner3 $pubdate[$j]";	
		}else{
			$rowhead[$j+1]= "Waveform code=\"$code[$j]\" $networkEvent $singleStationEvent $tremor station=\"$station\" owner1=\"$observ\" $pubdate[$j]";	
		}	
	}
*/

	for($j=0;$j<sizeof($code);$j++){

		$rowhead[$j+1]= "Waveform code=\"$code[$j]\" $networkEvent$singleStationEvent$tremor station=\"$station\" owner1=\"$observ\"$owner2[$j]$owner3[$j]$pubdate[$j]";
	}

$rowfoot = "Waveform";
}
?>