<?php
// Check login
require_once "php/include/login_check.php";
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$statt="Historical";$unn="Unnamed";$statusUnc='Uncertain';$statusNot='Not a Volcano';
	$vdid=$_GET["vdid"];
	$res=mysql_query("select vd_inf_type from vd_inf where vd_id='$vdid'");
	while($vv=mysql_fetch_array($res)){
		$vdtype=$vv[0];
	}
	$result = mysql_query("select a.vd_id, a.vd_cavw, a.vd_name FROM vd a, vd_inf b WHERE a.vd_id=b.vd_id and SUBSTR(a.vd_name,1,7)!='$unn' and b.vd_inf_status!='$statusUnc' and b.vd_inf_status!='$statusNot' ORDER by vd_name");
	$kk=0;$baris=mysql_num_rows($result);

	$nrand=rand(2, $baris);	
	if ($nrand==1) {$nrand=rand(2,$baris);}	
	
	echo '<select id="vd_name2" onchange="volcano2_selected()" style="font-size:12px; width:158px;">';
	while ($v_arr = mysql_fetch_array($result)) {
		$row[$kk]=htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "	cp1252");
		if($kk==$nrand){
			echo "<option value=\"$v_arr[0]\" selected=\"selected\">".$v_arr[2]."_".$v_arr[1]."</option>";
		}else{
			echo "<option value=\"$v_arr[0]\">".$v_arr[2]."_".$v_arr[1]."</option>";
		}
		$kk++;
	} 
	echo '</select>';

?>
