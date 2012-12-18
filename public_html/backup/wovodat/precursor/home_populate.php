<?php

// Start session
session_start();

// Regenerate session ID
session_regenerate_id(true);

// Initialize navigation variable
$_SESSION['redirect']="";

// Get root url
require_once "php/include/get_root.php";

// If session was already started
if (isset($_SESSION['login'])) {
	// Check login
	require_once("php/include/login_check.php");
	
	// Get cp_access
	$cp_access=$_SESSION['permissions']['access'];
	
	// Message from upload_form
	if (isset($_SESSION['upload_form']['upload_ok'])) {
		$upload_ok=$_SESSION['upload_form']['upload_ok'];
		$_SESSION['upload_form']['upload_ok']=FALSE;
	}
	else {
		$upload_ok=FALSE;
	}
}
else {
	// Session was not yet started
	// If no username was posted
	if (!isset($_POST['uname'])) {
		// Redirect to login required page
		header('Location: '.$url_root.'login_required.php');
		exit();
	}

	// Verify username and password
	require_once("php/funcs/db_funcs.php");

	// Get username
	$uname=trim($_POST['uname']);

	// If username was not entered
	if ($uname=="") {
		header('Location: '.$url_root.'index.php?attempt=1');
		exit();
	}

	// Check if the user was registered and get password
	$select_table="cr";
	$select_field_name=array();
	$select_field_value=array();
	$select_field_name[0]="cr_pwd";
	$select_where_field_name=array();
	$select_where_field_value=array();
	$select_where_field_name[0]="cr_uname";
	$select_where_field_value[0]=$uname;
	$errors="";
	if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
		// Database error
		switch ($errors) {
			case "Error in the parameters given":
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=1043;
				$_SESSION['errors'][0]['message']=$errors." to db_select()";
				$_SESSION['l_errors']=1;
				// Redirect user to system error page
				header('Location: '.$url_root.'system_error.php');
				exit();
			default:
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=4015;
				$_SESSION['errors'][0]['message']=$errors;
				$_SESSION['l_errors']=1;
				// Redirect user to database error page
				header('Location: '.$url_root.'db_error.php');
				exit();
		}
	}
	$num=count($select_field_value);

	// If this is an unknown user
	if ($num==0) {
		// Unknown user
		header('Location: '.$url_root.'index.php?attempt=1');
		exit();
	}

	// It's a known user
	// Verify password
	$cr_pwd=$select_field_value[0][0];
	if (crypt($_POST['password'], $cr_pwd)!=$cr_pwd) {
		// Wrong password
		header('Location: '.$url_root.'index.php?attempt=1');
		exit();
	}

	// The user was correctly identified
	
	// Store information in login history file
	$history_file=fopen("/home/wovodat/login_history.txt", "a");
	// If error when opening file
	if (!$history_file) {
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=2555;
		$_SESSION['errors'][0]['message']="An error occurred when trying to open login history file";
		$_SESSION['l_errors']=1;
		// Redirect user to server error page
		header('Location: '.$url_root.'server_error.php');
		exit();
	}
	$line=$uname."\t".$_SERVER['REMOTE_ADDR']."\t".date("c")."\n";
	if (!fwrite($history_file, $line)) {
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=2020;
		$_SESSION['errors'][0]['message']="An error occurred when trying to write login history file";
		$_SESSION['l_errors']=1;
		// Redirect user to server error page
		header('Location: '.$url_root.'server_error.php');
		exit();
	}
	if (!fclose($history_file)) {
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=2556;
		$_SESSION['errors'][0]['message']="An error occurred when trying to close login history file";
		$_SESSION['l_errors']=1;
		// Redirect user to server error page
		header('Location: '.$url_root.'server_error.php');
		exit();
	}
	
	// Get cr_id and cc_id
	$select_table="cr";
	$select_field_name=array();
	$select_field_value=array();
	$select_field_name[0]="cr_id";
	$select_field_name[1]="cc_id";
	$select_where_field_name=array();
	$select_where_field_value=array();
	$select_where_field_name[0]="cr_uname";
	$select_where_field_value[0]=$uname;
	$errors="";
	if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
		// Database error
		switch ($errors) {
			case "Error in the parameters given":
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=1044;
				$_SESSION['errors'][0]['message']=$errors." to db_select()";
				$_SESSION['l_errors']=1;
				// Redirect user to system error page
				header('Location: '.$url_root.'system_error.php');
				exit();
			default:
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=4016;
				$_SESSION['errors'][0]['message']=$errors;
				$_SESSION['l_errors']=1;
				// Redirect user to database error page
				header('Location: '.$url_root.'db_error.php');
				exit();
		}
	}
	$l_select_field_value=count($select_field_value);
	if ($l_select_field_value>1) {
		// Only 1 result should be found
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=1092;
		$_SESSION['errors'][0]['message']="Multiple rows in the cr table correspond to this cr_uname: '".$uname."'";
		$_SESSION['l_errors']=1;
		// Redirect user to system error page
		header('Location: '.$url_root.'system_error.php');
		exit();
	}
	$cr_id=$select_field_value[0][0];
	$cc_id=$select_field_value[0][1];

	// Get first name, last name and observatory name
	$select_table="cc";
	$select_field_name=array();
	$select_field_value=array();
	$select_field_name[0]="cc_fname";
	$select_field_name[1]="cc_lname";
	$select_field_name[2]="cc_obs";
	$select_where_field_name=array();
	$select_where_field_value=array();
	$select_where_field_name[0]="cc_id";
	$select_where_field_value[0]=$cc_id;
	$errors="";
	if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
		// Database error
		switch ($errors) {
			case "Error in the parameters given":
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=1045;
				$_SESSION['errors'][0]['message']=$errors." to db_select()";
				$_SESSION['l_errors']=1;
				// Redirect user to system error page
				header('Location: '.$url_root.'system_error.php');
				exit();
			default:
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=4017;
				$_SESSION['errors'][0]['message']=$errors;
				$_SESSION['l_errors']=1;
				// Redirect user to database error page
				header('Location: '.$url_root.'db_error.php');
				exit();
		}
	}
	$l_select_field_value=count($select_field_value);
	if ($l_select_field_value>1) {
		// Only 1 result should be found
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=1093;
		$_SESSION['errors'][0]['message']="Multiple rows in the cc table correspond to this cc_id: '".$cc_id."'";
		$_SESSION['l_errors']=1;
		// Redirect user to system error page
		header('Location: '.$url_root.'system_error.php');
		exit();
	}
	$cc_fname=$select_field_value[0][0];
	$cc_lname=$select_field_value[0][1];
	$cc_obs=$select_field_value[0][2];

	// Form user name
	$user_name="";
	if ($cc_fname!="") {
		$user_name.=$cc_fname;
		if ($cc_lname!="") {
			$user_name.=" ".$cc_lname;
		}
	}
	else {
		if ($cc_lname!="") {
			$user_name.=$cc_lname;
		}
		else {
			// No first name and no last name
			$user_name.=$cc_obs;
		}
	}

	// Store login information in session variable
	$_SESSION['login']=array();
	$_SESSION['login']['cr_uname']=$uname;
	$_SESSION['login']['cc_id']=$cc_id;
	$_SESSION['login']['user_name']=$user_name;

	// Get permission access
	$select_table="cp";
	$select_field_name=array();
	$select_field_value=array();
	$select_field_name[0]="cp_access";
	$select_where_field_name=array();
	$select_where_field_value=array();
	$select_where_field_name[0]="cr_id";
	$select_where_field_value[0]=$cr_id;
	$errors="";
	if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
		// Database error
		switch ($errors) {
			case "Error in the parameters given":
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=1046;
				$_SESSION['errors'][0]['message']=$errors." to db_select()";
				$_SESSION['l_errors']=1;
				// Redirect user to system error page
				header('Location: '.$url_root.'system_error.php');
				exit();
			default:
				$_SESSION['errors'][0]=array();
				$_SESSION['errors'][0]['code']=4018;
				$_SESSION['errors'][0]['message']=$errors;
				$_SESSION['l_errors']=1;
				// Redirect user to database error page
				header('Location: '.$url_root.'db_error.php');
				exit();
		}
	}
	$l_select_field_value=count($select_field_value);
	if ($l_select_field_value>1) {
		// Only 1 result should be found
		$_SESSION['errors'][0]=array();
		$_SESSION['errors'][0]['code']=1094;
		$_SESSION['errors'][0]['message']="Multiple rows in the cp table correspond to this cr_id: '".$cr_id."'";
		$_SESSION['l_errors']=1;
		// Redirect user to system error page
		header('Location: '.$url_root.'system_error.php');
		exit();
	}
	$cp_access=$select_field_value[0][0];
	// Store permissions variable in session
	$_SESSION['permissions']=array();
	$_SESSION['permissions']['access']=$cp_access;

	// If the user is not a developper, get for whom they have permissions
	if ($cp_access!=0) {
		$select_table="jj_concon";
		$select_field_name=array();
		$select_field_value=array();
		$select_field_name[0]="cc_id";
		$select_field_name[1]="jj_concon_view";
		$select_field_name[2]="jj_concon_upload";
		$select_field_name[3]="jj_concon_update";
		$select_field_name[4]="jj_concon_admin";
		$select_where_field_name=array();
		$select_where_field_value=array();
		$select_where_field_name[0]="cc_id_granted";
		$select_where_field_value[0]=$cc_id;
		$errors="";
		if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
			// Database error
			switch ($errors) {
				case "Error in the parameters given":
					$_SESSION['errors'][0]=array();
					$_SESSION['errors'][0]['code']=1046;
					$_SESSION['errors'][0]['message']=$errors." to db_select()";
					$_SESSION['l_errors']=1;
					// Redirect user to system error page
					header('Location: '.$url_root.'system_error.php');
					exit();
				default:
					$_SESSION['errors'][0]=array();
					$_SESSION['errors'][0]['code']=4018;
					$_SESSION['errors'][0]['message']=$errors;
					$_SESSION['l_errors']=1;
					// Redirect user to database error page
					header('Location: '.$url_root.'db_error.php');
					exit();
			}
		}
		// Get results
		$num_users=count($select_field_value);
		// Create user permissions
		$user_view=array();
		$l_user_view=0;
		$user_upload=array();
		$l_user_upload=0;
		$user_update=array();
		$l_user_update=0;
		$user_admin=array();
		$l_user_admin=0;
		// Loop on results
		for ($i=0; $i<$num_users; $i++) {
			// Local variable
			$user_id=$select_field_value[$i][0];
			
			$select_table="cc";
			$select_field_name=array();
			$select_field_value=array();
			$select_field_name[0]="cc_fname";
			$select_field_name[1]="cc_lname";
			$select_field_name[2]="cc_obs";
			$select_field_name[3]="cc_code";
			$select_where_field_name=array();
			$select_where_field_value=array();
			$select_where_field_name[0]="cc_id";
			$select_where_field_value[0]=$user_id;
			$errors="";
			if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
				// Database error
				switch ($errors) {
					case "Error in the parameters given":
						$_SESSION['errors'][0]=array();
						$_SESSION['errors'][0]['code']=1046;
						$_SESSION['errors'][0]['message']=$errors." to db_select()";
						$_SESSION['l_errors']=1;
						// Redirect user to system error page
						header('Location: '.$url_root.'system_error.php');
						exit();
					default:
						$_SESSION['errors'][0]=array();
						$_SESSION['errors'][0]['code']=4018;
						$_SESSION['errors'][0]['message']=$errors;
						$_SESSION['l_errors']=1;
						// Redirect user to database error page
						header('Location: '.$url_root.'db_error.php');
						exit();
				}
			}
			// Get results
			$cc_fname=htmlentities($select_field_value[0][0], ENT_COMPAT, "cp1252");
			$cc_lname=htmlentities($select_field_value[0][1], ENT_COMPAT, "cp1252");
			$cc_obs=htmlentities($select_field_value[0][2], ENT_COMPAT, "cp1252");
			$cc_code=htmlentities($select_field_value[0][3], ENT_COMPAT, "cp1252");
			
			// Form user name
			if (trim($cc_code)!="") {
				$username=$cc_code." - ";
			}
			else {
				$username="";
			}
			if ($cc_fname!="") {
				$username.=$cc_fname;
				if ($cc_lname!="") {
					$username.=" ".$cc_lname;
				}
			}
			else {
				if ($cc_lname!="") {
					$username.=$cc_lname;
				}
				else {
					// No first name and no last name
					$username.=$cc_obs;
				}
			}
			
			// Viewing permissions
			if ($select_field_value[$i][1]==1) {
				// Store ID and name in user viewing permission array
				$user_view['id'][$l_user_view]=$user_id;
				$user_view['name'][$l_user_view]=$username;
				$l_user_view++;
			}
			
			// Uploading permissions
			if ($select_field_value[$i][2]==1) {
				// Store ID and name in user uploading permission array
				$user_upload['id'][$l_user_upload]=$user_id;
				$user_upload['name'][$l_user_upload]=$username;
				$l_user_upload++;
			}
			
			// Updating permissions
			if ($select_field_value[$i][3]==1) {
				// Store ID and name in user updating permission array
				$user_update['id'][$l_user_update]=$user_id;
				$user_update['name'][$l_user_update]=$username;
				$l_user_update++;
			}
			
			// Admin permissions
			if ($select_field_value[$i][4]==1) {
				// Store ID and name in user admin permission array
				$user_admin['id'][$l_user_admin]=$user_id;
				$user_admin['name'][$l_user_admin]=$username;
				$l_user_admin++;
			}
		}
	}
	// User is a developer
	else {
		$select_table="cc";
		$select_field_name=array();
		$select_field_value=array();
		$select_field_name[0]="cc_id";
		$select_field_name[1]="cc_fname";
		$select_field_name[2]="cc_lname";
		$select_field_name[3]="cc_obs";
		$select_field_name[4]="cc_code";
		$select_where_field_name=array();
		$select_where_field_value=array();
		$errors="";
		if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
			// Database error
			switch ($errors) {
				case "Error in the parameters given":
					$_SESSION['errors'][0]=array();
					$_SESSION['errors'][0]['code']=1046;
					$_SESSION['errors'][0]['message']=$errors." to db_select()";
					$_SESSION['l_errors']=1;
					// Redirect user to system error page
					header('Location: '.$url_root.'system_error.php');
					exit();
				default:
					$_SESSION['errors'][0]=array();
					$_SESSION['errors'][0]['code']=4018;
					$_SESSION['errors'][0]['message']=$errors;
					$_SESSION['l_errors']=1;
					// Redirect user to database error page
					header('Location: '.$url_root.'db_error.php');
					exit();
			}
		}
		// Get results
		$num_users=count($select_field_value);
		// Create user permissions
		$user_view=array();
		$l_user_view=0;
		$user_upload=array();
		$l_user_upload=0;
		$user_update=array();
		$l_user_update=0;
		$user_admin=array();
		$l_user_admin=0;
		// Loop on results
		for ($i=0; $i<$num_users; $i++) {
			// Local variable
			$user_id=$select_field_value[$i][0];
			
			// Do not include the user himself
			if ($user_id==$cc_id) {
				continue;
			}
			
			$cc_fname=htmlentities($select_field_value[$i][1], ENT_COMPAT, "cp1252");
			$cc_lname=htmlentities($select_field_value[$i][2], ENT_COMPAT, "cp1252");
			$cc_obs=htmlentities($select_field_value[$i][3], ENT_COMPAT, "cp1252");
			$cc_code=htmlentities($select_field_value[$i][4], ENT_COMPAT, "cp1252");
			
			// Form user name
			if (trim($cc_code)!="") {
				$username=$cc_code." - ";
			}
			else {
				$username="";
			}
			if ($cc_fname!="") {
				$username.=$cc_fname;
				if ($cc_lname!="") {
					$username.=" ".$cc_lname;
				}
			}
			else {
				if ($cc_lname!="") {
					$username.=$cc_lname;
				}
				else {
					// No first name and no last name
					$username.=$cc_obs;
				}
			}
			
			// Store ID and name in user viewing permission array
			$user_view['id'][$l_user_view]=$user_id;
			$user_view['name'][$l_user_view]=$username;
			$l_user_view++;
			// Store ID and name in user uploading permission array
			$user_upload['id'][$l_user_upload]=$user_id;
			$user_upload['name'][$l_user_upload]=$username;
			$l_user_upload++;
			// Store ID and name in user updating permission array
			$user_update['id'][$l_user_update]=$user_id;
			$user_update['name'][$l_user_update]=$username;
			$l_user_update++;
			// Store ID and name in user admin permission array
			$user_admin['id'][$l_user_admin]=$user_id;
			$user_admin['name'][$l_user_admin]=$username;
			$l_user_admin++;
		}
	}
	
	// Sort arrays
	if ($l_user_view>1) {
		$user_view_lowercase=array_map('strtolower', $user_view['name']);
		array_multisort($user_view_lowercase, $user_view['name'], $user_view['id']);
	}
	if ($l_user_upload>1) {
		$user_upload_lowercase=array_map('strtolower', $user_upload['name']);
		array_multisort($user_upload_lowercase, $user_upload['name'], $user_upload['id']);
	}
	if ($l_user_update>1) {
		$user_update_lowercase=array_map('strtolower', $user_update['name']);
		array_multisort($user_update_lowercase, $user_update['name'], $user_update['id']);
	}
	if ($l_user_admin>1) {
		$user_admin_lowercase=array_map('strtolower', $user_admin['name']);
		array_multisort($user_admin_lowercase, $user_admin['name'], $user_admin['id']);
	}
	
	// Store permissions
	$_SESSION['permissions']['user_view']=$user_view;
	$_SESSION['permissions']['l_user_view']=$l_user_view;
	$_SESSION['permissions']['user_upload']=$user_upload;
	$_SESSION['permissions']['l_user_upload']=$l_user_upload;
	$_SESSION['permissions']['user_update']=$user_update;
	$_SESSION['permissions']['l_user_update']=$l_user_update;
	$_SESSION['permissions']['user_admin']=$user_admin;
	$_SESSION['permissions']['l_user_admin']=$l_user_admin;
	
	// No "upload ok" message
	$upload_ok=FALSE;
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
	<script language="javascript" type="text/javascript" src="/js/scripts.js"></script>
	<script src="/js/jquery-1.4.2.min.js"></script>
	<script language="javascript" type="text/javascript">
		function sendfile(){
			$("#submitfile2").hide();
			$.get('/populate/submit_file_pu.php', show_form_submit);
		}
		function show_form_submit(res){
			$('#submitfile').html(res);
		}
		function convertfile(){
			$("#submitfile2").hide();
			$.get('/populate/convert_file_pu.php', show_form_submit);
		}
		function uploadwovomlfile(){
			$("#submitfile2").hide();
			$.get('/populate/upload_file_pu.php?type=wovoml',  show_form_submit);
		}
		function uploadcsvfile(){
			$.get('/populate/upload_csvfile_pu.php?type=wovoml', show_form_submit);
		}
		function uploadform(){
			$.get('/populate/upload_withform_pu.php', show_form_submit);
		}
	</script>
