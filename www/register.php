<?php
	session_start();
	if(isset($_SESSION['userID'])){
		session_destroy();
		session_start();
	}
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Register</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once("required/head.php") ?>
        <link href="styles/register.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
            <div class="title">Register.</div>
            <section>
                <?php if(isset($_COOKIE['registerMessage']))	echo("<div class=\"message\">" . $_COOKIE['registerMessage'] . "</div>");
                else echo("<div class=\"message\">Please provide complete and real information.</div>")?>
                <form action="/scripts/registerPost.php" method="post">
                	<table>
                        <tr><td>First Name: <input type="text" name="firstName" autofocus></tr></td>
                        <tr><td>Last Name: <input type="text" name="lastName"></tr></td>
                        <tr><td>E-mail: <input type="email" name="email"></tr></td>
                        <tr><td>Re-type E-mail: <input type="email" name="email1"></tr></td>
                        <tr><td>Password: <input type="password" name="password"></tr></td>
                        <tr><td>Re-type Password: <input type="password" name="password1"></tr></td>
                        <tr><td><input type="checkbox" value="agree" name="terms"><small>I agree to the <a id="termsLink" href="/terms" target="_blank">Terms and Conditions</a> set by gharryg.com.</small></td></tr>
                        <tr><td class="center"><input id="submit" type="submit" value="Register"></tr></td>
					</table>
                </form>
			</section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>