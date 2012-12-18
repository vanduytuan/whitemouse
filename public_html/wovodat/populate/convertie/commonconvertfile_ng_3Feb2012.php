<?php
include "csv2xmlconversion_ng.php";
include "model/common_model_ng.php";


$a=array();
$b=array();
$csvfile=array();


$observ=$_POST['observ'];
$vol=$_POST['vol2'];
$conv=$_POST['conv'];

if(isset($_POST['network']))
	$network =$_POST['network'];


if(isset($_POST['station']))
	$station = $_POST['station'];


if(isset($_POST['instrument']))
	$instrument = $_POST['instrument'];
	
	
$volcode=getvolcode($vol);        // Get cavw from DB 


if($conv == 'SeismicStation' || $conv == 'DeformationStation' || $conv == 'GasStation' ||   
$conv == 'HydrologicStation' ||$conv == 'ThermalStation' || $conv == 'FieldsStation'){
	
	$network=getnetworkcode($network,$conv);  // Get network code from cn / sn table
}

else if($conv == 'SeismicInstrument' || $conv == 'DeformationInstrument_General' || 
$conv == 'DeformationInstrument_Tilt/Strain' || $conv == 'GasInstrument' || $conv == 'HydrologicInstrument' || $conv == 'ThermalInstrument' || $conv == 'FieldsInstrument' ){
	
	$station=getstationcode($station,$conv);  // Get station code from DB
}

else if($conv == 'SeismicComponent'){
	$instrument=getinstrcode($instrument,$conv);// Get instr code from si table bcoz only seismic component xml need  
}
	
	
	
$filename=$_FILES['fname']['name'];
$filesize=$_FILES['fname']['size'];


$infile="../../../../incoming/to_be_translated/"."$filename";//prepare the name of inputfile


//Move "temp" to inputfile name	
if($filesize<= 1000000){
	if (!move_uploaded_file($_FILES['fname']['tmp_name'],$infile)){
		$fileerrors = "File submission fails.  Please try again!";
		include "showxmlresult_ng.php";
		exit();
	}    		  
}else{
	$fileerrors = "File size is too big.<br> File submission fails. Please try again!";
	include "showxmlresult_ng.php";
	exit();
}


$tempfile="../../../../incoming/translated/".$filename;//prepare the name of temp file

$outputfile="../../../../incoming/translated/";     //prepare the directory of output file
$fileextension=substr($filename,0,-4).".xml"; 
$outfile=$outputfile.$fileextension;


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


include "commonmonitoring_ng.php";     // Get repective variables for each Monitoring Data(a lot of if/esle) 
      

for($i=1;$i<$count;$i++){           // Main point transform csvarray to get same order as xsd scehma 
	
	for($j=0;$j<sizeof($fileheader[0]);$j++){
	
			if($fileheader[0][$j] == 'di_gen_type'){  // To put di_gen type from post value user choose from drop down
				$a[]=$fileheader[0][$j];
				$b[]=$_POST['digen_select'];
			}
			else if($fileheader[0][$j] == 'di_tlt_type'){ // To put di_gen type from post value from drop down
				$a[]=$fileheader[0][$j];
				$b[]=$_POST['ditltstrain_select'];			
			}
			else{
				$a[]=$fileheader[0][$j];
				$b[]=$fileheader[$i][$j];
			
			}
		
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



$xml =csv2xml($tempfile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot);  //convert xml 

unlink($tempfile);   // delete my own temp file from "to_be_translated_temp" folder.  


include "showxmlresult_ng.php";      //Show every results here...        

?>


