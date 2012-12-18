<?php
include "csv2xmlconversion_ng.php";
include "model/common_model_data_ng.php";


$conv= "IntensityData";
$filename=$_GET['filename'];
$observ=$_GET['observ'];
$vol=$_GET['vol'];
$filesize=$_GET['filesize'];


$infile="../../../../incoming/to_be_translated/"."$filename";//prepare the name of inputfile

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




$xml =csv2xml_data($tempfile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot);  //convert xml 
	
unlink($tempfile);   // delete my own temp file from "to_be_translated_temp" folder.  


include "showxmlresult_ng.php";      //Show every results here...    

?>