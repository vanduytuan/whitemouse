<?php
session_start();
include "php/include/db_connect_view.php";  // connect to database
include "equakegmt.php";                    // call gmt function


echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
        <meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
        <meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
<link href="/css/styles_beta.css" rel="stylesheet">
<link href="/css/earthquakeStyle.css" rel="stylesheet" />

</head>
<body>
       <div id="wrapborder">
            <div id="wrap">
HTML;
     
include 'php/include/header_beta.php';
        
               
        echo'<div id="content">'; 
                    
	putenv("PATH=/bin:/usr/bin:/usr/lib/gmt/bin:/usr/lib/gmt/share:/usr/lib/gmt/lib:/usr/lib/gmt/include");
        putenv("GMTHOME=/usr/lib/gmt");

  
        # defines the public_html root directory (absolute path on the Apache server)
        $htmroot = "/var/phivolcsvmepd/public_html/vmep";

	# subdiretory name
          $outdir = 'output';  
	
        # basename for output files    
          $tmp = 'eq';  

        # created a temporary and unique directory
          $name = uniqid();

        # get the visualization type, 2D or 3D, rotation degree
         $visualType = $_GET['visual_type'];
         $initial_value= $_GET['init_azim'];  
	    
	    
        if(isset($visualType)) {	    
	    
            $updatedAzim= $_GET['degree'];  
            
            $wovodir = ($visualType == "3D") ? "wovodat3D" : "wovodat2D";
            $tmpdir = "$htmroot/$outdir/$wovodir.$name";
            $htmout = "/$outdir/$wovodir.$name";
            mkdir ($tmpdir);

	    # timestamp text      
            $stamp = "by WOVOdat/EOS"; 

            # get parameters
            $vd_id = $_GET['vd_id'];
            $qty = $_GET['qty'];
            if ($qty) {
                $limit = " limit $qty";
            }
            else $limit = "";

            $date_start = $_GET['date_start'];
            $date_end = $_GET['date_end'];
            $dr_start = $_GET['dr_start'];
            $dr_end = $_GET['dr_end'];
            $eqtype = $_GET['eqtype'];
            if ($date_start && $date_end) {
                $startDate = split('/', $date_start);
                $endDate = split('/', $date_end);
                $dates = " and c.sd_evn_time BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";
            }

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

            

            # delete files older than 1 hour
            exec("find $htmroot/$outdir -name 'wovodat.*' \! \\( -newerct '1 hour ago' \\) | xargs rm -rf");  
            #exec("rm -rf $htmroot/$outdir/w*");

            # SQL query: get the volcano position Lat/Lon, volcano name
            $sql_statement = "SELECT vd_inf.vd_inf_slat, vd_inf.vd_inf_slon, vd.vd_name FROM vd, vd_inf WHERE vd.vd_id = vd_inf.vd_id AND vd_inf.vd_id =  '$vd_id'";
            $query = mysql_query($sql_statement,$link);
            $vd_latlon = mysql_fetch_assoc($query);


            # SQL query: get the data (approximate selection from map width)
            $sql_statement ="(select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM sn b, sd_evn c, vd_inf d WHERE b.sn_id = c.sn_id AND b.vd_id=d.vd_id AND d.vd_id = '$vd_id' $dates $depth $quaketype ORDER BY (sd_evn_time) DESC $limit) UNION (select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM jj_volnet a, sn b, sd_evn c, vd_inf d WHERE a.vd_id = '$vd_id' AND a.jj_net_id = b.sn_id AND b.sn_id = c.sn_id AND d.vd_id = '$vd_id' AND a.jj_net_flag = 'S' $dates $depth $quaketype AND (sqrt(power(d.vd_inf_slat - c.sd_evn_elat, 2) + power(d.vd_inf_slon - c.sd_evn_elon, 2))*111)<=1.5*$wkm ORDER BY (sd_evn_time) DESC $limit)";
            $query = mysql_query($sql_statement,$link);

            # writes the data into a single file
            $nb = 0;

            $fh = fopen("$tmpdir/$tmp.txt",'w') or die("can't open file for writing txt file <br/>");

            while( $row = mysql_fetch_assoc($query) ) {
                fwrite($fh,join(',',$row)."\n");
                $nb++;
            }
            fclose($fh);

            $J = 74*20/$wkm;	# Jm scale (normalized with map width)
            $ldep = 20;	# max depth for profiles (km)
   	    
   	    $title =$vd_latlon['vd_name']."($nb events)";
            $vlon = $vd_latlon['vd_inf_slon'];
            $vlat = $vd_latlon['vd_inf_slat'];
            $kmlat = 6370*deg2rad(1);	# length of a latitude degree (in km)
            $kmlon = $kmlat*cos(deg2rad($vlat));# length of a longitude degree at the volcano latitude (in km)
            $lon1 = ($vlon-0.5*$wkm/$kmlon);
            $lon2 = ($vlon+0.5*$wkm/$kmlon);
            $lat1 = ($vlat-0.5*$wkm/$kmlat);
            $lat2 = ($vlat+0.5*$wkm/$kmlat);
            $Rll = "-R$lon1/$lon2/$lat1/$lat2";
            $slat = ($vlat-0.44*$wkm/$kmlat);	# latitude position of km scale
            $Jlat = $J*$kmlon/$kmlat;
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


            if ( $visualType == "2D" ) {
                visual2D();
                
   				echo "<br/><form name=\"back\" action=\"equake3d_view.php\" method=\"get\">";
				echo"<input type=\"submit\" value=\"Back to previous page\" >";
				echo"</form>";               
                echo"<p align=\"center\" style=\"height:500px;\">";
                echo"<a href=\"$htmout/$imageFile\"><img height=\"680\" width=\"508\" src=\"$htmout/$imageSrc\"></a>"; 
                echo"</p><br/><br/><br/>";

            }
            else {
		visual3D();
	    }


        }	  // this block works only first time for GMT 



  if($visualType != "2D"){   // for 3D only. Get all images from respective image folder 
      
        
        $updatedAzim=$_GET['degree'];
        
        $initial_value = $_SESSION['init_azim']; 
  
        
        $lastimageno=floor((360-$initial_value)/$updatedAzim);            //Get total images number
       //$lastimageno=$lastimageno+2;                     // because the image starts from zero

       $lastimageno=$lastimageno+1;                     // because the image starts from zero
         
        if(isset($_GET['dir'])){
                $htmout=$_GET['dir'];
        }

        if(isset($_GET['init_azim'])){   // for first time & auto 
                           
              $lflag= $_GET['lflag'];
              $rflag= $_GET['rflag'];
           
              $initialimage="frame_000000.jpg";    // This is the default image name is generated by GMT

        }
        else if(isset($_GET['auto'])){   // for first time & auto 
                           
              $lflag= $_GET['lflag'];
              $rflag= $_GET['rflag'];
           
              $initialimage=$tmp.".gif";  

        }
        else if(isset($_GET['rflagon'])){    // for right button 
        
                $rflag= $_GET['rflag'];
              
                if($rflag < $lastimageno){
                        $rflag = sprintf ('%03d', $rflag +1);  
                        
                      
                }else{
                        $rflag = sprintf ('%03d', 0);     
                          
                }
            
                $lflag= $rflag;
                $initialimage="frame_000".$rflag.".jpg";  
                
             
        }
        else if(isset($_GET['lflagon'])){         // for left button 
        
                $lflag= $_GET['lflag'];
                $rflag= $_GET['rflag'];
              
                if(($lflag > 0) && ($lflag < $lastimageno)){
                        $lflag = sprintf ('%03d', $lflag -1);
                         $initialimage="frame_000".$lflag.".jpg";  
                 
                           $rflag= $lflag;   
                       
                
                }else if($lflag == $lastimageno){
                
                       
                        $lflag = sprintf ('%03d', $lflag -1);         
                        $initialimage="frame_000".$lflag.".jpg";      
                        $rflag= $lflag;   
                        
                }else {                 // zero
                
                      
                        $lflag = sprintf ('%03d', $lastimageno);    
                        $initialimage="frame_000".$lflag.".jpg";     
                        $rflag= $lflag;   
                     
                }
           
        }

       
        echo "<br/><form name='back' action='equake3d_view.php' method='get'><input type='submit' value='Back to previous page'/></form>";
        echo"<p align=\"center\" style=\"padding-top:10px;font-weight:bold;\"> {$_SESSION['volinfo']} </p>";
        echo"<p align=\"center\">";
        echo"<a href=\"$htmout/$initialimage\"><img height=\"780\" width=\"700\" src=\"$htmout/$initialimage\">";
        echo"</a></p>";	
	
echo <<<HTML


    <table cellpadding="0" cellspacing="0" style="padding-left:280px;">
        <tr><td>
                <form id="leftbtnform" name="leftbtnform" action="equake3d.php" method="get">
                <input type="hidden" name="lflagon" value="1">
                <input type="hidden" name="dir" value="$htmout">
                <input type="hidden" name="lflag" value="$lflag">
                <input type="hidden" name="rflag" value="$rflag">
                <input type="hidden" name="degree" value="$updatedAzim"> 
                <input type="Submit" id="leftbut" name="leftbut" value="" title="click to decrease degree">
                </form>     
        </td><td style="padding-left:100px;">
   
                <form id="autoform" name="autoform" action="equake3d.php" method="get">
                <input type="hidden" name="dir" value="$htmout">
                <input type="hidden" name="lflag" value="$lflag">
                <input type="hidden" name="rflag" value="$rflag">
                <input type="hidden" name="degree" value="$updatedAzim">
                <input type="Submit" id="auto" name="auto" value="Animation" />
                </form>
      
        </td><td style="padding-left:100px;">

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

       </div>

        <!-- Footer -->
        <div id="footer">
HTML;
          
       include 'php/include/footer_main_beta.php';
echo <<<HTML
        </div>

    </div></div>
    </body>
</html>
HTML;
?>
