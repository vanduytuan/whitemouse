<?php
	// Start session
	session_start();
	// Regenerate session ID
	session_regenerate_id(true);
	$uname="";
	$ccd="";
?>
<script src="/js/jquery-1.4.2.min.js"></script>

<?php
	if (isset($_SESSION['login'])) {
		$uname=$_SESSION['login']['cr_uname'];
		$ccd=$_SESSION['login']['cc_id'];
	}
?>

	<script language='javascript' type='text/javascript'>
		function select_observatory_change2(){
			$('#observ').change(update_volcano2);
		}
		function update_volcano2(){
			var institute=$('#observ').attr('value');
			$.get('./convertie/selectVolOfInstitute2.php?kode='+institute, show_gunung2);
		}
		function show_gunung2(res){
			$('#volano2').html(res);
		}
		function respon2filecontent(){
			var statype=$('#sttype').attr('value');
			var sg = document.form1.vol2.selectedIndex;
			var sgu = document.form1.vol2.options[sg].value;
			$.get('./convertie/selectNetwork_form.php?volcan='+sgu + '&statype='+statype, show_networkavail);
		}
		function show_networkavail(res){
			$('#networketc').html(res);
		}

		function show_stationavail(res){
			$('#stationdiv').html(res);
		}

		function show_instrumentavail(res){
			$('#instrumdiv').html(res);
		}
		
		$(document).ready(select_observatory_change2);
		
	</script>

<div style="padding:0px 0px 0px 5px;"><br>

<?php
	$mndata=$_GET['tipedata'];
	if($mndata=="data"){
		echo "<h1>Converting Data Format for Data-file</h1>";
	}else{
		echo "<h1>Converting Data Format for Station-file</h1>";
	}
?>

	<form name="form1" id="form1" action="./convertie/Conversion2xml.php?monidata=<?php print $mndata; ?>" method="post" enctype="multipart/form-data">
	<div style="height:50px; padding-left:20px;">
		<div style="float:left; width:185px;">
			<p1>Observatory (data owner): </p1><br>
			<div id='observo'>
				<select name='observ' id='observ' style="width:180px">
					<option value="Select institute">.....</option>
<?php
						include 'php/include/db_connect_view.php';
						if ($uname=='ratdomopurbo' || $uname=='cwidiwijayanti' || $uname=='chris') {
							$result = mysql_query("select cc_code, cc_country, cc_obs, cc_id		from cc		order by cc_country");
						}else{
							$result = mysql_query("select cc_code, cc_country, cc_obs, cc_id 	from cc 	where cc_id='$ccd'  order by cc_country");
						} 
						while ($v_arr = mysql_fetch_array($result)) {
							if(!is_numeric($v_arr[0])){
								$titles=htmlentities($v_arr[2], ENT_COMPAT, "cp1252");
								if($v_arr[1]==""){
									if($v_arr[3]==$ccd){
										echo "<option value=\"$v_arr[0]\" title=\"$titles\" selected=\"selected\">".htmlentities($v_arr[0], ENT_COMPAT, "cp1252")."</option>";
									}else{echo "<option value=\"$v_arr[0]\" title=\"$titles\">".htmlentities($v_arr[0], ENT_COMPAT, "cp1252")."</option>";}
								}else{
									if($v_arr[3]==$ccd){echo "<option value=\"$v_arr[0]\" title=\"$titles\" selected=\"selected\">".htmlentities($v_arr[1].",".$v_arr[0], ENT_COMPAT, "cp1252")."</option>";
									}else{echo "<option value=\"$v_arr[0]\" title=\"$titles\">".htmlentities($v_arr[1].",".$v_arr[0], ENT_COMPAT, "cp1252")."</option>";}
								}
							}
						} 
?>
				</select>
			</div>
		</div>
		<div id="vola2">
			<p1>Volcano: </p1><br>
			<div id="volano2">
				<select name='vol2' id='vol2' style="width:140px">
					<option>...</option>
				</select>
			</div>
		</div>
	</div>
	
	
	
	<div>
		<div id="rightleft" style="width:5%; height:60px;  float:left;"></div>
		<div id="rightleft" style="width:45%; height:60px;  float:left;">
			<div style="">
				<p1>File content to convert: </p1><br>
				<select name='sttype' id='sttype' style="width:180px" onChange='javascript:respon2filecontent()'>
					<option value="select station type" option="selected">...</option>
				</select>
