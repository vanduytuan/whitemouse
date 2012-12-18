<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
        <meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
        <meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
        <link href="/css/styles_beta.css" rel="stylesheet">
        <link href="/js2/navig.css" rel="stylesheet">
        <link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
        <script language="javascript" type="text/javascript" src="/js/scripts.js"></script>
    </head>
    <body>
        <div id="wrapborder">
            <div id="wrap">
                <?php include 'php/include/header_beta.php'; ?>

                <div id="content">
                    <div id="contentl"><br>
                        <h1>More About WOVOdat</h1>
                        <p style="padding:0px 30px 0px 30px;">
                            WOVOdat is a Database of Volcanic Unrest; instrumentally and visually recorded changes in seismicity, ground deformation, gas emission, and other parameters from their normal baselines.    The database is created per the structure and format as described in the WOVOdat 1.0 report of Venezky and Newhall (USGS Openfile report 2007-1117), updated in WOVOdat 1.1 <a href="http://www.wovodat.org/doc/database/1.1/wovodat11_doc.pdf">(here)</a>.
                            <br><br>
                            Data are recorded first at stations, and stations for which we already have some data may be seen by clicking Volcano, above, and using the scroll-down menu to locate your favorite volcano. If the data from a station have physical significance by themselves, they are reported in WOVOdat in tables of Station data. Other data, e.g., earthquake hypocenters, are more meaningful when considered across a network. These are presented in WOVOdat as network data. Tables are organized mainly by the parameters which are measured, e.g., seismicity, ground deformation, etc.
                            <br><br>
                            Nearly all data in WOVOdat will be time-stamped and georeferenced, so that they can be studied in both space and time.<br>
                            WOVOdat stores mainly <span style="text-decoration:underline"> historical data</span>.  Active data that are younger than a 2-year grace period are generally not available, and they are still being used by Observatories and other contributors.   WOVOdat welcomes more current data, but it respects the prerogative of those who collect the data to have first option in interpretation and publication.
                            <br><br>
                        </p>
                        <br>
                    </div>
                    <div id="contentr">
                        <br><br><p align="right"><img src="/gif2/wdatschemaMay2011b.gif" width="410" height="250" alt="schema"></p>
                        <p style="padding:0px 20px 0px 10px;"><br><br>
                            Detail of WOVOdat data formats and tables are given under <a href="/doc/index.php"><b>Documentation</b></a>. Script for building your own MySQL WOVOdat-compatible database for monitoring data is available for free.<br>
                            Initially, we will also provide tools for simple graphical comparisons such as the RSAM plots on our homepage. Later we will add tools for Boolean searches and searches based on pattern recognition.
                        </p>
                    </div>
                </div>
                <?php include 'php/include/footer_main_beta.php'; ?>
            </div>
        </div> <!--end of wrapborder-->
    </body>
</html>



