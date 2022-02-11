<?php
session_start();
if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
    $email = $_POST['email'];
    $id = getIDByEmail($email);
    toggleBan($id);
    echo("<script>window.location = \"/admin\"</script>");
}
else{
    header("Location: /errors/404");
    die();
}
?>