<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

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

function findElement() {
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

function testing() {
    include "php/include/db_connect_view.php";

    $result = mysql_query("select dd_gps_time, dd_gps_lat from dd_gps ");
    if (!$result) {
        echo "die";
        return;
    }
    while ($array = mysql_fetch_array($result)) {
        echo $array[0] . " " . $array[1] . "<br/>";
    }
}
?>
<html>
    <head>
        <link href="/css/tooltip.css" rel="stylesheet">
        <link type="text/css" href="/js/jqueryui/css/custom-theme/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.tuan.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.marks.js"></script>

        <script type="text/javascript" src="/js/wovodat.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ9kUvUtmawmFJ62hWVsigWFTh3CKUzzM&sensor=false"></script>
        <script type="text/javascript" src="/js/Tooltip_v3.js"></script>
        <script type="text/javascript">
        </script>
        <style text="text/css">
            nav ul ul{
                display: none;
            }
            nav ul li:hover > ul{
                display: block
            }
            nav ul{
                background: #efefef;
                background: linear-gradient(top, #efefef 0%, #bbbbbb 100%);  
                background: -moz-linear-gradient(top, #efefef 0%, #bbbbbb 100%); 
                background: -webkit-linear-gradient(top, #efefef 0%,#bbbbbb 100%); 
                padding: 0 20px;
                border-radius: 10px;
                list-style: none;
                position: relative;
                display: inline-table;
            }
            nav ul:after {
                content: ""; clear: both; display: block;
            }
            nav ul li {
                float: left;
            }
            nav ul li:hover {
                background: #4b545f;
                background: linear-gradient(top, #4f5964 0%, #5f6975 40%);
                background: -moz-linear-gradient(top, #4f5964 0%, #5f6975 40%);
                background: -webkit-linear-gradient(top, #4f5964 0%,#5f6975 40%);
            }
            nav ul li:hover a {
                color: #fff;
            }
            nav ul li a {
                display: block; 
                padding: 25px 40px;
                color: #757575; 
                text-decoration: none;
            }
            nav ul ul {
                background: #5f6975; 
                border-radius: 0px; 
                padding: 0;
                position: absolute; 
                top: 100%;
            }
            nav ul ul li {
                float: none; 
                border-top: 1px solid #6b727c;
                border-bottom: 1px solid #575f6a;
                position: relative;
            }
            nav ul ul li a {
                padding: 15px 40px;
                color: #fff;
            }	
            nav ul ul li a:hover {
                background: #4b545f;
            }
            nav ul ul ul {
                position: absolute; left: 100%; top:0;
            }
        </style>
    </head>
    <body>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li>
                <a href="#">Tutorials</a>
                <ul>
                    <li><a href="#">Photoshop</a></li>
                    <li><a href="#">Illustrator</a></li>
                    <li>
                        <a href="#">Web Design</a>
                        <ul>
                            <li><a href="#">HTML</a></li>
                            <li><a href="#">CSS</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">Articles</a>
                <ul>
                    <li><a href="#">math</a></li>
                    <li><a href="#">Physics</a></li>
                </ul>
            </li>
            <li><a href="#">Inspiration</a></li>
        </ul>
    </nav>
</body>
</html>