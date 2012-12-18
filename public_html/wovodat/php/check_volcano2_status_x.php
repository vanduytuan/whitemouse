<?php
// Get root url
require_once "php/include/get_root.php";
?>

<?php
	include 'php/include/db_connect_view.php';
	$vdi2=$_GET["vdi2"];
	$result = mysql_query("select a.vd_name, b.vd_inf_status, b.vd_inf_type, c.cc_url FROM vd a, vd_inf b, cc c WHERE a.vd_id='$vdi2' and a.vd_id=b.vd_id and a.cc_id=c.cc_id");
	while ($v_arr = mysql_fetch_array($result)) {
		$vol_name=htmlentities($v_arr[0], ENT_COMPAT, "cp1252");
		$vol_status=$v_arr[1];
		$vol_type=$v_arr[2];
		$obs_url=$v_arr[3];
		}
		
	if ($vol_type=="Shield volcano") $vol_type="Shield volc.";	
	echo "<p style=\"font-size:10px;\">($vol_type";
	if($vol_status!="Historical"){
		echo " -$vol_status)<br>";
	}else{
		echo ")</p>";
	}	
	if ($obs_url!='' and $obs_url!='http://www.volcano.si.edu/') echo "<a style=\"font-size:10px;\" href='$obs_url' target='_blank'>($obs_url)</a>";
	
	echo "<br/><br/>";
	echo "<table>";
	echo "<tr>";
	echo "<td>";
	
	$result2 = mysql_query(" select c.ed_stime FROM vd a, ed c WHERE a.vd_id='$vdi2' and a.vd_id=c.vd_id ORDER by c.ed_stime DESC");
	
	echo "<p style=\"font-size:10px; width=55px;\">Eruption:";
	echo "</td>";
	echo "<td>";
	echo "<select id=\"list_erupt\" style=\"font-size:10px; width:95px; align:right;\" >";
	
	echo "<br>";
	$kk1=0;
	while ($e_arr = mysql_fetch_array($result2)){
		$erupt=substr($e_arr[0],0,4);
		if (substr($erupt,0,1)=="0"){
			if (substr($erupt,0,2)=="00"){
				if (substr($erupt,0,3)=="000"){
					$erupt=substr($erupt,3,1);
				}else{		
					$erupt=substr($erupt,2,2);
				}
			}else{		
				$erupt=substr($erupt,1,3);
			}
		}
		if ($erupt!="0000" && $erupt!="" && $erupt!="0") {
			$kk1++;
			echo "<option>$erupt AD</option>";
		}
	}
	if ($kk1==0) echo "<option>---</option>";
	echo "</p>";	
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	
?>
