<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$vdid=$_GET["vdid"];
	$res=mysql_query("select vd_inf_type from vd_inf where vd_id='$vdid'");
	while($vv=mysql_fetch_array($res)){
		$vdtype=$vv[0];
	}
	$result = mysql_query("select a.vd_id, a.vd_cavw, a.vd_name from vd a, vd_inf b where a.vd_id=b.vd_id and b.vd_inf_type='$vdtype'   order by vd_name");
	$kk=0;
	while ($v_arr = mysql_fetch_array($result)) {
		$row[$kk]=htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "	cp1252");
		$kk++;
	} 
	sort($row);
	echo "<p>Group:\"$vdtype\"=>$kk</p>";
	echo '<select name="volcateg" onchange="volcano_selected2()" style="width:100%"><option>',
			  join('</option><option>',$row),'</select>';
?>
