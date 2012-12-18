<?php 
$ds_id = $_REQUEST['ds_id'];
//$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
$link = mysql_connect("localhost", "wovodat", "123_nousironsauxbois") or die(mysql_error());
mysql_select_db("wovodat") or die(mysql_error());
$query = "select distinct(ds_id_ref1) from dd_gps where ds_id = '$ds_id' order by ds_id_ref1 asc";
$result = mysql_query($query);
echo "DS ID Ref1:&nbsp;&nbsp;<select id=\"ds_id_ref1\" onchange='setupChart(); '>";
echo "<option>Select ds_id_ref1:</option>";
while ($ds_id_obj = mysql_fetch_object($result))
	echo "<option>$ds_id_obj->ds_id_ref1</option>";
mysql_close($link);
echo "</select>";
?>