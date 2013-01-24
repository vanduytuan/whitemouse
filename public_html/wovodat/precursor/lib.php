<?php

function findTableSize() {
    include "php/include/db_connect_view.php";
    $result = mysql_query("show table status from wovodat;");

    while ($array = mysql_fetch_array($result)) {
        $total = $array[Data_length] + $array[Index_length];
        if ($array[Data_length] < 10000000)
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
function findElement(){
    include "php/include/db_connect_view.php";
    $result = mysql_query("select distinct sn_code from sn");
    while ($array = mysql_fetch_array($result)) {
        echo $array[0] . "<br/>";
    }
    $result = mysql_query("select distinct sd_evn_code from sd_evn");
    while ($array = mysql_fetch_array($result)) {
        echo $array[0] . "<br/>";
    }
}
findElement();
?>
<html>
    <head>
        <link href="/css/styles_beta.css" rel="stylesheet"> 
        <link href="/css/tooltip.css" rel="stylesheet">
        <link type="text/css" href="/js/jqueryui/css/custom-theme/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.tuan.js"></script>
        <script type="text/javascript" src="/js/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.marks.js"></script>

        <script type="text/javascript" src="/js/wovodat.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ9kUvUtmawmFJ62hWVsigWFTh3CKUzzM&sensor=false"></script>
        <script type="text/javascript" src="/js/Tooltip_v3.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript">
            $(function(){
                var data = [[[1,5],[2,4],[3,1]]];
                var options = {};
                if($.plot)
                    $.plot(document.getElementById('test'),data,options);
            });
        </script>
    </head>
    <body>
        <div id="test" style="height:200px; width: 500px"></div>
    </body>
</html>