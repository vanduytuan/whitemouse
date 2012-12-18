<?php
/******************************************************************************************************
* Plots chronology of eruption
******************************************************************************************************/

// Get vd_id of the volcano
$vdi=$_REQUEST['vdi'];
$nvol=$_REQUEST['nvolc'];

include 'php/include/db_connect_view.php';
$result = mysql_query("UNIX_TIMESTAMP(select d.ed_phs_stime) as t, d.ed_phs_vei 	FROM vd a, ed c, ed_phs d 	WHERE a.vd_id='$vdi' and a.vd_id=c.vd_id and c.ed_id=d.ed_id 		ORDER by d.ed_stime DESC");

// If no results
if (mysql_num_rows($result)==0) {
echo <<<STRING
		<p>fail: no data</p>
STRING;
	mysql_close($link);
	exit;
}

// MySQL result to array for Flot
$chrono_data=array();
$chrono_data[0]=array();
$chrono_data[0]['values']=array();
$chrono_data[0]['points']=FALSE;
$chrono_data[0]['lines']=FALSE;
$chrono_data[0]['bars']=TRUE;
$chrono_data[0]['color']=NULL;
$chrono_data[0]['label']='';

// Loop on results
while ($row=mysql_fetch_object($result)) {
	// x
	$x=($row->t)*1000;
	// y
	$y=$row->ed_phs_vei;

	// New values
		array_push($chrono_data[0]['values'], array('x' => $x, 'y' => $y));
}

// Prepare div to contain graphs
$chronoxdisp="chrono_disp".$nvol."_x";
$chronoydisp="chrono_disp".$nvol."_y";

echo <<<STRING
	<div id="chrono_graph" style="float:left;">
		<div style="float:bottom; height:60px;align:center;" id="chrono_disp$nvol"></div>
			<p style="clear:both; font-size:8px;">Date: <span id="$chronoxdisp"></span><span>&nbsp&nbsp Rsam:</span><span id="$chronoydisp"></span></p>
	</div>
STRING;
echo <<<STRING
		<div style="clear:both; visibility:hidden; height:0px;">.</div>
STRING;

// Call flot function for displaying graph
include_once "php/funcs/flot_funcs.php";
flot_plot_time($chrono_data, "chrono_disp$nvol");

?>
