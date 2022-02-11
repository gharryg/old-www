<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$email1 = $_POST['email1'];
$password = $_POST['password'];
$password1 = $_POST['password1'];
$terms = $_POST['terms'];

$problem = false;
$message = "";

if(empty($firstName) || empty($lastName) || empty($email) || empty($email1) || empty($password) || empty($password1)){
    setcookie("registerMessage", "Please fill in every field.", time() + 20, "/");
    echo("<script>history.go(-1)</script>");
}
elseif($terms != "agree"){
    setcookie("registerMessage", "You must agree to the Terms and Conditions.", time() + 20, "/");
    echo("<script>history.go(-1)</script>");
}
elseif($password == "password"){
    setcookie("registerMessage", "You put \"password\" for your password? Really?<br>Try again with a better password.", time() + 20, "/");
    echo("<script>history.go(-1)</script>");
}
else{
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE email = '$email'");
    $row = mysql_fetch_assoc($result);

    if($email != $email1){
        $message = "The e-mails do not match.";
        $problem = true;
    }
    else if($row){
        $message = "This e-mail is associated with another account.";
        $problem = true;
    }

    if(strlen($password) < 8){
        if($problem)	$message = $message . "<br>Your password must be at least 8 characters long!";
        else	$message = "Your password must be at least 8 characters long!";
        $problem = true;
    }
    elseif($password != $password1){
        if($problem)	$message = $message . "<br>The passwords do not match.";
        else	$message = "The passwords do not match.";
        $problem = true;
    }

    if($problem){
        setcookie("registerMessage", $message, time() + 20, "/");
        echo("<script>history.go(-1)</script>");
    }

    if(!$problem){
        $password = hashPassword($password);
        $verificationCode = makeCode();
        $firstName = ucfirst(strtolower($firstName));
        $lastName = ucfirst(strtolower($lastName));

        mysql_query("INSERT INTO users (firstName, lastName, email, password, verificationCode) VALUES ('$firstName', '$lastName', '$email', '$password', '$verificationCode')");

        logActivity("register", getIDByEmail($email));

        // 2022 UPDATE: Instead of sending an email, we'll just present the link.
        // $subject = "gharryg.com Verification E-Mail";
        // $content = "Click the link to verify you account: https://gharryg.com/scripts/verify.php?email=" . $email . "&code=" . $verificationCode;
        // $headers = "From: gharryg.com <noreply@gharryg.com>";
        // mail($email, $subject, $content, $headers);

        setcookie("signinMessage", "A verification e-mail has been sent to $email.<br>After clicking the link to verify your email, you may sign in.<br><i>If you use Gmail, make sure to check your junk folder!</i><br><small>Psst: Click <a href=\"/scripts/verify.php?email=$email&code=$verificationCode\">here</a> to verify.<small>", time() + 20, "/");
        echo("<script>window.location = \"/signin\"</script>");
    }
}
?>
