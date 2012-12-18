<?php

// Start session
session_start();

// Regenerate session ID
session_regenerate_id(true);

// Set root url
$url_root="";

// If session was not yet started
if (!isset($_SESSION['dev_login']) || !($_SESSION['dev_login'])) {
	// If no username was posted
	if (!isset($_REQUEST['uname'])) {
		// Redirect to welcome page
		die("Enter a valid username!");
	}

	// Verify username and password
	require_once("php/funcs/db_funcs.php");

	// Get username
	$uname=trim($_REQUEST['uname']);

	// If username was not entered
	if ($uname=="") {
		die("Username is empty! Enter a valid username!");
	}

	// Check if the user was registered and get password
	$select_table="cr";
	$select_field_name=array();
	$select_field_value=array();
	$select_field_name[0]="cr_pwd";
	$select_field_name[1]="cr_id";
	$select_where_field_name=array();
	$select_where_field_value=array();
	$select_where_field_name[0]="cr_uname";
	$select_where_field_value[0]=$uname;
	$errors="";
	if (!db_select($select_table, $select_field_name, $select_where_field_name, $select_where_field_value, $select_field_value, $errors)) {
		// Database error
		switch ($errors) {
			case "Error in the parameters given":
				die($errors." to db_select()");
			default:
				die($errors);
		}
	}
	$num=count($select_field_value);

	// If this is a unknown user
	if($num==0) {
		// Unknown user
		die("Invalid Username!");
	}

	// It's a known user
	// Verify password
	$cr_pwd=$select_field_value[0][0];
	$cr_id=$select_field_value[0][1];
	if (crypt($_REQUEST['password'], $cr_pwd)!=$cr_pwd || $cr_id!=32) {
		// Wrong password
		die("Oops! Sorry, wrong password!");
	}
	// The user was correctly identified
	$_SESSION['dev_login']=TRUE;
}

	echo("Login Success!");
?>