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
    	<title>gharryg.com | Sign In</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="styles/signin.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
            <div class="title">Sign In.</div>
            <section>
				<?php if(isset($_COOKIE['signinMessage']))	echo("<div class=\"message\">" . $_COOKIE['signinMessage'] . "</div>") ?>
                <form action="/scripts/signinPost.php" method="post">
                	<table>
                        <tr><td>E-mail: <input type="email" name="email" autofocus></td></tr>
                        <tr><td>Password: <input type="password" name="password"></td></tr>
                   	 	<tr><td class="center"><input id="submit" type="submit" value="Sign In"></td></tr>
                        <tr><td class="center"><small id="tinyLinks"><a href="forgot">Forgot your password?</a>&nbsp<a href="register">New User?</a></small></td></tr>
                    </table>
                </form>
			</section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>