</head>
<body>
	<script language="JavaScript" src="/js2/menu_array.js" type="text/javascript"></script>
	<script language="JavaScript" src="/js2/mmenu.js" type="text/javascript"></script>

	<div id="wrapborder">
	<div id="wrap">
		<?php include 'php/include/header_beta.php'; ?>
		<!-- Content -->
		<div id="content">	
			<div id="contentl"><br>
			
		<!-- Top of the page -->
		<!-- Page content -->
		<h2 style="padding:0px 20px 0px 25px;"><b>DATA POPULATION</b></h2>
		<div style="padding:0px 20px 0px 25px;">
			<p class="green"><?php if ($upload_ok) {print "Upload successful!";} ?></p>
			<p align="justify" style="font-size:13px;"><blockquote>
			<b>Thank you</b> for your data contribution!  <br> For now, the WOVOdat-MySQL system accepts only data in the <a href="/doc/system/0.2/wovoml.php">WOVOml</a> format. We offer 4 options for contributors to submit data:</blockquote>
			</p>
		</div><br>
		<div style="padding:0px 10px 0px 25px;font-size:11px;">
			<p><span style="font-family:times; font-size:12px;color:blue; text-decoration:underline;"><a href="javascript:sendfile()">1.<b>Submit</b> a file</a></span><br>
			Submit a file in its original observatory data format. Choose this option if you wish to send a file of any format to WOVOdat and let the WOVOdat team convert and upload it to the database; We suggest to use this option for contributing to WOVOdat database, OR</p>
			<p><span style="font-family:times; font-size:12px;color:blue; text-decoration:underline;"><a href="javascript:convertfile()">2.<b>Convert</b> csv-data to WOVOML and Submit</a></span><br>		
			This option presently accepts generic csv datafiles (known format) and converts them to WOVOml format; This applicable for a small files<300kb; OR</p>
			<p><span style="font-family:times; font-size:12px;color:blue; text-decoration:underline;"><a href="javascript:uploadwovomlfile()">3.<b>Upload</b> WOVOml datafile to WOVOdat</a></span><br>
			This option submit a file in WOVOml1.1 format directly into the database.   The system will check if the file is properly formatted.  If not, the uploader needs to check again the format of the data file or sent the file with "1" option above.  OR</p>
			<p><span style="font-family:times; font-size:12px; color:blue; text-decoration:underline;"><a href="javascript:uploadform()">4.<b>Upload</b> data  manually, via forms</a></span><br>
			This option may be the easiest for small amounts of data.   Please refer to WOVOdat 1.1 for the exact names of the fields and other details of required formats..</p>

