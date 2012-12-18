<?php
session_start();  // Start session
session_regenerate_id(true);// Regenerate session ID
$uname="";
$ccd="";

if(isset($_SESSION['login'])) {
	$uname=$_SESSION['login']['cr_uname'];
	$ccd=$_SESSION['login']['cc_id'];
}
else{
header('Location: '.$url_root.'login_required.php');// Session was not yet started.... Redirect to login required page
exit();
}

?>
<html>
	<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
	<script language='javascript' type='text/javascript'>
	
	$(document).ready(function(){
	
		$("#observ").change(function(){
			$('#vol2').val('...');
			var institute = $("select#observ").val();
			loadvolcano (institute);  // after get obs, then get values of vocalno & convert file 
			checkvolcano();
		});

		
		$("select#vol2").live('click', function() {		
			checkvolcano();
		}); 
		
		
		function checkvolcano(){    // if nothing select in volcano box, then don't load convert value..
			
			var volu = $("select#vol2").val();
					
			if(volu != '...'){
				loadconvert();                
			}else{                //put bk convert drop down as a first time "Non-selected" case
				$('select#conv').remove();
				$("#convertblock").html("<select name='conv' id='conv' style='width:180px;'> <option value='...'>...</option></select>");
			}
			
			resetall();		 // Reset all like first time page starts loading
		}
		
		function resetall(){
			
			$('select#network').remove();
			$('select#stat').remove();
			$('#id_net_stat_text').remove();
			$('select#stat2').remove();
			$('#id_net_stat_text2').remove();
			$('select#stat3').remove();
			$('#id_net_stat_text3').remove();			
			$('h1').remove();
			
			$('#trm_ivl_select').val('Network');
			$('#eventtype_waveselect').val('...'); 
			$('#eventcode').val('');			
					
			$('#kmeter').val('40');
			$('#kilometer').css("display","none");			
			$('#trm_ivl').css("display","none");
			$('.spaceid').remove();
			$('.spaceclass2').remove();
			$('.spaceclass3').remove();
			$('.spaceclass4').remove();
			$('.spaceclass5').remove();
			
			$('#rsam_ssam').css("display","none");
			$('#rsam_ssamcode').val('');
			
			$('#uploadfile').css("display","block");
			$('#uploadfile2').css("display","none");
			$('#uploadfile3').css("display","none");    
			$('#wave_textfield').css("display","none");
			
			$('#gd_plume').css("display","none");
			$('#satellite_type').css("display","none");
			$('select#gd_plume_select').val('...');
			$('select#sate_type_select').val('...');  
			$('select#satellite').remove();
			$('select#airplane').remove();
			$('#id_air_sat_select').remove();
			
			$('select#instrument').remove();
			$('#id_inst_text').remove();
		}


		function loadvolcano(institute){
			
			$('#vol2').load('./convertie/selectVolOfInstitute2_ng.php',"kode=" + institute); 
			return false;
		}

		function loadconvert( ) {
			
			$('#conv').load('./convertie/wovodatdatatablelist.php');
			return false;           
		}
	
		
	
		$("select#conv").live('click', function() {	
		
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 
	
			resetall();		 // Reset all like first time page starts loading		
		
			if(stationvalue != '...'){
			
				var sg = document.form1.vol2.selectedIndex;   // get volcano index 
				var sgu = document.form1.vol2.options[sg].value;  //get volcano value
				
				var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
				var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			
				
				if(stationdisplay=="EventDataFromNetwork"){
					
		
					$('#networkform').load('./convertie/selectNetwork_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue);
					
				}	
				else if(stationdisplay=="EventDataFromSingleStation" || stationdisplay =="RSAMData" || stationdisplay =="SSAMData" || stationdisplay=="RepresentativeWaveform" || stationdisplay == "ElectronicTiltData" || stationdisplay == "TiltVectorData" || stationdisplay == "StrainMeterData" || stationdisplay=="EDMData" || stationdisplay =="AngleData" || stationdisplay =="GPSData" || stationdisplay == "GPSVectors" || stationdisplay == "DirectlySampledGas" || stationdisplay == "SoilEffluxData" || stationdisplay == "PlumeData" || stationdisplay == "HydrologicData" || stationdisplay == "MagneticFieldsData" || stationdisplay == "MagnetorVectorData" || stationdisplay == "ElectricFieldsData" || stationdisplay == "GravityData" ||stationdisplay == "GroundBasedThermalData"){  // Added stationdisplay=="PlumeData" on 9-May-2012
					
					$.get('./convertie/selectcheckstation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay,function(result){
					
						var check_sn_jj = result;
					
						if(check_sn_jj == 'true'){
						
							if(stationdisplay =="EDMData" || stationdisplay == "MagneticFieldsData" || stationdisplay == "ElectricFieldsData" || stationdisplay == "GravityData"){
								
								$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer',function(){

									var currentId= $("#stationform h1").attr("id");
									
									if(currentId != "nostation" ){
								
										$('#stationform2').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer&station2=stat2');	
									}
								});			
							}else if(stationdisplay =="AngleData" || stationdisplay =="GPSData"){
								
								$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer',function(){

									var currentId= $("#stationform h1").attr("id");
									
									if(currentId != "nostation" ){
									
										$('#stationform2').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer&station2=stat2');
										
										$('#stationform3').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer&station3=stat3');
									}
								});	
							
							}else{
								$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer');
							}		
						}
						else{
							$('#kilometer').css("display","block");
							showkilometer();
						}
					});					
				}     
				else if(stationdisplay=="SeismicTremor" || stationdisplay=="IntervalSwarmData"){				
										
					$('#trm_ivl').css("display","block");
					load_trm_ivl();

				} // deleted stationdisplay=="PlumeData" on 9-May-2012
				else if(stationdisplay=="ThermalImage and ThermalImageData"){
				
					$('#gd_plume').css("display","block");
					
					if(stationdisplay=="ThermalImage and ThermalImageData"){
						$('#uploadfile').css('display','none');
						$('#uploadfile2').css('display','block');
						$('#text1').text("Browse Thermal Image file to convert:");
						$('#text2').text("Browse Thermal Pixels file to convert:");
						
					}
					
					load_gd_plume();
				}
				else if(stationdisplay=="InSARImage and InSARData" ){
					$('#uploadfile').css('display','none');
					$('#uploadfile2').css('display','block');
					
					$('#text1').text("Browse InSAR Image file to convert:");
					$('#text2').text("Browse InSAR Data file to convert:");				
				
					$('#sate_air_select').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=none&satellitetype=S');	
				}
			}else{	
				resetall();		 // Reset all like first time page starts loading
			}
		}); 

		$("select#kmeter").live('click', function() {
			showkilometer();
		});
		
		function showkilometer(){
			var kilometervalue=$('#kmeter').attr('value');   //get kilo meter value
			
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			
			if(stationdisplay =="EDMData" || stationdisplay == "MagneticFieldsData" || stationdisplay == "ElectricFieldsData" || stationdisplay == "GravityData"){			

				$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue,function(){

					var currentId= $("#stationform h1").attr("id");
				
					if(currentId != "nostation" ){	

						$('#stationform2').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue+ '&station2=stat2');	
					}
				});	
			}
			else if(stationdisplay =="AngleData" || stationdisplay =="GPSData" || stationdisplay == "LevelingData"){			
				
				$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue,function(){

					var currentId= $("#stationform h1").attr("id");
					
					if(currentId != "nostation" ){
					
						$('#stationform2').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue+ '&station2=stat2');				
					
						$('#stationform3').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue+ '&station3=stat3');
					}	
				});	
			}
			else{
			
				if(stationdisplay=="ThermalImage and ThermalImageData"){// deleted stationdisplay=="PlumeData" on 9-May-2012
					$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue);	
				}
				else{
					$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+'&stationdisplay='+stationdisplay+ '&kilometer=' +kilometervalue);	
				
				}
			}
		}	

		
		$("select#trm_ivl_select").live('click', function() {
			load_trm_ivl(); 
		});
			

		function load_trm_ivl(){	   // Tremor / Interval case 
			$('.spaceid').remove();
			var trm_ivl_value=$('#trm_ivl_select').attr('value');   

			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value

			if(trm_ivl_value == 'Network'){	
			
				$('select#stat').remove();
				$('#id_net_stat_text').remove();
				
				$('#kmeter').val('40');
				$('#kilometer').css("display","none");
				$('h1').remove();
				
				$('#networkform').load('./convertie/selectNetwork_data_ng.php','volcan='+sgu+ '&stationdisplay='+	stationdisplay+ '&stationvalue='+stationvalue);
			}
			else if(trm_ivl_value == 'Station'){

				$('select#network').remove();
				$('#id_net_stat_text').remove();
				$('h1').remove();
				
				$.get('./convertie/selectcheckstation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay,function(result){
					
					var check_sn_jj = result;
				
					
					if(check_sn_jj == 'true'){
						$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer');
					}
					else{
						$('#kilometer').css("display","block");
						showkilometer();
					}
				});	
			}
			else{
				$('select#network').remove();
				$('select#stat').remove();
				$('#id_net_stat_text').remove();
				$('#kmeter').val('40');
				$('#kilometer').css("display","none");
				$('.spaceid').remove();
				$('h1').remove();
			}
		
		}
		
		
		
		$("select#gd_plume_select").live('click', function() {
			load_gd_plume();
		});
			

		function load_gd_plume(){		 // Gd plume data
		
			var gd_plume_value=$('#gd_plume_select').attr('value');   

			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value		
		
			if(gd_plume_value == "cs"){
				
				$('#satellite_type').css("display","block");
				$('select#sate_type_select').val('...');
				
				$('select#stat').remove();
				$('#id_net_stat_text').remove();
				$('#kmeter').val('40');
				$('#kilometer').css("display","none");
				$('h1').remove();
				
				load_satellite();
			}
			else if(gd_plume_value == 'ground_based'){
				
				$('#satellite_type').css("display","none");
				$('select#satellite').remove();
				$('select#airplane').remove();
				$('#id_air_sat_select').remove();
				$('h1').remove();
				
				$.get('./convertie/selectcheckstation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay,function(result){
					
					var check_plume_jj = result;
				
					
					if(check_plume_jj == 'true'){
						//$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=none'); changed on 23feb2012 
								
						$('#stationform').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=nokilometer');						
					}
					else{
						$('#kilometer').css("display","block");
						showkilometer();
					}
				});			
			}
			else{

				$('#satellite_type').css("display","none");
				$('select#sate_type_select').val('...');

				$('select#stat').remove();
				$('#id_net_stat_text').remove();	
				$('#kmeter').val('40');
				$('#kilometer').css("display","none");
				
				$('select#satellite').remove();
				$('select#airplane').remove();
				$('#id_air_sat_select').remove();
				$('h1').remove();
			}
		}
	
		$("select#sate_type_select").live('click', function() {
			$('select#satellite').remove();
			$('select#airplane').remove();
			$('#id_air_sat_select').remove();
			$('h1').remove();
			
			$('select#instrument').remove();
			$('#id_inst_text').remove();
			load_satellite();
		});		
		
		function load_satellite(){
			
			var sate_type_value=$('#sate_type_select').attr('value');   
			
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value	
			
			if(sate_type_value != '...'){
				$('#sate_air_select').load('./convertie/selectStation_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&kilometer=none&satellitetype=' +sate_type_value);	
			}	
		}
	
		$("select#airplane").live('click', function() {
			
			var satellitename=$('#airplane').attr('value');
			 load_satellite_instrument(satellitename);
		});	
		
		function load_satellite_instrument(satellitename){
	
			$('select#instrument').remove();
			$('#id_inst_text').remove();
			$('h1').remove();
	
			var sate_type_value=$('select#sate_type_select').attr('value'); 
	
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value	
			
			if(satellitename != 'Choose Satellite'){
				$('#instrumentform').load('./convertie/selectInstrument_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&staname='+satellitename+'&satellitetype='+sate_type_value);
			}		
		}
	
		$("select#stat").live('click',function() {   // click station dropdown
			
			$('select#instrument').remove();
			$('#id_inst_text').remove();
				
			
			var stationname=$("#stat option:selected").text();  //get station name		
			
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value		
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value

			
			if(stationdisplay== "RSAMData" || stationdisplay== "SSAMData"){
				$("#rsam_ssam").css("display","block");
			
			}else if(stationdisplay=="RepresentativeWaveform"){
				$('#wave_textfield').css("display","block");
			}
			else if(stationdisplay == "ElectronicTiltData" || stationdisplay == "TiltVectorData" || stationdisplay == "StrainMeterData" || stationdisplay =="EDMData" || stationdisplay == "AngleData" || stationdisplay == "GPSData" || stationdisplay == "GPSVectors" || stationdisplay == "DirectlySampledGas" || stationdisplay == "SoilEffluxData" || stationdisplay == "PlumeData" || stationdisplay == "HydrologicData" || stationdisplay == "MagneticFieldsData" || stationdisplay == "MagnetorVectorData" || stationdisplay == "ElectricFieldsData" || stationdisplay == 
			"GravityData" || stationdisplay == "GroundBasedThermalData" || stationdisplay== "ThermalImage and ThermalImageData"){   // Added stationdisplay=="PlumeData" on 9-May-2012
			
				var station_value=$('#stat').attr('value');    // Get station value 
				
				if(station_value != 'Choose Station'){
					$('#instrumentform').load('./convertie/selectInstrument_data_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&staname='+stationname);	
				}
			}
						
		});
		
	});
		
	</script>

