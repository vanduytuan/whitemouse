<?php
//ini_set('display_errors',1);
// Start session
	session_start();
	// Regenerate session ID
	session_regenerate_id(true);
	$uname="";
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
	<link href="/js2/navig.css" rel="stylesheet">
	<link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
	<script language="JavaScript" src="http://gmaps-utility-library.googlecode.com/svn/trunk/mapiconmaker/1.1/src/mapiconmaker.js"></script>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAxDps_3N-nXQ_bdLMel4mmBSpYxtWavb02HYAejULMuX2376JrBRzgAQVIz2XhDDWIvBAQrCsmkOIYQ" type="text/javascript"></script>
	
	<link type="text/css" href="/development-bundle/themes/redmond/jquery.ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="/development-bundle/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.button.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="/development-bundle/ui/jquery.ui.dialog.js"></script>

	<script type="text/javascript" src="/development-bundle/flot/excanvas.min.js" language="javascript"></script>
	<script type="text/javascript" src="/development-bundle/flot/jquery.flot.js" language="javascript"></script>
	<script type="text/javascript" src="/development-bundle/flot/jquery.flot.navigate.js" language="javascript"></script>
	<script type="text/javascript" src="/js/scripts.js" language="javascript"></script>

<?php
	// If session was already started
	if (isset($_SESSION['login'])) {
		// Get login ID and user name
		$uname=$_SESSION['login']['cr_uname'];
		if($uname=="ratdomopurbo" || $uname=="abaguet@ntu.edu.sg" || $uname=="cwidiwijayanti" || $uname=="only4developers" || $uname="cnewhall"){
?>			
			<script type="text/javascript" src="/js/GPSCharts_devel.js" language="JavaScript"></script>
<?php
		}
	}else{
?>
		<script type="text/javascript" src="/js/GPSCharts_beta.js" language="JavaScript"></script>	
<?php
	}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#volcompare').hide(); 
		document.getElementById('vd_name').onchange();
	});
	
	function volcano_selected(){
		setupMap(0);
		volcanostatus();
		secondvolcano();
		}

	function volcano2_selected(){
		setupMap2(0);
		volcanostatus2();
		}

	function show_info(res){$('#viewcontent2').html(res);}
	
	function volcano_category_change(){
		var categ=$('#v_categ').attr('value');
		$.get('./selectVolOfCategory.php?kode='+categ, show_vulkan);
	}
	function show_vulkan(res){
		$('#volanocateg').html(res);
	}
</script>

</head>
<body onunload="GUnload()">
	<script language="JavaScript" src="/js2/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js2/mmenu.js" type="text/javascript"></script>
	<div id="popupBox" title="Message from WOVOdat"></div>
	<div id="wrapborder_x">
		<div id="wrap_x">
			<!-- Header -->
			<?php include 'php/include/header_beta.php'; ?>
			<!-- Content -->
			<div id="content_x">
				
				<!-- Right -->
				<div id="contentrview_x">
				   	<div id="volselect">
						<div id="top" align="left">
							<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
							<p align="left">Login as: <b><?php print $uname; ?></b>|<a href="logout.php">Logout</a></p>
				   		</div>
						<div>
							<div style="height:25px;font-size:9px;">
							<span id="filLoading">Loading ...<img src="../gif2/loadinfo.net.gif"/></span>
							</div>
						</div>
						<div>
						<table><tr><td>
							<div style="width:50px;font-size:9px;float:left;">Volcano1:</div></td>
							<td>
							<div style="float:left;">
								<select id="vd_name" onchange="volcano_selected()" style="font-size:10px;width:145px;">
<?php						
									$nr=0;$unn="Unnamed";
									$status='Historical'; $statusUnc='Uncertain';$statusNot='Not a Volcano';
									include 'php/include/db_connect_view.php';
