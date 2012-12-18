<HTML>
<HEAD>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
	<link href="../dropshadow_v1a.css" rel="stylesheet"> 
	<link href="http://www.wovodat.org/gif/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
	
	<script language="JavaScript" src="http://gmaps-utility-library.googlecode.com/svn/trunk/mapiconmaker/1.1/src/mapiconmaker.js"></script>
	<script language="JavaScript" src="../JSClass/FusionCharts.js"></script>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAxDps_3N-nXQ_bdLMel4mmBSpYxtWavb02HYAejULMuX2376JrBRzgAQVIz2XhDDWIvBAQrCsmkOIYQ" type="text/javascript"></script>
	<link type="text/css" href="development-bundle/themes/redmond/jquery.ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="development-bundle/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.button.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="development-bundle/ui/jquery.ui.dialog.js"></script>
	<link type="text/css" href="../demos.css" rel="stylesheet" />
	<script language="JavaScript" src="GPSCharts.js"></script>
</HEAD>

<BODY onunload="GUnload()">
	<?php include("checkuser.php");?>
	<script language="JavaScript" src="/js/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js/mmenu.js" type="text/javascript"></script>
	<div id="popupBox" title="Message from WOVOdat"></div>
	<div id="wrap">
		<div id="headershadow">
			<?php include '/header/header_beta.php'; ?>
		</div>

	  <div id="content">	
			<div id="contentl">
				<h1 class="ui-widget-header">Page for Single Volcano Precursors</h1>
					</script>
					<div id="viewcontent" style="width:98%+1px; align:center">
						<ul>
						<div id="#eastTab"><li><a href="#east" onclick="return false;">First</a></li></div>
						<div id="#northTab"><li><a href="#north" onclick="return false;">Second</a></li></div>
						<div id="#elevationTab"><li><a href="#elevation" onclick="return false;">Third</a></li></div>
						<div id="#fourthTab"><li><a href="#fourth" onclick="return false;">Fourth</a></li></div>
						</ul>
						<div style="float:bottom;" id="east"></div>
						<div style="float:bottom;" id="north"></div>
						<div style="float:bottom;" id="elevation"></div>
						<div style="float:bottom;" id="fourth"></div>
					</div>
				<div id="bigmap" style="position:relative; left:5%;top:5%;width:90%;height:80%;" title="Big Map"></div>
			</div>
			<div id="contentr">
				<div id="viewcontrol">
				<ul>
				<li><a href="#spatial" onclick="return showSpatial();">Map</a></li>
				<li><a href="#temporal" onclick="return showTemporal();">Graph</a></li>
				</ul></div>
				<div>
				<span id="filLoading">Loading... <img src="../loadinfo.net.gif"/></span>
				<select id="vd_name" onchange="setupMap()" style="width:100%">
				<option value="">Select a volcano name:</option>
				<?php
//				$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
				$link = mysql_connect("localhost", "wovodat", "123_nousironsauxbois") or die(mysql_error());
				mysql_select_db("wovodat") or die(mysql_error());
				$result = mysql_query("select vd_id, vd_cavw, vd_name from vd order by vd_name");
				while ($vdObj = mysql_fetch_object($result))
				{
					echo "<option value=\"$vdObj->vd_id\">$vdObj->vd_name</option>";
				}
				?>
				</select> <br/>
				<div id="spatial">
				<a href="#" onclick="return false;" id="plfil">use filters</a><a href="#" onclick="return false;" id="minfil">no filters</a>
				<div id="filterSS" title="Filter Maps">
				<table width='100%'>
				<input type="hidden" id="filter" value="0" />
				<tr><td>Events: </td><td align=right><select id="events"><option>100</option><option>200</option><option>300</option><option>400</option><option>500</option>
				</select></td></tr>
				<tr><td>Start Date: </td><td align=right><input type="textfield" id="ss_start" /></td></tr>
				<tr><td>End Date: </td><td align=right><input type="textfield" id="ss_end" /></td></tr>
				<tr><td>Depth: </td><td align=right><input id="dp_min" size='6'> to <input id="dp_max" size='6'></td></tr>
				<tr><td>Type: </td><td align=right><select id="eqtype"><option value="">All</option>
				<option value="R">Regional</option>
				<option value="Q">Quary Blast</option>
				<option value="V">Volcano Tectonic</option>
				<option value="H">Hybrid</option>
				<option value="LF">Low Frequency</option>
				<option value="VLP">Very Long Period</option>
				<option value="E">Explosion</option>
				<option value="T">Tremor</option>
				</select></td></tr></table>
				</div>
				<div align=center><button id="reloadBtn">Reload Map</button><button id="filterBtn">Filter</button></div>
				</div><div id="temporal">
				<div id="temporalQueryDiv">
				<select id="dataType" onchange="changedDataType();" style="width:100%; margin-top:2px">
				<option>Select Data Type</option>
				<option>Interval</option>
				<option>RSAM</option>
				<option>SSAM</option>
				<option>GPS</option>
				<option>Tiltmeter</option>
				<option>EDM</option>
				<option>Strainmeter</option>
				<option>SO2</option>
				<option>Thermal</option>
				</select>
				<div id="selectStationDiv"></div>
				<a href="#" onclick="return false;" id="plfilG">use filters</a><a href="#" onclick="return false;" id="minfilG">no filters</a>
				<div id="filterSSG" title="Filter Graphs">
				<table width='100%'>
				<input type="hidden" id="filterG" value="0" />
				<tr><td>Start Date: </td><td align=right><input type="textfield" id="startdate" /></td></tr>
				<tr><td>End Date: </td><td align=right><input type="textfield" id="enddate" /></td></tr>
				</table>
				</div>
				<div id="chartXML"></div>
				</div>
				</div>
				<div id="dialog" style="width:100%;height:360px;"title="Map"></div>
				</div>
			</div>
		</div>
		<div id="footer">
			<div align="left">
				&nbsp;Copyright © 2000-2009 <a href="http://www.wovo.org" target="_blank">The World Organization of Volcano Observatories</a></div>
			<div align="right"><font size="1" face="Arial,Helvetica,Geneva,Swiss,SunSans-Regular">
				<b>last updated: <script type="text/javascript">document.write(document.lastModified)</script>
			 | website hosted by <a href="http://www.eos-singapore.org">EOS</a>&nbsp;</b>
			</div>
		</div>
	</div>
</BODY>
</HTML>