<?php

// Get root url
require_once "php/include/get_root.php";

// Check login
require_once "php/include/login_check.php";

// Get user ID
$user_id=$_SESSION['login']['cc_id'];

	?>
	<script language="javascript">alert("temporary stop1");</script>
	<?
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
	<link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
	<script language="javascript" type="text/javascript" src="/js/scripts.js"></script>
</head>
<body>
	<div id="wrapborder">
	<div id="wrap">
		<?php include 'php/include/header_beta.php'; ?>
		<!-- Content -->
		<div id="content">	
				<div id="top">
				
					<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
					<p>You are logged in as <b><?php print $uname; ?></b> | <a href="logout.php">Logout</a></p>
					
					
				</div>
			<div id="contentl">
				<!-- Top of the page -->
			
				<!-- Page content -->
				<h1>Test For Record-Delete</h1>

<?php				
if($user_id==200){
	require_once("php/MYDB.php");
	// Connect to database
	$conn=db_connect();
	$tbl_nama="cn";
	$rec_id="191";
//	$sql="DELETE FROM ".$tbl_nama." WHERE ds_id=".$tbl_id;
//	$sql="DELETE FROM ".$conn->escapeSimple($tbl_nama)." WHERE cn_id=".$rec_id;

	// Process SQL query
	//	$sql=mb_convert_encoding($sql,"ISO-8859-1","UTF-8");
//	$result = $conn->query($sql);
	$conn->disconnect();
	?>
	<script language="javascript">alert("temporary stop2");</script>
	<p>A record deleted from Table: <?php print $tbl_nama; ?> at id: <?php print $rec_id; ?></p>
	<?
}else{
	?><p>Nothing deleted</p><?
}
?>
			</div>
			<div id="contentr">
			</div>
		</div>
		
		<!-- Footer -->
		<div id="footer">
			<?php include 'php/include/footer_beta.php'; ?>
		</div>
		
	</div>
</body>
</html>
