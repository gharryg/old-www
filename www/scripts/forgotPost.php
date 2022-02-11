<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

$email = $_POST['email'];

$problem = false;
$message = "";

getDB();
$result = mysql_query("SELECT * FROM users WHERE email = '$email'");
$row = mysql_fetch_assoc($result);

if($row){
    if($row['verified'] == 0){
        $problem = true;
        $message = "Your account has not been verified.<br>Please check your e-mail for a verification link.";
    }
    else if($row['banned'] != 1){
        $expireTime = time() + 900;
        $resetCode = makeCode();

        $result = mysql_query("UPDATE `users` SET resetCode = '$resetCode', resetExpireTime = '$expireTime' WHERE email = '$email'");

        // 2022 UPDATE: Instead of sending an email, we'll just present the link.
        // $subject = "gharryg.com Password Reset";
        // $content = "Click the link to reset your password: https://gharryg.com/reset?email=" . $email . "&code=" . $resetCode;
        // $headers = "From: gharryg.com <noreply@gharryg.com>";
        // mail($email, $subject, $content, $headers);

        logActivity("requestReset", getIDByEmail($email));

        setcookie("signinMessage", "You have been sent a password reset link.<br>If you use Gmail, make sure to check your junk folder!<br><small>Psst: Click <a href=\"/reset?email=$email&code=$resetCode\">here</a> to reset your password.<small>", time() + 20, "/");
        echo("<script>window.location = \"/signin\"</script>");
    }
    else{
        if($problem)	$message += "<br>You have been banned.";
        else{
            $problem = true;
            $message = "You have been banned.";
        }
    }
}
else{
    $problem = true;
    $message = "There is no account associated with this e-mail.";
}

if($problem){
    setcookie("forgotMessage", $message, time() + 20, "/");
    echo("<script>window.location = \"/forgot\"</script>");
}
?>