<?php
				if($mndata=="data"){
					print <<<STRING
					<script language="JavaScript">
						for (i=0; i<=44; i++){
							if (i=="0") val="EventDataFromNetwork";
							if (i=="1") val="EventDataFromSingleStation";
							if (i=="2") val="IntensityData";
							if (i=="3") val="SeismicTremor";
							if (i=="4") val="IntervalSwarmData";
							if (i=="5") val="RSAMSSAMData";
							if (i=="6") val="RSAMData";
							if (i=="7") val="SSAMData";
							if (i=="8") val="RepresentativeWaveform";
							if (i=="9") val="EarthquakeTranslation";
							if (i=="10") val="ElectronicTiltData";
							if (i=="11") val="TiltVectorData";
							if (i=="12") val="StrainMeterData";
							if (i=="13") val="EDMData";
							if (i=="14") val="AngleData";
							if (i=="15") val="GPSData";
							if (i=="16") val="GPSVectors";
							if (i=="17") val="LevelingData";
							if (i=="18") val="InSARImage";
							if (i=="19") val="InSARSateliteJunction";
							if (i=="20") val="InSARData";
							if (i=="21") val="DirectlySampledGas";
							if (i=="22") val="SoilEffluxData";
							if (i=="23") val="PlumeData";
							if (i=="24") val="HydrologicData";
							if (i=="25") val="MagneticFieldsData";
							if (i=="26") val="MagnetorVectorData";
							if (i=="27") val="ElectricFieldsData";
							if (i=="28") val="GravityData";
							if (i=="29") val="GroundBasedThermalData";
							if (i=="30") val="ThermalImage";
							if (i=="31") val="ThermalImageData";
							if (i=="32") val="MagmaMovement";
							if (i=="33") val="VolatileSaturation";
							if (i=="34") val="BuildUpMagmaPressure";
							if (i=="35") val="HydrothermalSystemInteraction";
							if (i=="36") val="RegionalTectonicInteraction";
							if (i=="37") val="BibliographicData";
							if (i=="38") val="Images";
							if (i=="39") val="ImageJunction";
							if (i=="40") val="Satellite";
							if (i=="41") val="VolcanoNetworkJunction";
							if (i=="42") val="Maps";
							if (i=="43") val="Observation";
							if (i=="44") val="";
							document.form1.sttype.length++;
							document.form1.sttype.options[document.form1.sttype.length-1].text=val;
							document.form1.sttype.options[document.form1.sttype.length-1].value=val;}
					</script>
STRING;

				}elseif($mndata=="station"){
		
					print <<<STRING
					<script language="JavaScript">
						for (i=0; i<=19; i++){
							if (i=="0") val="SeismicNetwork";
							if (i=="1") val="SeismicStation";
							if (i=="2") val="SeismicInstrument";
							if (i=="3") val="SeismicComponent";
					
							if (i=="4") val="DeformationNetwork";
							if (i=="5") val="DeformationStation";
							if (i=="6") val="DeformationInstrument";
					
							if (i=="7") val="GasNetwork";
							if (i=="8") val="GasStation";
							if (i=="9") val="GasInstrument";
					
							if (i=="10") val="HydrologicNetwork";
							if (i=="11") val="HydrologicStation";
							if (i=="12") val="HydrologicInstrument";
					
							if (i=="13") val="ThermalNetwork";
							if (i=="14") val="ThermalStation";
							if (i=="15") val="ThermalInstrument";
					
							if (i=="16") val="FieldsNetwork";
							if (i=="17") val="FieldsStation";
							if (i=="18") val="FieldsInstrument";
							if (i=="19") val="";
							document.form1.sttype.length++;
							document.form1.sttype.options[document.form1.sttype.length-1].text=val;
							document.form1.sttype.options[document.form1.sttype.length-1].value=val;}
					</script>
STRING;
				}
?>
			</div>
		</div>
		<div id="rightright" style="width:50%;  height:60px; float:left;"></div>
	</div>
	
	<div id="networketc">
	</div>
	
	<div style="float:left";">
		<div style="padding-left:20px;">
			Browse file to convert:<br>
			<input name="MAX_FILE_SIZE" type="hidden" value="2000000">
			<input name="fname" type="file" size="45" maxlength="100">
			<br>
			<input type="submit" name="Submit" value="Select">
		</div>
	</div>
	</form>  <!-- end form -->
</div>

<div id="extra">
</div>
</html>