<?php
session_start();
include "php/include/db_connect_view.php";  // connect to database
//whenever come to this page, it will delet "all image folders under output" and "unset session related to this 2D/3D" 
// For Linux
$imagefolder = "/var/www/wovodat/public_html/wovodat/output/";

$dh = opendir($imagefolder);
if ($dh) {
    $subfolder = "";
    $subfolder_array = Array();
// Get all subfolers here
    while (false !== ($subfolder = readdir($dh)))
        array_push($subfolder_array, $subfolder);

    $files = Array();
    for ($i = 0; $i < sizeof($subfolder_array); $i++) {
        if ($subfolder_array[$i] != '.' && $subfolder_array[$i] != '..') {
            array_push($files, $subfolder_array[$i]);             // Get all image folders without '.' & '..'
        }
    }
    for ($i = 0; $i < sizeof($files); $i++) {
        // The format of the output folder for our images is 'wovodat*'.
        // Therefore, I will ignore other folder.
        if (substr($files[$i], 0, 7) != 'wovodat')
            continue;
        // Delete one array room per one time
        // For Linux
        // $imagefolder2 = "/var/phivolcsvmepd/public_html/vmep/output/" . $files[$i] . "/";        
        // For Windows
        $imagefolder2 = $imagefolder . $files[$i] . "/";
        // Delete all visible files
        foreach (glob($imagefolder2 . '*') as $file)
            unlink($file);
        // Delete all hidden/invisible files
        foreach (glob($imagefolder2 .'.' . 'gmt*') as $file) {
            // we ignore the file name '.' and '..' as they are the way that 
            // Windows will use to go back to previous level
            unlink($file);
        }
        rmdir($imagefolder2);
    }
}

/*
 * Delete any previous sessions variable
 */
unset($_SESSION['vd_name']);
unset($_SESSION['lon']);
unset($_SESSION['lat']);
unset($_SESSION['init_azim']);
unset($_SESSION['volinfo']);


//This part is to show all the input fields
$sql_statement = "select vd_id,vd_name from vd";
$query = mysql_query($sql_statement, $link);
while ($row = mysql_fetch_assoc($query)) {
    $data[$row['vd_id']] = $row['vd_name'];
}

asort($data);    // sort the volcano according to the volcano name
?>


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
        <script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="/js/jquery.defaultvalue.js"></script>

        <script type="text/javascript">

            $(document).ready(function(){

                $('#init_azim').defaultValue('100');
                $('#degree').defaultValue('50');
                $('#map_width').defaultValue('20');
                $('#qty').defaultValue('1000');
            });
        </script>

    </head>
    <body>
        <div id="wrapborder">
            <div id="wrap">
                <?php include 'php/include/header_beta.php'; ?>
                <!-- Content -->
                <div id="content">


                    <div id="contentl"><br/><br/>
                        <div style="text-align:center;font-size:12px;padding-bottom:15px;font-weight:bold;">2D/3D of Earthquake hypocenter</div>	
                        <!-- Top of the page -->
                        <!-- Page content -->
                        <form name="form" action="equake3d.php" method="get">
                            <div class="row">
                                <div class="leftside">Select volcano:</div>
                                <div class="rightside">
                                    <select name="vd_id" size="1" style="width:180px;">
                                        <?php
                                        foreach ($data as $key => $value) {
                                            ?>
                                            <option value="<?php echo $key;?>" <?php  if($key == 972) echo "selected"?>><?php echo $value; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>
                            <div class="row">
                                <div class="leftside">Visualization (2D or 3D)</div>
                                <div class="rightside">
                                    <select name="visual_type" size="1" style="width:180px;">
                                        <option value="2D">2D</option>
                                        <option value="3D" selected='selected'>3D</option>
                                    </select>
                                </div>            
                            </div>
                            <div class="row">
                                <div class="leftside">Initial Azimuth (in degree):</div>
                                <div class="rightside"><input type="text" id="init_azim" name="init_azim" size="5" value="10" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Rotation Degree (for 3D):</div>
                                <div class="rightside"><input type="text" id="degree" name="degree" size="5" value="30" style="width:180px;"></div>
                            </div>	    	
                            <div class="row">
                                <div class="leftside">Map width (km):</div>
                                <div class="rightside"><input type="text" id="map_width" name="map_width" size="4" value="10" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Max. nb of events:</div>
                                <div class="rightside"><input type="text" id="qty" name="qty" size="5" value="100" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Start date:</div>
                                <div class="rightside"><input type="text" id="date_start" name="date_start" size="5" value="1/1/1990" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">End date:</div>
                                <div class="rightside"><input type="text" id="date_end" name="date_end" size="5" value="1/1/2012" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Dr start:</div>
                                <div class="rightside"><input type="text" id="dr_start" name="dr_start" size="5" value="0" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Dr end:</div>
                                <div class="rightside"><input type="text" id="dr_end" name="dr_end" size="5" value="0" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Earthquake type:</div>
                                <div class="rightside"><input type="text" id="eqtype" name="eqtype" size="5" value="" style="width:180px;"></div>
                            </div>
                            <div style="text-align:center;"><input type="submit" value="Submit"/></div>
                        </form>
                    </div>
                    <div id="contentr">

                    </div>
                </div>

                <!-- Footer -->
                <div id="footer">
                    <?php include 'php/include/footer_main_beta.php'; ?>
                </div>

            </div></div>
    </body>
</html>

