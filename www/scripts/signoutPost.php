<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

session_start();

logActivity("signout", $_SESSION['userID']);

session_destroy();

echo("<script>window.location = \"/\"</script>");
?>