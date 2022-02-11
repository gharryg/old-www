<?php
    session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
	
	if(!(getSiteStatus()) && ($_SESSION['admin'] != 1))	echo("<script>window.location = \"/sitedown\"</script>");
?>