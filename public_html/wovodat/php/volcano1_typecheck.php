<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$vid=$_GET["vd_id"];
	$re=mysql_query("select vd_inf_type from vd_inf where vd_id='$vid'");
	while($v=mysql_fetch_array($re)){
		$vtype=$v[0];
	}
	echo $vtype;
?>