<?php

// If user is developer, allow them to see link to view data (temporary links)
//if ($cp_access==0) {
//	print <<<STRING
//				<br />
//				<h3>For developers (temporary):</h3>
//				<li>
//					<p><a href="select_volcano.php">View eruptions</a></p>
//				</li>
//STRING;
//}
// If user is Alex, allow them to see link to do DB admin (can be changed later, it's just to avoid making other developers confused)
if ($_SESSION['login']['cc_id']==3) {
	print <<<STRING
				<br />
				<h3>Admin tools</h3>
				<li><p><a href="check_select_table.php">Check tables</a></p></li>
				<li><p><a href="delete_ul_file_list.php">File incoming</a></p></li>
STRING;
}

?>
			</ul>
		</div>
			
			</div>
			<div id="contentr">
				<div id="top" align="left">
					<!-- Aligned to the right: You are logged in as username (FName LName | Obs) | Logout -->
					<p align="right">Login as: <b><?php print $uname; ?></b>|<a href="logout.php">Logout</a></p>
				</div><br>
		
				<div id="submitfile">
				</div>
				<div id="submitfile2">
					<br><br>
					<p align="center"><img src="/gif2/flowschema2.png" width="360" height="340" alt="schema"></p>
				</div>
			</div>
		</div>
		
		<!-- Footer -->
		<div id="footer">
			<?php include 'php/include/footer_beta.php'; ?>
		</div>
		
	</div>
</body>
</html>