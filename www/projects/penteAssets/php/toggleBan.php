<?php
session_start();
if(isset($_SESSION['admin']))   if($_SESSION['admin'] == 1){
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
    $id = $_GET['id'];
    toggleBan($id);
}
else    echo("<script>window.location=\"/errors/404\"</script>");
?>