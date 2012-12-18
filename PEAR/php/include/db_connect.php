<?php

/**********************************

This script is for connecting to the database with FULL permissions (INSERT, UPDATE, SELECT...).

**********************************/

//$link=mysql_connect("localhost", "root", "1234root") or die(mysql_error());
$link=mysql_connect("www.wovodat.org", "wovodat_view", "+00World") or die(mysql_error());
mysql_select_db("monitoringdb") or die(mysql_error());
?>

