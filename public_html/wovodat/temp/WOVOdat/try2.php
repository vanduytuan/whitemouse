<HTML>
<HEAD>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
	<link href="../dropshadow_v1.css" rel="stylesheet">
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
<body>
				<select id="vd_name" style="width:100%">
				<option>Select a volcano name:</option>
				<?php
				$link = mysql_connect("localhost", "wovodat", "born2_makan") or die(mysql_error());
				mysql_select_db("wovodat") or die(mysql_error());
				$result = mysql_query("select vd_id, vd_cavw, vd_name from vd order by vd_name");
				while ($vdObj = mysql_fetch_object($result))
				{
					echo "<option value=\"$vdObj->vd_id\">$vdObj->vd_name</option>";
				}
				?>
				</select> <br/>
				
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
				
						<div style="float:bottom; background-color:red" id="east"></div>
						<div style="float:bottom; background-color:yellow" id="north"></div>
						<div style="float:bottom; background-color:green" id="elevation"></div>
						</body>
						</html>