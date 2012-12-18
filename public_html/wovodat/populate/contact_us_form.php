<?php

// Start session
session_start();

// Regenerate session ID
session_regenerate_id(true);

// If session already started
if (isset($_SESSION['HTTP_USER_AGENT'])) {
	if ($_SESSION['HTTP_USER_AGENT']!=md5($_SERVER['HTTP_USER_AGENT'])) {
		// Destroy session variables
		session_destroy();
	}
}

// Else
$_SESSION['HTTP_USER_AGENT']=md5($_SERVER['HTTP_USER_AGENT']);

// If 1st time access
if (!isset($_SESSION['contact'])) {
	// Blank fields
	$subject="";
	$message="";
	$name="";
	$email="";
}
// 2nd time access
else {
	// Get fields
	$subject=$_SESSION['contact']['subject'];
	$message=$_SESSION['contact']['message'];
	$name=$_SESSION['contact']['name'];
	$email=$_SESSION['contact']['email'];
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
	<link href="/js2/navig.css" rel="stylesheet">
	<link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
</head>
<body>

	<div id="popupBox" title="Message from WOVOdat"></div>
	<div id="wrapborder">
		<div id="wrap">
<!-- Header -->
			<?php include 'php/include/header_beta.php'; ?>
			<!-- Content -->
			<div id="content" >

<!-- Left -->
				<div id="contentlview"><br><br>
					<table width="400" border="0" align="center" cellpadding="3" cellspacing="1">
						<tr><td><strong><span style="font-size:16px;">Contact Form </span></strong></td></tr>
					</table>
					<table width="400" border="0" align="center" cellpadding="0" cellspacing="1">
						<tr>
							<td>
								<form name="form1" method="post" action="/populate/contact_us.php">
									<table width="100%" border="0" cellspacing="1" cellpadding="3">
										<tr>
											<td>Subject</td>
											<td>:</td>
											<td><input name="subject" type="text" id="subject" size="50" maxlength="255" value="<?php echo $subject; ?>" /></td>
										</tr>
										<tr>
											<td>Message</td>
											<td>:</td>
											<td><textarea name="message" cols="50" rows="6" id="message"><?php echo $message; ?></textarea></td>
										</tr>
										<tr>
											<td>Name</td>
											<td>:</td>
											<td><input name="name" type="text" id="name" size="50" maxlength="255" value="<?php echo $name; ?>" /></td>
										</tr>
										<tr>
											<td>Email</td>
											<td>:</td>
											<td><input name="email" type="text" id="email" size="50" maxlength="255" value="<?php echo $email; ?>" /></td>
										</tr>
										<tr>
											<td>No spam</td>
											<td>:</td>
											<td><img src="/php/captcha.php?page=contact" />&nbsp;&nbsp;<input style="vertical-align:top;" name="captcha" type="text" id="captcha" maxlength="6" value="" /></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												<input type="submit" name="Submit" value="Submit" />
												<input type="reset" name="Submit2" value="Reset" />
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>
<!-- Right -->
				<div id="contentrview">
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
