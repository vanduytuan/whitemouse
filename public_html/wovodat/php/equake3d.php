<link href="/css/earthquakeStyle.css" rel="stylesheet">
<?php
// connect to database
include "php/include/db_connect_view.php";

// helper functions for GMT
include "equakegmt.php";

// This path is important for GMT to work, please change this path into where you put it in the main server
putenv("PATH=/bin:/usr/bin:/usr/local/gmt440/bin:/usr/local/gmt440/share:/usr/local/gmt440/lib:/usr/local/gmt440/include");
putenv("GMTHOME=/usr/local/gmt440");

# defines the public_html root directory (absolute path on the Apache server)
$htmroot = "/var/www/wovodat/public_html/wovodat";
//$htmroot = "C:/xampp/htdocs/public_html/wovodat";
# subdiretory name

$outdir = 'output';

# basename for output files    
$tmp = 'eq';

# created a temporary and unique directory
$name = uniqid();

# get the visualization type, 2D or 3D, rotation degree
$visualType = $_GET['visual_type'];
$initial_value = $_GET['init_azim'];
if (isset($visualType)) {
    $updatedAzim = $_GET['degree'];

    $wovodir = ($visualType == "3D") ? "wovodat3D" : "wovodat2D";
    //$tmpdir = "$htmroot/$outdir/$wovodir.$name";
    $tmpdir = "$htmroot/$outdir/$wovodir.$name";

    //$htmout = "/$outdir/$wovodir.$name";
    $htmout = "/$outdir/$wovodir.$name";
    mkdir($tmpdir);

    # timestamp text      
    $stamp = "by WOVOdat/EOS";

    # get parameters
    $vd_id = $_GET['vd_id'];
    $qty = $_GET['qty'];
    if ($qty) {
        $limit = " limit $qty";
    }
    else
        $limit = "";

    $date_start = $_GET['date_start'];
    $date_end = $_GET['date_end'];
    $dr_start = $_GET['dr_start'];
    $dr_end = $_GET['dr_end'];
    $eqtype = $_GET['eqtype'];
    if ($date_start && $date_end) {
        $startDate = preg_split('/\//', $date_start);
        $endDate = preg_split('/\//', $date_end);
        $dates = " and c.sd_evn_time BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";
    }
    $quaketype = "";
    if ($eqtype) {
        $quaketype = " and sd_evn_eqtype = $eqtype ";
    }
    if ($dr_start && $dr_end) {
        $depth = " and c.sd_evn_edep BETWEEN $dr_start AND $dr_end ";
    }

    $wkm = $_GET['map_width'];
    if ($wkm == "") {
        $wkm = 20;
    }

    $cavw = $o['cavw'];
    # delete files older than 1 hour
    //exec("find $htmroot/$outdir -name 'wovodat.*' \! \\( -newerct '1 hour ago' \\) | xargs rm -rf");
    #exec("rm -rf $htmroot/$outdir/w*");
    # SQL query: get the volcano position Lat/Lon, volcano name
    $sql_statement = "select vd_id from vd where vd_cavw = '$cavw'";
    $query = mysql_query($sql_statement);
    $vd_id = mysql_fetch_array($query);
    $vd_id = $vd_id[0];
    $sql_statement = "SELECT vd_inf.vd_inf_slat, vd_inf.vd_inf_slon, vd.vd_name FROM vd, vd_inf WHERE vd.vd_id = vd_inf.vd_id AND vd_inf.vd_id =  '$vd_id'";
    $query = mysql_query($sql_statement, $link);
    $vd_latlon = mysql_fetch_assoc($query);

    $dates = "";
    $depth = "";
    $quaketype = "";


    # SQL query: get the data (approximate selection from map width)
    $sql_statement = "(select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, 
    c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM sn b, sd_evn c, vd_inf d WHERE 
    b.sn_id = c.sn_id AND b.vd_id=d.vd_id AND d.vd_id = '$vd_id' $dates $depth $quaketype ORDER BY 
    (sd_evn_time) DESC $limit) UNION (select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, 
    c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM jj_volnet a, 
    sn b, sd_evn c, vd_inf d WHERE a.vd_id = '$vd_id' AND a.jj_net_id = b.sn_id AND b.sn_id = c.sn_id 
    AND d.vd_id = '$vd_id' AND a.jj_net_flag = 'S' $dates $depth $quaketype AND 
    (sqrt(power(d.vd_inf_slat - c.sd_evn_elat, 2) + power(d.vd_inf_slon - c.sd_evn_elon, 2))*111)<=1.5*$wkm 
    ORDER BY (sd_evn_time) DESC $limit)";
    $query = mysql_query($sql_statement, $link);

    # writes the data into a single file
    $nb = 0;

    //$fh = fopen("$tmpdir/$tmp.txt", 'w') or die("can't open file for writing txt file <br/>");
    $fh = fopen("$tmpdir/$tmp.txt", 'w') or die("can't open file for writing txt file <br/>");
    while ($row = mysql_fetch_assoc($query)) {
        fwrite($fh, join(',', $row) . "\n");
        $nb++;
    }
    fclose($fh);

    $J = 74 * 20 / $wkm; # Jm scale (normalized with map width)
    $ldep = 20; # max depth for profiles (km)

    $title = $vd_latlon['vd_name'] . "($nb events)";
    $vlon = $vd_latlon['vd_inf_slon'];
    $vlat = $vd_latlon['vd_inf_slat'];
    $kmlat = 6370 * deg2rad(1); # length of a latitude degree (in km)
    $kmlon = $kmlat * cos(deg2rad($vlat)); # length of a longitude degree at the volcano latitude (in km)
    $lon1 = ($vlon - 0.5 * $wkm / $kmlon);
    $lon2 = ($vlon + 0.5 * $wkm / $kmlon);
    $lat1 = ($vlat - 0.5 * $wkm / $kmlat);
    $lat2 = ($vlat + 0.5 * $wkm / $kmlat);
    $Rll = "-R$lon1/$lon2/$lat1/$lat2";
    $slat = ($vlat - 0.44 * $wkm / $kmlat); # latitude position of km scale
    $Jlat = $J * $kmlon / $kmlat;
    $Jlon = $J;
    $box = "'0 0\n1 0\n1 -1\n0 -1\n0 0\n'";

    $_SESSION['vd_name'] = $vd_latlon['vd_name'];
    $_SESSION['lon'] = $vd_latlon['vd_inf_slon'];
    $_SESSION['lat'] = $vd_latlon['vd_inf_slat'];
    $_SESSION['init_azim'] = $_GET['init_azim'];
    $_SESSION['volinfo'] = $title;

    # default to the 2D ".ps" and ".png" files.
    # They will be updated when visual type is in 3D.
    $imageFile = $tmp . ".ps";
    $imageSrc = $tmp . ".png";


    if ($visualType == "2D") {
        visual2D();

        echo "<br/><form name=\"back\" action=\"equake3d_view.php\" method=\"get\">";
        echo"<input type=\"submit\" value=\"Back to previous page\" >";
        echo"</form>";
        echo"<p align=\"center\" style=\"height:500px;\">";
        echo"<a href=\"$htmout/$imageFile\"><img height=\"680\" width=\"508\" src=\"$htmout/$imageSrc\"></a>";
        echo"</p><br/><br/><br/>";
    } else {
        $minDep = 0; // min depth (km)
        $maxDep = 50; // max depth (km)
        $zMin = (-1 * $maxDep);
        $zMax = (-1 * $minDep);
        $dx = ($lon2 - ($lon1));
        $dy = ($lat2 - ($lat1));
        $dz = ($maxDep - $minDep);
        $anno_x = $dx / 5; // Set 4 tickmarks for longitude 
        $anno_y = $dy / 5;  // Set 6 tickmarks for latitude
        $Rll_3D = "-R$lon1/$lon2/$lat1/$lat2/$zMin/$zMax";


        // The numbers, 0.06, 0.12, 0.18,..., are the drawing size
        // of the symbol. They are obtained by magnitude * 0.04.
        // e.g: magnitude is 2, symbol_size = 2*0.06 = 0.08
        $legend = array(
            "0" => "H 10 1  Magnitude",
            "1" => "D 0 1p",
            "2" => "N 5",
            "3" => "V 0 1p",
            "4" => "S 0.6c c 0.16c - 0.3p 1c M 1",
            "5" => "S 0.6c c 0.32c - 0.3p 1c M 2",
            "6" => "S 0.6c c 0.48c - 0.3p 1c M 3",
            "7" => "S 0.6c c 0.64c - 0.3p 1c M 4",
            "8" => "S 0.6c c 0.8c - 0.35p 1c M > 4",
            "11" => "V 0 1p ",
        );

        $newLegend = implode("\n", $legend);
        $fh = fopen("$tmpdir/gmt.legend", "w");
        fwrite($fh, $newLegend);
        fclose($fh);

        $gmtLegend = "$tmpdir/gmt.legend";
        $dpi = 100;
        $elev = 25;
        $fh = fopen("$tmpdir/$tmp.gmt", 'w') or die("can't open file for writing gmt file <br/>");

        // GMT set parameters
        fwrite($fh, ". gmt_shell_functions.sh\n");
        //fwrite($fh,"gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh, "gmtset PAPER_MEDIA=Custom_570x570 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh, "gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n");
        fwrite($fh, "gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n");
        fwrite($fh, "gmtset PAGE_COLOR=243/255/237 \n");
        fwrite($fh, "gmtset CHAR_ENCODING ISOLatin1+\n");

        // makes colormap
        fwrite($fh, "makecpt -Crainbow -T$zMin/$zMax/5 -Z > $tmp.cpt\n");

        // generate frames
        fwrite($fh, "frame=0\n");
        fwrite($fh, "name=\"frame\"\n");

        // $initial_value is given by the user. 
        fwrite($fh, "azim=$initial_value\n");

        fwrite($fh, "exitFlag=0\n");

        fwrite($fh, "while ((\$azim <= 360)); do \n");

        fwrite($fh, "file=`gmt_set_framename \$name \$frame ` \n");


        // $3 is sd_evn_elon, $2 is sd_evn_elat, 
        // $4 is sd_evn_edep, $5 is sd_evn_pmag.  
        //
    // Note: 1. The symbol size for magnitude 1 is 0.06i. So, 
        //          "0.06*$5" is the symbol size for magnitude specified
        //          by $5. . The symbol size is used to show the level of 
        //          magnitude. Also see the function createLegend().
        //       2. if sd_evn_pmag <= 0, assign the symbol size to 0.04. 
        fwrite($fh, "awk -F , '{if (\$5<=1) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16} else if (\$5>=5) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.7 } else {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16*\$5}}' $tmp.txt | psxyz $Rll_3D -JM3.5i -JZ3.5i -E\$azim/$elev -B$anno_x/$anno_y/10::wsneZ+ -Sc -C$tmp.cpt -Wthinnest -X2i -Y2.5i -K -P  > $tmp.ps\n");


        // Draw the red N-directional sign.
        // posX/posY is the position to draw the N-directional sign.
        $posX = ($lon2 - 0.03);
        $posY = ($lat1 + ($lat2 - ($lat1)) / 2);
        fwrite($fh, "psbasemap $Rll -J -E\$azim/$elev -T$posX/$posY/1i --COLOR_BACKGROUND=red --TICK_PEN=thinner,black -O -K >> $tmp.ps\n");


        // Draw the lon/lat/depth (km) distant scale bar. 
        $deltaX = (($lon2 - ($lon1)) / 2);
        $sBar_x = ($lon1 + $deltaX);
        $sBar_y = ($lat1 - 0.03);

        fwrite($fh, "psbasemap -R -J -O -K -E\$azim/$elev -Lf$sBar_x/$sBar_y/$sBar_y/20k --TICK_PEN=thinner,black  >> $tmp.ps\n");

        // Draw the color scale for DEPTH
        fwrite($fh, "psscale -C$tmp.cpt -D2i/-1.0i/4.5i/0.2ih -O -K -Ac -B5:DEPTH:/:km: -E >> $tmp.ps\n");

        // Draw the magnitude scale. 
        fwrite($fh, "pslegend -Dx2i/-1.8i/6.0i/0.575i/TC -J -R -O -F gmt.legend -Glightyellow >> $tmp.ps\n");

        // Convert ps to tif.
        fwrite($fh, "ps2raster $tmp.ps -Tj -E$dpi\n");
        fwrite($fh, "mv $tmp.jpg \$file.jpg\n");

        fwrite($fh, "frame=`gmt_set_framenext \$frame`\n");
        fwrite($fh, "((azim += $updatedAzim))\n");

        // Draw the frame for remaining angle.
        fwrite($fh, "if (( (\$azim > 360) && (\$exitFlag==0) ));\n");
        fwrite($fh, "then\nazim=360\nexitFlag=1 \nfi\n");

        fwrite($fh, "done \n"); # end of while loop
        // 100 ticks per second. 3000 ticks == 30s
        // The total display time is 30s.
        // The $delay is the pause between each frame.
        $displaytime = 3000;
        $delay = ($displaytime * $updatedAzim / 360);

        // makes tif from gif file
        fwrite($fh, "convert -delay $delay  *.jpg $tmp.gif\n");

        fclose($fh);
        // execute the script
        passthru("cd $tmpdir ;  bash $tmp.gmt");

        $imageFile = $tmp . ".gif";
        $imageSrc = $imageFile;
    }
}   // this block works only first time for GMT 



