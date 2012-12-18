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
	<script src="/js/jquery-1.4.2.min.js"></script>
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
			resetall();
		}
		
		
		function loadvolcano(institute){
			
			$('#vol2').load('./convertie/selectVolOfInstitute2_ng.php',"kode=" + institute ,function(response, status, xhr) {
				if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#vol2").html(msg + xhr.status + " " + xhr.statusText);
				}
			});
			return false;
		}

		function loadconvert( ) {
			
			$('#conv').load('./convertie/wovodatmonitoringtablelist_ng.php');
			return false;           
		}
		
		function resetall(){
			
			$('select#network').remove();
			$('select#station').remove(); 
			$('select#instrument').remove(); 
			
			$('#pnet').remove(); 
			$('#pstat').remove(); 
			$('#pinst').remove(); 
			$('h1').remove();
			
			$('#kmeter').val('40');
						
			$('#kilometer').css("display","none");
			$('#digen_div').css("display","none");
			$('#ditltstrain_div').css("display","none"); 
		}
		
		$("select#conv").live('click', function() {	
		
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 
			
			resetall();			
			
			if(stationvalue != '...'){
			
				var sg = document.form1.vol2.selectedIndex;   // get volcano index 
				var sgu = document.form1.vol2.options[sg].value;  //get volcano value
				
				var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
				var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			
				if(stationdisplay=="SeismicStation" || stationdisplay=="DeformationStation" || stationdisplay=="GasStation" || stationdisplay=="HydrologicStation" || stationdisplay=="ThermalStation" || stationdisplay=="FieldsStation"){
					
					$('#networkform').load('./convertie/selectNetwork_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue);
	
				}
				else if(stationdisplay=="SeismicInstrument" || stationdisplay=="SeismicComponent" || stationdisplay=="DeformationInstrument_General" || stationdisplay=="DeformationInstrument_Tilt/Strain" || stationdisplay=="GasInstrument" || stationdisplay=="HydrologicInstrument" || stationdisplay=="ThermalInstrument" || stationdisplay=="FieldsInstrument"){				
					
					$('#networkform').load('./convertie/selectInstrument_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue+'&networkdisplay=none&stationcheck=check1&instrucomponent=noinstru1&kilometer=nokilometer'); 
				}               
			}else{	
				resetall();
			}
		}); 
		
		$("select#network").live('click', function() {
			
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			
			if(stationdisplay=="SeismicInstrument" || stationdisplay=="SeismicComponent" || stationdisplay=="DeformationInstrument_General" || stationdisplay=="DeformationInstrument_Tilt/Strain" || stationdisplay=="GasInstrument" || stationdisplay=="HydrologicInstrument" || stationdisplay=="ThermalInstrument" || stationdisplay=="FieldsInstrument"){
				
				var networkdisplay=$('#network').attr('value');
				
				if(networkdisplay !='Choose Network'){
					loadstation();
				}
				else{
				
					$('select#station').remove(); 
					$('select#instrument').remove(); 
					$('#pstat').remove(); 
					$('#pinst').remove(); 
					$('h1').remove();
					
					$('#kmeter').val('40');
					$('#kilometer').css("display","none");
					$('#digen_div').css("display","none");
					$('#ditltstrain_div').css("display","none");
				}	
			}	
		});

		
		function loadstation(){	//choose instrument case 
		
			var networkdisplay=$('#network').attr('value');

			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			
			
			$.get('./convertie/selectStation_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue+ '&networkdisplay='+networkdisplay,function(result){
					
					var check_sn_jj = result;
				
					
					if(check_sn_jj == 'true'){
						$('#stationform').load('./convertie/selectInstrument_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue+'&networkdisplay=' +networkdisplay+'&stationcheck=check2&instrucomponent=noinstru2&kilometer=nokilometer');
					}
					else{
						$('#kilometer').css("display","block");
						showkilometer();
					}
			});
		}  
		
		$("select#kmeter").live('click', function() {
			showkilometer();
		});
		
		
		function showkilometer(){
			var kilometervalue=$('#kmeter').attr('value');   //get kilo meter value
			
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
			
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			var networkdisplay=$('#network').attr('value');
			
			$('#stationform').load('./convertie/selectInstrument_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue+'&networkdisplay=' +networkdisplay+			'&stationcheck=check2&instrucomponent=noinstru2&kilometer=' +kilometervalue);
		}
			 
		$("select#station").live('click', function() {
			
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value
			var stationcheck=$('#station').attr('value');
				
			if(stationcheck !='Choose Station'){	
		
				if(stationdisplay=="SeismicComponent"){
					loadinstrument();
				}
				else if(stationdisplay=="DeformationInstrument_General" || stationdisplay=="DeformationInstrument_Tilt/Strain"){
					digen_tilstrain(stationdisplay);
				}	
			}
			else{
				
				$('select#instrument').remove();
				$('#pinst').remove(); 
				$('h1').remove();
				
				$('#digen_select').val('Choose Instrument type');
				$('#ditltstrain_select').val('Choose Instrument type');
			}
		
		
		}); 
		
		
		function digen_tilstrain(stationdisplay){ 
		   	$('#digen_select').val('Choose Instrument type');
			$('#ditltstrain_select').val('Choose Instrument type');
			
			if(stationdisplay=="DeformationInstrument_General"){
				$('#digen_div').css("display","block");
			}
			else if(stationdisplay=="DeformationInstrument_Tilt/Strain"){
				$('#ditltstrain_div').css("display","block");
			}	
		} 
	
		function loadinstrument(){
		
			var networkdisplay=$('#network').attr('value');
			var station=$('#station').attr('value');
			var kilometervalue=$('#kmeter').attr('value');   //get kilo meter value
			
			var sg = document.form1.vol2.selectedIndex;   // get volcano index 
			var sgu = document.form1.vol2.options[sg].value;  //get volcano value
			
			var stationvalue=$('#conv').attr('value');   //get File content to convert value 	
			var stationdisplay=$("#conv option:selected").text();  //get station "display" value

			$('#instruform').load('./convertie/selectInstrument_ng.php','volcan='+sgu+ '&stationdisplay='+stationdisplay+ '&stationvalue='+stationvalue+'&networkdisplay=' +networkdisplay+'&stationcheck='+station+ '&instrucomponent=noinstru3&kilometer=' +kilometervalue);
		}
	});
		
	</script>

<div style="padding:0px 0px 0px 5px;">


<?php
	$mndata=$_GET['tipedata'];
	if($mndata=="station"){
		echo "<h2>Conversion of Monitoring System</h2>";
		echo "<p></p><blockquote>Input: CSV file of network, station, or instrument information. The data must follow the WOVOdat1.1 standard format</blockquote>";
	}
?>
	<br>
	<br>

		<form name="form1" id="form1" action="./convertie/commonconvertfile_ng.php" method="post" enctype="multipart/form-data">
		<div id="lfleft" style="width:5%;"></div>
		<div style="width:40%;  padding-left:90px;">
			<p1>Observatory (data owner): </p1><br>
			<div id='observo'>
				<select name='observ' id='observ' style="width:180px">
					<option value="Select institute">...</option>
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
			<p1>Type of Data to convert: </p1><br>
			<div id="convertblock">
				<select name='conv' id='conv' style="width:180px;">
				<option value='...'> ... </option>
				</select>
			</div>
		</div>

		
		<div style="width:10%;">&nbsp;</div>
		<div id="networkblock" style="width:45%;padding-left:90px;">
			<div id="networkform">
			</div>
		</div>

		
		<div style="width:10%;">&nbsp;</div>
		<div id="kilometer" style="width:45%; padding-left:90px;display:none;">
			<p1>Choose Kilometer to see station: </p1><br>
			<div id="kmeter_id">
				<select name='kmeter' id='kmeter' style="width:180px;">
					<option value='40' selected='ture'>40</option>
					<option value='80'>80</option>
					<option value='100'>100</option>
					<option value='all'>See all stations</option>
				</select>
			</div>
		</div>
		
		
		<div style="width:10%;">&nbsp;</div>
		<div id="stationblock" style="width:45%;padding-left:90px;">
			<div id="stationform">
			</div>
		</div>
		
		<div style="width:10%;">&nbsp;</div>
		<div id="digen_div" style="width:45%; padding-left:90px;display:none;">
			<p1>Please Choose Instrument types: </p1><br>
			<div id="digen_id">
				<select name='digen_select' id='digen_select' style="width:180px;">
					<option value='Choose Instrument type' selected='ture'>Choose Instrument type</option>
					<option value='Angle'>Angle</option>
					<option value='CGPS'>CGPS</option>
					<option value='EDM'>EDM</option>
					<option value='EDM Reflector'>EDM Reflector</option>
					<option value='GPS'>GPS</option>
					<option value='Total_Station'>Total_Station</option>
					<option value='Other_di_gen types'>Other instrument types</option>				
				</select>
			</div>
		</div>
		
		<div id="ditltstrain_div" style="width:45%; padding-left:90px;display:none;">
			<p1>Please Choose Instrument types: </p1><br>
			<div id="ditltstrain_id">
				<select name='ditltstrain_select' id='ditltstrain_select' style="width:180px;">
					<option value='Choose Instrument type' selected='ture'>Choose Instrument type</option>
					<option value='Tilt'>Tilt</option>
					<option value='Strain'>Strain</option>
				</select>
			</div>
		</div>	
		
		
		<div style="width:10%;">&nbsp;</div>
		<div id="instrublock" style="width:45%;padding-left:90px;">
			<div id="instruform">
			</div>
		</div>
		
		<div style="width:10%;">&nbsp;</div>
		<div id="componentblock" style="width:45%;padding-left:90px;">
			<div id="componentform">
			</div>
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
	</form>  
</div>
</html>