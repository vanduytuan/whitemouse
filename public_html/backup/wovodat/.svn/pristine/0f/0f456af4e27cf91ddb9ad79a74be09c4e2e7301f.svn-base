<?php

// Connect to database
include "php/include/db_connect_view.php";

// Get title
if (($title=urldecode($_GET['title']))=="") {
	exit;
}

// Query database
$result=mysql_query("SELECT cb_id, cb_title, cb_auth, cb_year, cb_journ, cb_vol, cb_pub, cb_page, cb_doi, cb_isbn, cb_url, cb_labadr, cb_keywords FROM cb WHERE cb_title LIKE '%".mysql_real_escape_string($title)."%' LIMIT 5");

while ($row=mysql_fetch_array($result)) {
	// Create <div class="suggestion" id="cb_id">Author (Year). Title</div>
	$cb_id=$row['cb_id'];
	$cb_auth=$row['cb_auth'];
	$cb_year=$row['cb_year'];
	$start_pos=stripos($row['cb_title'], $title);
	$end_pos=$start_pos+strlen($title);
	$cb_title=substr($row['cb_title'], 0, $start_pos)."<b>".substr($row['cb_title'], $start_pos, strlen($title))."</b>".substr($row['cb_title'], $end_pos);
	?><div class="suggest" id="<?php echo $cb_id; ?>" onmousedown="load_all('<?php echo $row['cb_title']."', '".$cb_auth."', '".$cb_year."', '".$row['cb_journ']."', '".$row['cb_vol']."', '".$row['cb_pub']."', '".$row['cb_page']."', '".$row['cb_doi']."', '".$row['cb_isbn']."', '".$row['cb_url']."', '".$row['cb_labadr']."', '".$row['cb_keywords']; ?>')"><?php echo $cb_auth; ?> (<?php echo $cb_year; ?>). <?php echo $cb_title; ?></div><?php
}

?>