if ($visualType != "2D") {   // for 3D only. Get all images from respective image folder 
    $updatedAzim = $_GET['degree'];
    $initial_value = $_SESSION['init_azim'];
    $lastimageno = floor((360 - $initial_value) / $updatedAzim);            //Get total images number
    //$lastimageno=$lastimageno+2;                     // because the image starts from zero

    $lastimageno = $lastimageno + 1;                     // because the image starts from zero

    if (isset($_GET['dir'])) {
        $htmout = $_GET['dir'];
    }

    if (isset($_GET['init_azim'])) {   // for first time & auto 
        $lflag = $_GET['lflag'];
        $rflag = $_GET['rflag'];

        $initialimage = "frame_000000.jpg";    // This is the default image name is generated by GMT
    } else if (isset($_GET['auto'])) {   // for first time & auto 
        $lflag = $_GET['lflag'];
        $rflag = $_GET['rflag'];

        $initialimage = $tmp . ".gif";
    } else if (isset($_GET['rflagon'])) {    // for right button 
        $rflag = $_GET['rflag'];

        if ($rflag < $lastimageno) {
            $rflag = sprintf('%03d', $rflag + 1);
        } else {
            $rflag = sprintf('%03d', 0);
        }

        $lflag = $rflag;
        $initialimage = "frame_000" . $rflag . ".jpg";
    } else if (isset($_GET['lflagon'])) {         // for left button 
        $lflag = $_GET['lflag'];
        $rflag = $_GET['rflag'];

        if (($lflag > 0) && ($lflag < $lastimageno)) {
            $lflag = sprintf('%03d', $lflag - 1);
            $initialimage = "frame_000" . $lflag . ".jpg";

            $rflag = $lflag;
        } else if ($lflag == $lastimageno) {


            $lflag = sprintf('%03d', $lflag - 1);
            $initialimage = "frame_000" . $lflag . ".jpg";
            $rflag = $lflag;
        } else {                 // zero
            $lflag = sprintf('%03d', $lastimageno);
            $initialimage = "frame_000" . $lflag . ".jpg";
            $rflag = $lflag;
        }
    }

    echo"<p align=\"center\" style=\"padding-top:10px;font-weight:bold;\"> {$_SESSION['volinfo']} </p>";
    echo"<p align=\"center\">";
    echo"<a href=\"$htmout/$initialimage\"><img height=\"480\" width=\"400\" src=\"$htmout/$initialimage\">";
    echo"</a></p>";

    echo <<<HTML


    <table cellpadding="0" cellspacing="0">
        <tr><td>
                <form id="leftbtnform" name="leftbtnform" action="equake3d.php" method="get">
                <input type="hidden" name="lflagon" value="1">
                <input type="hidden" name="dir" value="$htmout">
                <input type="hidden" name="lflag" value="$lflag">
                <input type="hidden" name="rflag" value="$rflag">
                <input type="hidden" name="degree" value="$updatedAzim"> 
                <input type="Submit" id="leftbut" name="leftbut" value="" title="click to decrease degree">
                </form>     
        </td><td>
   
                <form id="autoform" name="autoform" action="equake3d.php" method="get">
                <input type="hidden" name="dir" value="$htmout">
                <input type="hidden" name="lflag" value="$lflag">
                <input type="hidden" name="rflag" value="$rflag">
                <input type="hidden" name="degree" value="$updatedAzim">
                <input type="Submit" id="auto" name="auto" value="Animation" />
                </form>
      
        </td><td>

                <form id="rightbutform" name="rightbutform" action="equake3d.php" method="get">
                <input type="hidden" name="rflagon" value="1">
                <input type="hidden" name="dir" value="$htmout">
                <input type="hidden" name="lflag" value="$lflag">
                <input type="hidden" name="rflag" value="$rflag">
                <input type="hidden" name="degree" value="$updatedAzim">
                <input type="Submit" id="rightbut" name="rightbut" value="" title="click to increase degree">
                </span>
                </form>

        </td></tr></table><br/>  
HTML;
}




echo <<<HTML
        Available outputs: <a href="$htmout/$imageFile">GIF image file</a>, 
        <a href="$htmout/$tmp.txt">ASCII data file</a>,
        <a href="$htmout/$tmp.gmt">GMT script file</a><br>
	Volcano name = {$_SESSION['vd_name']}<br/>
	Latitude = {$_SESSION['lat']}  <br/>
	Lontitude= {$_SESSION['lon']}  <br/>
	<br/>
HTML;
?>




