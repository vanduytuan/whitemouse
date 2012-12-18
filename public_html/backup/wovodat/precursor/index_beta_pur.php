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
	<link href="/gif/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
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
	<script language="JavaScript" src="/js/GPSCharts_beta.js"></script>
	<script language="javascript" type="text/javascript" src="/js/scripts.js"></script>
	<script src="/development-bundle/flot/excanvas.min.js" language="javascript" type="text/javascript"></script>
	<script src="/development-bundle/flot/jquery.flot.js" language="javascript" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function(){
		document.getElementById('vd_name').onchange();
	});
	function volcano_selected(){
		setupMap(0);
	}
	function show_info(res){
		$('#viewcontent2').html(res);
	}
</script>

</head>
<body onunload="GUnload()">
	<script language="JavaScript" src="/js2/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js2/mmenu.js" type="text/javascript"></script>
	<div id="popupBox" title="Message from WOVOdat"></div>
	<div id="wrapborder">
		<div id="wrap">
			<!-- Header -->
			<?php include 'php/include/header_beta.php'; ?>
			<!-- Content -->
			<div id="content">
				<!-- Left -->
				<div id="contentlview"><br>
					<div style="position:relative; left:8%; width:60%;" title="Notice">
						<p class="home1"><b>PAGE FOR FUTURE DATA QUERY</b></p>
						<p class="home2">Plotting unrest data not yet available. Database still in population-phase. Once the database has a critical mass, it will be open to all.</p>
					</div>
					<!-- Map -->
					<div id="bigmap" style="position:relative; left:5%;top:2%;width:85%;height:40%;" title="Big Map"></div>
					<!-- Legend -->
					<div id="map_legend" style="margin-top:8px;font-size:8px;">
						<table>
							<tr style="margin:0px; padding:0px;" >
								<td><p><span style="font-size:10px;"><b>Legend:&nbsp&nbsp&nbsp<b></span></p></td>
								<td><p><img src="/img/pin_volcano_selected.png" style="width:16px;height:16px;"></img> Volcano</p></td>
								<td><p><img src="/img/pin_volcano.png" style="width:16px;height:16px;"></img> Other Volcano</p></td>
								<td><p><img src="/img/pin_ds.png" style="width:16px;height:16px;"></img> Deformation sta.</p></td>
								<td><p><img src="/img/pin_gs.png" style="width:16px;height:16px;"></img> Gas sta.</p></td>
								<td><p><img src="/img/pin_hs.png" style="width:16px;height:16px;"></img> Hydrologic sta.</p></td>
								<td><p><img src="/img/pin_ss.png" style="width:16px;height:16px;"></img> Seismic sta.</p></td>
								<td><p><img src="/img/pin_ts.png" style="width:16px;height:16px;"></img> Thermal sta.</p></td>
							</tr>
						</table>
					</div>
					<!-- Flot -->
					<div id="viewcontent" style="width:98%+1px; height:98%+1px; align:center; padding:10px;"></div>
				</div>
				<!-- Right -->
				<div id="contentrview">
					<div id="selectvolc">
						<table><tr>
							<td><p>Select volcano:</p></td>
							<td><span id="filLoading">Loading... <img src="./loadinfo.net.gif"/></span>
							</td>
						</tr></table>
						<select id="vd_name" onchange="volcano_selected()" style="width:100%">
<?php
							$n_rand=rand(1, 1544);
							$nr=0;
//							$n_rand=830;
							$link = mysql_connect("localhost", "wovodat", "123_nousironsauxbois") or die(mysql_error());
							mysql_select_db("wovodat") or die(mysql_error());
							$result = mysql_query("select vd_id, vd_cavw, vd_name from vd order by vd_name");
							while ($v_arr = mysql_fetch_array($result)) {
								$nr++;							
								if($nr==$n_rand){
									echo "<option value=\"$v_arr[0]\" selected=\"selected\">".htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "cp1252")."</option>";
								}else{
									echo "<option value=\"$v_arr[0]\">".htmlentities($v_arr[2]."_".$v_arr[1], ENT_COMPAT, "cp1252")."</option>";
								}
							} 
?>
						</select><br>
					</div>
					<div align="right">
						<button id="go2gvp" style="font-size:9px;">Open GVP-Smithsonian</button>
					</div>
					<br>
					<div align="left">
						<p>Monitoring Stations</p>
						<button id="reloadSta" style="font-size:9px;">Display all Stations</button>
					</div>
					<div id="list_station" align="left"></div>
					<div align="left">
						<button id="removeSta" style="font-size:9px;">Hide Stations</button>
					</div>
					<br>

					
					<div id="contentrviewcontrol">
						<div id="stationcheck">
							<div id="staavailable"></div>
						</div>
						
						<div id="spatial">
							<span background="ff0000"><button id="reloadEqk" style="font-size:9px;">Display Equake</button><span>
							<a href="#" onclick="return false;" id="plfil">use filters</a>
							<a href="#" onclick="return false;" id="minfil">no filters</a>
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
						
						<div id="temporal">
							<div id="temporalQueryDiv">							
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

						<!-- Volcano Info -->
						<div id="viewcontent2" style="width:90%+1px; height:90%+1px; align:center; padding:10px;">
						</div>
					</div>
				</div>
			</div>
			<!-- Footer -->
			<div id="footer">
				<?php include 'php/include/footer_beta.php'; ?>
			</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->
</body>
</html>
