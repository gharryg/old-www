<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

session_start();

if(isset($_SESSION['admin']))	$admin = $_SESSION['admin'];
else $admin = 0;

if(isset($_SESSION['userID'])){
    $isBanned = checkBan($_SESSION['userID']);
}
else $isBanned = 0;

if(($admin == 1) || ($isBanned == 0))	echo("data: 0\n\n");
else	echo("data: 1\n\n");

flush();
?>