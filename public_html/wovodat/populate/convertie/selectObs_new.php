<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
require("listObs.php");
$sour=$_GET["source"];
$row=$listObserv[$sour];
echo '<select name="obs"><option>',
			  join('</option><option>',$row),'</select>';
?>
