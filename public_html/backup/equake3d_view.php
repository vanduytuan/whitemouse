<?php
session_start();
include "php/include/db_connect_view.php";  // connect to database
//whenever come to this page, it will delet "all image folders under output" and "unset session related to this 2D/3D" 
// For Linux
// $imagefolder = "/var/phivolcsvmepd/public_html/vmep/output/";
// For Windows
$imagefolder = "C:\\xampp\\htdocs\\public_html\\wovodat\\output";

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
            array_push($files,$subfolder_array[$i]);             // Get all image folders without '.' & '..'
        }
    }
    for($i = 0; $i < sizeof($files) ; $i++){
        
        // For Linux
        // $imagefolder2 = "/var/phivolcsvmepd/public_html/vmep/output/" . $files[$i] . "/";         //Delete one array room per one time
        // For Windows
        $imagefolder2 = "C:\\xampp\\htdocs\\public_html\\wovodat\\output" . $files[$i] . "\\";
        echo $imagefolder2 . "<br/>";
//        foreach (glob($imagefolder2 . '*') as $file) {                //Delete all visible files
//            unlink($file);
//        }
//
//        foreach (glob($imagefolder2 . '.*') as $file) {             // Delete all hidden/invisible files
//            unlink($file);
//        }
//
//        if (substr($files[$i], 0, 7) == 'wovodat') {
//
//            rmdir($imagefolder2);                          // Now can delete folder after deleting all sub-files.
//        }
    }
}


unset($_SESSION['vd_name']);
unset($_SESSION['lon']);
unset($_SESSION['lat']);
unset($_SESSION['init_azim']);
unset($_SESSION['volinfo']);

//finish deleting all image folders and unset session related to this 2D/3D        
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
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
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
                                        <option value="3D">3D</option>
                                    </select>
                                </div>            
                            </div>
                            <div class="row">
                                <div class="leftside">Initial Azimuth (in degree):</div>
                                <div class="rightside"><input type="text" id="init_azim" name="init_azim" size="5" value="" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Rotation Degree (for 3D):</div>
                                <div class="rightside"><input type="text" id="degree" name="degree" size="5" value="" style="width:180px;"></div>
                            </div>	    	
                            <div class="row">
                                <div class="leftside">Map width (km):</div>
                                <div class="rightside"><input type="text" id="map_width" name="map_width" size="4" value="" style="width:180px;"></div>
                            </div>
                            <div class="row">
                                <div class="leftside">Max. nb of events:</div>
                                <div class="rightside"><input type="text" id="qty" name="qty" size="5" value="" style="width:180px;"></div>
                            </div>
                            <br/>
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

