<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
	
	session_destroy();
	
	if(isset($_COOKIE['forgotMessage']))	$message = $_COOKIE['forgotMessage'];
	else	$message = "You will be sent a link to reset your password.";
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Forgot</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="styles/forgot.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Forgot Your Password?</div>
            <section>
                <form action="/scripts/forgotPost.php" method="post">
                	<div class="message"><?php echo("$message") ?></div>
                    E-mail: <input type="email" name="email" autofocus><br>
                    <input id="submit" type="submit" value="Request Link">
                </form>
			</section>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>