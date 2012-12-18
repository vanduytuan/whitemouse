<!--
index_unrest_devel.php

in <head>
the sourse of google maps api had been changed to version 3, 
-->
<?php
// Start session
	session_start();
	// Regenerate session ID
	session_regenerate_id(true);
	$uname="";
// If session was already started
	if (isset($_SESSION['login'])) {
		// Get login ID and user name
		$uname=$_SESSION['login']['cr_uname'];}
?>
	
<!DOCTYPE html>
<html>
<head>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes">
	<link href="/css/styles_beta.css" rel="stylesheet">
	<link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
  <!--library for creating the icon on the google map-->	
	<script language="JavaScript" src="http://gmaps-utility-library.googlecode.com/svn/trunk/mapiconmaker/1.1/src/mapiconmaker.js"></script>
  <!-- bread crum (use this line for production environment  -->
  
  <!--google maps api version 3-->
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<!--Here is to make google maps api version 3 as refrence
	and here can add the version of google maps api, because there are 9 versions of google maps in version 3.
	and also if you want to make this page to support the mobile device, you can give the sensor value to "trure"
	moreover, you can choose a special liberary in google maps api version 3-->

	<link type="text/css" href="/css/tooltip.css" rel="stylesheet" />
	
	<link type="text/css" href="/js/development-bundle/themes/base/jquery.ui.theme.css" rel="stylesheet"/>
	<link type="text/css" href="/js/development-bundle/themes/base/jquery.ui.base.css" rel="stylesheet"/>
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" />
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.button.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.dialog.js"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/excanvas.min.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.selection.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.navigate.js" language="javascript"></script>
	
	<script type="text/javascript" src="/js/flot/jquery.flot.symbol.js"></script>	
	<script type="text/javascript" src="/js/scripts.js" language="javascript"></script>	
	
	<!--this is Tooltip-->
	<script type="text/javascript" src="/js/Tooltip_v3.js" language="javascript"></script>
	<!--the orignal tooltip.js and tooltip1.js had be changed, because there are some google maps api version 2 synatic, so i fixed it and make it fit to google maps api version 3-->
	<!--Tooltip end-->
	
	<script type="text/javascript" src="/js/GPSCharts_devel.js" language="JavaScript"></script>
	<!--the main change from here to begin, first go to GPSCharts_devel.js-->


<script type="text/javascript">
	$(document).ready(function(){
		document.getElementById('vd_name1').onchange();
	});
	
	function loadmap(){
		setupMaps(0,"1");
		volcanostatus1();
		secondvolcano();
		}
		
	function volcano1_selected(){
		setupMaps(0,"1");
		volcanostatus1();
		}
		
	function volcano2_selected(){
		setupMaps(0,"2");
		volcanostatus2();
		}
	function show_info(res){
		$('#viewcontent2').html(res);
	}
	function volcano_category_change(){
		var categ=$('#v_categ').attr('value');
		$.get('./selectVolOfCategory.php?kode='+categ, show_vulkan);
	}
	function show_vulkan(res){
		$('#volanocateg').html(res);
	}
</script>
</head>
<body onload="loadmap()"><!--Here add onload() action to load the two maps when first open developer page-->
	<div id="popupBox" title="Message from WOVOdat"></div>
	<div id="wrapborder_x">
		<div id="wrap_x">
			<!-- Header -->
			<?php include 'php/include/header_beta.php'; ?>
			<!-- Content -->
			<div id="content_x">
				<!-- Right panel-->
				<div id="contentrview_x">
				   	<div id="volselect">
							<div id="top" align="left">
								<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
								<p align="right">Login as: <b><?php print $uname; ?></b>|<a href='/populate/logout.php'>Logout</a></p>
				   		</div>
							<div style="height:25px;font-size:9px;">
								<span id="filLoading">Loading ...<img src="../gif2/loadinfo.net.gif"/></span>
							</div>
              <!-- display information about the volcano and other selection options -->
							<div style="margin-left:15px; display:table; width:198px;">
								<div>
									<div><b>Volcano1:</b></div>
									<div style="display:table-cell; width:150px; text-align:left;">
										<select id="vd_name1" onchange="volcano1_selected()" style="font-size:12px; width:158px;">
<?php						
									$nr=0;$unn="Unnamed";$status='Historical';$statusUnc='Uncertain';$statusNot='Not a Volcano';$statusUr='Uranium-series'; 
									include 'php/include/db_connect_view.php';
									$result = mysql_query("SELECT a.vd_id, a.vd_cavw, a.vd_name, a.cc_id FROM vd a, vd_inf b WHERE a.vd_cavw=b.vd_inf_cavw and SUBSTR(a.vd_name,1,7)!='$unn' and b.vd_inf_status!='$statusUnc' and b.vd_inf_status!='$statusNot' and b.vd_inf_status!='$statusUr' ORDER by a.vd_name");								
									$n_rand=mt_rand(5, 1200);	
									if ($n_rand<5) $n_rand=rand(5, 1200);
									if ($n_rand<5) $n_rand=10;
									while ($v_arr = mysql_fetch_array($result)) {
										$nr++;							
										if($nr==$n_rand && $n_rand!=0){		
											echo "<option value=\"$v_arr[0]\" selected=\"selected\">".$v_arr[2]."_".$v_arr[1]."</option>";								
											$information=$v_arr[3];
										}else{	
											echo "<option value=\"$v_arr[0]\">".$v_arr[2]."_".$v_arr[1]."</option>";
										}
									}
									mysql_data_seek($result,0); 
?>
										</select>
									</div>
								</div>
								<div>
									<div></div>
									<div id="status_1">
										<div id="volc1_info">.</div>
									</div>
								</div>
								<div id="volc1_left"></div>
								<div id="volc1_info2">.</div>
								
								<div id="showhide" style="display:inline; text-align:left;">
									<button id="reloadSta1" style="font-size:9px; border:#f3ffed;">Show All</button>
									<button id="removeSta1" style="font-size:9px; border:#f3ffed;">Hide</button>
									<br>
									<br>
								</div>
							</div>
 
              <!-- used to display the information about the second volcano for displaying -->
              <div id="searchmode" title="Searchmode" style=" display: table-row;">
								<div id="search1" style="width:90px; display: table-cell;">&nbsp</div>
								<div id="search2" style="float:right;width:90px; display: table-cell;">

								</div>
							</div>
						</div>  
 
              <!-- used to display the information about the second volcano for displaying -->
              <div id="volc_compare" style="margin-left:15px";>
							<div>
								<div>
									<div><b>Volcano2:</b></div>
									<div id="volanocateg">
										<select id="vd_name2"  onchange="volcano2_selected()" style="font-size:10px; width:158px;">
											<option value="volcanocateg">...</option>
										</select>
									</div>
								</div>
								<div style="display:table-row;">
									<div></div>
									<div id="status_2">
										<div id="volcano2_info">.</div>
									</div>
								</div>
								<div id="volc2_left" style="display:table-cell;"></div>
								<div id="volc2_info2">.</div>
								<div id="showhide2" style="display:inline; text-align:left;">
										<button id="reloadSta2" style="font-size:9px; border:#f3ffed;">Show All</button>
									<button id="removeSta2" style="font-size:9px; border:#f3ffed;">Hide</button>
								</div>
							</div>
						</div>

						<div id="volc_single">
							<div id="selectvolc">
							</div>
								<div id="contentrviewcontrol">
								<div id="stationcheck">
									<div id="staavailable"></div>
								</div>
							</div>
						</div>
					</div>
                    <!-- Left panel -->
                    <!-- display map and other information -->
                    <div id="contentlview_x" style="">
                        <!-- diplay list of symbol -->
                        <div id="legend">
                            <!-- Legend -->
                            <div id="map_legend" style="font-size:8px;margin-top: 10px;margin-bottom: 10px;text-align: center;">
                                <img src="/img/pin_ds.png" alt="" style="width:16px;height:16px;"/> Deformation
                                <img src="/img/pin_gs.png" alt="" style="width:16px;height:16px;"/> Gas
                                <img src="/img/pin_hs.png" alt="" style="width:16px;height:16px;"/> Hydrologic
                                <img src="/img/pin_ss.png" alt="" style="width:16px;height:16px;"/> Seismic
                                <img src="/img/pin_ts.png" alt="" style="width:16px;height:16px;"/> Thermal
                            </div>
                        </div>

                        <!-- Display -->
                        <div id="mapsite" style="display:table-row;">
                            <div id="map1" style="width:345px; display:table-cell; text-align: left;">
                                <div id="bigmap1" style="width:407px; height:220px;"></div>
                                <div id="gotogvp1" style="padding-right:10px;"><button id="go2gvp1" style="border:none;background:none;font-size:9px;float:right;">click to GVP</button></div><br>
                                <div id="chronoholder1" style="background:none; width:345px; height:10px;" title="Chronology1">.</div>

                                <div id="spatial1" >
                                    <div id="displayeq1">
                                        <span><button id="reloadEqk1" style="font-size:9px;">Display Equake</button></span>
                                        <a href="#" onclick="return false;" id="plfil1">Use Filters</a>
                                        <a href="#" onclick="return false;" id="minfil1">Hide Filters</a>
                                    </div>
                                </div>
                                <div id="filterSS1" title="Filter Maps">
                                    <table width='100%' >
                                        <input type="hidden" id="filter1" value="0" />
                                        <tr>
                                            <td>Events: </td>
                                            <td align=right>
                                                <select id="events1">
                                                    <option>100</option>
                                                    <option>200</option>
                                                    <option>300</option>
                                                    <option>400</option>
                                                    <option>500</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Start Date: </td>
                                            <td align=right>
												Day<select id="ss_d1">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												<option>13</option>
												<option>14</option>
												<option>15</option>
												<option>16</option>
												<option>17</option>
												<option>18</option>
												<option>19</option>
												<option>20</option>
												<option>21</option>
												<option>22</option>
												<option>23</option>
												<option>24</option>
												<option>25</option>
												<option>26</option>
												<option>27</option>
												<option>28</option>
												<option>29</option>
												<option>30</option>
												<option>31</option>
												</select>
												Month
												<select id="ss_m1">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												</select>Year
												<input type="textfield" id="ss_y1" size="4" title="YYYY">
												<!--<input type="textfield" id="ss_start1" />-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>End Date: </td>
                                            <td align=right>
                                            Day<select id="se_d1">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												<option>13</option>
												<option>14</option>
												<option>15</option>
												<option>16</option>
												<option>17</option>
												<option>18</option>
												<option>19</option>
												<option>20</option>
												<option>21</option>
												<option>22</option>
												<option>23</option>
												<option>24</option>
												<option>25</option>
												<option>26</option>
												<option>27</option>
												<option>28</option>
												<option>29</option>
												<option>30</option>
												<option>31</option>
												</select>
												Month
												<select id="se_m1">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												</select>Year
												<input type="textfield" id="se_y1" size="4" title="YYYY">
                                            
												<!--<input type="textfield" id="ss_end1" />-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Depth: </td>
                                            <td align=right><input id="dp_min1" size='4'> km to <input id="dp_max1" size='4'> km</td>
                                        </tr>
                                        <tr>
                                            <td>Type: </td>
                                            <td align=right>
                                                <select id="eqtype1">
                                                    <option value="">All</option>
                                                    <option value="R">Regional</option>
                                                    <option value="Q">Quary Blast</option>
                                                    <option value="V">Volcano Tectonic</option>
                                                    <option value="H">Hybrid</option>
                                                    <option value="LF">Low Frequency</option>
                                                    <option value="VLP">Very Long Period</option>
                                                    <option value="E">Explosion</option>
                                                    <option value="T">Tremor</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>


                                <div align="center">
                                    <button id="reloadBtn1" style="font-size:9px;">Reload Map</button>
                                    <button id="filterBtn1" style="font-size:9px;">Filter</button>
                                </div>
                                <!-- display x-z graph, y-z graph, time series graph of selected volcano -->
                                <div id="displayEquakeInformation1" style="padding-top: 20px;padding-bottom: 20px;"></div>


                                <!-- -->
                                <div id="temporal1"  style="">
                                    <!-- -->
                                    <div id="selectData1"></div>
                                    <!-- -->
                                    <div id="selectStationDiv1"></div>
                                    <a href="#" onclick="return false;" id="plfilG1">use filters</a>
                                    <a href="#" onclick="return false;" id="minfilG1">no filters</a>
                                    <div id="filterSSG1" title="Filter Graphs">
                                        <table width='100%'>
                                            <input type="hidden" id="filterG1" value="0" />
                                            <tr>
                                                <td>Start Date: </td>
                                                <td align=right><input type="textfield" id="startdate1" /></td>
                                            </tr>
                                            <tr>
                                                <td>End Date: </td>
                                                <td align=right><input type="textfield" id="enddate1" /></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Flot 1-->

                                        <!--<div id="viewcontent1" style="width:360px; text-align:center;"></div>-->
                                        <div id="viewcontent1" style="width:100%; text-align:center;"></div>
                                        
							</div>
						</div>
						
                            <!-- the division between the two maps -->
                            <div id="mapblank" style="width:5px;"></div>

                            <!-- the portion of second map -->
                            <div id="map2" style="width:345px; display: table-cell;">
                                <div id="bigmap2" style="width:407px; height:220px;"></div>
                                <div id="gotogvp2" style="padding-right:10px;"><button id="go2gvp2" style="border:none;background:none;font-size:9px;float:right;">click to GVP</button></div><br>
                                <div id="chronoholder2" style="background:none; width:345px; height:10px;" title="Chronology2"></div>
                                <div id="spatial2">
                                    <div id="displayeq2">
                                        <span ><button id="reloadEqk2" style="font-size:10px;background:none;">Display Equake</button></span>
                                        <a href="#" onclick="return false;" id="plfil2">Use Filters</a>
                                        <a href="#" onclick="return false;" id="minfil2">Hide Filters</a>
                                    </div>
                                    <div id="filterSS2" title="Filter Maps" >
                                        <table width='100%' >
                                            <input type="hidden" id="filter2" value="0" />
                                            <tr>
                                                <td>Events: </td>
                                                <td align=right>
                                                    <select id="events2">
                                                        <option>100</option>
                                                        <option>200</option>
                                                        <option>300</option>
                                                        <option>400</option>
                                                        <option>500</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Start Date: </td>
                                                <td align=right>
                                                Day<select id="ss_d2">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												<option>13</option>
												<option>14</option>
												<option>15</option>
												<option>16</option>
												<option>17</option>
												<option>18</option>
												<option>19</option>
												<option>20</option>
												<option>21</option>
												<option>22</option>
												<option>23</option>
												<option>24</option>
												<option>25</option>
												<option>26</option>
												<option>27</option>
												<option>28</option>
												<option>29</option>
												<option>30</option>
												<option>31</option>
												</select>
												Month
												<select id="ss_m2">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												</select>Year
												<input type="text" id="ss_y2" size="4" title="YYYY">
                                                <!--<input type="textfield" id="ss_start2" />-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>End Date: </td>
                                                <td align=right>
                                                Day<select id="se_d2">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												<option>13</option>
												<option>14</option>
												<option>15</option>
												<option>16</option>
												<option>17</option>
												<option>18</option>
												<option>19</option>
												<option>20</option>
												<option>21</option>
												<option>22</option>
												<option>23</option>
												<option>24</option>
												<option>25</option>
												<option>26</option>
												<option>27</option>
												<option>28</option>
												<option>29</option>
												<option>30</option>
												<option>31</option>
												</select>
												Month
												<select id="se_m2">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
												<option>9</option>
												<option>10</option>
												<option>11</option>
												<option>12</option>
												</select>Year
												<input type="text" id="se_y2" size="4" title="YYYY">
                                                <!--<input type="textfield" id="ss_end2" />-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Depth: </td>
                                                <td align=right><input id="dp_min2" size='6'> to <input id="dp_max2" size='6'></td>
                                            </tr>
                                            <tr>
                                                <td>Type: </td>
                                                <td align=right>
                                                    <select id="eqtype2">
                                                        <option value="">All</option>
                                                        <option value="R">Regional</option>
                                                        <option value="Q">Quary Blast</option>
                                                        <option value="V">Volcano Tectonic</option>
                                                        <option value="H">Hybrid</option>
                                                        <option value="LF">Low Frequency</option>
                                                        <option value="VLP">Very Long Period</option>
                                                        <option value="E">Explosion</option>
                                                        <option value="T">Tremor</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div align="center">
                                        <button id="reloadBtn2" style="font-size:9px;">Reload Map</button>
                                        <button id="filterBtn2" style="font-size:9px;">Filter</button>
                                    </div>
                                </div>


                                <!---->
                                <div id="displayEquakeInformation2" style="padding-top: 20px;padding-bottom: 20px;"></div>


                                <div id="temporal2">
                                    <div id="selectData2"></div>
                                    <div id="selectStationDiv2"></div>
                                    <a href="#" onclick="return false;" id="plfilG2">use filters</a>
                                    <a href="#" onclick="return false;" id="minfilG2">no filters</a>
                                    <div id="filterSSG2" title="Filter Graphs">
                                        <table width='100%'>
                                            <input type="hidden" id="filterG2" value="0" />
                                            <tr>
                                                <td>Start Date: </td>
                                                <td align=right><input type="textfield" id="startdate2" /></td>
                                            </tr>
                                            <tr>
                                                <td>End Date: </td>
                                                <td align=right><input type="textfield" id="enddate2" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <!-- Flot 2-->
                                <div id="viewcontent2" style="width:320px; text-align:center;float:left;">
                                </div>

                            </div>
                        </div>
                        <div style="display: table-row">
                            <div id="flotgraphspace1" style="width:685px; height:1px; display:table-cell; padding-left:5px;"></div>
                            <div id="flotgraphspace2" style="width:685px; height:5px; display:table-cell; padding-left:5px;"></div>
                        </div>
                        <div id="flotgraph" style="padding-top:5px;">
                            <!-- Flot 1-->
                            <div id="viewcontent1a" style="width:360px; text-align:center; float:left;"></div>

                            <div id="flotspace" style="width:4px; text-align:center;float:left;"></div>

                        </div>

                    </div>

                </div>
 
		
			<!-- Footer -->
			<div id="footer"><br>
				<?php include 'php/include/footer_nologout.php'; ?>
			</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->
</body>
</html>
