<?php

/*
 * Connect wovodat database
 * View only
 */
$link = mysql_connect("www.wovodat.org", "wovodat_view", "+00World");
if (!$link) {
    include "php/include/db_connect_view2.php"; 
} else {
    mysql_select_db("wovodat") or die(mysql_error());
}
?>

