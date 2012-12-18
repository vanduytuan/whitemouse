<?php
function getNextDay($today) 
{
	$nxtDate = $today;
	$mkDate = explode("/", $today);
	if (checkdate($mkDate[0]+0, $mkDate[1]+1, $mkDate[2])) {
		$nxtDate = $mkDate[0] . "/" . ($mkDate[1]+1 < 10 ? "0" . ($mkDate[1]+1) : ($mkDate[1]+1) ) . "/" . $mkDate[2];
	}
	else if (checkdate($mkDate[0]+1, 1, $mkDate[2]))
	{
		$nxtDate = ($mkDate[0]+1 < 10 ? "0" . ($mkDate[0]+1) : ($mkDate[0]+1) ) . "/01/" . $mkDate[2];
	}
	else if (checkdate(1, 1, $mkDate[2]+1))
	{
		$nxtDate = "01/01/" . ($mkDate[2]+1);
	}
	return $nxtDate;
}

$file1 = "merapi_EDM_Kal.csv";
$lines = file($file1);
$first = true; $prevDate = "";
foreach($lines as $line_num => $line) // process each line from the csv file
{
	if (!$line) continue; // ignore empty strings fetched
	if ($first) { // process the header from the csv file.
		$xaxis = strtok($line, ",\n");
		$firstFieldName = strtok(",\n");
		$secondFieldName = strtok(",\n");
		$cats = "<categories>";
		$firstField = "<dataset renderAs='line' linethickness='2' seriesName=\"$firstFieldName\" parentYAxis='P'>";
		$secondField = "<dataset renderAs='line' linethickness='2' seriesName=\"$secondFieldName\" parentYAxis='S'>";
		$first = false;
	}
	else {
		$line = str_replace(",,", ", ,", $line); // empty strings are ignored when tokenized so put at least one space to indicate lack of data.
		$tok = trim(strtok($line, ",\n"));
		$date = explode("/", $tok);
		if (!$prevDate) { // create a label for the start of month. 
			$prevDate = $date[0] . "/01/" . $date[2];
			if ($tok != $prevDate) {
				$cats .= "<category label=\"$prevDate\"/>\n";
				$firstField .= "<set value=\"\" />\n";
				$secondField .= "<set value=\"\" />\n";
			}
		}
		while ($prevDate != $tok && $prevDate && $tok)
		{
			$prevDate = getNextDay($prevDate);
			$mkDate = explode("/", $prevDate);
			if ($prevDate == $tok) break;
			$cats .= "<category label=\"$prevDate\" showLabel=\"". ($mkDate[1] == 1? "1" : "0") ."\" />";
			$firstField .= "<set value=\"\" />\n";
			$secondField .= "<set anchorSides='3' value=\"\" />\n";
		}
		if ($date[1] == 1) $cats.="<category label=\"$tok\" />";
		else $cats .= "<category label=\"$tok\" showLabel='0' />\n";
		$prevDate = $tok;
		$tok = trim(strtok(",\n"));
		$firstField .= "<set value=\"$tok\" anchorRadius='5' />\n";
		
		if (!isset($PHighVal)) $PHighVal = $tok;
		else if ($PHighVal < $tok) $PHighVal = $tok;
		if (strlen($tok))
		if (!isset($PLowVal)) $PLowVal = $tok;
		else if ($PLowVal > $tok) $PLowVal = $tok;
		
		$tok = trim(strtok(",\n"));
		$secondField .= "<set anchorSides='3' anchorRadius='6' value=\"$tok\" />\n";
		
		if (!isset($SHighVal)) $SHighVal = $tok;
		else if ($SHighVal < $tok) $SHighVal = $tok;
		if (strlen($tok))
		if (!isset($SLowVal)) $SLowVal = $tok;
		else if ($SLowVal > $tok) $SLowVal = $tok;
	}
}
$SHighVal = ceil($SHighVal);
$PHighVal = ceil($PHighVal);
$SLowVal = floor($SLowVal);
$PLowVal = floor($PLowVal);
$cats .= "</categories>";
$firstField .= "</dataset>";
$secondField .= "</dataset>";

?> <chart caption="merapiEDMKal"  connectNullData='1' formatnumberscale="0" PYAxisMinValue="<?php echo $PLowVal; ?>" PYAxisMaxValue="<?php echo $PHighVal; ?>" SYAxisMinValue="<?php echo $SLowVal; ?>" SYAxisMaxValue="<?php echo $SHighVal; ?>" xaxisname="<?php echo $xaxis; ?>" PYAxisName="<?php echo $firstFieldName; ?>" SYAxisName="<?php echo $secondFieldName; ?>" showvalues="0" numbersuffix="m" Snumbersuffix="m" decimalprecision="5" palette="3" setAdaptiveYMin='1' >
<?php
echo "$cats $firstField $secondField";
?>
</chart>