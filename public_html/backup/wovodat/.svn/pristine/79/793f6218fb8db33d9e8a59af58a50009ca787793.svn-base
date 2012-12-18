<?php

$redirect=substr($_SERVER['PHP_SELF'], 1);
session_start();// Start session
session_regenerate_id(true);// Regenerate session ID
$uname="";
$ccd="";

if (isset($_SESSION['login'])) {
	$uname=$_SESSION['login']['cr_uname'];
	$ccd=$_SESSION['login']['cc_id'];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, stratovulcano">
	<link href="/css/styles_beta.css" rel="stylesheet">
	<script src="/js/jquery-1.4.2.min.js"></script>
</head>
<body>
	<div id="wrapborder">
	<div id="wrap">

		<?php include 'php/include/header_beta.php'; ?>

		<div id="content">	
			<div id="contentl">
				<div>
					<h1>Converting Data ...</h1>
					<div id="contentlhead"></div>
					<div id="contentlform">
					<p class="home3">
		<?php 
			$time = date("Y-m-d H:i:s");
		
			echo "Time: $time<br/><br/>";
			echo "Observatory Name:  $observ <br/>";
			echo "Volcano Name:  $vol <br/>";
			echo "File-type:$conv <br/>";
		
			if(isset($network))
				echo "Network Name: $network <br/>";
			
			if(isset($station))
				echo "Station Name: $station <br/>";
			
			if(isset($instrument))
				echo "Instrument Name:  $instrument <br/>";
			
			if(isset($filename2)){
				$f_csvrows_withoutheader=$count-1;
				
				$s_csvrows_withoutheader=$count2-1;
				
				echo "<br/><b>First CSV File Info:</b>";
				echo "<br/>Input File Name:  $filename <br/>";
				echo "Uploaded Total CSV rows: $f_csvrows_withoutheader rows <br/>";
				echo "Input File Size:$filesize bytes<br/>";

				echo "<br/><b>Second CSV File Info:</b>";				
				echo "<br/>Input File Name:  $filename2 <br/>";
				echo "Uploaded Total CSV rows: $s_csvrows_withoutheader rows <br/>";
				echo "Input File Size:$filesize2 bytes<br/>";		
			}else{
				$csvrows_withoutheader=$count-1;
				echo "<br/>Input File Name:  $filename <br/>";
				echo "Uploaded Total CSV rows: $csvrows_withoutheader rows <br/>";
				echo "Input File Size:$filesize bytes<br/>";
			}
			
			if(isset($fileextension))
				echo "<br/>Convert File Name:  $fileextension <br/>";
			
			if(!isset($fileerrors)){
				if(isset($filename2)){
					echo "<br/><b>Successfully converted from $filename file and $filename2 file to $fileextension file...</b>";
				}else{
					echo "<br/><b>Successfully converted from $filename file to $fileextension file...</b>";
				}
					
				echo"<br/><br/><b>If you would like to see the result of $fileextension, please click here to download it:</b>";
			
				echo"<div style='padding-left:13px;'>";
				echo"<form name='done' action='downloadxmlfile_ng.php' method='post' enctype='multipart/form-data'>";
				echo"<input name='fname' type='hidden' value='$outfile' />";
				echo"<input type='submit' value='Download XML file' />";
				echo"</form>";
				echo"</div>";
					
			}
			else{
				echo "<br/><b style='color:red;'>$fileerrors</b>";
			}	
		?>
				</p>
					</div>
					<div><p class="home2"></p></div>
				</div>
			</div>
			<div id="contentr">
				<div id="top" align="left">
					<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
					<p align="right">Login as: <b><?php print $uname; ?></b>|<a href="/populate/logout.php">Logout</a></p>

			
				</div>
				<br><br>

			</div>
		</div>
		<!-- Footer -->
		<div id="footer">
	
<?php include 'php/include/footer_main_beta.php'; ?>
			

			</div>
		</div> <!--end of wrap-->
	</div> <!--end of wrapborder-->

</body>
</html>