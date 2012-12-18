<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
require("listVol.php");
$kod=$_GET["kode"];
$row=$listVolcan[$kod];
echo '<select name="vol"><option>',
			  join('</option><option>',$row),'</select>';
?>
