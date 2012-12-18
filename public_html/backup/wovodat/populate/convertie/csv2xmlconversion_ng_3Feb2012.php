<?php
function csv2xml($infile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot)
{
$r = "$header\n";

$row = 0;
$cols = 0;
$titles = array();

// Open input CSV file
$inhandle = fopen($infile, 'r');
if (!$inhandle) return $inhandle;

// Open output XML file
$outhandle = fopen($outfile, 'w');
if (!$outhandle) return $outhandle;

$flag=0;
while (($data = fgetcsv($inhandle, 1000, ',')) !== FALSE)
{   

	if ($row > 0)
	{   
		$r .= "\t<{$rowhead[$flag]}>\n";
				
		if ($rowprefix != ""){
			$r .= "\t{$rowprefix}\n";
		}	
	}
	
	if (!$cols){ 
		$cols = count($data);
	}			
	
	for ($i = 0; $i < $cols; $i++) // CAN't this one  for ($i = 0; ($i < $cols) && (strlen($data[$i]) > 0); $i++)
	{		
		if ($row == 0)       // get csv header 
		{
			$titles[$i] = $data[$i];    
			continue;
		}

		if($data[$i] != '' && $data[$i] != 'NULL'){
			$r .= "\t\t<{$titles[$i]}>";
			$r .= $data[$i];
			$r .= "</{$titles[$i]}>\n";
		}
	}
	
	if ($row > 0){ 
		$r .= "\t</{$rowfoot}>\n";
	}
	$flag++;
	$row++;

}

$r .= $footer;

// Write XML to file
fwrite($outhandle, $r);

fclose($outhandle);
fclose($inhandle);

return htmlentities($r); // Successful conversion
}

function csv2xml_data($infile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot)
{
$r = "$header\n";

$row = 0;
$cols = 0;
$titles = array();

// Open input CSV file
$inhandle = fopen($infile, 'r');
if (!$inhandle) return $inhandle;

// Open output XML file
$outhandle = fopen($outfile, 'w');
if (!$outhandle) return $outhandle;

$flag=0;
while (($data = fgetcsv($inhandle, 1000, ',')) !== FALSE)
{   

	if ($row > 0)
	{   
		$r .= "\t<{$rowhead[$flag]}>\n";
				
		if ($rowprefix != ""){
			$r .= "\t{$rowprefix[$flag]}\n";    // this line is different from "function csv2xml"
		}	
	}
	
	if (!$cols){ 
		$cols = count($data);
	}			
	
	for ($i = 0; $i < $cols; $i++) // CAN't this one  for ($i = 0; ($i < $cols) && (strlen($data[$i]) > 0); $i++)
	{		
		if ($row == 0)       // get csv header 
		{
			$titles[$i] = $data[$i];    
			continue;
		}

		if($data[$i] != '' && $data[$i] != 'NULL'){
			$r .= "\t\t<{$titles[$i]}>";
			$r .= $data[$i];
			$r .= "</{$titles[$i]}>\n";
		}
	}
	
	if ($row > 0){ 
		$r .= "\t</{$rowfoot}>\n";
	}
	$flag++;
	$row++;

}

$r .= $footer;

// Write XML to file
fwrite($outhandle, $r);

fclose($outhandle);
fclose($inhandle);

return htmlentities($r); // Successful conversion
}


function twocsvfile_to_xml($infile, $outfile, $header, $footer, $rowprefix, $rowhead, $rowfoot)
{
$r = "$header\n";

$row = 0;
$cols = 0;
$titles = array();

// Open input CSV file
$inhandle = fopen($infile, 'r');
if (!$inhandle) return $inhandle;

// Open output XML file
$outhandle = fopen($outfile, 'w');
if (!$outhandle) return $outhandle;


while (($data = fgetcsv($inhandle, 1000, ',')) !== FALSE)
{    

	if($row >1){   // Always have one big <ThermalImage> block. if csv has more than one row,this one will break it.
		break;
	}
	
	if ($row > 0)
	{  
	
	//Upload Two csv files can't have more than one "rowhead <ThermalImage code="123" owner1="CVGHM"....>". Bcoz Thermalpixels child can't distinguish more than one parent.. So ThermalImage csv file always must have one row.
	
	//	$r .= "\t<{$rowhead[$flag]}>\n";  
		$r .= "\t<{$rowhead}>\n";		
		
		if ($rowprefix != ""){
			$r .= "\t{$rowprefix}\n";
		}	
	}
	
	if (!$cols){ 
		$cols = count($data);
	}			
	
	for ($i = 0; $i < $cols; $i++) // CAN't this one  for ($i = 0; ($i < $cols) && (strlen($data[$i]) > 0); $i++)
	{		
		if ($row == 0)       // get csv header 
		{
			$titles[$i] = $data[$i];    
			continue;
		
		}

		if($data[$i] != '' && $data[$i] != 'NULL'){
			$r .= "\t\t<{$titles[$i]}>";
			$r .= $data[$i];
			$r .= "</{$titles[$i]}>\n";
		} 
	}
	
	if ($row > 0){ 
	//$r .= "\t</{$rowfoot}>\n";
		for($i=0;$i<sizeof($rowfoot);$i++){
			$r .= "\t{$rowfoot[$i]}";
		}	
	}

	$row++;

}

$r .= $footer;

// Write XML to file
fwrite($outhandle, $r);

fclose($outhandle);
fclose($inhandle);

return htmlentities($r); // Successful conversion
}
?>