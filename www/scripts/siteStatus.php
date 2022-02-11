<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

session_start();

if(isset($_SESSION['admin']))	$admin = $_SESSION['admin'];
else $admin = 0;

$siteStatus = getSiteStatus();

if(($admin == 1) || ($siteStatus == 1))	echo("data: 1\n\n");
else	echo("data: 0\n\n");

flush();
?>