<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

session_start();

$email = $_POST['email'];
$password = $_POST['password'];
$password = hashPassword($password);

getDB();
$result = mysql_query("SELECT * FROM users WHERE email = '$email' and password = '$password'");
$row = mysql_fetch_assoc($result);

$siteStatus = getSiteStatus();

if(($siteStatus == 0) && ($row['admin'] == 0))	echo("<script>window.location = \"/sitedown\"</script>");

if($row){
    if($row['verified'] == 0){
        setcookie("signinMessage", "Your account has not been verified.<br>Please check your e-mail for the verification link.", time() + 20, "/");
        logActivity("signinAttempt", getIDByEmail($email));
        echo("<script>window.location = \"/signin\"</script>");
    }
    else if($row['banned'] != 1){
        $_SESSION['userID'] = $row['id'];
        $_SESSION['admin'] = $row['admin'];
        $_SESSION['firstName'] = $row['firstName'];
        $_SESSION['lastName'] = $row['lastName'];
        logActivity("signin", $row['id']);
        $userID = $row['id'];
        mysql_query("UPDATE `users` SET failedLoginAttempts = '0' WHERE id = '$userID'");
        echo("<script>window.location = \"/\"</script>");
    }
    else{
        setcookie("signinMessage", "You have been banned.<br>Please contact an admin to learn more.", time() + 20, "/");
        logActivity("signinAttempt", getIDByEmail($email));
        echo("<script>window.location = \"/signin\"</script>");
    }
}
else{
    $userID = getIDByEmail($email);
    if($userID != -1){
        $attempts = intval(failedLoginAttempt($userID));
        sleep($attempts);
    }
    setcookie("signinMessage", "Your e-mail and/or password is invalid.", time() + 20, "/");
    logActivity("signinAttempt", getIDByEmail($email));
    echo("<script>window.location = \"/signin\"</script>");
}
?>