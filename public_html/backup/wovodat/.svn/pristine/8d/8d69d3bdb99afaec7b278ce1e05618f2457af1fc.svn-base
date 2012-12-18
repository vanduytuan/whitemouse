<?php

/**********************************

This file creates a captcha image in form of HTML.
The captcha is made of 6 characters randomly chosen and the string is saved in a SESSION variable for testing later.

**********************************/

// Start session
session_start();

// Get origin page, if any - that determines in which SESSION variable the captcha string will be stored
if (!empty($_GET['page'])) {
	// The name of the origin page (e.g.: "contact" for the contact form)
	$page=$_GET['page'];
}
else {
	// Default page - captcha used on the registration page
	$page="register";
}

// JPGraph library
require_once 'php/lib/jpgraph/jpgraph_antispam.php';

// New instance of AntiSpam
$anti_spam=new AntiSpam();

// Generate new random string of 6 characters
$chars=$anti_spam->Rand(6);

// Save captcha answer in SESSION
$_SESSION[$page]['captcha']=$chars;

// Output image
$anti_spam->Stroke();

?>