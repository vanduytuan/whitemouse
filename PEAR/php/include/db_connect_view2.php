<?php

/**********************************

This script is for connecting to the database with RESTRICTED permissions (SELECT only, no INSERT or UPDATE).

**********************************/

$link=mysql_connect("localhost", "root", "1234root") or die(mysql_error());
mysql_select_db("monitoringdb") or die(mysql_error());  



?>

