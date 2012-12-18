<?php

// Connect to database
include "php/include/db_connect_view.php";

// Get journal
if (($pub=urldecode($_GET['pub']))=="") {
	exit;
}

// Query database
$result=mysql_query("SELECT cb_pub FROM cb WHERE cb_pub LIKE '%".mysql_real_escape_string($pub)."%' LIMIT 5");

while ($row=mysql_fetch_array($result)) {
	// Create <div class="suggestion">Publisher</div>
	$start_pos=stripos($row['cb_pub'], $pub);
	$end_pos=$start_pos+strlen($pub);
	$cb_pub=substr($row['cb_pub'], 0, $start_pos)."<b>".substr($row['cb_pub'], $start_pos, strlen($pub))."</b>".substr($row['cb_pub'], $end_pos);
	?><div class="suggest" onmousedown="load_pub('<?php echo $row['cb_pub']; ?>')"><?php echo $cb_pub; ?></div><?php
}

?>