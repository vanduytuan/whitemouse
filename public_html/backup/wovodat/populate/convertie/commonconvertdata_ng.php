<?php
include "csv2xmlconversion_ng.php";
include "model/common_model_data_ng.php";
include "f2genfunc/funcgen_datetime.php";

$a=array();
$b=array();
$csvfile=array();


$observ=$_POST['observ'];
$vol=$_POST['vol2'];
$conv=$_POST['conv'];

if(isset($_POST['network']))
	$network =$_POST['network'];


if(isset($_POST['stat']))
	$station = $_POST['stat'];

if(isset($_POST['stat2']))
	$station2 = $_POST['stat2'];
	
if(isset($_POST['stat3']))
	$station3 = $_POST['stat3'];	
	
if(isset($_POST['instrument']))
	$instrument = $_POST['instrument'];

if(isset($_POST['rsam_ssamcode']))
	$rsam_ssamcode= $_POST['rsam_ssamcode'];

if(isset($_POST['eventtype_waveselect']))
	$wav_eventtype= $_POST['eventtype_waveselect'];

if(isset($_POST['eventcode']))
	$wav_eventcode= $_POST['eventcode'];

	
if(isset($_POST['trm_ivl_select'])){  
	if($_POST['trm_ivl_select'] == 'Network'){
		if($conv == "SeismicTremor")
			$conv = "SeismicTremor_Network";
		else if($conv == "IntervalSwarmData")
			$conv = "SeismicIntervalSwarm_Network";
	}else if($_POST['trm_ivl_select'] == 'Station'){	
		if($conv == "SeismicTremor")
			$conv = "SeismicTremor_Station";
		else if($conv == "IntervalSwarmData")
			$conv = "SeismicIntervalSwarm_Station";		
	}
}

if(isset($_POST['airplane'])){     // InsarImage doesn't need to think about 'airplane'
	$airplane=$_POST['airplane'];
	$conv_backup=$conv;           // to use in getinstrcode function
	
	if($conv == "PlumeData")
		$conv="plume_satellite_type";
	else if($conv == "ThermalImage and ThermalImageData")	
		$conv= "ThermalImage_satellite_type";
}
	
if(isset($_POST['satellite'])){
	$satellite=$_POST['satellite'];
	$conv_backup=$conv;           // to use in getinstrcode function
	
	if($conv == "PlumeData")
		$conv="plume_satellite_type";
	else if($conv == "ThermalImage and ThermalImageData")	
		$conv= "ThermalImage_satellite_type";
	else if($conv == "InSARImage and InSARData")	
		$conv="Insar_satellite_type";
}	
	
	

	
$volcode=getvolcode($vol);        // Get cavw from DB 


if($conv == 'EventDataFromNetwork' || $conv == 'SeismicTremor_Network' || $conv == 'SeismicIntervalSwarm_Network'){
	
	$network=getnetworkcode($network,$conv);  // Get network code from cn / sn table
}
else if($conv=="EventDataFromSingleStation" || $conv == 'SeismicTremor_Station' || $conv == 'SeismicIntervalSwarm_Station' || $conv=="RSAMData" || $conv=="SSAMData" ){

	$station=getstationcode($station,$conv);  // Get station code from DB
}
else if($conv == 'ElectronicTiltData' || $conv == 'TiltVectorData' || $conv == 'StrainMeterData' || $conv == 'GPSVectors' || $conv == 'EDMData' || $conv == 'AngleData' || $conv == 'GPSData' || $conv == 'DirectlySampledGas' ||$conv == 'SoilEffluxData' || $conv == 'PlumeData' || $conv == "HydrologicData" || $conv == "MagneticFieldsData" || $conv == "MagnetorVectorData" || $conv == "ElectricFieldsData" || $conv == "GravityData" || $conv == "GroundBasedThermalData" || $conv == "ThermalImage and ThermalImageData"){

	$station=getstationcode($station,$conv);  // Get station code from DB
	$instrument=getinstrcode($instrument,$conv);   // Get instr code from DB 

}
else if($conv == "plume_satellite_type" || $conv == "ThermalImage_satellite_type" || $conv == "Insar_satellite_type"){
	if(isset($airplane)){
		$sat_type="A";
		$air_satellite=getsatellitecode($airplane,$sat_type); // Get Airline code from DB
		$instrument=getinstrcode($instrument,$conv_backup);   // Get instr code from DB 
	}else if(isset($satellite)){
		$sat_type="S";
		$air_satellite=getsatellitecode($satellite,$sat_type); // Get satellite code from DB
	}
	
}




