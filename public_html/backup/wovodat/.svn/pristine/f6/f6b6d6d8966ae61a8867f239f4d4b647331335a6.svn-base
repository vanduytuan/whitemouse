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
	<link rel="stylesheet" href="http://localhost/populate/convert/DataFormatConversion.css" type="text/css" media="screen" />

	<script language="JavaScript" src="/populate/convertie/regi.js"></script>
	<script src="/js/jquery-1.4.2.min.js"></script>

	<script> <!--
		function UnCryptMailto(s, shift) {
			var n=0; var r="";
			for(var i=0;i<s.length;i++) { 
				n=s.charCodeAt(i); 
				if (n>=8364) {n = 128;}
				r += String.fromCharCode(n-(shift));}
			return r;
		}
		function linkTo_UnCryptMailto(s, shift)	{
			location.href=UnCryptMailto(s, shift);}
		// --> 
	</script>

	<script>
		function select_region_change(){
			$('#observ').change(update_volcano);
		}
		function update_volcano(){
			var institute=$('#observ').attr('value');
			$.get('./convertie/selectVolOfInstitute.php?kode='+institute, show_gunung);
		}
		function show_gunung(res){
			$('#volano').html(res);
		}
		$(document).ready(select_region_change);
	</script>

</head>
<body>
	<script language="JavaScript" src="/js2/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js2/mmenu.js" type="text/javascript"></script>
	<div id="wrapborder">
	<div id="wrap">
			<?php include 'php/include/header_beta.php'; ?>

	  	<div id="content">	
			<div id="contentlconvert">
					<p class="home0">Simple CSV file</p>
					<p class="home1a">
						Generic csv files are data with simple format. Usually it's used in sequential daily count of
						monitoring data. For instance, data of seismic daily count with only 2-column table, one for
						datetime and two for daily number.<br></p>
					<p class="home0">Format Conversion</p>
					<p class="home1a">
						Original input data files need to be converted into the appropriate format prior to
						make an upload. This step translates any data format to WOVOdat1.1 data structure and
						creating an wovodat-equivalent xml-data file. <br></p>
			</div>
			<div id="contentlconvertm"></div>
			<div id="contentrconvertm"></div>
			<div id="contentrconvert" style="padding:0px 0px 0px 10px;">
				<h1><span><b>Converting Data Format</b></span></h1>
				<div id="contentlform">
					<form name="form1" action="./convertie/Conversion2xml.php" method="post" enctype="multipart/form-data">
						<tr>
							<td>
								<p1>Origin/Owner of Data and Volcano: </p1><br>
								<div id='observo' style="float:left">
									<select name='observ' id='observ'><option value="Select institute">.....</option></select>
									<script language="JavaScript">
										for (i=0; i<=12; i++){
											if (i=="0") val="INGV";
											if (i=="1") val="CVGHM";
											if (i=="2") val="USGS";
											if (i=="3") val="NIED";
											if (i=="4") val="GNS";
											if (i=="5") val="Phivolcs";
											if (i=="6") val="Unavco";
											if (i=="7") val="Iris";
											if (i=="8") val="INGP";
											if (i=="9") val="AVO";
											if (i=="10") val="CVO";
											if (i=="11") val="HVO";
											if (i=="12") val="Unam";
											document.form1.observ.length++;
											document.form1.observ.options[document.form1.observ.length-1].text=val;
											document.form1.observ.options[document.form1.observ.length-1].value=val;}
									</script>
								</div>
							</td>
							<td>
								<div style="float:left">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
								<div id="volano">
									<select name="vol" id="vol"><option value="volcano">.....</option></select>
								</div>
							</td>
						</tr>
						<tr></tr><br>
						</div>
						<tr>
						<table>
							<tr>
							<td>
								<p1>Station: </p1><br>
								<input type="text" name='staname' style="background:light-grey;width:60px"></input>
							</td>
							<td>&nbsp&nbsp&nbsp</td>
							<td>
								<p1>Type of data: </p1><br>
									<select name='dtype' onChange='javascript:typeMeas(this)'>
										<option value="select measurement">.....</option>
									</select>
									<script language="JavaScript">
										for (i=0; i<=14; i++){
											if (i=="0") val="Seismic Daily Count";
											if (i=="1") val="Seismic-Event";
											if (i=="2") val="RSAM";
											if (i=="3") val="SSAM";
											if (i=="4") val="Deformation-Tilt";
											if (i=="5") val="Deformation-EDM";
											if (i=="6") val="Deformation-GPS";
											if (i=="7") val="SampledGas";
											if (i=="8") val="PlumeSO2";
											if (i=="9") val="HydroDaily";
											if (i=="10") val="HydroNwis";
											if (i=="11") val="HydroRuapehu";
											if (i=="12") val="Magnetic";
											if (i=="13") val="Thermal";
											if (i=="14") val="";
											document.form1.dtype.length++;
											document.form1.dtype.options[document.form1.dtype.length-1].text=val;
											document.form1.dtype.options[document.form1.dtype.length-1].value=val;}
									</script>
							</td>
							</tr>
						</table>
						</tr><br>
						<table id="table1" width="320">
							<tr>
								<td>
									Browse the File to convert:<br>
								</td>
							</tr>
							<tr>
								<td>
									<input name="MAX_FILE_SIZE" type="hidden" value="2000000">
									<input name="fname" type="file" size="35" maxlength="100">
								</td>
							</tr>
							<tr><td><input type="submit" name="Submit" value="Select"></td></tr>
						</table>
					</form>  <!-- end form -->
				</div>
				<div>
					<p class="home2">
					</p>
				</div>
			</div>
		</div>
		<div id="footer">
			<div align="left"  style="display: inline">
				&nbsp;Copyright © 2000-2010 <a href="http://www.wovo.org" target="_blank">The World Organization of Volcano Observatories</a>
			</div>
			<div align="right" style="display: inline"><font size="1" face="Arial,Helvetica,Geneva,Swiss,SunSans-Regular">
				<b>last updated: <script type="text/javascript">document.write(document.lastModified)</script>
			 | website hosted by <a href="http://www.eos-singapore.org">EOS&nbsp;</a></b>
			</div>
		</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->
</body>
</html>



