<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
require("listVolOfInstitute.php");
$kod=$_GET["kode"];
$row2=$listVolcanOfInstitute[$kod];
$row=$row2;
sort($row);
	echo '<select name="vol2">';
	foreach($row as $k => $v){
		echo "<option value=\"$v\">".$v."</option>";
	}
	echo '</select>';

//echo '<select name="vol"><option>',  join('</option><option>',$row),'</select>';
?>