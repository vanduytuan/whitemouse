<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
require("listVolOfCategory.php");
$kod=$_GET["kode"];
$row=$listVolcanOfCategory[$kod];
sort($row);
echo '<select name="volcateg"><option>',
			  join('</option><option>',$row),'</select>';
?>
