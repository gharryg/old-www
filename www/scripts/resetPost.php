<?php
require_once("../required/utilities.php");

$password = $_POST['password'];
$password1 = $_POST['password1'];

session_start();

$email = $_SESSION['email'];
$code = $_SESSION['code'];

if($_SESSION['canReset'] == 1){
    if(empty($password) || empty($password1)){
        setcookie("resetMessage", "Please fill in every field.", time() + 20, "/");
        echo("<script>window.location = \"/reset?email=$email&code=$code\"</script>");
    }
    else{
        if($password == $password1){
            getDB();
            $newPassword = hashPassword($password);
            $result = mysql_query("UPDATE `users` SET password = '$newPassword' WHERE email = '$email'");
            session_destroy();
            $result = mysql_query("UPDATE `users` SET resetExpireTime = 0 WHERE email = '$email'");

            logActivity("passwordReset", getIDByEmail($email));

            setcookie("signinMessage", "Your password has been changed.", time() + 20, "/");
            echo("<script>window.location = \"/signin\"</script>");
        }
        else{
            setcookie("resetMessage", "Your passwords do not match.", time() + 20, "/");
            logActivity("passwordResetAttempt", getIDByEmail($email));
            echo("<script>window.location = \"/reset?email=$email&code=$code\"</script>");
        }
    }
}
else{
    logActivity("passwordResetAttempt", getIDByEmail($email));
    echo("<script>window.location = \"/\"</script>");
}
?>