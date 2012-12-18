<?php
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$vdi2=$_GET["vdi2"];
	$result = mysql_query("select a.vd_name, b.vd_inf_status, b.vd_inf_type FROM vd a, vd_inf b WHERE a.vd_id='$vdi2' and a.vd_id=b.vd_id");
	while ($v_arr = mysql_fetch_array($result)) {
		$vol_name=$v_arr[0];
		$vol_status=$v_arr[1];
		$vol_type=$v_arr[2];
		}
	echo "$vol_name ($vol_type) -activity: $vol_status";
?>
