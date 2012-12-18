<?php
session_start();

if(!isset($_SESSION['login'])){
	header('Location:index.php?nopost=1');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes">
	<link href="/css/styles_beta.css" rel="stylesheet">
	<link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
	
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.js"></script>
	<script language='javascript' type='text/javascript'>
	
		$(document).ready(function(){

			$("#downloadtool").validate();
		
			$("a.check").click(function() {
			
				$("#downloadtool").submit(); 
			});
			
		});

	</script>


<style type="text/css">
label.error {font-size:12px; display:block; float: none; color: red;}
</style>	
	
</head>
<body>
	<div id="wrapborder">
		<div id="wrap">	
			<?php include 'php/include/header_beta.php'; ?>
			<!-- Content -->
			<div id="content">	
				
				<!-- Left content -->
				<div id="contentl"><br>
					<p style="padding:0px 0px 0px 35px;font-size:14px;font-weight:bold;">Installing WOVOdat Structure on own system</p>
				
					<p style="padding:10px 50px 0px 2px;font-size:13px;text-align:justify;">
						WOVOdat scripts are also available for countries those willing to start developing their own database for managing volcano monitoring data. This also to familiarize users/observatories with the WOVOdat data formats.
					</p>
					<p style="padding:5px 50px 0px 2px; font-size:13px;text-align:justify;">
						We provide a ready installable MySQL database template (WOVOdat database), which follow schematic structure and format of WOVOdat, designated for each individual volcano observatory.	
					</p>	
					<p style="padding:5px 50px 0px 2px; font-size:13px;text-align:justify;">
						An interactive tool for user to submit data is also provided (<a href="wovodat_Tool.tar">WOVOdat tool</a>). The data will be converted from common WOVOdat CSV format into WOVOdat XML common formats (WOVOml), uploaded and store in the database system.	
					</p>					
					<p style="padding:5px 50px 0px 2px; font-size:13px;text-align:justify;">
						Detail information about installation is explained in the <a href="readme.pdf" target="_blank">README</a> file. 
					</p>					
				</div>
				
				<!-- Right content -->
				<div id="contentr">
					<div id="top" align="left">
						<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
						<p align="right">Login as: <b>
						<?php 
							echo $_GET['username']; 
							echo"</b>|<a href=\"http://{$_SERVER['SERVER_NAME']}/populate/logout.php\">Logout</a></p>"; 
						?>
					</div>	
					<div style="background:#ddffdd;"><br>
						<h2 style="padding:10px 0px 0px 30px;text-align:center;"><a href=""> Downloadable Packages</a></h2>
							
						<div class="home1" style="font-size:11px;">
						
						<h4><ul><li>WOVOdat database template:</ul></li></h4>
							<p style="padding:0px 35px 0px 45px;font-weight:bold;">Please select observatory before downloading the database.</p>
							<form name="downloadtool" id="downloadtool" method="post" action="download_db.php" >
							
							<table>
								<tr>
								<th colspan='6' style="padding:0px 35px 0px 45px;">Select Observatory:</th>
								<td>
									<select id='observ' name='observ' class="required">
										<option value="">Select Observatory</option>
										<option title="Deception Volcanic Observatory" value="DVO">Argentina,DVO</option>
										<option title="Institut de Recherches Géologiques et Minières" value="IRGM">Cameroun,IRGM</option>
										<option title="Natural Resources Canada" value="NRCAN">Canada,NRCAN</option>
										<option title="Pacific Geisciences Canada" value="PGC">Canada,PGC</option>
										<option title="Geological Survey of Canada" value="GSC">Canada,GSC</option>
										<option title="Observatório Vulcanológico de Cabo Verde" value="OVCV">Cape Verde,OVCV</option>
										<option title="Southern Andes Volcano observatory" value="OVDAS">Chile,OVDAS</option>
										<option title="Heilongjiang Wudalianchi Volcanic Monitoring Observatory" value="WVO">China,WVO</option>
										<option title="Changbaishan Tianchi Volcano Observatory" value="TVO">China,TVO</option>
										<option title="Servicio Geologico Colombiano" value="SGC">Colombia,SGC</option>
										<option title="Observatoire Volcanologique du Karthala" value="OVK">Comores,OVK</option>
										<option title="Observatoire Volcanologique de Goma" value="OVG">Congo,OVG</option>
										<option title="Observatorio Vulcanológico del Arenal et Miravalles" value="OSIVAM">Costa Rica,OSIVAM</option>
										<option title="Observatorio Vulcanológico y Sismológico de Costa Rica" value="OVSICORI">Costa Rica,OVSICORI</option>
										<option title="Instituto Geofísico - Escuela Politécnica Nacional" value="IG-EPN">Ecuador,IG-EPN</option>
										<option title="El Salvador Instituto de Ciencias de la Tierra, Universidad De El Salvador" value="UES">El Salvador,UES</option>
										<option title="Observatoire Volcanologique et Sismologique de Guadeloupe" value="OVSG">France,OVSG</option>
										<option title="Observatoires Volcanologiques" value="IPGP">France,IPGP</option>
										<option title="Observatoire de Physique du Globe de Clermont-Ferrand" value="OPGC">France,OPGC</option>
										<option title="Observatoire volcanologique du Piton de la Fournaise" value="OVPF">France,OVPF</option>
										<option title="Observatoire Volcanologique de la Montagne Pelée" value="OVSM">France,OVSM</option>
										<option title="Institute of Geodynamics, National Observatory of Athens" value="NOA">Greece,NOA</option>
										<option title="Department of Geology and Physical Geography" value="DGPG">Greece,DGPG</option>
										<option title="Instituto Nacional de Sismologia, Vulcanologia, Meteorologia y Hidrologia" value="INSIVUMEH">Guatemala,INSIVUMEH</option>
										<option title="Instituto Hondureno de Ciencas de la Terra" value="IHCIT">Honduras,IHCIT</option>
										<option title="The Science Institute, Earthquake Research Branch" value="RAUNVIS">Iceland,RAUNVIS</option>
										<option title="Icelandic Meteorological Office" value="IMO">Iceland,IMO</option>
										<option title="Nordic Volcanological Institute" value="NVI">Iceland,NVI</option>
										<option title="Volcanological Survey of Indonesia" value="CVGHM">Indonesia,CVGHM</option>
										<option title="Osservatorio Vesuviano" value="INGV-OV">Italy,INGV-OV</option>
										<option title="Instituto Nazionale di Geofisica e Vulcanologica" value="INGV">Italy,INGV</option>
										<option title="Catania" value="INGV-Catania">Italy,INGV-Catania</option>
										<option title="Earthquake and Volcano Observatory" value="EVO">Japan,EVO</option>
										<option title="National Research Institute for Earth Science and Disaster Prevention" value="NIED">Japan,NIED</option>
										<option title="Kusatsu-Shirane Volcano Observatory" value="KSVO">Japan,KSVO</option>
										<option title="Japan Meteorological Agency" value="JMA">Japan,JMA</option>
										<option title="Kirishima Volcano Observatory" value="KVO">Japan,KVO</option>
										<option title="Usu Volcano Observatory" value="UVO">Japan,UVO</option>
										<option title="Volcano Research Center, Earthquake Research Institute" value="VRC-ERI">Japan,VRC-ERI</option>
										<option title="Aso Volcanological Laboratory" value="AVL">Japan,AVL</option>
										<option title="Geological Survey of Japan" value="GSJ">Japan,GSJ</option>
										<option title="Geographical Survey Institute" value="GSI">Japan,GSI</option>
										<option title="Popocatepetl Volcano Observatory" value="POVO">Mexico,POVO</option>
										<option title="Observatorio Vulcanologico, Universidad de Colima" value="UCOL">México,UCOL</option>
										<option title="Montserrat Volcano Observatory" value="MVO">Montserrat,MVO</option>
										<option title="Institute of Geological & Nuclear Sciences Ltd" value="GNS">New Zealand,GNS</option>
										<option title="Instituto Nicaraguense de Estudios Territoriales" value="INETER">Nicaragua,INETER</option>
										<option title="Rabaul Volcanological Observatory" value="RVO">Papua New Guinea,RVO</option>
										<option title="Observatorio Volcanologico de Arequipa" value="IGP">Peru,IGP</option>
										<option title="Philippine Institute of Volcanology and Seismology" value="PHIVOLCS">Philippines,PHIVOLCS</option>
										<option title="Observatório Vulcanológico da Universidade dos Açores" value="OVUA">Portugal,OVUA</option>
										<option title="Institute of Volcanic Geology and Geochemistry" value="IVGG">Russia,IVGG</option>
										<option title="Water and Mineral Resources Division" value="WMRD">Solomon Islands,WMRD</option>
										<option title="Institute of Technology and Renewable Energy" value="ITER">Spain,ITER</option>
										<option title="Seismic Research Unit" value="SRU">Trinidad and Tobago,SRU</option>
										<option title="British Geological Survey" value="BGS">United Kingdom,BGS</option>
										<option title="Mount Erebus Volcano Observatory" value="MEVO">USA,MEVO</option>
										<option title="Alaska Volcano Observatory" value="AVO">USA,AVO</option>
										<option title="The Hawaiian Volcano Observatory" value="HVO">USA,HVO</option>
										<option title="David A. Johnston Cascades Volcano Observatory" value="CVO">USA,CVO</option>
										<option title="Smithsonian Institution, Global Volcanism Program" value="SI-GVP">USA,SI-GVP</option>
										<option title="U.S. Geological Survey" value="USGS">USA,USGS</option>
									</select>
								</td>
								<td></td>
								</tr>
							
								<tr height="50">
								<th colspan='6' style="padding:0px 0px 0px 45px;">Download WOVOdat Database: </th>
								<td><a href="#" class="check">WOVODAT Database package</a> </td>
								
								</tr>
								
								<tr>
								<th colspan='6'><ul><li>Download WOVOdat UI Tool: </ul></li></th>
								<td>  <a href="wovodat_Tool.tar">WOVODAT User Interface Tool</a> </td>
								</tr>
								
							</table>
							</form>
							<br/><br/>	
						</div>	
					</div>
				</div> <!-- contentr -->
			</div> <!-- content -->
			<!-- Footer -->
			<div id="footer">
				<?php include 'php/include/footer_main_beta.php'; ?>
			</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->
</body>
</html>