//									$result = mysql_query("select a.vd_id, a.vd_cavw, a.vd_name FROM vd a, vd_inf b WHERE a.vd_cavw=b.vd_inf_cavw and b.vd_inf_status='$status' and SUBSTR(a.vd_name,1,7)!='$unn' ORDER by a.vd_name"); //only for historical volcanoes
									$result = mysql_query("select a.vd_id, a.vd_cavw, a.vd_name FROM vd a, vd_inf b WHERE a.vd_cavw=b.vd_inf_cavw and SUBSTR(a.vd_name,1,7)!='$unn' and b.vd_inf_status!='$statusUnc' and b.vd_inf_status!='$statusNot' ORDER by a.vd_name");
									$n_rand=mt_rand(5, 1200);	
									if ($n_rand<5) {$n_rand=rand(5, 1200);}
									if ($n_rand<5) $n_rand=10;
									while ($v_arr = mysql_fetch_array($result)) {
										$nr++;							
										if($nr==$n_rand && $n_rand!=0){
											echo "<option value=\"$v_arr[0]\" selected=\"selected\">".htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "	cp1252")."</option>";
										}else{	
										echo "<option value=\"$v_arr[0]\">".htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "cp1252")."</option>";
										}
									} 
?>
								</select>
							</div>
							</td>
							</tr>
							<tr><td></td><td>
								<div id="status_1" style="font-size:9px;width:140px;" valign="top"></div>
								</td>
							</tr>
							</table>
							</div>
							
					
						<div id="volcano1_info">
							<table><tr>
								<td>
									<div id="volc1_left" style="width:128px; float:left;">
										<div id="volc1_info">.</div>	
									</div>
								</td>
								<td>
									<div id="volc1_right" style="width:70px; align:right;">
									<button id="reloadSta" style="font-size:9px;">Show Sta.</button>
									<button id="removeSta" style="font-size:9px;">Hide Sta.</button>
								</div>
								</td>
							</tr></table>
						</div><br>
						<div id="searchmode" title="Searchmode"><br>
							<table><tr>
								<td>
								<div id="search1" style="width:105px; float:left;">&nbsp</div>
								</td>
								<td>
									<div id="search2" style="width:90px; align:right;">
										<button id="CompareVolcanoBtn" style="font-size:9px;">View Volcano.2</button>
										<button id="SingleVolcanoBtn" style="font-size:9px;">Hide Volcano.2</button>
									</div>
								</td>
							</tr></table>
						</div>
					</div>
					<div id="volc_compare">
						<div>
							<table><tr><td>
									<div style="width:50px;font-size:9px;float:left;">Volcano2:</div>
								</td>
								<td>
									<div id="volanocateg" style="font-size:9px;float:left;">
										<select id="volc2"  onchange="volcano2_selected()" style="width:145px;">
											<option value="volcanocateg">...</option>
										</select>
									</div>
								</td>
								</tr>
								<tr><td></td><td>
									<div id="status_2"  style="font-size:9px;width:155px;" valign="top"></div>
								</tr>
							</table>	
						</div>	
						<br>
						
						<div id="volcano2_info">
							<table><tr>
								<td>
									<div id="volc2_left" style="width:128px; float:left;">
										<div id="volc2_info">.</div>	
									</div>
								</td>
								<td>
									<div id="volc2_right" style="width:70px; align:right;">
									<button id="reloadSta2" style="font-size:9px;">Show Sta.</button>
									<button id="removeSta2" style="font-size:9px;">Hide Sta.</button>
								</div>
								</td>
							</tr></table>
						</div>
					</div>
					
					<div id="volc_single">
						<div id="selectvolc">
							<table><tr></tr></table>
						</div><br>
				
						<div id="contentrviewcontrol">
							<div id="stationcheck">
								<div id="staavailable"></div>
							</div>
	
						</div>
					</div>
					<div id="test"><p>&nbsp</p>
						<div id="testplaceholder" style="width:90%; height:120px;"></div>
					</div>
				</div>

								
				<!-- Left -->
				<div id="contentlview_x">
					<div id="legend">
						<table>
							<tr>
								<td>
									<!-- Legend -->
									<div id="map_legend" style="font-size:8px;">
										<table>
											<tr>
												<td><p><img src="/img/pin_ds.png" style="width:16px;height:16px;"></img> Deformation</p></td>
												<td><p><img src="/img/pin_gs.png" style="width:16px;height:16px;"></img> Gas </p></td>
												<td><p><img src="/img/pin_hs.png" style="width:16px;height:16px;"></img> Hydrologic</p></td>
												<td><p><img src="/img/pin_ss.png" style="width:16px;height:16px;"></img> Seismic</p></td>
												<td><p><img src="/img/pin_ts.png" style="width:16px;height:16px;"></img> Thermal</p></td>
											</tr>
										</table>
									</div>
								</td
							</tr>
						</table>
					</div>
					
					<!-- Display -->
					<div id="mapsite">
						<div id="map1" style="width:360px; float:left; padding-left:5px;">
							<div id="bigmap" style="width:340px; height:200px;" title="BigMap"></div>
							<div id="gotogvp1" style="padding-right:10px;"><button id="go2gvp" style="font-size:9px;float:right;">click to GVP</button></div><br>
							<div id="chronoholder1" style="background:green; width:340px; height:10px;" title="Chronology1">.</div>
							<div id="spatial1">
								<div id="displayeq">
									<span background="ff0000"><button id="reloadEqk" style="font-size:9px;">Display Equake</button><span>
									<a href="#" onclick="return false;" id="plfil">use filters</a>
									<a href="#" onclick="return false;" id="minfil">no filters</a>
								</div>
							
								<div id="filterSS" title="Filter Maps" >
									<table width='100%' >
										<input type="hidden" id="filter" value="0" />
										<tr>
											<td>Events: </td>
											<td align=right>
												<select id="events">
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
											<td align=right><input type="textfield" id="ss_start" /></td>
										</tr>
										<tr>
											<td>End Date: </td>
											<td align=right><input type="textfield" id="ss_end" /></td>
										</tr>
										<tr>
											<td>Depth: </td>
											<td align=right><input id="dp_min" size='6'> to <input id="dp_max" size='6'></td>
										</tr>
										<tr>
											<td>Type: </td>
											<td align=right>
												<select id="eqtype">
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
									<button id="reloadBtn" style="font-size:9px;">Reload Map</button>
									<button id="filterBtn" style="font-size:9px;">Filter</button>
								</div>
							</div>
							<div id="temporal1">
							
								<div id="selectData"></div>									
								<div id="selectStationDiv"></div>
								
								<a href="#" onclick="return false;" id="plfilG">use filters</a>
								<a href="#" onclick="return false;" id="minfilG">no filters</a>
								<div id="filterSSG" title="Filter Graphs">
									<table width='100%'>
										<input type="hidden" id="filterG" value="0" />
										<tr>
											<td>Start Date: </td>
											<td align=right><input type="textfield" id="startdate" /></td>
										</tr>
										<tr>
											<td>End Date: </td>
											<td align=right><input type="textfield" id="enddate" /></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						
						<div id="mapblank" style="width:4px; float:left;"></div>
												
						<div id="map2" style="width:320px; float:left;">
							<div id="bigmap2" style="width:300px; height:200px;" title="Bigmap2"></div>
							<div id="gotogvp2" style="padding-right:10px;"><button id="go2gvp2" style="font-size:9px;float:right;">click to GVP</button></div><br>
							<div id="chronoholder2" style="width:320px; height:10px;" title="Chronology2"></div>
							<div id="spatial2">
								<div id="displayeq2">
									<span background="ff0000"><button id="reloadEqk2" style="font-size:9px;">Display Equake</button><span>
									<a href="#" onclick="return false;" id="plfil2">use filters</a>
									<a href="#" onclick="return false;" id="minfil2">no filters</a>
								</div>
								<div id="filterSS2" title="Filter Maps" >
									<table width='100%' >
										<input type="hidden" id="filter" value="0" />
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
											<td align=right><input type="textfield" id="ss_start2" /></td>
										</tr>
										<tr>
											<td>End Date: </td>
											<td align=right><input type="textfield" id="ss_end2" /></td>
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
							
						</div>
						<div id="flotgraphspace1" style="width:685px; height:1px; float:left; padding-left:5px;"></div>					
						<div id="flotgraphspace2" style="width:685px; height:5px; float:left; padding-left:5px;"></div>					
						<div id:"flotgraph" style="padding-top:5px;">							
							<!-- Flot 1-->
							<div id="viewcontent1" style="width:360px; align:center; float:left;"></div>					

							<div id="flotspace" style="width:4px; align:center;float:left;"></div>					

							<!-- Flot 2-->
							<div id="viewcontent2" style="width:320px; align:center;float:left;">
							</div>
						</div>
												
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
