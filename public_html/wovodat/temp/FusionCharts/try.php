<HTML>
<HEAD>
	<script language="JavaScript" src="../JSClass/FusionCharts.js"></script>
	<TITLE>
	Trying Fusion Charts with PHP
	</TITLE>
	<script type="text/javascript">
	var defaultType = 'Column2D';
	function setChart(type)
	{
		defaultType = type;
		if (document.getElementById('XML').value == '') {
		var chart = new FusionCharts(type+'.swf', "ChartId", "800", "550", "0", "0");
		chart.setDataURL("translateCSVtoXML.php");		   
		chart.render("chartdiv");
		}
		else {
		var fileName = document.getElementById('XML').value;
		var chart = new FusionCharts(type+'.swf', "ChartId", "800", "550", "0", "0");
		chart.setDataURL(fileName);		   
		chart.render("chartdiv");
		}
	}
	function plotXMLData()
	{ 
		if (document.getElementById('XMLdata').value != '')
		{
		var chart = new FusionCharts(defaultType+'.swf', "ChartId", "800", "550", "0", "0");
		chart.setDataXML(document.getElementById('XMLdata').value);		   
		chart.render("chartdiv");
		}
		else {
		var chart = new FusionCharts(defaultType+'.swf', "ChartId", "800", "550", "0", "0");
		chart.setDataURL("translateCSVtoXML.php");		   
		chart.render("chartdiv");
		}
	}
	</script>
</HEAD>
<BODY>
<h1 align="center">FusionCharts Examples</h1>
<div style="position:absolute; left:15px; top:5px">XML File: <input id="XML" type="textfield" /><br>XML Data: <input id="XMLdata" type="textfield" />
<br><button onclick="plotXMLData()">Plot</button>
<ul>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Column2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Column3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Doughnut2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Doughnut3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">FCExporter</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Line</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">MSArea</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">MSLine</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">MSBar3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">MSCombiDY2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Pie2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Pie3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">Scatter</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">ScrollArea2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">ScrollColumn2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">ScrollLine2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">ScrollStackedColumn2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">SSGrid</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedArea2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedBar2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedArea3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedBar3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedColumn2D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedColumn3D</a></li>
<li><a onclick="setChart(this.innerHTML); return false;" href="">StackedColumn3DLineDY</a></li>
</div>
<div style="position:absolute; left:33%" id="chartdiv">
<script type="text/javascript">
   var chart = new FusionCharts("MSCombiDY2D.swf", "ChartId", "800", "550", "0", "0");
   chart.setDataURL("translateCSVtoXML.php");		   
   chart.render("chartdiv");
</script>
</div>
<?php include("translateCSVtoXML.php");?>
</BODY>
</HTML>