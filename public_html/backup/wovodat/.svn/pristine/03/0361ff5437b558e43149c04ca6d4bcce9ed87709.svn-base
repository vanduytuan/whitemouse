<?php

// Connect to database
include "php/include/db_connect_view.php";

// Get journal
if (($journ=urldecode($_GET['journ']))=="") {
	exit;
}

// Query database
$result=mysql_query("SELECT cb_journ FROM cb WHERE cb_journ LIKE '%".mysql_real_escape_string($journ)."%' LIMIT 5");

while ($row=mysql_fetch_array($result)) {
	// Create <div class="suggestion">Journal</div>
	$start_pos=stripos($row['cb_journ'], $journ);
	$end_pos=$start_pos+strlen($journ);
	$cb_journ=substr($row['cb_journ'], 0, $start_pos)."<b>".substr($row['cb_journ'], $start_pos, strlen($journ))."</b>".substr($row['cb_journ'], $end_pos);
	?><div class="suggest" onmousedown="load_journ('<?php echo $row['cb_journ']; ?>')"><?php echo $cb_journ; ?></div><?php
}

?>