<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$vid=$_GET["vdid2"];
	$vd_name2=$_GET["vd_name2"];
	$kk=0;
	$res=mysql_query("select vd_inf_type from vd_inf where vd_id='$vid'");
	while($vv=mysql_fetch_array($res)){
		$vdtype=$vv[0];
	}
	echo "<p>Volcano Group: $vdtype =>".htmlentities($vid - $vd_name2, ENT_COMPAT, "cp1252")." </p>";
?>
