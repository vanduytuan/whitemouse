<?php
$directory = "/home/wovodat/login_history.txt";
 
//get all image files with a .jpg extension.
$handle = fopen($directory,"a");
if (!$handle)
	echo "false";
?>
