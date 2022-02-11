<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

$email = $_GET['email'];
$code = $_GET['code'];

getDB();
$result = mysql_query("SELECT * FROM users WHERE email = '$email'");
$row = mysql_fetch_assoc($result);

if($code == $row['verificationCode']){
    $result = mysql_query("UPDATE `users` SET verified = 1 WHERE email = '$email'");
    setcookie("signinMessage", "Your account has been verified!", time() + 20, "/");
    logActivity("verify", getIDByEmail($email));
    echo("<script>window.location = \"/signin\"</script>");
}
else{
    setcookie("signinMessage", "Your account could not be verified.<br>Please contact a site admin.", time() + 20, "/");
    logActivity("verifyAttempt", getIDByEmail($email));
    echo("<script>window.location = \"/signin\"</script>");
}
?>