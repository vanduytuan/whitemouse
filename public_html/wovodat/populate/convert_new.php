<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
	<link href="/css/styles_beta.css" rel="stylesheet">
	<link rel="stylesheet" href="/populate/convert/DataFormatConversion.css" type="text/css" media="screen" />
	<script language="JavaScript" src="/populate/convert/regi.js"></script>
	<script language="JavaScript" src="/js/jquery-1.4.2.js"></script>
	<script language="javascript" type="text/javascript" src="/js/scripts.js"></script>

	<script>
		function set_region(){
			$('#reg').change(update_volcano);
		}
		function update_volcano(){
			var kode=$('#reg').attr('value');
			$.get('http://localhost/populate/convert/selectVol_new.php?kode='+kode, show_volano);
		}
		function show_volano(res){
			$('#volano').html(res);
		}
		$(document).ready(set_region);
	</script>

	<script type="text/javascript">
		var xmlHttp=createRequestObject();
		function createRequestObject(){
		  var ro;
			var browser=navigator.appName;
			if(browser=="Microsoft Internet Explorer"){
				ro=new ActiveXObject("Microsoft.XMLHTTP");
			}else{
		    ro=new XMLHttpRequest();
		  }
			return ro;
		}
		function dinamik(combox){
			var kode=combox.value;
		  xmlHttp.open('get', '/populate/convert/selectVol_new.php?kode='+kode, true);
		  xmlHttp.onreadystatechange=function(){
		  	if(xmlHttp.readyState==4){
					document.getElementById("volano").innerHTML=xmlHttp.responseText;
				}
				return false;
		  }
		  xmlHttp.send(null);
		}
		function volcano(combo){
			var gapi=combo.value;}
		function typeMeas(combox){
			var typedata=combox.value;}
		function observo(combox){
			var obser=combox.value;
		}
	</script>
</head>
<body>
	<script language="JavaScript" src="/js/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js/mmenu.js" type="text/javascript"></script>
	<div id="wrapborder">
	<div id="wrap">
		<div id="headershadow">
			<?php include 'php/include/header_beta.php'; ?>
		</div>

	  	<div id="content">	
			<div id="contentlconvert">
					<p class="home0">Format Conversion</p>
					<p class="home1a">
						Original input data files need to be converted into the appropriate format prior to
						make an upload. This step translates any data format to WOVOdat1.1 data structure and
						creating an wovodat-equivalent xml-data file. <br><br></p>
					<p class="home0">Generic CSV file</p>
					<p class="home1a">
						Generic csv files are data with simple format. Usually it's used in sequential daily count of
						monitoring data. For instance, data of seismic daily count with only 2-column table, one for
						datetime and two for daily number.
					</p>
			</div>
			<div id="contentrconvert">
				<h1>Converting Data Format</h1><br>
				<div id="contentlform">
					<p class="home1">
					</p>
					<form name="form1" action="http://localhost/populate/convert/Conversion2xml.php" method="post" enctype="multipart/form-data">
						<tr><p1>Origin of Data: </p1></tr><br>
						<tr>
							<td>
								<div id='observo'>
									<select name='observ'>
										<option value="Select institute">Select Source</option>
									</select>
									<script language="JavaScript">
										for (i=0; i<=3; i++){
											if (i=="0") val="Observatory";
											if (i=="1") val="Institute/University";
											if (i=="2") val="IRIS";
											if (i=="3") val="Unavco";
											document.form1.observ.length++;
											document.form1.observ.options[document.form1.observ.length-1].text=val;
											document.form1.observ.options[document.form1.observ.length-1].value=val;}
									</script>
								</div>
							</td>
							<td>
								<div id='datatype'>
									<select name='dtype' onChange='javascript:typeMeas(this)'>
										<option value="select measurement">Type of Data</option>
									</select>
									<script language="JavaScript">
										for (i=0; i<=10; i++){
											if (i=="0") val="Seismic Daily Count";
											if (i=="1") val="Seismic-Event";
											if (i=="2") val="Seismic-RSAM";
											if (i=="3") val="Seismic-SSAM";
											if (i=="4") val="Deformation-Tilt";
											if (i=="5") val="Deformation-EDM";
											if (i=="6") val="Deformation-GPS";
											if (i=="7") val="Gas";
											if (i=="8") val="Hydrology";
											if (i=="9") val="Magnetic";
											if (i=="10") val="Thermal";
											document.form1.dtype.length++;
											document.form1.dtype.options[document.form1.dtype.length-1].text=val;
											document.form1.dtype.options[document.form1.dtype.length-1].value=val;}
									</script>
								</div>
							</td>
							<br>
							<p1>Select Region and Volcano: </p1><br>
							<td>
								<div id='regio'>
									<select name='reg' id='reg' onChange='javascript:dinamik(this)'>
										<option value="Select region">Select region</option>
									</select>
									<script language="JavaScript">
										for (i=0; i<=69; i++){
											document.form1.reg.length++;
											document.form1.reg.options[document.form1.reg.length-1].text=regi[i];
											document.form1.reg.options[document.form1.reg.length-1].value=regi[i];}
									</script>
								</div>
							</td>
							<td>
								<div id='volano'>
									<select name='vol' id='vol'> </select>
								</div>
							</td><br>
						</tr>
						<table id="table1" width="320">
							<tr>
								<td>
									<B><strong>File to convert:</strong></B><br>
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
			<?php include 'php/include/footer_beta.php'; ?>
		</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->
</body>
</html>



