<?php
session_start();
if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
    $status = $_POST['status'];
    $headerMessage = $_POST['headerMessage'];
    $bodyMessage = $_POST['bodyMessage'];
    setSiteStatus($status, $headerMessage, $bodyMessage);
    logActivity("siteStatus", $_SESSION['userID']);
    echo("<script>window.location = \"/admin\"</script>");
}
else{
    header("Location: /errors/404");
    die();
}
?>