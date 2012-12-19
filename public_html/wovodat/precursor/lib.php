<?php

function findTableSize() {
    include "php/include/db_connect_view.php";
    $result = mysql_query("show table status from wovodat;");

    while ($array = mysql_fetch_array($result)) {
        $total = $array[Data_length] + $array[Index_length];
        if ($array[Data_length] < 1000000)
            continue;
        echo '
            Table: ' . $array[Name] . '<br />
            Data Size: ' . $array[Data_length] / 1000000 . ' MB<br />
            Index Size: ' . $array[Index_length] / 1000000 . ' MB<br />
            Total Size: ' . $total / 1000000 . ' MB<br />
            Total Rows: ' . $array[Rows] . '<br />
            Average Size Per Row: ' . $array[Avg_row_length] . '<br /><br />';
    }
}



?>