<div style="padding:0px 0px 0px 5px;">


<?php
	$mndata=$_GET['tipedata'];
	if($mndata=="data"){
		echo "<h2>Conversion of Monitoring Data</h2>";
		echo "<p><blockquote>Input: CSV file of seismic, deformation, gas, hydrology, field, or thermal data. The data must follow WOVOdat1.1 standard format</blockquote></p>";
	}
?>	

	<form name="form1" id="form1" action="convertie/commonconvertdata_ng.php" method="post" enctype="multipart/form-data">
		<div id="lfleft" style="width:5%;"></div>
		<div style="width:40%;  padding-left:90px;">
			<p1>Observatory (data owner): </p1><br>
			<div id='observo'>
				<select name='observ' id='observ' style="width:180px">
					<option value="Select institute">.....</option>
<?php
						include 'php/include/db_connect.php';

						if ($_SESSION['permissions']['access']==0){
								$result = mysql_query("select cc_code, cc_country, cc_obs, cc_id from cc		order by cc_country");
						}else{
							$result = mysql_query("select cc_code, cc_country, cc_obs, cc_id from cc where cc_id='$ccd' order by cc_country");
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
		
		<div style="width:10%;">&nbsp;</div>
			<div id="vola2" style="width:45%; padding-left:90px;">
			<p1>Volcano: </p1><br>
			<div id="volano2">
				<select name='vol2' id='vol2' style="width:180px;">
				<option value='...'> ... </option>
				</select>
			</div>
		</div>
	
	

		<div style="width:10%;">&nbsp;</div>
		<div id="convertid" style="width:45%;padding-left:90px;">
			<p1>File content to convert: </p1><br>
			<div id="convertblock">
				<select name='conv' id='conv' style="width:180px;">
				<option value='...'> ... </option>
				</select>
			</div>
		</div>


		<div id="trm_ivl" style="display:none; padding-left:90px;">
			<div id="trm_ivl_form">
			<p> If an event is located by a network (or) by a single station, please select "Network" (or) "Station" respectively from a below drop down. </p>
			<select id='trm_ivl_select' name='trm_ivl_select' style='width:180px'>
				<option value='Network'> Network </option>
				<option value='Station'> Station </option>
			</select>	
			</div>
		</div>

		<div id="gd_plume" style="display:none; padding-left:90px;">
			<br/><p1>Please choose Ground_Based Station (OR) Satellite:</p1>
			<select id='gd_plume_select' name='gd_plume_select' style='width:180px;'>
				<option value='...' selected='true'>...</option>
				<option value='ground_based'>Ground_Based Station</option>
				<option value='cs'>Satellite</option> 
			</select>
		</div>		
		
		<div id="satellite_type" style="display:none; padding-left:90px;">
			<br/><p1>Please choose Satellite Type:</p1><br/>
			<select id='sate_type_select' name='sate_type_select' style='width:180px;'>
				<option value='...' selected='true'>...</option>
				<option value='A'>Airplane</option>
				<option value='S'>Satellite</option>
			</select>
		</div>		
		
	
		<div id="sate_air" style="width:45%;padding-left:90px;">
			<div id="sate_air_select">
			</div>
		</div>		
		
		<div id="networkblock" style="width:45%;padding-left:90px;">
			<div id="networkform">
			</div>
		</div>

	
		<div id="kilometer" style="width:45%; padding-left:90px;display:none;">
			<br/><p1>Choose Kilometer to see station: </p1><br/>
			<div id="kmeter_id">
				<select name='kmeter' id='kmeter' style="width:180px;">
					<option value='40' selected='ture'>40</option>
					<option value='80'>80</option>
					<option value='100'>100</option>
					<option value='all'>See all stations</option>
				</select>
			</div>
		</div>

		
		<div id="stationblock" style="width:45%;padding-left:90px;">
			<div id="stationform">
			</div>
		</div>
		

		<div id="stationblock" style="width:45%;padding-left:90px;">
			<div id="stationform2">
			</div>
		</div>		

		<div id="stationblock" style="width:45%;padding-left:90px;">
			<div id="stationform3">
			</div>
		</div>
		
		<div id="instrblock" style="width:45%;padding-left:90px;">
			<div id="instrumentform">
			</div>
		</div>
		
		<div id="rsam_ssam" style="display:none; padding-left:90px;">
			<br/><p1>Please Enter RSAMSSAM Code here:</p1>
			<input type="text" name="rsam_ssamcode" id="rsam_ssamcode" maxlength="30" style='width:180px'/>
		</div>		

		
		<div id="wave_textfield" style="display:none; padding-left:90px;">
			<div>
			<br/>Please Select Event you want to upload waveform:<br/>
			<select id='eventtype_waveselect' name='eventtype_waveselect' style='width:180px;'> 
				<option value='...' selected='true'> ... </option>
				<option value='EventDataFromNetwork'>EventDataFromNetwork</option>
				<option value='EventDataFromSingleStation'>EventDataFromSingleStation </option>
				<option value='SeismicTremor'>SeismicTremor</option>
			</select>
			</div>
			<br/>
			Please Enter Event Code here:<br/><input type="text" name="eventcode" id="eventcode" maxlength="30" style='width:180px'/>
		</div>		
		
	

		
	<div style="width:10%;">&nbsp;</div><div style="width:10%;">&nbsp;</div>
	<div id='uploadfile' style="float:left;display:block;">
		<div id='submit_form' style="padding-left:20px;">
			Browse file to convert:<br>
			<input name="MAX_FILE_SIZE" type="hidden" value="2000000"/>
			<input id="fname1" name="fname1" type="file" size="45" maxlength="100"/>
			<br>  
			<input type="submit" name="Submit" value="Select"/>
		</div>
	</div>  

	<div id='uploadfile2' style="float:left;display:none;">
		<div id='submit_form' style="padding-left:20px;">
			<div id='text1'></div>
			<input name="MAX_FILE_SIZE" type="hidden" value="2000000"/>
			<input id="fname" name="fname" type="file" size="45" maxlength="100"/>
			<br>  
			<div id='text2'></div>
			<input id="secondname" name="secondname" type="file" size="45" maxlength="100"/>
			<br>  			
			<input type="submit" name="Submit" value="Select"/>
		</div>
	</div>  
	
	</form>  
</div>

<div id="extra">
</div>
</html>