if(($_FILES['fname']['name'] != '') && ($_FILES['secondname']['name'] != '')){  // this function only work while uploading two csv files together

	$filename=$_FILES['fname']['name'];
	$filesize=$_FILES['fname']['size'];
	$infile="../../../../incoming/to_be_translated/"."$filename";//prepare the name of inputfile

	$filename2=$_FILES['secondname']['name'];
	$filesize2=$_FILES['secondname']['size'];
	$infile2="../../../../incoming/to_be_translated/"."$filename2";//prepare the name of inputfile

	$toalfilesize= $filesize+$filesize2;

	
	//Move "temp" to inputfile name	
	if($toalfilesize<= 1000000){
		if ((!move_uploaded_file($_FILES['fname']['tmp_name'],$infile)) || (!move_uploaded_file($_FILES['secondname']['tmp_name'],$infile2))){
			$fileerrors = "File submission fails.  Please try again!";
			include "showxmlresult_ng.php";
			exit();
		}    		  
	}else{
		$fileerrors = "File size is too big.<br> File submission fails. Please try again!";
		include "showxmlresult_ng.php";
		exit();
	}	
	
}else if($_FILES['fname1']['name'] != ''){ 

	$filename=$_FILES['fname1']['name'];
	$filesize=$_FILES['fname1']['size'];
	$infile="../../../../incoming/to_be_translated/"."$filename";//prepare the name of inputfile

	//Move "temp" to inputfile name	
	if($filesize<= 1000000){
		if (!move_uploaded_file($_FILES['fname1']['tmp_name'],$infile)){
			$fileerrors = "File submission fails.  Please try again!";
			include "showxmlresult_ng.php";
			exit();
		}    		  
	}else{
		$fileerrors = "File size is too big.<br> File submission fails. Please try again!";
		include "showxmlresult_ng.php";
		exit();
	}
}


$handle=fopen($infile,"r");          // read csv file and store csv into array
while(!feof($handle)){
	$fileheader[]=fgetcsv($handle);
}	

$line=count($fileheader);

if(!($fileheader[$line-1])){   // Try to remove empty last row from csv file if file has empty row at EOF
	$count = $line-1;
}else{
	$count = $line;
}



if($conv == 'IntensityData'){    // IntensityData link to intensity_csvxml__view_ng.php and continue from that page

	for($i=1;$i<$count;$i++){
		$int_time[]=$fileheader[$i][5];
	}
	for($j=0;$j<sizeof($int_time);$j++){
		$intensity_time[$j]=geteventnet_eventstat_time($int_time[$j]);
	}
	include "intensity_csvxml__view_ng.php";

}
else if($conv != 'IntensityData'){
	
	
	if(isset($infile2)){

		$handle2=fopen($infile2,"r");          // read csv file and store csv into array
		while(!feof($handle2)){
			$fileheader2[]=fgetcsv($handle2);
		}	

		$line2=count($fileheader2);


		if(!($fileheader2[$line2-1])){   // Try to remove empty last row from csv file if file has empty row at EOF
			$count2 = $line2-1;
		}else{
			$count2 = $line2;
		}
	}
	
	$tempfile="../../../../incoming/translated/".$filename;//prepare the name of temp file

	$outputfile="../../../../incoming/translated/";     //prepare the directory of output file
	$fileextension=substr($filename,0,-4).".xml"; 
	$outfile=$outputfile.$fileextension;
	
	include "commondata_ng.php";     // Get repective variables for each Monitoring Data(a lot of if/esle) 
		 

	for($i=1;$i<$count;$i++){           // Main point transform csvarray to get same order as xsd scehma 
		
		for($j=0;$j<sizeof($fileheader[0]);$j++){
		
			$a[]=$fileheader[0][$j];
			$b[]=$fileheader[$i][$j];
			
		}
			
		$csvfile=array_combine($a,$b);
		
		foreach($newfileheader as $key => $val){
			foreach($csvfile as $key1 => $val1){
				if($key == $key1){
						
						$newheader[$i-1][]=$val;
						$newheader[$i][$val]=$val1;
				}
			}
		}
	}  


	$fp = fopen($tempfile, "w");              // write array to tempfile
	foreach ($newheader as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);


	if($conv == "ThermalImage and ThermalImageData" || $conv == "Insar_satellite_type" || $conv == "ThermalImage_satellite_type"){
		$xml = twocsvfile_to_xml($tempfile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot);
	}else{
		$xml =csv2xml_data($tempfile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot);  //convert xml 
		
	}

	unlink($tempfile);   // delete my own temp file from "to_be_translated_temp" folder.  


	include "showxmlresult_ng.php";      //Show every results here...        
}
?>


