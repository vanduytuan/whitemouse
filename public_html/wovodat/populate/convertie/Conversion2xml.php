<?php
//ini_set('display_errors',1);
$redirect=substr($_SERVER['PHP_SELF'], 1);
?>
<?php
	// Start session
	session_start();
	// Regenerate session ID
	session_regenerate_id(true);
	$uname="";
	$ccd="";
?>
<script src="/js/jquery.js"></script>

<?php
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
	<meta name="keywords" content="Volcano, Vulcano, Volcanoes">
	<link href="/css/styles_beta.css" rel="stylesheet">
	<script src="/js/jquery.js"></script>
	<script src="/js/flot/jquery.flot.min.js" language="javascript" type="text/javascript"></script>
	<!--[if IE]><script src="/js/flot/excanvas.min.js" language="javascript" type="text/javascript"></script><![endif]-->
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
// 								echo "checked_00_first1"."<br>";
								include "./Conversion2xml_c.php";
//								print_ary($ary2,1); // wovoml array of data
//							?>
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
				<div id="contentrtitle">
					<p class="home1"><?php echo ucfirst($mondata).":&nbsp".$statype."---:".strtolower($datatype)."&nbsp&nbsp&nbsp&nbsp   Sta:".ucfirst($staname)."&nbsp&nbsp&nbsp Volcano: ".$volcn ?></p>
				</div>
				<div id="contentrplot" style="margin-left:20px">
					<p class="vsmall_text"></p>
				</div>
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





